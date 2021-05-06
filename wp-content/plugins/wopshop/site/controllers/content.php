<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class ContentWshopController extends WshopController{
    
    public function __construct($config = array()){
        parent::__construct($config);
        do_action_ref_array('onConstructWshopControllerContent', array(&$this));
    }
    
    public function display(){
        $this->view();
    }

    public function view(){
        $config = Factory::getConfig();
        //global $config;
        $page = Request::getVar('content-page');
        switch($page){
            case 'agb':
                $pathway = _WOP_SHOP_AGB;
            break;
            case 'return_policy':
                $pathway = _WOP_SHOP_RETURN_POLICY;
            break;
            case 'shipping':
                $pathway = _WOP_SHOP_SHIPPING;
            break;
            case 'privacy_statement':
                $pathway = _WOP_SHOP_PRIVACY_STATEMENT;
            break;
        }
        //appendPathWay($pathway);
        $seo = Factory::getTable("seo");
        $seodata = $seo->loadData("content-".$page);
        if ($seodata->title == ""){
            $seodata->title = $pathway;
        }
        $this->addMetaTag('description', $seodata->description);
        $this->addMetaTag('keyword', $seodata->keyword);
        $this->addMetaTag('title', $seodata->title);        
        
        $statictext = Factory::getTable("statictext");
        
        $order_id = Request::getInt('order_id');
        $cartp = Request::getInt('cart');
        
        if ($config->return_policy_for_product && $page=='return_policy' && ($cartp || $order_id)){
            if ($cartp){
                $cart = Factory::getModel('cart');
                $cart->load();
                $list = $cart->getReturnPolicy();
            }else{
                $order = Factory::getTable('order');
                $order->load($order_id);
                $list = $order->getReturnPolicy();
            }
            $listtext = array();
            foreach($list as $v){
                $listtext[] = $v->text;
            }
            $row = new stdClass();
            $row->id = -1;
            $row->text = implode('<div class="return_policy_space"></div>', $listtext);
        }else{
            $row = $statictext->loadData($page);
        }
                
        if (!$row->id){
            addMessage(_WOP_SHOP_PAGE_NOT_FOUND, 'error');
            return;
        }
        $text = $row->text;
        do_action_ref_array('onBeforeDisplayContent', array($page, &$text));
        
        $view_name = "content";
        $view=$this->getView($view_name);
        $view->setLayout("content");        
        $view->assign('text', $text);
        do_action_ref_array('onBeforeDisplayContentView', array(&$view));
        $view->display();
		$tmpl = Request::getVar('tmpl');
		if($tmpl)
			die();
    }
}
?>