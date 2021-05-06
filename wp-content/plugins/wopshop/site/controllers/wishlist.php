<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class WishlistWshopController extends WshopController{
    
    function __construct(){
        parent::__construct();
		do_action_ref_array('onConstructWshopControllerWishlist', array(&$this));
    }

    function display(){
        $this->view();
    }

    function view(){
	$config = Factory::getConfig();
        $session = Factory::getSession();
        //$params = $mainframe->getParams();
        $ajax = Request::getInt('ajax');

        $cart = Factory::getModel('cart');
        $cart->load("wishlist");
        $cart->addLinkToProducts(1, "wishlist");
        //freeAtribs
        $cart->setDisplayFreeAttributes();

        $seo = Factory::getTable("seo");
        $seodata = $seo->loadData("wishlist");

        //setMetaData($seodata->title, $seodata->keyword, $seodata->description, $params);
        $this->addMetaTag('description', $seodata->description);
        $this->addMetaTag('keyword', $seodata->keyword);
        $this->addMetaTag('title', $seodata->title);         

        $shopurl = SEFLink('controller=category&task=view',1);
        if ($config->cart_back_to_shop=="product"){
            $endpagebuyproduct = xhtmlUrl($session->get('wshop_end_page_buy_product'));
        }elseif ($config->cart_back_to_shop=="list"){
            $endpagebuyproduct = xhtmlUrl($session->get('wshop_end_page_list_product'));
        }
        if (isset($endpagebuyproduct) && $endpagebuyproduct){
            $shopurl = $endpagebuyproduct;
        }

	$view_name = "cart";
        $view=$this->getView($view_name);
        $view->setLayout("wishlist");        
        $view->assign('config', $config);
        $view->assign('products', $cart->products);
        $view->assign('image_product_path', $config->image_product_live_path);
        $view->assign('image_path', $config->live_path);
        $view->assign('no_image', $config->noimage);
        $view->assign('href_shop', $shopurl);
        $view->assign('href_checkout', SEFLink('controller=cart&task=view',1));
		do_action_ref_array('onBeforeDisplayWishlistView', array(&$view));
	$view->display();
        if ($ajax) die();
    }

    function delete(){
        header("Cache-Control: no-cache, must-revalidate");
        $ajax = Request::getInt('ajax');
        $cart = Factory::getModel('cart');
        $cart->load('wishlist');    
        $cart->delete(Request::getInt('number_id'));
//        if ($ajax){
//            print getOkMessageJson($cart);
//            die();
//        }
        $this->setRedirect(SEFLink('controller=wishlist&task=view',0,1));
    }

    function remove_to_cart(){
        header("Cache-Control: no-cache, must-revalidate");
        $ajax = Request::getInt('ajax');
        $number_id = Request::getInt('number_id');
        do_action_ref_array('onBeforeLoadWishlistRemoveToCart', array(&$number_id));
        
        $cart = Factory::getModel('cart');
        $cart->load("wishlist");
        $prod = $cart->products[$number_id];
        $attr = json_decode($prod['attributes'], 1);
        $freeattribut = json_decode($prod['freeattributes'], 1);
        $cart->delete($number_id);
                        
        $cart = Factory::getModel('cart');
        $cart->load("cart");        
        $cart->add($prod['product_id'], $prod['quantity'], $attr, $freeattribut);
        do_action_ref_array('onAfterWishlistRemoveToCart', array(&$cart));
//        if ($ajax){
//            print getOkMessageJson($cart);
//            die();
//        }
        $this->setRedirect( SEFLink('controller=cart&task=view',1,1) );
    }
}
?>