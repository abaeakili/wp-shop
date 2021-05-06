<?php
class ReviewsWshopAdminController extends WshopAdminController {
    function __construct() {
        parent::__construct();
    }
   
    function display(){
        $mainframe = Factory::getApplication();
        $id_vendor_cuser = getIdVendorForCUser();
        $reviews_model = $this->getModel("reviews");
        $products_model = $this->getModel("products");
        $context = "list.admin.reviews";
        //$limit = $mainframe->getUserStateFromRequest( $context.'limit', 'limit', 'list_limit');
        //$limitstart = $mainframe->getUserStateFromRequest( $context.'limitstart', 'limitstart', 0);
        $category_id = $mainframe->getUserStateFromRequest( $context.'category_id', 'category_id', 0);
        $text_search = $mainframe->getUserStateFromRequest( $context.'text_search', 's', '');
        $filter_order = $mainframe->getUserStateFromRequest($context.'filter_order', 'filter_order', "pr_rew.review_id");
        $filter_order_Dir = $mainframe->getUserStateFromRequest($context.'filter_order_Dir', 'order');

        $limit = getStateFromRequest( $context.'per_page', 'per_page', 20);
        $paged = $mainframe->getUserStateFromRequest($context.'paged', 'paged', '1');

        if ($category_id){
            $product_id = $mainframe->getUserStateFromRequest( $context.'product_id', 'product_id', 0, 'int' );
        } else {
            $product_id = null;
        }

        $products_select = "";

        if ($category_id){
            $prod_filter = array("category_id"=>$category_id);
            if ($id_vendor_cuser) $prod_filter['vendor_id'] = $id_vendor_cuser;
            $products = $products_model->getAllProducts($prod_filter, 0, 100);
            if (count($products)) {
                $start_pr_option = JHTML::_('select.option', '0', _WOP_SHOP_SELECT_PRODUCT , 'product_id', 'name');
                array_unshift($products, $start_pr_option);   
                $products_select = JHTML::_('select.genericlist', $products, 'product_id', 'class="chosen-select" onchange="document.adminForm.submit();" size = "1" ', 'product_id', 'name', $product_id);
            }
        }

        $total = $reviews_model->getAllReviews($category_id, $product_id, NULL, NULL, $text_search, "count", $id_vendor_cuser, $filter_order, $filter_order_Dir);

        if(($paged-1) > ($total/$limit) ) $paged = 1;
        $limitstart = ($paged-1)*$limit;

        $pagination = $products_model->getPagination($total, $limit);
        $search = $products_model->search($text_search);

        $reviews = $reviews_model->getAllReviews($category_id, $product_id, $limitstart, $limit, $text_search, "list", $id_vendor_cuser, $filter_order, $filter_order_Dir);

        $start_option = HTML::_('select.option', '0', _WOP_SHOP_SELECT_CATEGORY,'category_id','name'); 

        $categories_select = buildTreeCategory(0,1,0);
        array_unshift($categories_select, $start_option);

        $categories = HTML::_('select.genericlist', $categories_select, 'category_id', 'class="chosen-select" onchange="document.adminForm.submit();" size = "1" ', 'category_id', 'name', $category_id);

        if($filter_order_Dir == 'asc') $filter_order_Dir = 'desc'; else $filter_order_Dir = 'asc';

        $view=$this->getView("comments");
        $view->setLayout("list");
        $view->assign('categories', $categories);
        $view->assign('reviews', $reviews); 
        $view->assign('limit', $limit);
        $view->assign('limitstart', $limitstart);
        $view->assign('text_search', $text_search); 
        $view->assign('pagination', $pagination); 
        $view->assign('products_select', $products_select);
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);
        $view->assign('search', $search);
        do_action_ref_array('onBeforeDisplayReviews', array(&$view));		
        $view->display();
     }

     function remove(){
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
        $mainframe = Factory::getApplication();
        $reviews_model = $this->getModel("reviews");
        $cid = Request::getVar('cid');
        $review = $reviews_model->getReview($cid[0]);

        $config = Factory::getConfig();
        $options = array();
        $options[] = HTML::_('select.option', 0, 'none','value','text');
        for($i=1;$i<=$config->max_mark;$i++){
            $options[] = HTML::_('select.option', $i, $i,'value','text'); 
        }

        $mark = HTML::_('select.genericlist', $options, 'mark', 'class = "inputbox" size = "1" ', 'value', 'text', $review->mark); 
        //JFilterOutput::objectHTMLSafe($review, ENT_QUOTES);
        
        $view=$this->getView("comments", 'html');
        $view->setLayout("edit");
        /*if ($this->getTask()=='edit'){
            $view->assign('edit', 1);
        }*/
        $view->assign('review', $review); 
        $view->assign('mark', $mark);
        //$view->assign('etemplatevar', '');
        
        do_action_ref_array('onBeforeEditReviews', array(&$view));
        $view->display();
     }
     
     function save(){
        $review = Factory::getTable('review');
        $post = Request::get('post');
        if (intval($post['review_id'])==0) $post['time'] = getJsDate();
        do_action_ref_array( 'onBeforeSaveReview', array(&$post) );
        if (!$post['product_id']){
            addMessage(_WOP_SHOP_ERROR_DATA, 'error');
            $this->setRedirect("admin.php?page=options&tab=reviews");
            return 0;
        }

        if (!$review->bind($post)) {
            addMessage(_WOP_SHOP_ERROR_BIND, 'error');
            $this->setRedirect("admin.php?page=options&tab=reviews");
            return 0;
        }
        if (!$review->store()) {
            addMessage(_WOP_SHOP_ERROR_SAVE_DATABASE, 'error');
            $this->setRedirect("admin.php?page=options&tab=reviews&task=edit&cid[]=".$review->review_id);
            return 0;
        }

        $product = Factory::getTable('product');
        $product->load($review->product_id);
        $product->loadAverageRating();
        $product->loadReviewsCount();
        $product->store();
        do_action_ref_array( 'onAfterSaveReview', array(&$review) );
		$this->setRedirect("admin.php?page=options&tab=reviews");
    }
     
    function publish(){
        $this->_publish(1);
        $this->setRedirect("admin.php?page=options&tab=reviews");
    }
    
    function unpublish(){
        $this->_publish(0);
        $this->setRedirect("admin.php?page=options&tab=reviews");
    }    
    
    function _publish($flag) {
        $config = Factory::getConfig();
        global $wpdb;
        $cid = Request::getVar('rows');

        do_action_ref_array( 'onBeforePublishReview', array(&$cid, &$flag) );
        foreach ($cid as $key => $value) {
            $wpdb->update( $wpdb->prefix."wshop_products_reviews", array( 'publish' => esc_sql($flag) ), array( 'review_id' => esc_sql($value) ));
            $review = Factory::getTable('review');
            $review->load($value);
            $product = Factory::getTable('product');
            $product->load($review->product_id);
            $product->loadAverageRating();
            $product->loadReviewsCount();
            $product->store();
            unset($product);
            unset($review);
        }
        
        do_action_ref_array('onAfterPublishReview', array(&$cid, &$flag) );
    }
}