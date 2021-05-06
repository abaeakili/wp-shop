<?php
class ProductFieldValuesWshopAdminController extends WshopAdminController {
    function __construct() {
        parent::__construct();
    }

    function display(){
        global $wpdb;
        $config = Factory::getConfig();

        $context = "admin.productfieldvalues.";
        $filter_order = getStateFromRequest($context.'filter_order', 'filter_order', "ordering");
        $filter_order_Dir = getStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc");
        $text_search = getStateFromRequest($context.'text_search', 'text_search', '');

        $field_id = Request::getInt("field_id");

        $_productfieldvalues = $this->getModel("productFieldValues");
        $filter = array("text_search"=>$text_search);

        $rows = $_productfieldvalues->getList($field_id, $filter_order, $filter_order_Dir, $filter);

        $actions = array(
            'delete' => _WOP_SHOP_DELETE
        );
        $bulk = $_productfieldvalues->getBulkActions($actions);
        if($filter_order_Dir == 'asc') $filter_order_Dir = 'desc'; else $filter_order_Dir = 'asc';
        $view = $this->getView("productfieldvalues", 'html');
        $view->setLayout("list");
        $view->assign('bulk', $bulk);
        $view->assign('rows', $rows);
        $view->assign('field_id', $field_id);
        $view->assign('text_search', $text_search);
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);
        do_action_ref_array('onBeforeDisplayProductFieldValues', array(&$view));
        $view->display();
    }
    function edit(){
        $field_id = Request::getInt("field_id");
        $id = Request::getInt("id");

        $productfieldvalue = Factory::getTable('productfieldvalue');
        $productfieldvalue->load($id);

        $_lang = $this->getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;    

        $view = $this->getView("productfieldvalues");
        $view->setLayout("edit");
        //FilterOutput::objectHTMLSafe($productfieldvalue, ENT_QUOTES);
        $view->assign('row', $productfieldvalue);
        $view->assign('languages', $languages);
        $view->assign('multilang', $multilang);
        $view->assign('field_id', $field_id);
        do_action_ref_array('onBeforeEditProductFieldValues', array(&$view));
        $view->display();
        
    }
    function save(){
        if ( !empty($_POST) && check_admin_referer('productfieldvalues_edit','name_of_nonce_field') )
        {
            $id = Request::getInt("id");
            $field_id = Request::getInt("field_id");
            $productfieldvalue = Factory::getTable('productfieldvalue');
            $post = Request::get("post");
            do_action_ref_array( 'onBeforeSaveProductFieldValue', array(&$post) );
            if (!$productfieldvalue->bind($post)) {
                addMessage(_WOP_SHOP_ERROR_BIND);
                $this->setRedirect("admin.php?page=options&tab=productfieldvalues");
                return 0;
            }
            if (!$id){
                $productfieldvalue->ordering = null;
                $productfieldvalue->ordering = $productfieldvalue->getNextOrder('field_id="'.$field_id.'"');
            }
            if (!$productfieldvalue->store()) {
                addMessage(_WOP_SHOP_ERROR_SAVE_DATABASE);
                $this->setRedirect("admin.php?page=options&tab=productfieldvalues");
                return 0; 
            }
            do_action_ref_array( 'onAfterSaveProductFieldValue', array(&$productfieldvalue) );
            $this->setRedirect("admin.php?page=options&tab=productfieldvalues&field_id=".$field_id); 
        }
    }
    function delete(){
        $field_id = Request::getInt("field_id");
        $cid = Request::getVar("rows");
        global $wpdb;
        $text = array();
        foreach ($cid as $key => $value) {
            if ($wpdb->delete( $wpdb->prefix."wshop_products_extra_field_values", array( 'id' => esc_sql($value) ))){
                $text[] = _WOP_SHOP_ITEM_DELETED;
            }
        }
        do_action_ref_array( 'onAfterRemoveProductFieldValue', array(&$cid) );
        $this->setRedirect("admin.php?page=options&tab=productfieldvalues&field_id=".$field_id, implode("</p><p>",$text)); 
    }
	
    function order(){
		$order = Request::getVar("order");
		$cid = Request::getInt("id");
		$number = Request::getInt("number");
		$field_id = Request::getInt("field_id");
		global $wpdb;
		switch ($order) {
			case 'up':
				$query = "SELECT a.id, a.ordering
					   FROM `".$wpdb->prefix."wshop_products_extra_field_values` AS a
					   WHERE a.ordering < '" . $number . "'
					   ORDER BY a.ordering DESC
					   LIMIT 1";
				break;
			case 'down':
				$query = "SELECT a.id, a.ordering
					   FROM `".$wpdb->prefix."wshop_products_extra_field_values` AS a
					   WHERE a.ordering > '" . $number . "'
					   ORDER BY a.ordering ASC
					   LIMIT 1";
		}
		$row = $wpdb->get_row($query);
		$query1 = "UPDATE `".$wpdb->prefix."wshop_products_extra_field_values` AS a
					 SET a.ordering = '" . $row->ordering . "'
					 WHERE a.id = '" . $cid . "'";
		$query2 = "UPDATE `".$wpdb->prefix."wshop_products_extra_field_values` AS a
					 SET a.ordering = '" . $number . "'
					 WHERE a.id = '" . $row->id . "'";
		$wpdb->query($query1);
		$wpdb->query($query2);
		
		$this->setRedirect("admin.php?page=options&tab=productfieldvalues&field_id=".$field_id);		
    }
    
    function saveorder(){
		$cid = Request::getVar("rows");
        $order = Request::getVar('order', array(), 'post', 'array' );   
		$field_id = Request::getInt("field_id");
        foreach($cid as $k=>$id){
            $table = Factory::getTable('ProductFieldValue');
            $table->load($id);
            if ($table->ordering!=$order[$k]){
                $table->ordering = $order[$k];
                $table->store();
            }
        }                
        $this->setRedirect("admin.php?page=options&tab=productfieldvalues&field_id=".$field_id);		
    }		
}