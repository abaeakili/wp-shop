<?php
class PaymentsWshopAdminController extends WshopAdminController {
    function __construct() {
        $config = Factory::getConfig();
        parent::__construct();
         if (file_exists($config->path.'payments/payment.php')){
             include_once $config->path.'payments/payment.php';            
         }
    }
    function display() {
        $context = "admin.payments.";
        $filter_order = getStateFromRequest($context.'filter_order', 'filter_order', "payment_ordering");
        $filter_order_Dir = getStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc");

        $model = $this->getModel('payments');
        $rows = $model->getAllPaymentMethods(0, $filter_order, $filter_order_Dir);        
        $actions = array(
            'delete' => _WOP_SHOP_DELETE,
            'publish' => _WOP_SHOP_ACTION_PUBLISH,
            'unpublish' => _WOP_SHOP_ACTION_UNPUBLISH,
        );
        $bulk = $model->getBulkActions($actions);
        if($filter_order_Dir == 'asc') $filter_order_Dir = 'desc'; else $filter_order_Dir = 'asc';
        $view = $this->getView('payments');
        $view->setLayout('list');
        $view->assign('bulk',$bulk);
	$view->assign('rows', $rows);
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);
		do_action_ref_array('onBeforeDisplayPayments', array(&$view));
        $view->display();              
    }

    function edit(){
        $config = Factory::getConfig();
        $payment_id = Request::getInt("rows");
        $payment = Factory::getTable('paymentmethod');
        $payment->load($payment_id);
        $parseString = new parseString($payment->payment_params);
        $params = $parseString->parseStringToParams();
        $edit = ($payment_id)?($edit = 1):($edit = 0);

        $_lang = $this->getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
        $_payments = $this->getModel("payments");

        if ($edit){
            $paymentsysdata = $payment->getPaymentSystemData();
            if ($paymentsysdata->paymentSystem){
                ob_start();
                $paymentsysdata->paymentSystem->showAdminFormParams($params);
                $lists['html'] = ob_get_contents();
                ob_get_clean();
            }else{
                $lists['html'] = '';
            }
        } else {
            $lists['html'] = '';
        }
        $currencyCode = getMainCurrencyCode();
        if ($config->tax){
            $_tax = $this->getModel("taxes");
            $all_taxes = $_tax->getAllTaxes();
            $list_tax = array();
            $list_tax[] = HTML::_('select.option', -1,_WOP_SHOP_PRODUCT_TAX_RATE,'tax_id','tax_name');
            foreach($all_taxes as $tax) {
                $list_tax[] = HTML::_('select.option', $tax->tax_id, $tax->tax_name . ' (' . $tax->tax_value . '%)','tax_id','tax_name');
            }
            $lists['tax'] = HTML::_('select.genericlist', $list_tax, 'tax_id', 'class = "inputbox"','tax_id','tax_name', $payment->tax_id);
        }

        $list_price_type = array();
        $list_price_type[] = HTML::_('select.option', "1", $currencyCode, 'id','name');
        $list_price_type[] = HTML::_('select.option', "2", "%", 'id','name');
        $lists['price_type'] = HTML::_('select.genericlist', $list_price_type, 'price_type', 'class = "inputbox"', 'id', 'name', $payment->price_type);

        $payment_type = array('1' => _WOP_SHOP_TYPE_DEFAULT,'2' => _WOP_SHOP_PAYPAL_RELATED);;
        $opt = array();
        foreach($payment_type as $key => $value) {
            $opt[] = HTML::_('select.option', $key, $value, 'id', 'name');
        }
        if ($config->shop_mode==0 && $payment_id){
            $disabled = 'disabled';
        }else{
            $disabled = '';
        }
        $lists['type_payment'] = HTML::_('select.genericlist', $opt, 'payment_type','class = "inputbox" '.$disabled, 'id','name', $payment->payment_type);

        $nofilter = array();
        //FilterOutput::objectHTMLSafe($payment, ENT_QUOTES, $nofilter);

        $view = $this->getView('payments');
        $view->setLayout('edit');
        $view->assign('payment', $payment);
        $view->assign('edit', $edit);
        $view->assign('params', $params);
        $view->assign('lists', $lists);
        $view->assign('languages', $languages);
        $view->assign('multilang', $multilang);
        $view->assign('config', $config); 
		do_action_ref_array('onBeforeEditPayments', array(&$view));
        $view->display(); 
    }
    function save(){
        if (check_admin_referer('payment_edit','wop_shop') && !empty($_POST)  ) {
            $payment_id = Request::getInt("payment_id");
        
            $payment = Factory::getTable('paymentmethod');
            $post = Request::get("post");

            if (!isset($post['payment_publish'])) $post['payment_publish'] = 0;
            if (!isset($post['show_descr_in_email'])) $post['show_descr_in_email'] = 0;
            $post['price'] = saveAsPrice($post['price']);
            $post['payment_class'] = Request::getCmd("payment_class");
            if (!$post['payment_id']) $post['payment_type'] = 1;
			do_action_ref_array( 'onBeforeSavePayment', array(&$post) );
            $_lang = $this->getModel("languages");
            $languages = $_lang->getAllLanguages(1);

            foreach($languages as $lang){
                $post['description_'.$lang->language] = Request::getVar('description'.$lang->id,'','post',"string",2);
            }
            $payment->bind($post);

            $_payments = $this->getModel("payments");
            if (!$payment->payment_id){
                $payment->payment_ordering = $_payments->getMaxOrdering() + 1;
            }
            if (isset($post['pm_params'])) {
                $parseString = new parseString($post['pm_params']);
                $payment->payment_params = $parseString->splitParamsToString();
            }

            if (!$payment->check()){
                addMessage($payment->getError());
                $this->setRedirect("admin.php?page=options&tab=payments&task=edit&rows=".$payment->payment_id);
                return 0;
            }
            $payment->store();

			do_action_ref_array('onAfterSavePayment', array(&$payment) );
            $this->setRedirect("admin.php?page=options&tab=payments", _WOP_SHOP_MESSAGE_SAVEOK);
            
        } 
    }  
    
    function publish(){
        $this->publishPayment(1);
    }
    
    function unpublish(){
        $this->publishPayment(0);
    }
    
    function delete(){
        global $wpdb;
        $cid = Request::getVar("rows");
        $text = '';
		do_action_ref_array( 'onBeforeRemovePayment', array(&$cid) );
        foreach ($cid as $key => $value) {
            $result = $wpdb->delete( $wpdb->prefix.'wshop_payment_method', array( 'payment_id' => esc_sql($value) ));
            //$wpdb->show_errors();            $wpdb->print_error();
            if ($result > 0)
                $text .= _WOP_SHOP_PAYMENT_DELETED."<br>";
            else
                $text .= _WOP_SHOP_ERROR_PAYMENT_DELETED."<br>";
        }
		do_action_ref_array( 'onAfterRemovePayment', array(&$cid) );
        $this->setRedirect("admin.php?page=options&tab=payments", $text);
        /*$this->publishPayment(-1);
        $this->setRedirect('admin.php?page=options&tab=payments', _WOP_SHOP_ACTION_DELETED);*/
    }

    function publishPayment($flag){
        global $wpdb;
        $rows = Request::getVar("rows");
		do_action_ref_array( 'onBeforePublishPayment', array(&$rows, &$flag) );
        foreach ($rows as $key => $value) {
            $wpdb->update( $wpdb->prefix.'wshop_payment_method', array( 'payment_publish' => esc_sql($flag) ), array( 'payment_id' => esc_sql($value) ));
        }
		do_action_ref_array( 'onAfterPublishPayment', array(&$cid, &$flag) );
        $this->setRedirect('admin.php?page=options&tab=payments', $text);
    }
	
    function order(){
		$order = Request::getVar("order");
		$cid = Request::getInt("id");
		$number = Request::getInt("number");
		global $wpdb;
		switch ($order) {
			case 'up':
				$query = "SELECT a.payment_id, a.payment_ordering
					   FROM `".$wpdb->prefix."wshop_payment_method` AS a
					   WHERE a.payment_ordering < '" . $number . "'
					   ORDER BY a.payment_ordering DESC
					   LIMIT 1";
				break;
			case 'down':
				$query = "SELECT a.payment_id, a.payment_ordering
					   FROM `".$wpdb->prefix."wshop_payment_method` AS a
					   WHERE a.payment_ordering > '" . $number . "'
					   ORDER BY a.payment_ordering ASC
					   LIMIT 1";
		}
		$row = $wpdb->get_row($query);
		$query1 = "UPDATE `".$wpdb->prefix."wshop_payment_method` AS a
					 SET a.payment_ordering = '" . $row->payment_ordering . "'
					 WHERE a.payment_id = '" . $cid . "'";
		$query2 = "UPDATE `".$wpdb->prefix."wshop_payment_method` AS a
					 SET a.payment_ordering = '" . $number . "'
					 WHERE a.payment_id = '" . $row->payment_id . "'";
		$wpdb->query($query1);
		$wpdb->query($query2);
		
		$this->setRedirect("admin.php?page=options&tab=payments");		
    }
    
    function saveorder(){
		$cid = Request::getVar("rows");
        $order = Request::getVar('order', array(), 'post', 'array' );                
        foreach($cid as $k=>$id){
            $table = Factory::getTable('paymentmethod');
            $table->load($id);
            if ($table->payment_ordering!=$order[$k]){
                $table->payment_ordering = $order[$k];
                $table->store();
            }
        }                
        $this->setRedirect("admin.php?page=options&tab=payments");		
    }	
}