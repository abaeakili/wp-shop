<?php
class UnitsWshopAdminController extends WshopAdminController {
    function __construct(){
        parent::__construct();
    }

    function display() {
		$_units = $this->getModel("units");
		$rows = $_units->getUnits();
        $actions = array(
            'delete' => _WOP_SHOP_DELETE
        );
        $bulk = $_units->getBulkActions($actions);        
		$view=$this->getView("units");
        $view->setLayout("list");		
        $view->assign('rows', $rows);
		$view->assign('bulk', $bulk);
        do_action_ref_array('onBeforeDisplayUnits', array(&$view)); 		
		$view->display();		
    }
    function edit(){
        $id = Request::getInt("id");
        $units = Factory::getTable('unit');
        $units->load($id);
        $edit = ($id)?(1):(0);
        $_lang = Factory::getAdminModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
        if (!$units->qty) $units->qty = 1;

		$view=$this->getView("units");
        $view->setLayout("edit");
        $view->assign('units', $units);        
        $view->assign('edit', $edit);
        $view->assign('languages', $languages);
        $view->assign('multilang', $multilang);        
        do_action_ref_array('onBeforeEditUnits', array(&$view));
		$view->display();		
    }
    function save(){
        if ( !empty($_POST) && check_admin_referer('unit_edit','name_of_nonce_field') )
        {
			$id = Request::getInt("id");
			$units = Factory::getTable('unit');
			$post = Request::get("post");

			do_action_ref_array( 'onBeforeSaveUnit', array(&$post) );        

			if (!$units->bind($post)) {
				addMessage(_WOP_SHOP_ERROR_BIND, 'error');
				$this->setRedirect("admin.php?page=options&tab=units");
				return 0;
			}

			if (!$units->store()) {
				addMessage(_WOP_SHOP_ERROR_SAVE_DATABASE, 'error');
				$this->setRedirect("admin.php?page=options&tab=units");
				return 0;
			}

			do_action_ref_array( 'onAfterSaveUnit', array(&$units) );
			$this->setRedirect("admin.php?page=options&tab=units");	
		}
			
    }
    function delete(){
		global $wpdb;
		$text = array();
		$cid = Request::getVar("cid");        
        do_action_ref_array( 'onBeforeRemoveUnit', array(&$cid) );
		foreach ($cid as $key => $value) {
			$result = $wpdb->delete( $wpdb->prefix.'wshop_unit', array( 'id' => esc_sql($value) ));
			if($result) $text[] = _WOP_SHOP_ITEM_DELETED."<br>";			
		}
        do_action_ref_array( 'onAfterRemoveUnit', array(&$cid) );
        $this->setRedirect("admin.php?page=options&tab=units", implode("</li><li>", $text));
    }
}