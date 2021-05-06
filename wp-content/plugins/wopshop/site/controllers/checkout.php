<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
//include_once(WOPSHOP_PLUGIN_URL."payments/payment.php");
//include_once(WOPSHOP_PLUGIN_URL."shippingform/shippingform.php");

class CheckoutWshopController extends WshopController{
    
    public function __construct(){
        parent::__construct();
        
        do_action_ref_array('onConstructWshopControllerCheckout', array(&$this));
    }
    
    public function display(){
        $this->step2();
    }
    
    public function step2(){
        $config = Factory::getConfig();
        $checkout = Factory::getModel('checkout');
        $checkout->checkStep(2);
        $format = str_replace(array("%d","%m","%Y"), array('dd','mm','yy'), $config->field_birthday_format);
        
        Factory::loadDatepicker();
        
        do_action_ref_array('onLoadCheckoutStep2', array());
        
        $session = Factory::getSession();
        $user = wp_get_current_user();
        $country = Factory::getTable('country');
        
        $checkLogin = Request::getInt('check_login');
        if ($checkLogin){
            $session->set("show_pay_without_reg", 1);
            checkUserLogin();
        }

//        appendPathWay(_WOP_SHOP_CHECKOUT_ADDRESS);
        $seo = Factory::getTable("seo");
        $seodata = $seo->loadData("checkout-address");
        if ($seodata->title==""){
            $seodata->title = _WOP_SHOP_CHECKOUT_ADDRESS;
        }
        $this->addMetaTag('description', $seodata->description);
        $this->addMetaTag('keyword', $seodata->keyword);
        $this->addMetaTag('title', $seodata->title);
        
        $cart = Factory::getModel('cart');
        $cart->load();
        $cart->getSum();

        $adv_user = Factory::getUser();
        
        $adv_user->birthday = getDisplayDate($adv_user->birthday, $config->field_birthday_format);
        $adv_user->d_birthday = getDisplayDate($adv_user->d_birthday, $config->field_birthday_format);
        
        $tmp_fields = $config->getListFieldsRegister();
        $config_fields = $tmp_fields['address'];
        $count_filed_delivery = getEnableDeliveryFiledRegistration('address');

        $checkout_navigator = $checkout->showCheckoutNavigation(2);
        if ($config->show_cart_all_step_checkout){
            $small_cart = $this->_showSmallCart(2);
        }else{
            $small_cart = '';
        }

        $view_name = "checkout";
        //$view_config = array("template_path"=>$config->template_path.$config->template."/".$view_name);
        $view = $this->getView($view_name);
        $view->setLayout("adress");
        $view->assign('select', $config->user_field_title);
        
        if (!$adv_user->country) {
            $adv_user->country = $config->default_country;
        }
        if (!$adv_user->d_country) {
            $adv_user->d_country = $config->default_country;
        }

        $option_country[] = HTML::_('select.option',  '0', _WOP_SHOP_REG_SELECT, 'country_id', 'name' );
        $option_countryes = array_merge($option_country, Factory::getAllCountries());
        $select_countries = HTML::_('select.genericlist', $option_countryes, 'country', 'class = "inputbox" size = "1"','country_id', 'name', $adv_user->country );
        $select_d_countries = HTML::_('select.genericlist', $option_countryes, 'd_country', 'class = "inputbox" size = "1"','country_id', 'name', $adv_user->d_country);

        foreach($config->user_field_title as $key => $value) {
            $option_title[] = HTML::_('select.option', $key, $value, 'title_id', 'title_name');
        }
        $select_titles = HTML::_('select.genericlist', $option_title, 'title', 'class = "inputbox"','title_id', 'title_name', $adv_user->title);            
        $select_d_titles = HTML::_('select.genericlist', $option_title, 'd_title', 'class = "inputbox"','title_id', 'title_name', $adv_user->d_title);
        
        $client_types = array();
        foreach ($config->user_field_client_type as $key => $value) {
            $client_types[] = HTML::_('select.option', $key, $value, 'id', 'name' );
        }
        $select_client_types = HTML::_('select.genericlist', $client_types,'client_type','class = "inputbox" onchange="showHideFieldFirm(this.value)"','id','name', $adv_user->client_type);
        
        //filterHTMLSafe( $adv_user, ENT_QUOTES);

		if ($config_fields['birthday']['display'] || $config_fields['d_birthday']['display']){
            //HTML::_('behavior.calendar');
        }
        $view->assign('config', $config);
        $view->assign('format', $format);
        $view->assign('select_countries', $select_countries);
        $view->assign('select_d_countries', $select_d_countries);
        $view->assign('select_titles', $select_titles);
        $view->assign('select_d_titles', $select_d_titles);
        $view->assign('select_client_types', $select_client_types);
        $view->assign('live_path', URI::base());
        $view->assign('config_fields', $config_fields);
        $view->assign('count_filed_delivery', $count_filed_delivery);
        $view->assign('user', $adv_user);
        $view->assign('delivery_adress', $adv_user->delivery_adress);
        $view->assign('checkout_navigator', $checkout_navigator);
        $view->assign('small_cart', $small_cart);
        $view->assign('action', SEFLink('controller=checkout&task=step2save', 0, 0, $config->use_ssl));
        do_action_ref_array('onBeforeDisplayCheckoutStep2View', array(&$view));
        $view->display();
    }
    
    public function step2save(){
        $config = Factory::getConfig();
        $checkout = Factory::getModel('checkout');
        $checkout->checkStep(2);

        do_action_ref_array('onLoadCheckoutStep2save', array());

        $cart = Factory::getModel('cart');
        $cart->load();

        $post = Request::get('post');
        if (!count($post)){
            addMessage(_WOP_SHOP_ERROR_DATA, 'error');
            $this->setRedirect(SEFLink('controller=checkout&task=step2', 0, 1, $config->use_ssl));
            return 0;
        }
        
        if ($post['birthday']) {
            $post['birthday'] = getJsDateDB($post['birthday'], $config->field_birthday_format);
        }
        
        if ($post['d_birthday']) {
            $post['d_birthday'] = getJsDateDB($post['d_birthday'], $config->field_birthday_format);
        }
        unset($post['user_id']);
        unset($post['usergroup_id']);
        $post['lang'] = $config->cur_lang;
        $user = wp_get_current_user();
        $adv_user = Factory::getUser();
        
        $adv_user->bind($post);
        if (!$adv_user->check("address")){
            addMessage($adv_user->getError(), 'error');
            $this->setRedirect(SEFLink('controller=checkout&task=step2', 0, 1, $config->use_ssl));
            return 0;
        }
        
        do_action_ref_array('onBeforeSaveCheckoutStep2', array(&$adv_user, &$user, &$cart));
        
        if (!$adv_user->store()){
            addMessage(_WOP_SHOP_REGWARN_ERROR_DATABASE, 'error');
            $this->setRedirect(SEFLink('controller=checkout&task=step2', 0, 1, $config->use_ssl));
            return 0;
        }
//              TO-DO
//        if($user->id && !$config->not_update_user_joomla){
//            $user = clone(Factory::getUser());
//			if ($adv_user->email){
//				$user->email = $adv_user->email;
//			}
//			if ($adv_user->f_name || $adv_user->l_name){
//				$user->name = $adv_user->f_name." ".$adv_user->l_name;
//			}
//			if ($adv_user->f_name || $adv_user->l_name || $adv_user->email){
//				$user->save();
//			}
//        }
        
        setNextUpdatePrices();
        
		$cart->setShippingId(0);
		$cart->setShippingPrId(0);
		$cart->setShippingPrice(0);
		$cart->setPaymentId(0);
		$cart->setPaymentParams("");
		$cart->setPaymentPrice(0);
			
        do_action_ref_array('onAfterSaveCheckoutStep2', array(&$adv_user, &$user, &$cart));
        
        if ($config->without_shipping && $config->without_payment) {
            $checkout->setMaxStep(5);
            $this->setRedirect(SEFLink('controller=checkout&task=step5',0,1, $config->use_ssl));
            return 0; 
        }
        
        if ($config->without_payment){
            $checkout->setMaxStep(4);
            $this->setRedirect(SEFLink('&controller=checkout&task=step4',0,1,$config->use_ssl));
            return 0;
        }

		if ($config->step_4_3){
            if ($config->without_shipping){
                $checkout->setMaxStep(3);
                $this->setRedirect(SEFLink('controller=checkout&task=step3',0,1,$config->use_ssl));
                return 0;
            }
            $checkout->setMaxStep(4);
            $this->setRedirect(SEFLink('controller=checkout&task=step4',0,1,$config->use_ssl));
        }else{
			$checkout->setMaxStep(3);
			$this->setRedirect(SEFLink('controller=checkout&task=step3',0,1,$config->use_ssl));
		}
    }
    
    function step3(){
        $config = Factory::getConfig();
        $checkout = Factory::getModel('checkout');
    	$checkout->checkStep(3);

        do_action_ref_array('onLoadCheckoutStep3', array() );

        $session = Factory::getSession();
        $cart = Factory::getModel('cart');
        $cart->load();
        
        $user = wp_get_current_user();
        $adv_user = Factory::getUser();
        
//        appendPathWay(_WOP_SHOP_CHECKOUT_PAYMENT);
        $seo = Factory::getTable("seo");
        $seodata = $seo->loadData("checkout-payment");
        if ($seodata->title==""){
            $seodata->title = _WOP_SHOP_CHECKOUT_PAYMENT;
        }
        
        $this->addMetaTag('description', $seodata->description);
        $this->addMetaTag('keyword', $seodata->keyword);
        $this->addMetaTag('title', $seodata->title);         
        
        //$checkout_navigator = $this->_showCheckoutNavigation(3);
        $checkout_navigator = $checkout->showCheckoutNavigation(3);
        if ($config->show_cart_all_step_checkout){
            $small_cart = $this->_showSmallCart(3);
        }else{
            $small_cart = '';
        }
        
        if ($config->without_payment){
            $checkout->setMaxStep(4);
            $this->setRedirect(SEFLink('controller=checkout&task=step4',0,1,$config->use_ssl));
            return 0;
        }

        $paymentmethod = Factory::getTable('paymentmethod');
		$shipping_id = $cart->getShippingId();
        $all_payment_methods = $paymentmethod->getAllPaymentMethods(1, $shipping_id);
        $i = 0;
        $paym = array();
        foreach($all_payment_methods as $pm){
            $paym[$i] = new stdClass();
            if ($pm->scriptname!=''){
                $scriptname = $pm->scriptname;    
            }else{
                $scriptname = $pm->payment_class;   
            }
            $paymentmethod->load($pm->payment_id); 
            $paymentsysdata = $paymentmethod->getPaymentSystemData($scriptname);
            if ($paymentsysdata->paymentSystem){
                $paym[$i]->existentcheckform = 1;
				$paym[$i]->payment_system = $paymentsysdata->paymentSystem;
            }else{
                $paym[$i]->existentcheckform = 0;
            }
            
            $paym[$i]->name = $pm->name;
            $paym[$i]->payment_id = $pm->payment_id;
            $paym[$i]->payment_class = $pm->payment_class;
            $paym[$i]->scriptname = $pm->scriptname;
            $paym[$i]->payment_description = $pm->description;
            $paym[$i]->price_type = $pm->price_type;
            $paym[$i]->image = $pm->image;
            $paym[$i]->price_add_text = '';
            if ($pm->price_type==2){
                $paym[$i]->calculeprice = $pm->price;
                if ($paym[$i]->calculeprice!=0){
                    if ($paym[$i]->calculeprice>0){
                        $paym[$i]->price_add_text = '+'.$paym[$i]->calculeprice.'%';
                    }else{
                        $paym[$i]->price_add_text = $paym[$i]->calculeprice.'%';
                    }
                }
            }else{
                $paym[$i]->calculeprice = getPriceCalcParamsTax($pm->price * $config->currency_value, $pm->tax_id, $cart->products);
                if ($paym[$i]->calculeprice!=0){
                    if ($paym[$i]->calculeprice>0){
                        $paym[$i]->price_add_text = '+'.formatprice($paym[$i]->calculeprice);
                    }else{
                        $paym[$i]->price_add_text = formatprice($paym[$i]->calculeprice);
                    }
                }
            }
            
            $s_payment_method_id = $cart->getPaymentId();
            if ($s_payment_method_id == $pm->payment_id){
                $params = $cart->getPaymentParams();
            }else{
                $params = array();
            }

            $parseString = new parseString($pm->payment_params);
            $pmconfig = $parseString->parseStringToParams();

            if ($paym[$i]->existentcheckform){
                $paym[$i]->form = $paymentmethod->loadPaymentForm($paym[$i]->payment_system, $params, $pmconfig);
            }else{
                $paym[$i]->form = "";
            }
            
            $i++;
        }
        
        $s_payment_method_id = $cart->getPaymentId();
        $active_payment = intval($s_payment_method_id);

        if (!$active_payment){
            $list_payment_id = array();
            foreach($paym as $v){
                $list_payment_id[] = $v->payment_id;
            }
            if (in_array($adv_user->payment_id, $list_payment_id)) $active_payment = $adv_user->payment_id;
        }
        
        if (!$active_payment){
            if (isset($paym[0])){
                $active_payment = $paym[0]->payment_id;
            }
        }
        
        if ($config->hide_payment_step){
            $first_payment = $paym[0]->payment_class;
            if (!$first_payment){
                //JError::raiseWarning("", _WOP_SHOP_ERROR_PAYMENT);
                return 0;
            }
            $this->setRedirect(SEFLink('controller=checkout&task=step3save&payment_method='.$first_payment,0,1,$config->use_ssl));
            return 0;
        }
        $view_name = "checkout";
        $view = $this->getView($view_name);
        $view->setLayout("payments");        
        $view->assign('payment_methods', $paym);
        $view->assign('active_payment', $active_payment);
        $view->assign('checkout_navigator', $checkout_navigator);
        $view->assign('small_cart', $small_cart);
        $view->assign('action', SEFLink('controller=checkout&task=step3save', 0, 0, $config->use_ssl));
        do_action_ref_array('onBeforeDisplayCheckoutStep3View', array(&$view));
        $view->display();    
    }
    
    function step3save(){
        $checkout = Factory::getModel('checkout');
        $checkout->checkStep(3);
        
        $session = Factory::getSession();
        $config = Factory::getConfig();
        $post = Request::get('post');

        do_action_ref_array('onBeforeSaveCheckoutStep3save', array(&$post) );
        
        $cart = Factory::getModel('cart');
        $cart->load();
        
        $user = wp_get_current_user();
        $adv_user = Factory::getUser();
        
        $payment_method = Request::getVar('payment_method'); //class payment method
        $params = Request::getVar('params');
        if (isset($params[$payment_method])){
            $params_pm = $params[$payment_method];
        }else{
            $params_pm = '';
        }
        
        $paym_method = Factory::getTable('paymentmethod');
        $paym_method->class = $payment_method;
        $payment_method_id = $paym_method->getId();
        $paym_method->load($payment_method_id);
        $pmconfigs = $paym_method->getConfigs();
        $paymentsysdata = $paym_method->getPaymentSystemData();
        $payment_system = $paymentsysdata->paymentSystem;
        if ($paymentsysdata->paymentSystemError || $paym_method->payment_publish==0){
            $cart->setPaymentParams('');
            //JError::raiseWarning(500, _WOP_SHOP_ERROR_PAYMENT);
            $this->setRedirect(SEFLink('controller=checkout&task=step3',0,1,$config->use_ssl));
            return 0;
        }
        if ($payment_system){
            if (!$payment_system->checkPaymentInfo($params_pm, $pmconfigs)){
                $cart->setPaymentParams('');
                //JError::raiseWarning("", $payment_system->getErrorMessage());
                $this->setRedirect(SEFLink('controller=checkout&task=step3',0,1));
                return 0;
            }            
        }
        
        $paym_method->setCart($cart);
        $cart->setPaymentId($payment_method_id);
        $price = $paym_method->getPrice();
        $cart->setPaymentDatas($price, $paym_method);
        
        if (isset($params[$payment_method])) {
            $cart->setPaymentParams($params_pm);
        } else {
            $cart->setPaymentParams('');
        }
        
        $adv_user->saveTypePayment($payment_method_id);
        
        do_action_ref_array( 'onAfterSaveCheckoutStep3save', array(&$adv_user, &$paym_method, &$cart) );
        
        if ($config->without_shipping) {
            $checkout->setMaxStep(5);
            $this->setRedirect(SEFLink('controller=checkout&task=step5',0,1,$config->use_ssl));
            return 0; 
        }
        
		if ($config->step_4_3){
            $checkout->setMaxStep(5);
            $this->setRedirect(SEFLink('controller=checkout&task=step5',0,1,$config->use_ssl));
        }else{
			$checkout->setMaxStep(4);
			$this->setRedirect(SEFLink('controller=checkout&task=step4',0,1,$config->use_ssl));
		}
    }
    
    function step4(){
        $checkout = Factory::getModel('checkout');
        $checkout->checkStep(4);
        
        $session = Factory::getSession();
        $config = Factory::getConfig();

        do_action_ref_array('onLoadCheckoutStep4', array() );

        $seo = Factory::getTable("seo");
        $seodata = $seo->loadData("checkout-shipping");
        if ($seodata->title==""){
            $seodata->title = _WOP_SHOP_CHECKOUT_SHIPPING;
        }
        $this->addMetaTag('description', $seodata->description);
        $this->addMetaTag('keyword', $seodata->keyword);
        $this->addMetaTag('title', $seodata->title);          
        $cart = Factory::getModel('cart');
        $cart->load();
        
        $user = wp_get_current_user();
        $adv_user = Factory::getUser();
		$checkout_navigator = $checkout->showCheckoutNavigation(4);
        if ($config->show_cart_all_step_checkout){
            $small_cart = $this->_showSmallCart(4);
        }else{
            $small_cart = '';
        }
        
        if ($config->without_shipping){
        	$checkout->setMaxStep(5);
            $this->setRedirect(SEFLink('controller=checkout&task=step5',0,1,$config->use_ssl));
            return 0; 
        }
        
        $shippingmethod = Factory::getTable('shippingmethod');
        $shippingmethodprice = Factory::getTable('shippingmethodprice');
        
        if ($adv_user->delivery_adress){
            $id_country = $adv_user->d_country;
        }else{
            $id_country = $adv_user->country;
        }
        if (!$id_country) $id_country = $config->default_country;
        
        if (!$id_country){
            addMessage(_WOP_SHOP_REGWARN_COUNTRY, 'error');
        }
        
        if ($config->show_delivery_time_checkout){
            $deliverytimes = Factory::getAllDeliveryTime();
            $deliverytimes[0] = '';
        }
        if ($config->show_delivery_date){
            $deliverytimedays = Factory::getAllDeliveryTimeDays();
        }
        $sh_pr_method_id = $cart->getShippingPrId();
        $active_shipping = intval($sh_pr_method_id);
        $payment_id = $cart->getPaymentId();
        $shippings = $shippingmethod->getAllShippingMethodsCountry($id_country, $payment_id);
        foreach($shippings as $key=>$value){
            $shippingmethodprice->load($value->sh_pr_method_id);
            if ($config->show_list_price_shipping_weight){
                $shippings[$key]->shipping_price = $shippingmethodprice->getPricesWeight($value->sh_pr_method_id, $id_country, $cart);
            }
            $prices = $shippingmethodprice->calculateSum($cart);
            $shippings[$key]->calculeprice = $prices['shipping']+$prices['package'];
            $shippings[$key]->delivery = '';
            $shippings[$key]->delivery_date_f = '';
            if ($config->show_delivery_time_checkout){
                $shippings[$key]->delivery = $deliverytimes[$value->delivery_times_id];
            }
            if ($config->show_delivery_date){
                $day = $deliverytimedays[$value->delivery_times_id];
                if ($day){
                    $shippings[$key]->delivery_date = getCalculateDeliveryDay($day);
                    $shippings[$key]->delivery_date_f = formatdate($shippings[$key]->delivery_date);
                }
            }
            
            if ($value->sh_pr_method_id==$active_shipping){
                $params = $cart->getShippingParams();
            }else{
                $params = array();
            }
            
            $shippings[$key]->form = $shippingmethod->loadShippingForm($value->shipping_id, $value, $params);
        }

        if (!$active_shipping){
            foreach($shippings as $v){
                if ($v->shipping_id == $adv_user->shipping_id){
                    $active_shipping = $v->sh_pr_method_id;
                    break;
                }
            }
        }
        if (!$active_shipping){
            if (isset($shippings[0])){
                $active_shipping = $shippings[0]->sh_pr_method_id;
            }
        }
        
        if ($config->hide_shipping_step){
            $first_shipping = $shippings[0]->sh_pr_method_id;
            if (!$first_shipping){
                addMessage(_WOP_SHOP_ERROR_SHIPPING, 'error');
                return 0;
            }
            $this->setRedirect(SEFLink('controller=checkout&task=step4save&sh_pr_method_id='.$first_shipping,0,1,$config->use_ssl));
            return 0;
        }
        $view_name = "checkout";
        $view = $this->getView($view_name);
        $view->setLayout("shippings");        
        $view->assign('shipping_methods', $shippings);
        $view->assign('active_shipping', $active_shipping);
        $view->assign('config', $config);        
        $view->assign('checkout_navigator', $checkout_navigator);
        $view->assign('small_cart', $small_cart);
        $view->assign('action', SEFLink('controller=checkout&task=step4save',0,0,$config->use_ssl));
        do_action_ref_array('onBeforeDisplayCheckoutStep4View', array(&$view));
        $view->display();
    }
    
    function step4save(){
        $checkout = Factory::getModel('checkout');
    	$checkout->checkStep(4);
        $session = Factory::getSession();
        $config = Factory::getConfig();

        do_action_ref_array( 'onBeforeSaveCheckoutStep4save', array());

        $cart = Factory::getModel('cart');
        $cart->load();
        
        $user = wp_get_current_user();
        $adv_user = Factory::getUser();
        
        if ($adv_user->delivery_adress){
            $id_country = $adv_user->d_country;
        }else{
            $id_country = $adv_user->country;
        }
        if (!$id_country) $id_country = $config->default_country;
        
        $sh_pr_method_id = Request::getInt('sh_pr_method_id');
                
        $shipping_method_price = Factory::getTable('shippingmethodprice');
        $shipping_method_price->load($sh_pr_method_id);
        
        $sh_method = Factory::getTable('shippingmethod');
        $sh_method->load($shipping_method_price->shipping_method_id);
        
        if (!$shipping_method_price->sh_pr_method_id){
            addMessage(_WOP_SHOP_ERROR_SHIPPING, 'error');
            $this->setRedirect(SEFLink('controller=checkout&task=step4',0,1,$config->use_ssl));
            return 0;
        }
        
        if (!$shipping_method_price->isCorrectMethodForCountry($id_country)){
            addMessage(_WOP_SHOP_ERROR_SHIPPING, 'error');
            $this->setRedirect(SEFLink('controller=checkout&task=step4',0,1,$config->use_ssl));
            return 0;
        }
        
        if (!$sh_method->shipping_id){
            addMessage(_WOP_SHOP_ERROR_SHIPPING, 'error');
            $this->setRedirect(SEFLink('controller=checkout&task=step4',0,1,$config->use_ssl));
            return 0;
        }
        
        $allparams = Request::getVar('params');
        $params = $allparams[$sh_method->shipping_id];
        
        if (isset($params)){
            $cart->setShippingParams($params);
        }else{
            $cart->setShippingParams('');
        }
        
        $shippingForm = $sh_method->getShippingForm();
        
        if ($shippingForm && !$shippingForm->check($params, $sh_method)){
            addMessage($shippingForm->getErrorMessage(), 'error');
            $this->setRedirect(SEFLink('controller=checkout&task=step4',0,1,$config->use_ssl));
            return 0;
        }
        
        $prices = $shipping_method_price->calculateSum($cart);
        $cart->setShippingId($sh_method->shipping_id);
        $cart->setShippingPrId($sh_pr_method_id);
        $cart->setShippingsDatas($prices, $shipping_method_price);
        
        if ($config->show_delivery_date){
            $delivery_date = '';
            $deliverytimedays = Factory::getAllDeliveryTimeDays();
            $day = $deliverytimedays[$shipping_method_price->delivery_times_id];
            if ($day){
                $delivery_date = getCalculateDeliveryDay($day);
            }else{
                if ($config->delivery_order_depends_delivery_product){
                    $day = $cart->getDeliveryDaysProducts();
                    if ($day){
                        $delivery_date = getCalculateDeliveryDay($day);                    
                    }
                }
            }
            $cart->setDeliveryDate($delivery_date);
        }

        //update payment price
        $payment_method_id = $cart->getPaymentId();
        if ($payment_method_id){
            $paym_method = Factory::getTable('paymentmethod');
            $paym_method->load($payment_method_id);
            $cart->setDisplayItem(1, 1);
            $paym_method->setCart($cart);
            $price = $paym_method->getPrice();
            $cart->setPaymentDatas($price, $paym_method);
        }

        $adv_user->saveTypeShipping($sh_method->shipping_id);
        
        do_action_ref_array('onAfterSaveCheckoutStep4', array(&$adv_user, &$sh_method, &$shipping_method_price, &$cart));   
		if ($config->step_4_3 && !$config->without_payment){            
            $checkout->setMaxStep(3);
            $this->setRedirect(SEFLink('controller=checkout&task=step3',0,1,$config->use_ssl));
        }else{		
			$checkout->setMaxStep(5);
			$this->setRedirect(SEFLink('controller=checkout&task=step5',0,1,$config->use_ssl));
		}
    }
    
    function step5(){
        $checkout = Factory::getModel('checkout');
        $checkout->checkStep(5);
        do_action_ref_array('onLoadCheckoutStep5', array() );

        $seo = Factory::getTable("seo");
        $seodata = $seo->loadData("checkout-preview");
        if ($seodata->title==""){
            $seodata->title = _WOP_SHOP_CHECKOUT_PREVIEW;
        }
        $this->addMetaTag('description', $seodata->description);
        $this->addMetaTag('keyword', $seodata->keyword);
        $this->addMetaTag('title', $seodata->title);  

        $cart = Factory::getModel('cart');
        $cart->load();

        $session = Factory::getSession();
        $config = Factory::getConfig(); 
        $user = wp_get_current_user();
        $adv_user = Factory::getUser();

        $sh_method = Factory::getTable('shippingmethod');
        $shipping_method_id = $cart->getShippingId();
        $sh_method->load($shipping_method_id);
        
        $sh_mt_pr = Factory::getTable('shippingmethodprice');
        $sh_mt_pr->load($cart->getShippingPrId());
        if ($config->show_delivery_time_checkout){
            $deliverytimes = Factory::getAllDeliveryTime();
            $deliverytimes[0] = '';
            $delivery_time = $deliverytimes[$sh_mt_pr->delivery_times_id];
            if (!$delivery_time && $config->delivery_order_depends_delivery_product){
                $delivery_time = $cart->getDelivery();
            }
        }else{
            $delivery_time = '';
        }
        if ($config->show_delivery_date){
            $delivery_date = $cart->getDeliveryDate();
            if ($delivery_date){
                $delivery_date = formatdate($cart->getDeliveryDate());
            }
        }else{
            $delivery_date = '';
        }
        
        $pm_method = Factory::getTable('paymentmethod');
        $payment_method_id = $cart->getPaymentId();
		$pm_method->load($payment_method_id); 

        $field_country_name = "name_".$config->cur_lang;
        
        $invoice_info = array();
        $country = Factory::getTable('country');
        $country->load($adv_user->country);
        $invoice_info['f_name'] = $adv_user->f_name;
        $invoice_info['l_name'] = $adv_user->l_name;
        $invoice_info['firma_name'] = $adv_user->firma_name;
        $invoice_info['street'] = $adv_user->street;
        $invoice_info['street_nr'] = $adv_user->street_nr;
        $invoice_info['zip'] = $adv_user->zip;
        $invoice_info['state'] = $adv_user->state;
        $invoice_info['city'] = $adv_user->city;
        $invoice_info['country'] = $country->$field_country_name;
        $invoice_info['home'] = $adv_user->home;
        $invoice_info['apartment'] = $adv_user->apartment;
        
		if ($adv_user->delivery_adress){
            $country = Factory::getTable('country');
            $country->load($adv_user->d_country);
			$delivery_info['f_name'] = $adv_user->d_f_name;
            $delivery_info['l_name'] = $adv_user->d_l_name;
			$delivery_info['firma_name'] = $adv_user->d_firma_name;
			$delivery_info['street'] = $adv_user->d_street;
            $delivery_info['street_nr'] = $adv_user->d_street_nr;
			$delivery_info['zip'] = $adv_user->d_zip;
			$delivery_info['state'] = $adv_user->d_state;
            $delivery_info['city'] = $adv_user->d_city;
			$delivery_info['country'] = $country->$field_country_name;
            $delivery_info['home'] = $adv_user->d_home;
            $delivery_info['apartment'] = $adv_user->d_apartment;
		} else {
            $delivery_info = $invoice_info;
		}
        
        $no_return = 0;
        if ($config->return_policy_for_product){
            $cart_products = array();
            foreach($cart->products as $products){
                $cart_products[] = $products['product_id'];
            }
            $cart_products = array_unique($cart_products);
            $_product_option = Factory::getTable('productOption');
            $list_no_return = $_product_option->getProductOptionList($cart_products, 'no_return');
            $no_return = intval(in_array('1', $list_no_return));
        }
        if ($config->no_return_all){
            $no_return = 1;
        }
        
        $tmp_fields = $config->getListFieldsRegister();
        $config_fields = $tmp_fields['address'];
        $count_filed_delivery = $config->getEnableDeliveryFiledRegistration('address');
        
        $checkout_navigator = $checkout->showCheckoutNavigation(5);
        $small_cart = $this->_showSmallCart(5);

        $view_name = "checkout";
        $view = $this->getView($view_name);
        $view->setLayout("previewfinish");        
        
        do_action_ref_array('onBeforeDisplayCheckoutStep5', array(&$sh_method, &$pm_method, &$delivery_info, &$cart, &$view));
        $name = "name_".$config->cur_lang;
        $sh_method->name = $sh_method->$name;
        $view->assign('no_return', $no_return);
		$view->assign('sh_method', $sh_method );
		$view->assign('payment_name', $pm_method->$name);
        $view->assign('delivery_info', $delivery_info);
		$view->assign('invoice_info', $invoice_info);
        $view->assign('action', SEFLink('controller=checkout&task=step5save',0,0, $config->use_ssl));       
        $view->assign('config', $config);
        $view->assign('delivery_time', $delivery_time);
        $view->assign('delivery_date', $delivery_date);
        $view->assign('checkout_navigator', $checkout_navigator);
        $view->assign('small_cart', $small_cart);
		$view->assign('count_filed_delivery', $count_filed_delivery);
        do_action_ref_array('onBeforeDisplayCheckoutStep5View', array(&$view));
    	$view->display();
    }

    public function step5save(){
		$session = Factory::getSession();
        $config = Factory::getConfig();
        $checkout = Factory::getModel('checkout');
        $checkout->checkStep(5);
		$checkagb = Request::getVar('agb');
		do_action_ref_array('onLoadStep5save', array(&$checkagb));
        
        $user = wp_get_current_user();
        $adv_user = Factory::getUser();
        $cart = Factory::getModel('cart');
        $cart->load();
        $cart->setDisplayItem(1, 1);
        $cart->setDisplayFreeAttributes();
		
		if ($config->check_php_agb && $checkagb!='on'){
            addMessage(_WOP_SHOP_ERROR_AGB, 'error');
            $this->setRedirect(SEFLink('controller=checkout&task=step5',0,1,$config->use_ssl));
            return 0;
        }

        if (!$cart->checkListProductsQtyInStore()){
            $this->setRedirect(SEFLink('controller=cart&task=view',1,1));
            return 0;
        }
		if (!$session->get('checkcoupon')){
            if (!$cart->checkCoupon()){
                $cart->setRabatt(0,0,0);
                addMessage(_WOP_SHOP_RABATT_NON_CORRECT, 'error');
                $this->setRedirect(SEFLink('controller=cart&task=view',1,1));
                return 0;
            }
            $session->set('checkcoupon', 1);
        }

        $orderNumber = $config->getNextOrderNumber();
        $config->updateNextOrderNumber();

        $payment_method_id = $cart->getPaymentId();
        $pm_method = Factory::getTable('paymentmethod');
        $pm_method->load($payment_method_id);
		$payment_method = $pm_method->payment_class;

        if ($config->without_payment){
            $pm_method->payment_type = 1;
            $paymentSystemVerySimple = 1; 
        }else{
            $paymentsysdata = $pm_method->getPaymentSystemData();
            $payment_system = $paymentsysdata->paymentSystem;
            if ($paymentsysdata->paymentSystemVerySimple){
                $paymentSystemVerySimple = 1;
            }
            if ($paymentsysdata->paymentSystemError){
                $cart->setPaymentParams("");
                addMessage(_WOP_SHOP_ERROR_PAYMENT, 'error');
                $this->setRedirect(SEFLink('controller=checkout&task=step3',0,1,$config->use_ssl));
                return 0;
            }
        }

        $order = Factory::getTable('order');
        $arr_property = $order->getListFieldCopyUserToOrder();
        foreach($adv_user as $key => $value){
            if (in_array($key, $arr_property)){
                $order->$key = $value;
            }
        }

        $sh_mt_pr = Factory::getTable('shippingmethodprice');
        $sh_mt_pr->load($cart->getShippingPrId());

        $order->order_date = $order->order_m_date = getJsDate();
        $order->order_tax = $cart->getTax(1, 1, 1);
        $order->setTaxExt($cart->getTaxExt(1, 1, 1));
        $order->order_subtotal = $cart->getPriceProducts();
        $order->order_shipping = $cart->getShippingPrice();
        $order->order_payment = $cart->getPaymentPrice();
        $order->order_discount = $cart->getDiscountShow();
        $order->shipping_tax = $cart->getShippingPriceTaxPercent();
        $order->setShippingTaxExt($cart->getShippingTaxList());
        $order->payment_tax = $cart->getPaymentTaxPercent();
        $order->setPaymentTaxExt($cart->getPaymentTaxList());
        $order->order_package = $cart->getPackagePrice();
        $order->setPackageTaxExt($cart->getPackageTaxList());
        $order->order_total = $cart->getSum(1, 1, 1);
        $order->currency_exchange = $config->currency_value;
        $order->vendor_type = $cart->getVendorType();
        $order->vendor_id = $cart->getVendorId();
        $order->order_status = $config->default_status_order;
        $order->shipping_method_id = $cart->getShippingId();
        $order->payment_method_id = $cart->getPaymentId();
        $order->delivery_times_id = $sh_mt_pr->delivery_times_id;
        if ($config->delivery_order_depends_delivery_product){
            $order->delivery_time = $cart->getDelivery();
        }
        if ($config->show_delivery_date){
            $order->delivery_date = $cart->getDeliveryDate();
        }
        $order->coupon_id = $cart->getCouponId();

        $pm_params = $cart->getPaymentParams();

        if (is_array($pm_params) && !$paymentSystemVerySimple){
            $payment_system->setParams($pm_params);
            $payment_params_names = $payment_system->getDisplayNameParams();
            $order->payment_params = getTextNameArrayValue($payment_params_names, $pm_params);
            $order->setPaymentParamsData($pm_params);
        }
        
        $sh_params = $cart->getShippingParams();
        if (is_array($sh_params)){
            $sh_method = Factory::getTable('shippingmethod');
            $sh_method->load($cart->getShippingId());
            $shippingForm = $sh_method->getShippingForm();
            if ($shippingForm){
                $shipping_params_names = $shippingForm->getDisplayNameParams();            
                $order->shipping_params = getTextNameArrayValue($shipping_params_names, $sh_params);
            }
            $order->setShippingParamsData($sh_params);
        }
        
        $name = "name_".$config->cur_lang;
        $order->ip_address = $_SERVER['REMOTE_ADDR'];
        $order->order_add_info = Request::getVar('order_add_info','');
        $order->currency_code = $config->currency_code;
        $order->currency_code_iso = $config->currency_code_iso;
        $order->order_number = $order->formatOrderNumber($orderNumber);
        $order->order_hash = md5(time().$order->order_total.$order->user_id);
        $order->file_hash = md5(time().$order->order_total.$order->user_id."hashfile");
        $order->display_price = $config->display_price_front_current;
        $order->lang = $config->cur_lang;
        
        if ($order->client_type){
            $order->client_type_name = $config->user_field_client_type[$order->client_type];
        }else{
            $order->client_type_name = "";
        }
		
		if ($order->order_total==0){
            $pm_method->payment_type = 1;
            $config->without_payment = 1;
            $order->order_status = $config->payment_status_paid;
        }
        
        if ($pm_method->payment_type == 1){
            $order->order_created = 1; 
        }else {
            $order->order_created = 0;
        }
        
        if (!$adv_user->delivery_adress) {
			$order->copyDeliveryData();
		}
        
        do_action_ref_array('onBeforeCreateOrder', array(&$order));

        $order->store();

        do_action_ref_array('onAfterCreateOrder', array(&$order));

        if ($cart->getCouponId()){
            $coupon = Factory::getTable('coupon');
            $coupon->load($cart->getCouponId());
            if ($coupon->finished_after_used){
                $free_discount = $cart->getFreeDiscount();
                if ($free_discount > 0){
                    $coupon->coupon_value = $free_discount / $config->currency_value;
                }else{
                    $coupon->used = $adv_user->user_id;
                }
                $coupon->store();
            }
        }

        $order->saveOrderItem($cart->products);

		do_action_ref_array('onAfterCreateOrderFull', array(&$order));
		
        $session->set("wshop_end_order_id", $order->order_id);

        $order_history = Factory::getTable('orderhistory');
        $order_history->order_id = $order->order_id;
        $order_history->order_status_id = $order->order_status;
        $order_history->status_date_added = $order->order_date;
        $order_history->customer_notify = 1;
        $order_history->store();
        
        if ($pm_method->payment_type == 1){
            if ($config->order_stock_removed_only_paid_status){
                $product_stock_removed = (in_array($order->order_status, $config->payment_status_enable_download_sale_file));
            }else{
                $product_stock_removed = 1;
            }
            if ($product_stock_removed){
                $order->changeProductQTYinStock("-");
            }
            if ($config->send_order_email){
                $checkout->sendOrderEmail($order->order_id);
            }
        }
        
        do_action_ref_array('onEndCheckoutStep5', array(&$order) );

        $session->set("wshop_send_end_form", 0);
        
        if ($config->without_payment){
            $checkout->setMaxStep(10);
            $this->setRedirect(SEFLink('controller=checkout&task=finish',0,1,$config->use_ssl));
            return 0;
        }
        
        $pmconfigs = $pm_method->getConfigs();
        
        $task = "step6";
        if (isset($pmconfigs['windowtype']) && $pmconfigs['windowtype']==2){
            $task = "step6iframe";
            $session->set("jsps_iframe_width", $pmconfigs['iframe_width']);
            $session->set("jsps_iframe_height", $pmconfigs['iframe_height']);
        }
        $checkout->setMaxStep(6);
        $this->setRedirect(SEFLink('controller=checkout&task='.$task,0,1,$config->use_ssl));
    }

    function step6iframe(){
        $checkout = Factory::getModel('checkout');
        $checkout->checkStep(6);
        $config = Factory::getConfig();
        $session = Factory::getSession();
        $width = $session->get("jsps_iframe_width");
        $height = $session->get("jsps_iframe_height");
        if (!$width) $width = 600;
        if (!$height) $height = 600;
        do_action_ref_array('onBeforeStep6Iframe', array(&$width, &$height));
        ?><iframe width="<?php print $width?>" height="<?php print $height?>" frameborder="0" src="<?php print SEFLink('controller=checkout&task=step6&wmiframe=1',0,1,$config->use_ssl)?>"></iframe><?php
    }

    function step6(){
        $checkout = Factory::getModel('checkout');
        $checkout->checkStep(6);
        $config = Factory::getConfig();
        $session = Factory::getSession();
        header("Cache-Control: no-cache, must-revalidate");
        $order_id = $session->get('wshop_end_order_id');
        $wmiframe = Request::getInt("wmiframe");

        if (!$order_id){
            addMessage(_WOP_SHOP_SESSION_FINISH, 'error');
            if (!$wmiframe){
                $this->setRedirect(SEFLink('controller=checkout&task=step5',0,1,$config->use_ssl));
            }else{
                $this->iframeRedirect(SEFLink('controller=checkout&task=step5',0,1,$config->use_ssl));
            }
        }
        
        $cart = Factory::getModel('cart');
        $cart->load();
        
        $order = Factory::getTable('order');
        $order->load($order_id);

        // user click back in payment system 
        $wshop_send_end_form = $session->get('wshop_send_end_form');
        if ($wshop_send_end_form == 1){
            $this->_cancelPayOrder($order_id);
            return 0;
        }

        $pm_method = Factory::getTable('paymentmethod');
        $payment_method_id = $order->payment_method_id;
        $pm_method->load($payment_method_id);
        $payment_method = $pm_method->payment_class; 
        
        $paymentsysdata = $pm_method->getPaymentSystemData();
        $payment_system = $paymentsysdata->paymentSystem;
        if ($paymentsysdata->paymentSystemVerySimple){
            $paymentSystemVerySimple = 1;
        }
        if ($paymentsysdata->paymentSystemError){
            $cart->setPaymentParams("");
            addMessage(_WOP_SHOP_ERROR_PAYMENT, 'error');
            if (!$wmiframe){
                $this->setRedirect(SEFLink('controller=checkout&task=step3',0,1,$config->use_ssl));
            }else{
                $this->iframeRedirect(SEFLink('controller=checkout&task=step3',0,1,$config->use_ssl));
            }
            return 0;
        }
		
        if ($pm_method->payment_type == 1 || $paymentSystemVerySimple) { 
            $checkout->setMaxStep(10);
            if (!$wmiframe){
                $this->setRedirect(SEFLink('controller=checkout&task=finish',0,1,$config->use_ssl));
            }else{
                $this->iframeRedirect(SEFLink('controller=checkout&task=finish',0,1,$config->use_ssl));
            }
            return 0;
        }

        do_action_ref_array('onBeforeShowEndFormStep6', array(&$order, &$cart, $pm_method));
        $session->set('wshop_send_end_form', 1);
        $pmconfigs = $pm_method->getConfigs();
        $payment_system->showEndForm($pmconfigs, $order);

    }

    public function step7(){
        $checkout = Factory::getModel('checkout');
        $wmiframe = Request::getInt("wmiframe");
        $config = Factory::getConfig();
        $session = Factory::getSession();
        do_action_ref_array('onLoadStep7', array());
        $pm_method = Factory::getTable('paymentmethod');
        
        $str = "url: ".$_SERVER['REQUEST_URI']."\n";
        foreach($_POST as $k=>$v) $str .= $k."=".$v."\n";
        saveToLog("paymentdata.log", $str);
        
        $act = Request::getVar("act");
        $payment_method = Request::getVar("js_paymentclass");
        
        $pm_method->loadFromClass($payment_method);
        
        $paymentsysdata = $pm_method->getPaymentSystemData();
        $payment_system = $paymentsysdata->paymentSystem;
        if ($paymentsysdata->paymentSystemVerySimple){
            if (Request::getInt('no_lang')) {
                Factory::loadLanguageFile();
            }
            saveToLog("payment.log", "#001 - Error payment method file. PM ".$payment_method);
            addMessage(_WOP_SHOP_ERROR_PAYMENT, 'error');
            return 0;
        }
        
        if ($paymentsysdata->paymentSystemError){
            if (Request::getInt('no_lang')) {
                Factory::loadLanguageFile();
            }
            saveToLog("payment.log", "#002 - Error payment. CLASS ".$payment_method);
            addMessage(_WOP_SHOP_ERROR_PAYMENT, 'error');
            return 0;
        }
        
        $pmconfigs = $pm_method->getConfigs();
        $urlParamsPS = $payment_system->getUrlParams($pmconfigs);
        
        $order_id = $urlParamsPS['order_id'];
        $hash = $urlParamsPS['hash'];
        $checkHash = $urlParamsPS['checkHash'];
        $checkReturnParams = $urlParamsPS['checkReturnParams'];
        
        $session->set('wshop_send_end_form', 0);
        
        if ($act == "cancel"){
            $this->_cancelPayOrder($order_id);
            return 0;
        }

        if ($act == "return" && !$checkReturnParams){
            $checkout->setMaxStep(10);
            if (!$wmiframe){
                $this->setRedirect(SEFLink('controller=checkout&task=finish', 0, 1, $config->use_ssl));
            } else {
                $this->iframeRedirect(SEFLink('controller=checkout&task=finish', 0, 1, $config->use_ssl));
            }
            return 1;
        }
        
        $order = Factory::getTable('order');
        $order->load($order_id);
        
        if (Request::getInt('no_lang')){
            Factory::loadLanguageFile($order->getLang());
        }

        if ($checkHash && $order->order_hash != $hash){
            saveToLog("payment.log", "#003 - Error order hash. Order id ".$order_id);
            addMessage(_WOP_SHOP_ERROR_ORDER_HASH, 'error');
            return 0;
        }
        
        if (!$order->payment_method_id){
            saveToLog("payment.log", "#004 - Error payment method id. Order id ".$order_id);
            addMessage(_WOP_SHOP_ERROR_PAYMENT, 'error');
            return 0;
        }

        if ($order->payment_method_id != $pm_method->payment_id){
            saveToLog("payment.log", "#005 - Error payment method set url. Order id ".$order_id);
            addMessage(_WOP_SHOP_ERROR_PAYMENT, 'error');
            return 0;
        }

        $res = $payment_system->checkTransaction($pmconfigs, $order, $act);
        $rescode = $res[0];
        $restext = $res[1];
        $transaction = $res[2];
        $transactiondata = $res[3];
        
        $status = $payment_system->getStatusFromResCode($rescode, $pmconfigs);
        
        $order->transaction = $transaction;
        $order->store();
        $order->saveTransactionData($rescode, $status, $transactiondata);
        
        if ($restext != ''){
            saveToLog("payment.log", $restext);
        }        

        if ($status && !$order->order_created){
            $order->order_created = 1;
            $order->order_status = $status;
            do_action_ref_array('onStep7OrderCreated', array(&$order, &$res, &$checkout, &$pmconfigs));
            $order->store();
            if ($config->send_order_email){
                $checkout->sendOrderEmail($order->order_id);
            }
            if ($config->order_stock_removed_only_paid_status){
                $product_stock_removed = in_array($status, $config->payment_status_enable_download_sale_file);
            }else{
                $product_stock_removed = 1;
            }
            if ($product_stock_removed){
                $order->changeProductQTYinStock("-");
            }
            $checkout->changeStatusOrder($order_id, $status, 0);
        }

        if ($status && $order->order_status != $status){
           $checkout->changeStatusOrder($order_id, $status, 1);
        }
        
        do_action_ref_array('onStep7BeforeNotify', array(&$order, &$checkout, &$pmconfigs));
        
        if ($act == "notify"){
            $payment_system->nofityFinish($pmconfigs, $order, $rescode);
            die();
        }
        
        $payment_system->finish($pmconfigs, $order, $rescode, $act);

        if (in_array($rescode, array(0,3,4))){
            addMessage($restext, 'error');
            if (!$wmiframe){
                $this->setRedirect(SEFLink('controller=checkout&task=step5', 0, 1, $config->use_ssl));
            } else {
                $this->iframeRedirect(SEFLink('controller=checkout&task=step5', 0, 1, $config->use_ssl));
            }
            return 0;
        } else {
            $checkout->setMaxStep(10);
            if (!$wmiframe){
                $this->setRedirect(SEFLink('controller=checkout&task=finish', 0, 1, $config->use_ssl));
            } else {
                $this->iframeRedirect(SEFLink('controller=checkout&task=finish', 0, 1, $config->use_ssl));
            }
            return 1;
        }
    }

    public function finish(){
        $checkout = Factory::getModel('checkout');
        $checkout->checkStep(10);
        $session = Factory::getSession();
        $order_id = $session->get('wshop_end_order_id');

        $this->addMetaTag('title', _WOP_SHOP_CHECKOUT_FINISH);  
        $statictext = Factory::getTable("statictext");
        $rowstatictext = $statictext->loadData("order_finish_descr");
        $text = $rowstatictext->text;

        do_action_ref_array('onBeforeDisplayCheckoutFinish', array(&$text, &$order_id));

        if (trim(strip_tags($text))==""){
            $text = '';
        }
        $view_name = "checkout";
        $view = $this->getView($view_name);
        $view->setLayout("finish");  
        $view->assign('text', $text);
        $view->display();

        if ($order_id){
            $order = Factory::getTable('order');
            $order->load($order_id);
            $pm_method = Factory::getTable('paymentmethod');
            $payment_method_id = $order->payment_method_id;
            $pm_method->load($payment_method_id);
            $paymentsysdata = $pm_method->getPaymentSystemData();
            $payment_system = $paymentsysdata->paymentSystem;
            if ($payment_system){
                $pmconfigs = $pm_method->getConfigs();
                $payment_system->complete($pmconfigs, $order, $pm_method);
            }
            do_action_ref_array('onAfterDisplayCheckoutFinish', array(&$text, &$order, &$pm_method));
        }

        $cart = Factory::getModel('cart');
        $cart->load();
        $cart->getSum();
        $cart->clear();
        $checkout->deleteSession();
    }

    function _showSmallCart($step = 0){
        $config =  Factory::getConfig();
        
        $cart = Factory::getModel('cart');
        $cart->load();
        $cart->addLinkToProducts(0);
        $cart->setDisplayFreeAttributes();
        
        if ($step == 5){
            $cart->setDisplayItem(1, 1);
        }elseif ($step == 4 && !$config->step_4_3) {
            $cart->setDisplayItem(0, 1);
        }elseif ($step == 3 && $config->step_4_3){
            $cart->setDisplayItem(1, 0);
		}else{
            $cart->setDisplayItem(0, 0);
        }
        $cart->updateDiscountData();

        $weight_product = $cart->getWeightProducts();
        if ($weight_product==0 && $config->hide_weight_in_cart_weight0){
            $config->show_weight_order = 0;
        }
        do_action_ref_array( 'onBeforeDisplaySmallCart', array(&$cart) );
                
        $view_name = "cart";
        $view = $this->getView($view_name);
        $view->setLayout("checkout");
        $view->assign('step', $step);
        $view->assign('config', $config);
        $view->assign('products', $cart->products);
        $view->assign('summ', $cart->getPriceProducts());
        $view->assign('image_product_path', $config->image_product_live_path);
        $view->assign('no_image', $config->noimage);
        $view->assign('discount', $cart->getDiscountShow());
        $view->assign('free_discount', $cart->getFreeDiscount());
        $deliverytimes = Factory::getAllDeliveryTime();
        $view->assign('deliverytimes', $deliverytimes);
        
        $payment_method_id = $cart->getPaymentId();
        if ($payment_method_id){
            $pm_method = Factory::getTable('paymentmethod');            
            $pm_method->load($payment_method_id);
            $name = 'name_'.$config->cur_lang;
            $payment_name = $pm_method->$name;
        }else{
            $payment_name = '';
        }
        $view->assign('payment_name', $payment_name);
		
        if ($step == 5){
            if (!$config->without_shipping){
                $view->assign('summ_delivery', $cart->getShippingPrice());
                if ($cart->getPackagePrice()>0 || $config->display_null_package_price){
                    $view->assign('summ_package', $cart->getPackagePrice());
                }
				$view->assign('summ_payment', $cart->getPaymentPrice());
                $fullsumm = $cart->getSum(1,1,1);
                $tax_list = $cart->getTaxExt(1,1,1);
            }else{
				$view->assign('summ_payment', $cart->getPaymentPrice());
                $fullsumm = $cart->getSum(0,1,1);
                $tax_list = $cart->getTaxExt(0,1,1);
            }
        }elseif($step == 4 && !$config->step_4_3){
            $view->assign('summ_payment', $cart->getPaymentPrice());
            $fullsumm = $cart->getSum(0,1,1);
            $tax_list = $cart->getTaxExt(0,1,1);
        }elseif($step == 3 && $config->step_4_3){
			$view->assign('summ_delivery', $cart->getShippingPrice());
            if ($cart->getPackagePrice()>0 || $config->display_null_package_price){
                $view->assign('summ_package', $cart->getPackagePrice());
            }
			$fullsumm = $cart->getSum(1,1,0);
            $tax_list = $cart->getTaxExt(1,1,0);
		}
		else{
            $fullsumm = $cart->getSum(0, 1, 0);
            $tax_list = $cart->getTaxExt(0, 1, 0);
        }
        
        $show_percent_tax = 0;
        if (count($tax_list)>1 || $config->show_tax_in_product) $show_percent_tax = 1;
        if ($config->hide_tax) $show_percent_tax = 0;
        $hide_subtotal = 0;
        if ($step == 5){
            if (($config->hide_tax || count($tax_list)==0) && !$cart->rabatt_summ && $config->without_shipping && $cart->getPaymentPrice()==0) $hide_subtotal = 1;
        }elseif ($step == 4 && !$config->step_4_3) {
            if (($config->hide_tax || count($tax_list)==0) && !$cart->rabatt_summ && $cart->getPaymentPrice()==0) $hide_subtotal = 1;
        }elseif ($step == 3 && $config->step_4_3){
            if (($config->hide_tax || count($tax_list)==0) && !$cart->rabatt_summ && $config->without_shipping) $hide_subtotal = 1;
        }else{
            if (($config->hide_tax || count($tax_list)==0) && !$cart->rabatt_summ) $hide_subtotal = 1;
        }
        
        $text_total = _WOP_SHOP_PRICE_TOTAL;
        if ($step == 5){
            $text_total = _WOP_SHOP_ENDTOTAL;
            if (($config->show_tax_in_product || $config->show_tax_product_in_cart) && (count($tax_list)>0)){
                $text_total = _WOP_SHOP_ENDTOTAL_INKL_TAX;
            }
        }

        $view->assign('tax_list', $tax_list);
        $view->assign('fullsumm', $fullsumm);
        $view->assign('show_percent_tax', $show_percent_tax);
        $view->assign('hide_subtotal', $hide_subtotal);
        $view->assign('text_total', $text_total);
        $view->assign('weight', $weight_product);
        do_action_ref_array('onBeforeDisplayCheckoutCartView', array(&$view));
    
        return $view->loadTemplate();
    }

    function _cancelPayOrder($order_id=""){
        $config = Factory::getConfig();
        $checkout = Factory::getModel('checkout');
        $wmiframe = Request::getInt("wmiframe");
        $session = Factory::getSession();
        if (!$order_id) $order_id = $session->get('wshop_end_order_id');
        if (!$order_id){
            addMessage(_WOP_SHOP_SESSION_FINISH, 'error');
            if (!$wmiframe){
                $this->setRedirect(SEFLink('controller=checkout&task=step5',0,1,$config->use_ssl));
            }else{
                $this->iframeRedirect(SEFLink('controller=checkout&task=step5',0,1,$config->use_ssl));
            }
            return 0;
        }

        $checkout->cancelPayOrder($order_id);
        addMessage(_WOP_SHOP_PAYMENT_CANCELED, 'error');
        if (!$wmiframe){ 
            $this->setRedirect(SEFLink('controller=checkout&task=step5',0,1, $config->use_ssl));
        }else{
            $this->iframeRedirect(SEFLink('controller=checkout&task=step5',0,1, $config->use_ssl));
        }
        return 0;
    }
    
    function iframeRedirect($url){
        echo "<script>parent.location.href='$url';</script>\n";
        exit();
    }
}