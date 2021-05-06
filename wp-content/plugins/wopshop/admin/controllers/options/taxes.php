<?php
class TaxesWshopAdminController extends WshopAdminController {
    function __construct(){
        parent::__construct();
    }

    function display() {
        $filter_order = getStateFromRequest('taxes_filter_order', 'filter_order', 'tax_name');
        $filter_order_Dir = getStateFromRequest('taxes_filter_order_Dir', 'filter_order_Dir', 'asc');

        $model = $this->getModel("taxes");
        $rows = $model->getAllTaxes($filter_order, $filter_order_Dir);
        
        $actions = array(
            'delete' => _WOP_SHOP_DELETE
        );
        $bulk = $model->getBulkActions($actions);
        
        if($filter_order_Dir == 'asc') $filter_order_Dir = 'desc'; else $filter_order_Dir = 'asc';
        $view=$this->getView("taxes");
        $view->setLayout("list");
        $view->assign('rows', $rows);
        $view->assign('bulk', $bulk);
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);
		do_action_ref_array('onBeforeDisplayTaxes', array(&$view));
        $view->display();
    }
    function edit(){
        $tax_id = Request::getInt("tax_id");
        $tax = Factory::getTable('tax');
        $tax->load($tax_id);
        $edit = ($tax_id)?($edit = 1):($edit = 0);

        $view=$this->getView("taxes");
        $view->setLayout("edit");
        //FilterOutput::objectHTMLSafe( $tax, ENT_QUOTES);
        $view->assign('tax', $tax); 
        $view->assign('edit', $edit);
		do_action_ref_array('onBeforeEditTaxes', array(&$view));
        $view->display();
    }
    function save(){
        if ( !empty($_POST) && check_admin_referer('tax_edit','name_of_nonce_field') )
        {
            $tax_id = Request::getInt("tax_id");
            $tax = Factory::getTable('tax');
            $post = Request::get("post"); 
            $post['tax_value'] = saveAsPrice($post['tax_value']);
			do_action_ref_array( 'onBeforeSaveTax', array(&$tax) );
            if (!$tax->bind($post)) {
                addMessage(_WOP_SHOP_ERROR_BIND);
                $this->setRedirect("admin.php?page=options&tab=taxes");
                return 0;
            }

            if (!$tax->store()) {
                addMessage(_WOP_SHOP_ERROR_SAVE_DATABASE);
                $this->setRedirect("admin.php?page=options&tab=taxes");
                return 0; 
            }
			do_action_ref_array( 'onAfterSaveTax', array(&$tax) );
            $this->setRedirect("admin.php?page=options&tab=taxes");
        }
        else addMessage(_WOP_SHOP_ERROR_BIND, 'error');
        $this->setRedirect('admin.php?page=options&tab=taxes');
    }
    
    /*function unpublish(){
        $rows = $_REQUEST['rows'];
        
        $model = $this->getModel('taxes');
        $result = $model->TaxesActionPublish('0', $rows);
        if($result == 'error') addMessage(_WOP_SHOP_ERROR_BIND, 'error');
        if($result == 'success') addMessage(_WOP_SHOP_ACTION_UNPUBLISHED);
        
        $this->setRedirect('admin.php?page=options&tab=taxes');
    }
    function publish(){
        $rows = $_REQUEST['rows'];
        
        $model = $this->getModel('taxes');
        $result = $model->TaxesActionPublish('1', $rows);
        if($result == 'error') addMessage(_WOP_SHOP_ERROR_BIND, 'error');
        if($result == 'success') addMessage(_WOP_SHOP_ACTION_PUBLISHED);
        
        $this->setRedirect('admin.php?page=options&tab=taxes');
    }*/
    function delete(){
        $cid = Request::getVar("rows");
        global $wpdb;
        $text = '';
		do_action_ref_array( 'onBeforeRemoveTax', array(&$cid) );
        foreach ($cid as $key => $value) {
            $tax = Factory::getTable('tax');
            $tax->load($value);
            $query2 = "SELECT pr.product_id
                       FROM `".$wpdb->prefix."wshop_products` AS pr
                       WHERE pr.product_tax_id = '".esc_sql($value)."'";
            $res = $wpdb->get_results($query2);
            if(count($res)){
                $text .= sprintf(_WOP_SHOP_TAX_NO_DELETED, $tax->tax_name)."<br>";
                continue;
            }
            
            if ($wpdb->delete($wpdb->prefix."wshop_taxes", array( 'tax_id' => esc_sql($value)) )){
                $text .= sprintf(_WOP_SHOP_TAX_DELETED,$tax->tax_name)."<br>";
            }
            $wpdb->delete($wpdb->prefix."wshop_taxes_ext", array( 'tax_id' => esc_sql($value)) );
        }
		do_action_ref_array( 'onAfterRemoveTax', array(&$cid) );
        $this->setRedirect("admin.php?page=options&tab=taxes", $text);
    }
}