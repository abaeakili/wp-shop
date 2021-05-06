<?php
class CurrenciesWshopAdminController extends WshopAdminController {
    function __construct() {
        parent::__construct();
    }
    function display() {
        $config = Factory::getConfig();
        $context = "admin.currencies.";
        $filter_order = getStateFromRequest($context.'filter_order', 'filter_order', "currency_ordering");
        $filter_order_Dir = getStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc");

        $current_currency = Factory::getTable('currency');
        $current_currency->load($config->mainCurrency);
        if ($current_currency->currency_value!=1){
            addMessage(_WOP_SHOP_ERROR_MAIN_CURRENCY_VALUE);
        }

        $currencies = $this->getModel("currencies");
        $rows = $currencies->getAllCurrencies(0, $filter_order, $filter_order_Dir);

        $actions = array(
            'delete' => _WOP_SHOP_DELETE,
            'publish' => _WOP_SHOP_ACTION_PUBLISH,
            'unpublish' => _WOP_SHOP_ACTION_UNPUBLISH,
        );
        $bulk = $currencies->getBulkActions($actions);
        if($filter_order_Dir == 'asc') $filter_order_Dir = 'desc'; else $filter_order_Dir = 'asc';

        $view=$this->getView("currencies");
        $view->setLayout("list");        
        $view->assign('rows', $rows);        
        $view->assign('config', $config);
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);
        $view->assign('bulk',$bulk);
		do_action_ref_array('onBeforeDisplayCourencies', array(&$view));
        $view->display();
    }
    
    function edit(){
        $currency = Factory::getTable('currency');
        $currencies = $this->getModel("currencies");
        $currency_id = Request::getInt('rows');
        $currency->load($currency_id);
        if ($currency->currency_value==0) $currency->currency_value = 1;
        $first[] = HTML::_('select.option', '0',_WOP_SHOP_ORDERING_FIRST,'currency_ordering','currency_name');
        $rows = array_merge($first, $currencies->getAllCurrencies() );
        $lists['order_currencies'] = HTML::_('select.genericlist', $rows, 'currency_ordering', 'class="form-control" size="1"', 'currency_ordering', 'currency_name', $currency->currency_ordering);
        $edit = ($currency_id)?($edit = 1):($edit = 0);
        //FilterOutput::objectHTMLSafe( $currency, ENT_QUOTES);
        $view=$this->getView("currencies");
        $view->setLayout("edit");
        $view->assign('currency', $currency);
        $view->assign('lists', $lists);
        $view->assign('edit', $edit);
        do_action_ref_array('onBeforeEditCurrencies', array(&$view));        
        $view->display();
    } 

    function save(){
        if (check_admin_referer('coutry_edit','name_of_nonce_field') ) {
            $currency_id = Request::getInt("currency_id");
            $currency = Factory::getTable('currency');
            $post = Request::get("post");
            $post['currency_value'] = saveAsPrice($post['currency_value']);
            do_action_ref_array( 'onBeforeSaveCurrencie', array(&$post) );
            if (!$currency->bind($post)) {
                addMessage(_WOP_SHOP_ERROR_BIND, 'error');
                $this->setRedirect("admin.php?page=options&tab=currencies");
                return 0;
            }
            if ($currency->currency_value==0) $currency->currency_value = 1;

            $this->_reorderCurrency($currency);
            if (!$currency->store()) {
                addMessage(_WOP_SHOP_ERROR_SAVE_DATABASE, 'error');
                $this->setRedirect("admin.php?page=options&tab=currencies");
                return 0;
            }
            do_action_ref_array( 'onAfterSaveCurrencie', array(&$currency) );
            $this->setRedirect("admin.php?page=options&tab=currencies");
        }
    }

    function _reorderCurrency(&$currency) {
        global $wpdb;
        $query = "UPDATE `".$wpdb->prefix."wshop_currencies` SET `currency_ordering` = currency_ordering + 1 WHERE `currency_ordering` > '" . $currency->currency_ordering . "'";
        $wpdb->get_results($query);
        $currency->currency_ordering++;
    }

    function delete(){
        global $wpdb;
        $text = '';
        $cid = Request::getVar("rows");
		do_action_ref_array( 'onBeforeRemoveCurrencie', array(&$cid) );
        foreach ($cid as $key => $value) {
            if($wpdb->delete( $wpdb->prefix.'wshop_currencies', array( 'currency_id' => esc_sql($value) )))
                $text .= _WOP_SHOP_CURRENCY_DELETED."<br>";
            else
                $text .= _WOP_SHOP_CURRENCY_ERROR_DELETED."<br>";
        }
		do_action_ref_array( 'onAfterRemoveCurrencie', array(&$cid) );
        $this->setRedirect('admin.php?page=options&tab=currencies', $text);
    }
    
    function publish(){
        $this->publishCurrency(1);
    }
    
    function unpublish(){
        $this->publishCurrency(0);
    }
    
    function publishCurrency($flag) {
        $cid = Request::getVar("rows");
		do_action_ref_array( 'onBeforePublishCurrencie', array(&$cid, &$flag) );
        global $wpdb;
        $config = Factory::getConfig();
        foreach ($cid as $key => $value) {
            if($value == $config->mainCurrency){
                //addMessage(_WOP_SHOP_ERROR_BIND, 'error');
                continue;
            }
            $wpdb->update( $wpdb->prefix.'wshop_currencies', array( 'currency_publish' => esc_sql($flag) ), array( 'currency_id' => esc_sql($value) ));
        }
		do_action_ref_array( 'onAfterPublishCurrencie', array(&$cid, &$flag) );
        $this->setRedirect("admin.php?page=options&tab=currencies");
        
    } 
    
    function setdefault(){
        $config = Factory::getConfig();
        $cid = Request::getInt("currency_id");
        //global $wpdb;
        if ($cid){
            $configuration = Factory::getTable('configuration');
            //print_r($configuration);
            $configuration->id = '1';
            $configuration->mainCurrency = $cid;
            $configuration->store();
        }
        $this->setRedirect('admin.php?page=options&tab=currencies');
    }
	
    function order(){
		$order = Request::getVar("order");
		$cid = Request::getInt("id");
		$number = Request::getInt("number");
		global $wpdb;
		switch ($order) {
			case 'up':
				$query = "SELECT a.currency_id, a.currency_ordering
					   FROM `".$wpdb->prefix."wshop_currencies` AS a
					   WHERE a.currency_ordering < '" . $number . "'
					   ORDER BY a.currency_ordering DESC
					   LIMIT 1";
				break;
			case 'down':
				$query = "SELECT a.currency_id, a.currency_ordering
					   FROM `".$wpdb->prefix."wshop_currencies` AS a
					   WHERE a.currency_ordering > '" . $number . "'
					   ORDER BY a.currency_ordering ASC
					   LIMIT 1";
		}
		$row = $wpdb->get_row($query);
		$query1 = "UPDATE `".$wpdb->prefix."wshop_currencies` AS a
					 SET a.currency_ordering = '" . $row->currency_ordering . "'
					 WHERE a.currency_id = '" . $cid . "'";
		$query2 = "UPDATE `".$wpdb->prefix."wshop_currencies` AS a
					 SET a.currency_ordering = '" . $number . "'
					 WHERE a.currency_id = '" . $row->currency_id . "'";
		$wpdb->query($query1);
		$wpdb->query($query2);
		
		$this->setRedirect("admin.php?page=options&tab=currencies");		
    }
    
    function saveorder(){
		$cid = Request::getVar("rows");
        $order = Request::getVar('order', array(), 'post', 'array' );                
        foreach($cid as $k=>$id){
            $table = Factory::getTable('currency');
            $table->load($id);
            if ($table->currency_ordering!=$order[$k]){
                $table->currency_ordering = $order[$k];
                $table->store();
            }
        }                
        $this->setRedirect("admin.php?page=options&tab=currencies");		
    }		
    
}