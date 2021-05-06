<?php
class shippingextpriceWshopAdminController extends WshopAdminController {
    function __construct() {
        parent::__construct();
    }

    function display() {
		$shippings = Factory::getAdminModel("shippingextprice");
		$rows = $shippings->getList();
		$view = $this->getView("shippingext");
        $view->setLayout("list");
		$view->assign('rows', $rows);
        do_action_ref_array('onBeforeDisplayShippingExtPrices', array(&$view));
		$view->display();				
    }
	
	
    function edit(){
		$id = Request::getInt("id");
        $row = Factory::getTable('shippingExt');
        $row->load($id);

        if (!$row->exec) {
			addMessage("Error load ShippingExt", 'error');
        }

        $shippings_conects = $row->getShippingMethod();

        $shippings = Factory::getAdminModel("shippings");
        $list_shippings = $shippings->getAllShippings(0);

        $view = $this->getView("shippingext");
        $view->setLayout("edit");
        $view->assign('row', $row);
        $view->assign('list_shippings', $list_shippings);
        $view->assign('shippings_conects', $shippings_conects);

        do_action_ref_array('onBeforeEditShippingExtPrice', array(&$view));
        $view->display();
    }
    function save(){
		$id = Request::getInt("id");		
        $post = Request::get("post");
        $row = Factory::getTable('shippingExt');        
        do_action_ref_array( 'onBeforeSaveShippingExtCalc', array(&$post));        
        $row->bind($post);        
        $row->setShippingMethod($post['shipping']);       
        $row->setParams($post['params']);		
		$row->store();
        
        do_action_ref_array( 'onAfterSaveShippingExtCalc', array(&$row) );        		
        $this->setRedirect("admin.php?page=options&tab=shippingextprice");	
    }
	
	function publish() {
		$shippings = Factory::getAdminModel("shippingextprice");
		$shippings->republish();
		$this->setRedirect("admin.php?page=options&tab=shippingextprice");
	}

	function unpublish() {
		$shippings = Factory::getAdminModel("shippingextprice");
		$shippings->republish();
		$this->setRedirect("admin.php?page=options&tab=shippingextprice");
	}
	
    function delete(){
		$shippings = Factory::getAdminModel("shippingextprice");
		$shippings->delete();
		$this->setRedirect("admin.php?page=options&tab=shippingextprice",  _WOP_SHOP_ITEM_DELETED);			
    }
	
	function orderup(){
		$shippings = Factory::getAdminModel("shippingextprice");
		$shippings->reorder();
		$this->setRedirect("admin.php?page=options&tab=shippingextprice");			
	}

	function orderdown(){
		$shippings = Factory::getAdminModel("shippingextprice");
		$shippings->reorder();
		$this->setRedirect("admin.php?page=options&tab=shippingextprice");			
	}
				
}