<?php
class DeliveryTimesWshopAdminController extends WshopAdminController {
    function __construct() {
        parent::__construct();
    }
   
    function display() {
        $context = "admin.deliverytimes.";
        $filter_order = getStateFromRequest($context.'filter_order', 'filter_order', "name");
        $filter_order_Dir = getStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc");

        $actions = array(
            'delete' => _WOP_SHOP_DELETE,
        );
        $_deliveryTimes = $this->getModel('deliverytimes');
        $bulk = $_deliveryTimes->getBulkActions($actions);
        $rows = $_deliveryTimes->getDeliveryTimes($filter_order, $filter_order_Dir);

        if($filter_order_Dir == 'asc') $filter_order_Dir = 'desc'; else $filter_order_Dir = 'asc';

        $view = $this->getView('deliverytimes');
        $view->setLayout('list');
        $view->assign('rows', $rows);
        $view->assign('filter_order',$filter_order);
        $view->assign('filter_order_Dir',$filter_order_Dir);
        $view->assign('bulk',$bulk);
		do_action_ref_array('onBeforeDisplayDeliveryTimes', array(&$view));
        $view->display();
        
    }
    function edit(){
        $id = Request::getInt("row");
        $deliveryTimes = Factory::getTable('deliverytimes');
        $deliveryTimes->load($id);
        $edit = ($id)?(1):(0);
        $_lang = $this->getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
        //FilterOutput::objectHTMLSafe( $deliveryTimes, ENT_QUOTES);

        $view=$this->getView("deliverytimes");
        $view->setLayout("edit");
        $view->assign('deliveryTimes', $deliveryTimes);        
        $view->assign('edit', $edit);
        $view->assign('languages', $languages);
        $view->assign('multilang', $multilang);
        do_action_ref_array('onBeforeEditDeliverytimes', array(&$view));
        $view->display();

    }
    function save(){
        if ( !empty($_POST) && check_admin_referer('deliverytimes_edit','name_of_nonce_field') )
        {
            //$mainframe = Factory::getApplication();
            $id = Request::getInt("id");
            $deliveryTimes = Factory::getTable('deliverytimes');
            $post = Request::get("post");
            do_action_ref_array( 'onBeforeSaveDeliveryTime', array(&$post) );
            if (!$deliveryTimes->bind($post)) {
                addMessage(_WOP_SHOP_ERROR_BIND);
                $this->setRedirect("admin.php?page=options&tab=deliverytimes");
                return 0;
            }
	
            if (!$deliveryTimes->store()) {
                addMessage(_WOP_SHOP_ERROR_SAVE_DATABASE);
                $this->setRedirect("admin.php?page=options&tab=deliverytimes");
                return 0;
            }
            do_action_ref_array( 'onAfterSaveDeliveryTime', array(&$deliveryTimes) );
        }
        else addMessage(_WOP_SHOP_ERROR_BIND, 'error');
        $this->setRedirect('admin.php?page=options&tab=deliverytimes');
    }
    

    function delete(){
        global $wpdb;
        $text = array();
        $cid = Request::getVar("rows");
        do_action_ref_array( 'onBeforeRemoveDeliveryTime', array(&$cid) );
        foreach ($cid as $key => $value) {
            if ($wpdb->delete($wpdb->prefix."wshop_delivery_times", array('id'=>esc_sql($value))))
                $text[] = _WOP_SHOP_DELIVERY_TIME_DELETED."<br>";
                else
                $text[] = _WOP_SHOP_DELIVERY_TIME_DELETED_ERROR_DELETED."<br>";
            }
        do_action_ref_array( 'onAfterRemoveDeliveryTime', array(&$cid) );
        $this->setRedirect('admin.php?page=options&tab=deliverytimes', implode("</p><p>", $text));
    }
    
}