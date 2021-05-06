<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class CartWshopController extends WshopController{
    
    public function __construct() {
        parent::__construct();
        global $cart_error;
        $cart_error = new WP_Error;     
    }
    
    public function display(){
        $this->view();
    }

    public function add(){
        header("Cache-Control: no-cache, must-revalidate");
        $config = Factory::getConfig(); 
        if ($config->user_as_catalog || !getDisplayPriceShop()) return 0;

        $ajax = Request::getInt('ajax');
        $product_id = Request::getInt('product_id');
        //$category_id = Request::getInt('category_id');
        if ($config->use_decimal_qty){
            $quantity = floatval(str_replace(",",".",Request::getVar('quantity',1)));
            $quantity = round($quantity, $config->cart_decimal_qty_precision);
        }else{
            $quantity = Request::getInt('quantity',1);
        }
        $to = Request::getVar('to',"cart");
        if ($to!="cart" && $to!="wishlist") $to = "cart"; 

        $wop_shop_attr_id = Request::getVar('wshop_attr_id');
        if (!is_array($wop_shop_attr_id)) $wop_shop_attr_id = array();
        foreach($wop_shop_attr_id as $k=>$v) $wopshop_attr_id[intval($k)] = intval($v);

        $freeattribut = Request::getVar("freeattribut");
        if (!is_array($freeattribut)) $freeattribut = array();
        
        $cart = Factory::getModel('cart');
        $cart->load($to);
        if (!$cart->add($product_id, $quantity, $wop_shop_attr_id, $freeattribut)){
            if ($ajax){
                print getMessageJson();
                die();
            }
            $session =Factory::getSession();
            $back_value = array('pid'=>$product_id, 'attr'=>$wop_shop_attr_id, 'freeattr'=>$freeattribut,'qty'=>$quantity);
            //$back_value = array('pid'=>$product_id, 'qty'=>$quantity);
            $session->set('product_back_value', $back_value);

            $this->setRedirect(SEFLink('controller=product&task=view&product_id='.$product_id, 0, 1));
            return 0;
        }

        if ($ajax){
            print getOkMessageJson($cart);
            die();
        }

        if ($config->not_redirect_in_cart_after_buy){
            if ($to=="wishlist"){
                $message = _WOP_SHOP_ADDED_TO_WISHLIST;
            }else{
                $message = _WOP_SHOP_ADDED_TO_CART;
            }
            $this->setRedirect($_SERVER['HTTP_REFERER'], $message);
            return 1;
        }

        if ($to == "cart"){
            $defaultitemid = 0;
        } else {
            $defaultitemid = 1;
        }
        
        $this->setRedirect(SEFLink($cart->getUrlList(), $defaultitemid, 1));
    }

    public function view(){
        $config = Factory::getConfig();  
        if ($config->user_as_catalog) return 0;
        $session = Factory::getSession();
        //$mainframe = Factory::getApplication();
        //$params = $mainframe->getParams();
        $ajax = Request::getInt('ajax');
        $cart = Factory::getModel('cart');
        $cart->load();
        $cart->addLinkToProducts(1);
        $cart->setDisplayFreeAttributes();
        $seo = Factory::getTable("seo");
        $seodata = $seo->loadData("cart");
        if ($seodata->title==""){
            $seodata->title = _WOP_SHOP_CART;
        }
        $this->addMetaTag('description', $seodata->description);
        $this->addMetaTag('keyword', $seodata->keyword);
        $this->addMetaTag('title', $seodata->title);     
        
        $shopurl = SEFLink('controller=products&task=display');
        if ($config->cart_back_to_shop=="product"){
            $endpagebuyproduct = xhtmlUrl($session->get('wshop_end_page_buy_product'));
        }elseif ($config->cart_back_to_shop=="list"){
            $endpagebuyproduct =  xhtmlUrl($session->get('wshop_end_page_list_product'));
        }
        if (isset($endpagebuyproduct) && $endpagebuyproduct){
            $shopurl = $endpagebuyproduct;
        }

        $statictext = Factory::getTable("statictext");
        $tmp = $statictext->loadData("cart");
        $cartdescr = $tmp->text;

        
        $weight_product = $cart->getWeightProducts();
        if ($weight_product==0 && $config->hide_weight_in_cart_weight0){
            $config->show_weight_order = 0;
        }
        
        if ($config->shop_user_guest==1){
            $href_checkout = SEFLink('controller=checkout&task=step2&check_login=1',1, 0, $config->use_ssl);
        }else{
            $href_checkout = SEFLink('controller=checkout&task=step2',1, 0, $config->use_ssl);
        }
        
        $tax_list = $cart->getTaxExt(0, 1);
        $show_percent_tax = 0;
        if (count($tax_list)>1 || $config->show_tax_in_product) $show_percent_tax = 1;
        if ($config->hide_tax) $show_percent_tax = 0;
        $hide_subtotal = 0;
        if (($config->hide_tax || count($tax_list)==0) && !$cart->rabatt_summ) $hide_subtotal = 1;
        $checkout = Factory::getModel('checkout');
        $checkout_navigator = $checkout->showCheckoutNavigation('0');
        do_action_ref_array('onBeforeDisplayCart', array(&$cart));

        $view_name = "cart";
        $view=$this->getView($view_name);
        $view->setLayout("cart");

        $view->assign('config', $config);
        $view->assign('products', $cart->products);
        $view->assign('summ', $cart->getPriceProducts());
        $view->assign('image_product_path', $config->image_product_live_path);
        $view->assign('image_path', $config->live_path);
        $view->assign('no_image', $config->noimage);
        $view->assign('href_shop', $shopurl);
        $view->assign('href_checkout', $href_checkout);
        $view->assign('discount', $cart->getDiscountShow());
        $view->assign('free_discount', $cart->getFreeDiscount());
        $view->assign('use_rabatt', $config->use_rabatt_code);
        $view->assign('tax_list', $cart->getTaxExt(0, 1));
        $view->assign('fullsumm', $cart->getSum(0, 1));
        $view->assign('show_percent_tax', $show_percent_tax);
        $view->assign('hide_subtotal', $hide_subtotal);
        $view->assign('weight', $weight_product);
        $view->assign('checkout_navigator', $checkout_navigator);
        $view->assign('shippinginfo', SEFLink($config->shippinginfourl));
        $view->assign('cartdescr', $cartdescr);
        do_action_ref_array('onBeforeDisplayCartView', array(&$view));
        $view->display();
        if ($ajax) die();
    }

    public function delete(){
        header("Cache-Control: no-cache, must-revalidate");
        $ajax = Request::getInt('ajax');
        $number_id = Request::getInt('number_id');
        $cart = Factory::getModel('cart');
        $cart->load();    
        $cart->delete($number_id);
        if ($ajax){
            print getOkMessageJson($cart);
            die();
        }
        $this->setRedirect(SEFLink($cart->getUrlList(), 0, 1));
    }

    public function refresh(){
        $ajax = Request::getInt('ajax');
        $quantitys = Request::getVar('quantity');
        $cart = Factory::getModel('cart');
        $cart->load();
        $cart->refresh($quantitys);
        if ($ajax){
            print getOkMessageJson($cart);
            die();
        }
        $this->setRedirect(SEFLink($cart->getUrlList(), 0, 1));
    }

    public function discountsave(){
        do_action_ref_array('onLoadDiscountSave', array());
        
        $ajax = Request::getInt('ajax');
        $coupon = Factory::getTable('coupon');
        $code = Request::getVar('rabatt');

        if ($coupon->getEnableCode($code)){
            $cart = Factory::getModel('cart');
            $cart->load();
            do_action_ref_array('onBeforeDiscountSave', array(&$coupon, &$cart) );
            $cart->setRabatt($coupon->coupon_id, $coupon->coupon_type, $coupon->coupon_value);
            do_action_ref_array('onAfterDiscountSave', array(&$coupon, &$cart) );
            if ($ajax){
                print getOkMessageJson($cart);
                die();
            }               
        } else {
            global $cart_error;
            $cart_error->add(1,$coupon->error);
            if ($ajax){
                print getMessageJson();
                die();
            }else{
                Factory::getApplication()->enqueueMessage($coupon->error, 'error');
            }
        }
        $this->setRedirect(SEFLink($cart->getUrlList(), 0, 1));
    }
}