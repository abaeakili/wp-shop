<?php
class FreeAttributesWshopAdminController extends WshopAdminController {
    function __construct() {
        parent::__construct();
    }

    function display() {
        $context = "admin.freeattributes.";
        $filter_order = getStateFromRequest($context.'filter_order', 'filter_order', 'ordering');
        $filter_order_Dir = getStateFromRequest($context.'filter_order_dir', 'filter_order_Dir', 'asc');

    	$freeattributes = $this->getModel("freeattribut");
        $rows = $freeattributes->getAll($filter_order, $filter_order_Dir);

        $actions = array(
            'delete' => _WOP_SHOP_DELETE
        );
        $bulk = $freeattributes->getBulkActions($actions);

        if($filter_order_Dir == 'asc') $filter_order_Dir = 'desc'; else $filter_order_Dir = 'asc';

        $view=$this->getView("freeattributes");
        $view->setLayout("list");
        $view->assign('rows', $rows);
        $view->assign('bulk', $bulk);
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);
        do_action_ref_array('onBeforeDisplayFreeAttributes', array(&$view));
        $view->display();
    }
    function edit(){
        $config = Factory::getConfig();
        global $wpdb;
        $id = Request::getInt("id");

        $attribut = Factory::getTable('freeattribut');
        $attribut->load($id);

        $_lang = $this->getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;

        //FilterOutput::objectHTMLSafe($attribut, ENT_QUOTES);

        $view = $this->getView("freeattributes");
        $view->setLayout("edit");
        $view->assign('attribut', $attribut);
        $view->assign('languages', $languages);
        $view->assign('multilang', $multilang);
        do_action_ref_array( 'onBeforeEditFreeAtribut', array(&$view, &$attribut) );
        $view->display();
    }
    function save(){
        if ( !empty($_POST) && check_admin_referer('freeattributes_edit','name_of_nonce_field') )
        {
            //global $wpdb;
            $id = Request::getInt('id');

            $attribut = Factory::getTable('freeattribut');
            $post = Request::get("post");
            if (!isset($post['required']) || !$post['required']) $post['required'] = 0;
            do_action_ref_array( 'onBeforeSaveFreeAtribut', array(&$post) );
            if (!$id){
                $attribut->ordering = null;
                $attribut->ordering = $attribut->getNextOrder();            
            }
            if (!$attribut->bind($post)) {
                addMessage(_WOP_SHOP_ERROR_BIND, 'error');
                $this->setRedirect("admin.php?page=options&tab=freeattributes");
                return 0;
            }
            if (!$attribut->store()) {
                addMessage(_WOP_SHOP_ERROR_SAVE_DATABASE, 'error');
                $this->setRedirect("admin.php?page=options&tab=freeattributes");
                return 0;
            }
            do_action_ref_array( 'onAfterSaveFreeAtribut', array(&$attribut) );
            $this->setRedirect("admin.php?page=options&tab=freeattributes");
        }
    }
    function delete(){
        $cid = Request::getVar("rows");
        $config = Factory::getConfig();
        global $wpdb;
        $text = '';
        do_action_ref_array( 'onBeforeRemoveFreeAtribut', array(&$cid) );
        foreach ($cid as $key => $value) {
            $value = intval($value);
            $wpdb->delete($wpdb->prefix."wshop_free_attr", array( 'id' => esc_sql($value) ) );
            $wpdb->delete($wpdb->prefix."wshop_products_free_attr", array( 'attr_id' => esc_sql($value) ) );
            
        }
    }
	
    function order(){
		$order = Request::getVar("order");
		$cid = Request::getInt("id");
		$number = Request::getInt("number");
		global $wpdb;
		switch ($order) {
			case 'up':
				$query = "SELECT a.id, a.ordering
					   FROM `".$wpdb->prefix."wshop_free_attr` AS a
					   WHERE a.ordering < '" . $number . "'
					   ORDER BY a.ordering DESC
					   LIMIT 1";
				break;
			case 'down':
				$query = "SELECT a.id, a.ordering
					   FROM `".$wpdb->prefix."wshop_free_attr` AS a
					   WHERE a.ordering > '" . $number . "'
					   ORDER BY a.ordering ASC
					   LIMIT 1";
		}
		$row = $wpdb->get_row($query);
		$query1 = "UPDATE `".$wpdb->prefix."wshop_free_attr` AS a
					 SET a.ordering = '" . $row->ordering . "'
					 WHERE a.id = '" . $cid . "'";
		$query2 = "UPDATE `".$wpdb->prefix."wshop_free_attr` AS a
					 SET a.ordering = '" . $number . "'
					 WHERE a.id = '" . $row->id . "'";
		$wpdb->query($query1);
		$wpdb->query($query2);
		
		$this->setRedirect("admin.php?page=options&tab=freeattributes");		
    }
    
    function saveorder(){
		$cid = Request::getVar("rows");
        $order = Request::getVar('order', array(), 'post', 'array' );                
        foreach($cid as $k=>$id){
            $table = Factory::getTable('FreeAttribut');
            $table->load($id);
            if ($table->ordering!=$order[$k]){
                $table->ordering = $order[$k];
                $table->store();
            }
        }                
        $this->setRedirect("admin.php?page=options&tab=freeattributes");		
    }		
}