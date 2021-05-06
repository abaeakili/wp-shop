<?php
class CouponsWshopAdminController extends WshopAdminController {
    function __construct() {
        parent::__construct();
    }
   
    function display() {
        $context = "admin.coupons.";
        $limit = getStateFromRequest( $context.'per_page', 'per_page', 20);
        $paged = getStateFromRequest($context.'paged', 'paged', 1);

        $filter_order = getStateFromRequest($context.'filter_order', 'filter_order', 'C.coupon_code');
        $filter_order_Dir = getStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', 'asc');

        $model = $this->getModel('coupons');
        $total = $model->getCountCoupons();

        $actions = array(
            'delete' => _WOP_SHOP_DELETE,
            'publish' => _WOP_SHOP_ACTION_PUBLISH,
            'unpublish' => _WOP_SHOP_ACTION_UNPUBLISH,
        );
        $bulk = $model->getBulkActions($actions);

        if(($paged-1) > ($total/$limit) ) $paged = 1;
        $limitstart = ($paged-1)*$limit;
        $pagination = $model->getPagination($total, $limit);

        $config = Factory::getConfig();

        //$coupons = $this->getModel("coupons");
        //$pageNav = new Pagination($total, $limitstart, $limit);
        //$pagination = $model->getPagination($total, $limit);
//echo '<br>limitstart='.$limitstart.' --- limit = '.$limit.' --- filter_order = '.$filter_order.' --- filter_order_Dir = '.$filter_order_Dir.'<br>';
        $rows = $model->getAllCoupons($limitstart, $limit, $filter_order, $filter_order_Dir);

        $currency = Factory::getTable('currency');
        $currency->load($config->mainCurrency);

        $view = $this->getView('coupons');
        $view->setLayout('list');
        $view->assign('rows', $rows);
        $view->assign('currency', $currency->currency_code);
        $view->assign('pagination', $pagination);
        $view->assign('filter_order', $filter_order);
        if($filter_order_Dir == 'asc') $filter_order_Dir = 'desc'; else $filter_order_Dir = 'asc';
        $view->assign('filter_order_Dir', $filter_order_Dir);
        $view->assign('bulk', $bulk);
		do_action_ref_array('onBeforeDisplayCoupons', array(&$view));
	$view->display();
    }

    function edit(){
        $coupon_id = Request::getInt('row');
        $coupon = Factory::getTable('coupon');
        $coupon->load($coupon_id);
        $edit = ($coupon_id)?($edit = 1):($edit = 0);
        $arr_type_coupon = array();
        $arr_type_coupon[0] = new StdClass();
        $arr_type_coupon[0]->coupon_type = 0;
        $arr_type_coupon[0]->coupon_value = _WOP_SHOP_COUPON_PERCENT;

        $arr_type_coupon[1] = new StdClass();
        $arr_type_coupon[1]->coupon_type = 1;
        $arr_type_coupon[1]->coupon_value = _WOP_SHOP_COUPON_ABS_VALUE;
        
        if (!$coupon_id){
          $coupon->coupon_type = 0;
          $coupon->finished_after_used = 1;
          $coupon->for_user_id = 0;
        }
        $currency_code = getMainCurrencyCode();

        $lists['coupon_type'] = HTML::_('select.radiolist', $arr_type_coupon, 'coupon_type', 'onchange="changeCouponType()"', 'coupon_type', 'coupon_value', $coupon->coupon_type);

        $_tax = $this->getModel("taxes");
        $all_taxes = $_tax->getAllTaxes();
        $list_tax = array();
        foreach ($all_taxes as $tax) {
            $list_tax[] = HTML::_('select.option', $tax->tax_id, $tax->tax_name . ' (' . $tax->tax_value . '%)','tax_id','tax_name');
        }
        $lists['tax'] = HTML::_('select.genericlist', $list_tax, 'tax_id', 'class = "inputbox" size = "1" ', 'tax_id', 'tax_name', $coupon->tax_id);

        $view=$this->getView("coupons");
        $view->setLayout("edit");
        $view->assign('coupon', $coupon);
        $view->assign('lists', $lists);
        $view->assign('edit', $edit);
        $view->assign('currency_code', $currency_code);
		do_action_ref_array('onBeforeEditCoupons', array(&$view));
        $view->display();
        
    }
    function save(){
        if ( !empty($_POST) && check_admin_referer('coupon_edit','name_of_nonce_field') )
        {
            $coupon_id = Request::getInt("coupon_id");
            $coupon = Factory::getTable('coupon');

            $post = Request::get("post");        
            $post['coupon_code'] = Request::getCmd("coupon_code");
            $post['coupon_publish'] = Request::getInt("coupon_publish");
            $post['finished_after_used'] = Request::getInt("finished_after_used");
            $post['coupon_value'] = saveAsPrice($post['coupon_value']);
			do_action_ref_array( 'onBeforeSaveCoupon', array(&$post) );
            if (!$post['coupon_code']){
                addMessage(_WOP_SHOP_ERROR_COUPON_CODE, 'error');
                $this->setRedirect("admin.php?page=options&tab=coupons&task=edit&row=".$coupon->coupon_id);
                return 0;
            }

            if ($post['coupon_value']<0 || ($post['coupon_value']>100 && $post['coupon_type']==0)){
                addMessage(_WOP_SHOP_ERROR_COUPON_VALUE, 'error');
                $this->setRedirect("admin.php?page=options&tab=coupons&task=edit&row=".$coupon_id);    
                return 0;
            }

            if(!$coupon->bind($post)) {
                addMessage(_WOP_SHOP_ERROR_BIND, 'error');
                $this->setRedirect("admin.php?page=options&tab=coupons");
                return 0;
            }

            if ($coupon->getExistCode()){
                addMessage(_WOP_SHOP_ERROR_COUPON_EXIST, 'error');
                $this->setRedirect("admin.php?page=options&tab=coupons");
                return 0;
            }

            if (!$coupon->store()) {
                addMessage(_WOP_SHOP_ERROR_SAVE_DATABASE, 'error');
                $this->setRedirect("admin.php?page=options&tab=coupons");
                return 0;
            }
			do_action_ref_array( 'onAfterSaveCoupon', array(&$coupon) );
            $this->setRedirect("admin.php?page=options&tab=coupons");
            
        }
        else addMessage(_WOP_SHOP_ERROR_BIND, 'error');
        $this->setRedirect('admin.php?page=options&tab=coupons');
    }

    function publish(){
        $this->publishCoupon(1);
    }
    
    function unpublish(){
        $this->publishCoupon(0);
    }

    function publishCoupon($flag) {
        global $wpdb;
        $cid = Request::getVar("rows");
		do_action_ref_array( 'onBeforePublishCoupon', array(&$cid,&$flag) );
        if(is_array($cid))
        foreach ($cid as $key => $value) {
            $wpdb->update( $wpdb->prefix.'wshop_coupons', array( 'coupon_publish' => esc_sql($flag) ), array( 'coupon_id' => esc_sql($value) ));
            //$wpdb->show_errors();            $wpdb->print_error();
        }
		do_action_ref_array( 'onAfterPublishCoupon', array(&$cid,&$flag) );
        $this->setRedirect("admin.php?page=options&tab=coupons", _WOP_SHOP_ACTION_COUPON_UPDATE);
    }
    function delete(){
        $cid = Request::getVar("rows");
        global $wpdb;
        $text = '';
		do_action_ref_array( 'onBeforeRemoveCoupon', array(&$cid) );
        foreach ($cid as $key => $value) {
            $wpdb->delete( $wpdb->prefix.'wshop_coupons', array( 'coupon_id' => esc_sql($value) ));
        }
		do_action_ref_array( 'onAfterRemoveCoupon', array(&$cid) );
        $this->setRedirect("admin.php?page=options&tab=coupons", _WOP_SHOP_COUPON_DELETED);
    }
}