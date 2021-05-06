<?php
class VendorsWshopAdminController extends WshopAdminController {
    function __construct() {
        parent::__construct();
    }
   
    function display(){
		$mainframe = Factory::getApplication();
        $context = "list.admin.vendors";
		$limit = getStateFromRequest( $context.'per_page', 'per_page', 20);
		$paged = $mainframe->getUserStateFromRequest($context.'paged', 'paged', '1');
		$text_search = $mainframe->getUserStateFromRequest( $context.'text_search', 's', '');
        
        $vendors = $this->getModel("vendors");        
        $total = $vendors->getCountAllVendors($text_search);
		$search = $vendors->search($text_search);
		
        $actions = array(
            'delete' => _WOP_SHOP_DELETE
        );
        $bulk = $vendors->getBulkActions($actions);

        if(($paged-1) > ($total/$limit) ) $paged = 1;
        $limitstart = ($paged-1)*$limit;
        $pagination = $vendors->getPagination($total, $limit);
		$rows = $vendors->getAllVendors($limitstart, $limit, $text_search);
		
        $view=$this->getView("vendors");
        $view->setLayout("list");
        $view->assign('rows', $rows); 
        $view->assign('limit', $limit);
        $view->assign('limitstart', $limitstart);
        $view->assign('search', $search); 
        $view->assign('pagination', $pagination);
		$view->assign('bulk', $bulk);
        do_action_ref_array('onBeforeDisplayVendors', array(&$view));		
        $view->display();		

     }

     function delete(){
        global $wpdb;
        $vendor = Factory::getTable('vendor');
        $cid = Request::getVar('rows');

        do_action_ref_array('onBeforeRemoveVendor', array(&$cid));
        foreach($cid as $id){
            $query = "select count(*) from `".$wpdb->prefix."wshop_products` where `vendor_id`=".intval($id);
            $cp = $wpdb->get_var($query);
			if (!$cp){
                $query = "delete from `".$wpdb->prefix."wshop_vendors` where id='".  esc_sql($id)."' and main=0";
				$wpdb->query($query);
            }else{
                $vendor->load($id);
				addMessage(sprintf(_WOP_SHOP_ITEM_ALREADY_USE, $vendor->f_name." ".$vendor->l_name), 'error');
            }
        }
        do_action_ref_array('onAfterRemoveVendor', array(&$cid));
        
        $this->setRedirect("admin.php?page=options&tab=vendors");		 
		 
		 
        $reviews_model = $this->getModel("reviews");
        $cid = Request::getVar('cid');

        do_action_ref_array('onBeforeRemoveReview', array(&$cid) );

        foreach($cid as $key => $value) {
             $review = Factory::getTable('review');
             $review->load($value);
             $reviews_model->deleteReview($value);
             $product = Factory::getTable('product');
             $product->load($review->product_id);
             $product->loadAverageRating();
             $product->loadReviewsCount();
             $product->store();
             unset($product);
             unset($review);
        }
        do_action_ref_array('onAfterRemoveReview', array(&$cid));
        $this->setRedirect("admin.php?page=options&tab=reviews");
     }
     
     function edit(){
        $id = Request::getInt("id");
        $vendor = Factory::getTable('vendor');
        $vendor->load($id);
        if (!$id){
            $vendor->publish = 1;
        }
        $_countries = $this->getModel("countries");
        $countries = $_countries->getAllCountries(0);
        $lists['country'] = HTML::_('select.genericlist', $countries,'country','class = "inputbox" size = "1"','country_id','name', $vendor->country);
        
        
        $view=$this->getView("vendors", 'html');
        $view->setLayout("edit");
        $view->assign('vendor', $vendor);  
        $view->assign('lists', $lists);

        do_action_ref_array('onBeforeEditVendors', array(&$view));
        $view->display();   		 
     }
     
     function save(){
        $vendor = Factory::getTable('vendor');

        $id = Request::getInt("id");
        $vendor->load($id);
        
        if (!isset($_POST['publish'])){
            $_POST['publish'] = 0;
        }
        $post = Request::get("post");
        do_action_ref_array('onBeforeSaveVendor', array(&$post) );
        $vendor->bind($post);        
        Factory::loadLanguageFile();
        if (!$vendor->check()) {            
			addMessage($vendor->getError(), 'error');
            $this->setRedirect("admin.php?page=options&tab=vendors&task=edit&id=".$vendor->id);
            return 0;
        }        
        if (!$vendor->store()) {
			addMessage(_WOP_SHOP_ERROR_SAVE_DATABASE, 'error');
            $this->setRedirect("admin.php?page=options&tab=vendors&task=edit&id=".$vendor->id);
            return 0;
        }        
        do_action_ref_array( 'onAfterSaveVendor', array(&$vendor) );
        $this->setRedirect("admin.php?page=options&tab=vendors");
    }
}