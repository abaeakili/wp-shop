<?php
class OrderStatusWshopAdminController extends WshopAdminController {
    function __construct() {
        parent::__construct();
    }
   
    function display() {
        $context = "admin.orderstatus.";
        $filter_order = getStateFromRequest($context.'filter_order', 'filter_order', "status_id");
        $filter_order_Dir = getStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc");

        $_order = $this->getModel("orders");
        $rows = $_order->getAllOrderStatus($filter_order, $filter_order_Dir);

        $actions = array(
            'delete' => _WOP_SHOP_DELETE
        );
        $bulk = $_order->getBulkActions($actions);
        if($filter_order_Dir == 'asc') $filter_order_Dir = 'desc'; else $filter_order_Dir = 'asc';
        
        $view=$this->getView("orderstatus");
        $view->setLayout("list");
        $view->assign('rows', $rows);
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);
        $view->assign('bulk',$bulk);
        do_action_ref_array('onBeforeDisplayOrderStatus', array(&$view));
        $view->display();
    }
    function edit(){
        $status_id = Request::getInt("row");
        $order_status = Factory::getTable('orderstatus');
        $order_status->load($status_id);
	$edit = ($status_id)?($edit = 1):($edit = 0);
        $_lang = $this->getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
        
        //FilterOutput::objectHTMLSafe( $order_status, ENT_QUOTES);

        $view=$this->getView("orderstatus");
        $view->setLayout("edit");
        $view->assign('order_status', $order_status);
        $view->assign('edit', $edit);
        $view->assign('languages', $languages);
        $view->assign('multilang', $multilang);
        do_action_ref_array('onBeforeEditOrderStatus', array(&$view));
        $view->display();
    }
    function save(){
        if ( !empty($_POST) && check_admin_referer('status_edit','name_of_nonce_field') )
        {
        $status_id = Request::getInt("status_id");
        $order_status = Factory::getTable('orderstatus');
        $post = Request::get("post");
        do_action_ref_array( 'onBeforeSaveOrderStatus', array(&$post) );
        if (!$order_status->bind($post)) {
            addMessage(_WOP_SHOP_ERROR_BIND, 'error');
            $this->setRedirect("admin.php?page=options&tab=orderstatus");
            return 0;
        }
        if (!$order_status->store()) {
            addMessage(_WOP_SHOP_ERROR_SAVE_DATABASE, 'error');
            $this->setRedirect("admin.php?page=options&tab=orderstatus");
            return 0;
        }
        do_action_ref_array( 'onAfterSaveOrderStatus', array(&$order_status) );
        }
        else addMessage(_WOP_SHOP_ERROR_BIND, 'error');
        $this->setRedirect('admin.php?page=options&tab=orderstatus');
    }
    
    
    function delete(){
        global $wpdb;
        $text = '';
        $query = '';
        $cid = Request::getVar("rows");
        do_action_ref_array( 'onBeforeRemoveOrderStatus', array(&$cid));
        foreach ($cid as $key => $value) {
            if ($wpdb->delete($wpdb->prefix."wshop_order_status", array('status_id'=>esc_sql($value))))
            $text .= _WOP_SHOP_ORDER_STATUS_DELETED."<br>";
            else
            $text .= _WOP_SHOP_ORDER_STATUS_ERROR_DELETED."<br>";
        }
        do_action_ref_array( 'onAfterRemoveOrderStatus', array(&$cid));
        $this->setRedirect('admin.php?page=options&tab=orderstatus', $text);
    }
}