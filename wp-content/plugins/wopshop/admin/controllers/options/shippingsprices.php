<?php
class ShippingsPricesWshopAdminController extends WshopAdminController {
    function __construct() {
        parent::__construct();
    }
   
    function display() {
        global $wpdb;

        $config = Factory::getConfig();
        $context = "admin.shippingsprices.";
        $filter_order = getStateFromRequest($context.'filter_order', 'filter_order', "shipping_price.sh_pr_method_id");
        $filter_order_Dir = getStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc");
        //$orderby = getStateFromRequest('shippingsprices_orderby', 'orderby', 'shipping_price.sh_pr_method_id');
        //$order = getStateFromRequest('shippingsprices_order', 'order', 'asc');

        $shipping_id_back = Request::getInt("shipping_id_back");
        $shipping_id_back = Request::getInt("shipping_id_back");
        $shippings = $this->getModel("shippings");
        $rows = $shippings->getAllShippingPrices(0, $shipping_id_back, $filter_order, $filter_order_Dir);
        $currency = Factory::getTable('currency');
        $currency->load($config->mainCurrency);

        $query = "select MPC.sh_pr_method_id, C.`name_".$config->cur_lang."` as name from `".$wpdb->prefix."wshop_shipping_method_price_countries` as MPC 
                  left join `".$wpdb->prefix."wshop_countries` as C on C.country_id=MPC.country_id order by MPC.sh_pr_method_id, C.ordering";
        $list = $wpdb->get_results($query, OBJECT);
        $shipping_countries = array();        
        foreach($list as $smp){
            $shipping_countries[$smp->sh_pr_method_id][] = $smp->name;
        }
        unset($list);
        foreach($rows as $k=>$row){
            $rows[$k]->countries = "";
            if (is_array($shipping_countries[$row->sh_pr_method_id])){
                if (count($shipping_countries[$row->sh_pr_method_id])>10){
                    $tmp =  array_slice($shipping_countries[$row->sh_pr_method_id],0,10);
                    $rows[$k]->countries = implode(", ",$tmp)."...";
                }else{
                    $rows[$k]->countries = implode(", ",$shipping_countries[$row->sh_pr_method_id]);
                }                
            }
        }
        $actions = array(
            'delete' => _WOP_SHOP_DELETE,
        );
        $bulk = $shippings->getBulkActions($actions);
        if($filter_order == 'asc') $filter_order = 'desc'; else $filter_order = 'asc';
        $view = $this->getView("shippingsprices");
        $view->setLayout("list");
        $view->assign('rows', $rows);
        $view->assign('currency', $currency);
        $view->assign('shipping_id_back', $shipping_id_back);
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);
        $view->assign('bulk', $bulk);
        do_action_ref_array('onBeforeDisplayShippngsPrices', array(&$view));
        $view->display();

    }
    function edit(){
        $config = Factory::getConfig();
        $sh_pr_method_id = Request::getInt('sh_pr_method_id');
        $shipping_id_back = Request::getInt("shipping_id_back");
        global $wpdb;
        $sh_method_price = Factory::getTable('shippingmethodprice');
        $sh_method_price->load($sh_pr_method_id);
        $sh_method_price->prices = $sh_method_price->getPrices();
        if ($config->tax){        
            $taxes = $this->getModel("taxes");
            $all_taxes = $taxes->getAllTaxes();
            $list_tax = array();		
            foreach ($all_taxes as $tax) {
                $list_tax[] = HTML::_('select.option', $tax->tax_id,$tax->tax_name . ' (' . $tax->tax_value . '%)','tax_id','tax_name');
            }
            $list_tax[] = HTML::_('select.option', -1,_WOP_SHOP_PRODUCT_TAX_RATE,'tax_id','tax_name');
            $lists['taxes'] = HTML::_('select.genericlist', $list_tax,'shipping_tax_id','class="inputbox"','tax_id','tax_name',$sh_method_price->shipping_tax_id);
            $lists['package_taxes'] = HTML::_('select.genericlist', $list_tax,'package_tax_id','class="inputbox"','tax_id','tax_name',$sh_method_price->package_tax_id);
        }
        $shippings = $this->getModel("shippings");
        $countries = $this->getModel("countries");		
        $actived = $sh_method_price->shipping_method_id;
        if (!$actived) $actived = $shipping_id_back;        
            $lists['shipping_methods'] = HTML::_('select.genericlist', $shippings->getAllShippings(0),'shipping_method_id','class = "inputbox" size = "1"','shipping_id','name', $actived);
            $lists['countries'] = HTML::_('select.genericlist', $countries->getAllCountries(0),'shipping_countries_id[]','class = "inputbox" size = "10", multiple = "multiple"','country_id','name', $sh_method_price->getCountries());
        
        if ($config->admin_show_delivery_time) {
            $_deliveryTimes = $this->getModel("deliveryTimes");
            $all_delivery_times = $_deliveryTimes->getDeliveryTimes();
            $all_delivery_times0 = array();
            $all_delivery_times0[0] = new stdClass();
            $all_delivery_times0[0]->id = '0';
            $all_delivery_times0[0]->name = _WOP_SHOP_NONE;
            $lists['deliverytimes'] = HTML::_('select.genericlist', array_merge($all_delivery_times0, $all_delivery_times),'delivery_times_id','class = "inputbox"','id','name', $sh_method_price->delivery_times_id);
        }
        
        $currency = Factory::getTable('currency');
        $currency->load($config->mainCurrency);
        $extensions = Factory::getShippingExtList($actived);

        $view=$this->getView("shippingsprices");
        $view->setLayout("edit");
        $view->assign('sh_method_price', $sh_method_price);
        $view->assign('lists', $lists);
        $view->assign('shipping_id_back', $shipping_id_back);
        $view->assign('currency', $currency);
        $view->assign('extensions', $extensions);
        $view->assign('config', $config);
        do_action_ref_array('onBeforeEditShippingsPrices', array(&$view));
        $view->display();
        
    }
    function save(){
        if ( !empty($_POST) && check_admin_referer('shippingsprices_edit','name_of_nonce_field') )
        {
            $sh_method_id = Request::getInt("sh_method_id");
            $shipping_id_back = Request::getInt("shipping_id_back");
            $shippings = $this->getModel("shippings");
            $shipping_pr = Factory::getTable('shippingmethodprice');
            $post = Request::get("post");        
            $post['shipping_stand_price'] = saveAsPrice($post['shipping_stand_price']);
            do_action_ref_array( 'onBeforeSaveShippingPrice', array(&$post) );
            $countries = Request::getVar('shipping_countries_id');
            if (!$shipping_pr->bind($post)){
                addMessage(_WOP_SHOP_ERROR_BIND);
                $this->setRedirect("admin.php?page=options&tab=shippingsprices");
                return 0;
            }
        if (isset($post['sm_params']))
            $shipping_pr->setParams($post['sm_params']);
        else 
            $shipping_pr->setParams('');
	
            if (!$shipping_pr->store()) {
                addMessage(_WOP_SHOP_ERROR_SAVE_DATABASE, 'error');
                $this->setRedirect("admin.php?page=options&tab=shippingsprices");
                return 0;
            }
            $shippings->savePrices($shipping_pr->sh_pr_method_id, $post);
            $shippings->saveCountries($shipping_pr->sh_pr_method_id, $countries);
                do_action_ref_array( 'onAfterSaveShippingPrice', array(&$shipping_pr) );
            $this->setRedirect("admin.php?page=options&tab=shippingsprices");

            
        }
    }
    function delete(){
        $cid = Request::getVar("rows");
        global $wpdb;
        $shipping_id_back = Request::getInt("shipping_id_back");
        do_action_ref_array( 'onBeforeRemoveShippingPrice', array(&$cid) );
        $text = '';
        foreach ($cid as $key => $value) {
            if ($wpdb->delete($wpdb->prefix."wshop_shipping_method_price", array('sh_pr_method_id'=>esc_sql($value)))) {
                $text .= _WOP_SHOP_SHIPPING_DELETED;
                $wpdb->delete($wpdb->prefix."wshop_shipping_method_price_weight", array('sh_pr_method_id'=>esc_sql($value)));
		$wpdb->delete($wpdb->prefix."wshop_shipping_method_price_countries", array('sh_pr_method_id'=>esc_sql($value)));
            } else {
                $text .= _WOP_SHOP_ERROR_SHIPPING_DELETED;
            }
        }
        do_action_ref_array( 'onAfterRemoveShippingPrice', array(&$cid) );
        $this->setRedirect("admin.php?page=options&tab=shippingsprices&shipping_id_back=".$shipping_id_back, $text);
       
    }
}