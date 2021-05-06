<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class CartWshopModel extends WshopModel{
    
    public $type_cart = "cart"; //cart,wishlist
    public $products = array();
    public $count_product = 0;
    public $price_product = 0;
    public $summ = 0;
    public $rabatt_id = 0;
    public $rabatt_value = 0;
    public $rabatt_type = 0;
    public $rabatt_summ = 0;
    
    public function __construct() {
        parent::__construct();
    }

    public function load($type_cart = "cart"){
        $config = Factory::getConfig();
        $this->type_cart = $type_cart;

        do_action_ref_array('onBeforeCartLoad', array(&$this));

        $session = Factory::getSession();
        $objcart = $session->get($this->type_cart);

        if (isset($objcart) && $objcart!=''){
            $temp_cart = unserialize($objcart);
            $this->products = $temp_cart->products;
            $this->rabatt_id = $temp_cart->rabatt_id;
            $this->rabatt_value = $temp_cart->rabatt_value;
            $this->rabatt_type = $temp_cart->rabatt_type;
            $this->rabatt_summ = $temp_cart->rabatt_summ;
        }
        
        if (isset($_COOKIE['wopshop_temp_cart']) && $this->type_cart=='wishlist' && !count($this->products)){
            $_tempcart = Factory::getModel('tempcart');
            $products = $_tempcart->getTempCart($_COOKIE['wopshop_temp_cart'], $this->type_cart);
            if (count($products)){
                $this->products = $products;
                $this->saveToSession();
            }
        }
        
        $this->loadPriceAndCountProducts();
        if ($config->use_extend_tax_rule){
            $this->updateTaxForProducts();
            $this->saveToSession();
        }

        do_action_ref_array('onAfterCartLoad', array(&$this));
    }

    function loadPriceAndCountProducts(){
        $config = Factory::getConfig();
        $this->price_product = 0;
        $this->price_product_brutto = 0;
        $this->count_product = 0;
        //print_r($this->products);
        if (is_array($this->products) && count($this->products)){
            foreach($this->products as $prod){
                $this->price_product += $prod['price'] * $prod['quantity'];
                if ($config->display_price_front_current==1){
                    $this->price_product_brutto += ($prod['price']*(1+$prod['tax']/100)) * $prod['quantity'];
                }else{
                    $this->price_product_brutto += $prod['price'] * $prod['quantity'];
                }
                $this->count_product += $prod['quantity'];
            }
        }
        do_action_ref_array('onAfterLoadPriceAndCountProducts', array(&$this));
    }

    function getPriceProducts(){
        return $this->price_product;
    }

    function getPriceBruttoProducts(){
        return $this->price_product_brutto;
    }

    function getCountProduct(){
        return $this->count_product;
    }

    function updateTaxForProducts(){
        if (count($this->products) and is_array($this->products)){
            $taxes = Factory::getAllTaxes();
            foreach ($this->products as $k=>$prod) {
                $this->products[$k]['tax'] = $taxes[$prod['tax_id']];
            }
        }
    }

    /**
    * get cart summ price
    * @param mixed $incShiping - include price shipping
    * @param mixed $incRabatt - include discount
    * @param mixed $incPayment - include price payment
    */
    function getSum( $incShiping = 0, $incRabatt = 0, $incPayment = 0 ) {
        $config = Factory::getConfig();
        
        $this->summ = $this->price_product;
        
        if ($config->display_price_front_current==1){
            $this->summ = $this->summ + $this->getTax($incShiping, $incRabatt, $incPayment);
        }

        if ($incShiping){
            $this->summ = $this->summ + $this->getShippingPrice();
            $this->summ = $this->summ + $this->getPackagePrice();
        }
        
        if ($incPayment){
            $price_payment = $this->getPaymentPrice();
            $this->summ = $this->summ + $price_payment;
        }
        
        if ($incRabatt){
            $this->summ = $this->summ - $this->getDiscountShow();
            if ($this->summ < 0) $this->summ = 0;
        }
        do_action_ref_array('onAfterCartGetSum', array(&$this, &$incShiping, &$incRabatt, &$incPayment));
        return $this->summ;
    }

    function getDiscountShow(){
        $summForCalculeDiscount = $this->getSummForCalculeDiscount();
        if ($this->rabatt_summ > $summForCalculeDiscount){
            return $summForCalculeDiscount;
        }else{
            return $this->rabatt_summ;
        }
    }

    function getFreeDiscount(){
        $summForCalculeDiscount = $this->getSummForCalculeDiscount();
        if ($this->rabatt_summ > $summForCalculeDiscount){
            return $this->rabatt_summ - $summForCalculeDiscount;
        }else{
            return 0;
        }
    }    

    function getTax($incShiping = 0, $incRabatt = 0, $incPayment = 0){
        $taxes = $this->getTaxExt($incShiping, $incRabatt, $incPayment);
        $tax_summ = array_sum($taxes);
    return $tax_summ;
    }

    function getTaxExt($incShiping = 0, $incRabatt = 0, $incPayment = 0){
        $config = Factory::getConfig();
        $tax_summ = array();
        foreach($this->products as $key=>$value){
            if ($value['tax']!=0){
                if (!isset($tax_summ[$value['tax']])) $tax_summ[$value['tax']] = 0;
                $tax_summ[$value['tax']] += $value['quantity'] * getPriceTaxValue($value['price'], $value['tax'], $config->display_price_front_current);                
            }
        }

        if ($incShiping){
            $lst = $this->getShippingTaxList();
            foreach($lst as $tax=>$value){
                if ($tax!=0 && $value!=0){
                    $tax_summ[$tax] += $value;
                }
            }
            $lst = $this->getPackageTaxList();
            foreach($lst as $tax=>$value){
                if ($tax!=0 && $value!=0){
                    $tax_summ[$tax] += $value;
                }
            }
        }

        if ($incPayment){
            $lpt = $this->getPaymentTaxList();
            foreach($lpt as $tax=>$value){
                if ($tax!=0 && $value!=0){
                    $tax_summ[$tax] += $value;
                }
            }
        }
        
        if ($incRabatt && $config->calcule_tax_after_discount && $this->rabatt_summ>0){
            $tax_summ = $this->getTaxExtCalcAfterDiscount($incShiping, $incPayment);
        }

        do_action_ref_array('onAfterCartGetTaxExt', array(&$this, &$tax_summ, &$incShiping, &$incRabatt, $incPayment));
        return $tax_summ;
    }

    function getTaxExtCalcAfterDiscount($incShiping = 0, $incPayment = 0){
        $config = Factory::getConfig();
        $summ = array();
        foreach($this->products as $key=>$value){
            $summ[$value['tax']] += $value['quantity'] * $value['price'];
        }

        if ($config->discount_use_full_sum){
            if ($incShiping && $this->display_item_shipping){
                $lspt = $this->getShippingPriceForTaxes();
                foreach($lspt as $tax=>$value){
                    if ($tax!=0 && $value!=0){
                        $summ[$tax] += $value;
                    }
                }
                $lspt = $this->getPackagePriceForTaxes();
                foreach($lspt as $tax=>$value){
                    if ($tax!=0 && $value!=0){
                        $summ[$tax] += $value;
                    }
                }
            }
            
            if ($incPayment && $this->display_item_payment){
                $lppt = $this->getPaymentPriceForTaxes();
                foreach($lppt as $tax=>$value){
                    if ($tax!=0 && $value!=0){
                        $summ[$tax] += $value;
                    }
                }
            }
        }

        $allsum = array_sum($summ);
        $discountsum = $this->getDiscountShow();

        $calc_taxes = array();
        foreach($summ as $tax=>$val){
            $percent = $val / $allsum;
            $pwd = $val - ($discountsum * $percent);
            if ($pwd<0) $pwd = 0;
            if ($config->display_price_front_current==1){
                $calc_taxes[$tax] = $pwd*$tax/100;
            }else{
                $calc_taxes[$tax] = $pwd*$tax/(100+$tax);
            }
        }

        if (!$config->discount_use_full_sum){
            if ($incShiping && $this->display_item_shipping){
                $lst = $this->getShippingTaxList();
                foreach($lst as $tax=>$value){
                    if ($tax!=0 && $value!=0){
                        $calc_taxes[$tax] += $value;
                    }
                }
                $lst = $this->getPackageTaxList();
                foreach($lst as $tax=>$value){
                    if ($tax!=0 && $value!=0){
                        $calc_taxes[$tax] += $value;
                    }
                }
            }

            if ($incPayment && $this->display_item_payment){
                $lpt = $this->getPaymentTaxList();
                foreach($lpt as $tax=>$value){
                    if ($tax!=0 && $value!=0){
                        $calc_taxes[$tax] += $value;
                    }
                }
            }
        }

        return $calc_taxes;
    }

    function setDisplayFreeAttributes(){
        $config = Factory::getConfig();
        if (count($this->products)){
            if ($config->admin_show_freeattributes){
                $_freeattributes = Factory::getTable('freeattribut');
                $namesfreeattributes = $_freeattributes->getAllNames();
            }
            foreach ($this->products as $k=>$prod){
                if ($config->admin_show_freeattributes){
                    $freeattributes = json_decode($prod['freeattributes'], 1);
                    if (!is_array($freeattributes)) $freeattributes = array();
                    $free_attributes_value = array();
                    foreach($freeattributes as $id=>$text){
                        $obj = new stdClass();
                        $obj->attr = $namesfreeattributes[$id];
                        $obj->value = $text;
                        $free_attributes_value[] = $obj;
                    }
                    $this->products[$k]['free_attributes_value'] = $free_attributes_value;
                }else{
                    $this->products[$k]['free_attributes_value'] = array();
                }
            }
        }
    }

    function setDisplayItem($shipping = 0, $payment = 0){
        $this->display_item_shipping = $shipping;
        $this->display_item_payment = $payment;
    }
    
    function setShippingsDatas($prices, $shipping_method_price){
        $this->setShippingPrice($prices['shipping']);
        $this->setShippingTaxId($shipping_method_price->shipping_tax_id);
        $this->setShippingTaxList($shipping_method_price->calculateShippingTaxList($prices['shipping'], $this));
        $this->setShippingPriceForTaxes($shipping_method_price->getShipingPriceForTaxes($prices['shipping'], $this));
        $this->setPackagePrice($prices['package']);
        $this->setPackageTaxId($shipping_method_price->package_tax_id);
        $this->setPackageTaxList($shipping_method_price->calculatePackageTaxList($prices['package'], $this));
        $this->setPackagePriceForTaxes($shipping_method_price->getPackegePriceForTaxes($prices['package'], $this));
    }

    function setShippingId($val){
        $session = Factory::getSession();
        $session->set("shipping_method_id", $val);
    }

    function getShippingId() {
        $session = Factory::getSession();
        return $session->get("shipping_method_id");
    }
    
    function setShippingPrId($val){
        $session = Factory::getSession();
        $session->set("sh_pr_method_id", $val);
    }

    function getShippingPrId() {
        $session = Factory::getSession();
        return $session->get("sh_pr_method_id");
    }

    function setShippingPrice($price){
        $session = Factory::getSession();
        $session->set("wopshop_price_shipping", $price);
    }
    function getShippingPrice() {
        $session = Factory::getSession();
        $price = $session->get("wopshop_price_shipping");
        return floatval($price);
    }
    
    function setPackagePrice($price){
        $session = Factory::getSession();
        $session->set("wopshop_price_package", $price);
    }
    function getPackagePrice() {
        $session = Factory::getSession();
        $price = $session->get("wopshop_price_package");
        return floatval($price);
    }

    function setShippingPriceTax($price){
        $session = Factory::getSession();
        $session->set("wopshop_price_shipping_tax", $price);
    }

    function getShippingPriceTax() {
        $session = Factory::getSession();
        $price = $session->get("wopshop_price_shipping_tax");
        return floatval($price);
    }

    function getShippingPriceTaxPercent(){
        $stl = $this->getShippingTaxList();
        if (is_array($stl) && count($stl)==1){
            $tmp = array_keys($stl);
            return $tmp[0];
        }else{
            return 0;
        }
    }
    
    function setShippingTaxId($id){
        $session = Factory::getSession();
        $session->set("wopshop_price_shipping_tax_id", $id);
    }
    function getShippingTaxId(){
        $session = Factory::getSession();
        return $session->get("wopshop_price_shipping_tax_id");
    }
    
    function setPackageTaxId($id){
        $session = Factory::getSession();
        $session->set("wopshop_price_package_tax_id", $id);
    }
    function getPackageTaxId(){
        $session = Factory::getSession();
        return $session->get("wopshop_price_package_tax_id");
    }
    
    function setShippingTaxList($list){
        $session = Factory::getSession();
        $session->set("wopshop_price_shipping_tax_list", $list);
    }
    function getShippingTaxList(){
        $session = Factory::getSession();
        return (array)$session->get("wopshop_price_shipping_tax_list");
    }
    
    function setPackageTaxList($list){
        $session = Factory::getSession();
        $session->set("wopshop_price_package_tax_list", $list);
    }
    function getPackageTaxList(){
        $session = Factory::getSession();
        return (array)$session->get("wopshop_price_package_tax_list");
    }
    
    function setShippingPriceForTaxes($list){
        $session = Factory::getSession();
        $session->set("wopshop_price_shipping_for_tax_list", $list);
    }
    function getShippingPriceForTaxes(){
        $session = Factory::getSession();
        return $session->get("wopshop_price_shipping_for_tax_list");
    }
    
    function setPackagePriceForTaxes($list){
        $session = Factory::getSession();
        $session->set("wopshop_price_package_for_tax_list", $list);
    }
    function getPackagePriceForTaxes(){
        $session = Factory::getSession();
        return $session->get("wopshop_price_package_for_tax_list");
    }

    function getShippingNettoPrice(){
        $config = Factory::getConfig();
        if ($config->display_price_front_current==1){
            return $this->getShippingPrice();
        }else{
            $price = $this->getShippingPrice();
            $lst = $this->getShippingTaxList();
            foreach($lst as $tax=>$value){
                $price -= $value;
            }
            return $price;
        }
    }
    
    function getShippingBruttoPrice(){
        $config = Factory::getConfig();
        if ($config->display_price_front_current==1){
            $price = $this->getShippingPrice();
            $lst = $this->getShippingTaxList();
            foreach($lst as $tax=>$value){
                $price += $value;
            }
            return $price;
        }else{
            return $this->getShippingPrice();
        }
    }
    
    function getPackageBruttoPrice(){
        $config = Factory::getConfig();
        if ($config->display_price_front_current==1){
            $price = $this->getPackagePrice();
            $lst = $this->getPackageTaxList();
            foreach($lst as $tax=>$value){
                $price += $value;
            }
            return $price;
        }else{
            return $this->getPackagePrice();
        }
    }
  
    function setShippingParams($val){
        $session = Factory::getSession();
        $session->set("shipping_params", $val);
    }

    function getShippingParams(){
        $session = Factory::getSession();
        $val = $session->get("shipping_params");
        return $val;
    }

    function setPaymentId($val){
        $session = Factory::getSession();
        $session->set("payment_method_id", $val);
    }

    function getPaymentId(){
        $session = Factory::getSession();
        return intval($session->get("payment_method_id"));
    }

    function setPaymentPrice($val){
        $session = Factory::getSession();
        $session->set("wopshop_payment_price", $val);
    }

    function getPaymentPrice(){
        $session = Factory::getSession();
        $price = $session->get("wopshop_payment_price");
        return floatval($price);
    }
    
    function setPaymentDatas($price, $payment){
        $this->setPaymentPrice($price);
        $this->setPaymentTaxList($payment->calculateTaxList($price));
        $this->setPaymentPriceForTaxes($payment->getPriceForTaxes($price));
    }

    function getPaymentBruttoPrice(){
        $config = Factory::getConfig();
        if ($config->display_price_front_current==1){
            $price = $this->getPaymentPrice();
            $lpt = $this->getPaymentTaxList();
            foreach($lpt as $tax=>$value){
                $price += $value;
            }
            return $price;
        }else{
            return $this->getPaymentPrice();
        }
        
    }
    
    function setPaymentTaxList($list){
        $session = Factory::getSession();
        $session->set("wopshop_price_payment_tax_list", $list);
    }
    function getPaymentTaxList(){
        $session = Factory::getSession();
        return (array)$session->get("wopshop_price_payment_tax_list");
    }
    
    function setPaymentPriceForTaxes($list){
        $session = Factory::getSession();
        $session->set("wopshop_price_payment_for_tax_list", $list);
    }
    function getPaymentPriceForTaxes(){
        $session = Factory::getSession();
        return $session->get("wopshop_price_payment_for_tax_list");
    }
 
    function getPaymentTax(){
        $session = Factory::getSession();
        $price = $session->get("wopshop_payment_tax");
        return $price;
    }
    function getPaymentTaxPercent(){
        $ptl = $this->getPaymentTaxList();
        if (is_array($ptl) && count($ptl)==1){
            $tmp = array_keys($ptl);
            return $tmp[0];
        }else{
            return 0;
        }
    }

    function setPaymentParams($val){
        $session = Factory::getSession();
        $session->set("pm_params", $val);
    }

    function getPaymentParams(){
        $session = Factory::getSession();
        $val = $session->get("pm_params");
        return $val;
    }    

    function getCouponId(){
        return $this->rabatt_id;
    }
    
    function setDeliveryDate($date){
        $session = Factory::getSession();
        $session->set("wopshop_delivery_date", $date);
    }
    function getDeliveryDate(){
        $session = Factory::getSession();
    return $session->get("wopshop_delivery_date");
    }

    function updateCartProductPrice() {
	$config = Factory::getConfig();
        if(is_array($this->products) && count($this->products)){
            foreach($this->products as $key=>$value) {
                $product = Factory::getTable('product');
                $product->load($this->products[$key]['product_id']);
                $attr_id = json_decode($value['attributes'], 1);
                $freeattributes = json_decode($value['freeattributes'], 1);
                $product->setAttributeActive($attr_id);
                $product->setFreeAttributeActive($freeattributes);            
                $this->products[$key]['price'] = $product->getPrice($this->products[$key]['quantity'], 1, 1, 1, $this->products[$key]);
                            if ($config->cart_basic_price_show){
                    $this->products[$key]['basicprice'] = $product->getBasicPrice();
                }
            }            
        }
        do_action_ref_array('onAfterUpdateCartProductPrice', array(&$this));
        $this->loadPriceAndCountProducts();
        $this->reloadRabatValue();
        $this->saveToSession();
    }

    function add($product_id, $quantity, $attr_id = array(), $freeattributes = array(), $additional_fields = array(), $usetriggers = 1, &$errors = array(), $displayErrorMessage = 1){
		global $cart_error;
		$cart_error = new WP_Error;		
        $config = Factory::getConfig();
        if ($quantity <= 0){
            $errors['100'] = 'Error quantity';
			if ($displayErrorMessage){
                            addMessage($errors['100'], 'error');
							$cart_error->add(100,$errors['100']);
                            trigger_error ($errors['100']); 
            }
            return 0;
        }
        $updateqty = 1;

		do_action_ref_array('onBeforeAddProductToCart', array(&$this, &$product_id, &$quantity, &$attr_id, &$freeattributes, &$updateqty, &$errors, &$displayErrorMessage, &$additional_fields, &$usetriggers));

        $attr_serialize = json_encode($attr_id);
        $free_attr_serialize = json_encode($freeattributes);

        $product = Factory::getTable('product');
        $product->load($product_id);

        //check attributes
        if ( (count($product->getRequireAttribute()) > count($attr_id)) || in_array(0, $attr_id)){
            $errors['101'] = _WOP_SHOP_SELECT_PRODUCT_OPTIONS;
            if ($displayErrorMessage){
                addMessage($errors['101'], 'error');
                trigger_error ($errors['101']);
				$cart_error->add(101,$errors['101']);
            }
            return 0;
        }

        //check free attributes
        if ($config->admin_show_freeattributes){
            $allfreeattributes = $product->getListFreeAttributes();
            $error = 0;
            foreach($allfreeattributes as $k=>$v){
                if ($v->required && trim($freeattributes[$v->id])==""){
                    $error = 1;
                    $errors['102_'.$v->id] = sprintf(_WOP_SHOP_PLEASE_ENTER_X, $v->name);
                    if ($displayErrorMessage){
                        addMessage($errors['102_'.$v->id], 'error');
						$cart_error->add('102_'.$v->id,$errors['102_'.$v->id]);
                        trigger_error ($errors['101']);                        
                    }
                }
            }
            if ($error){
                return 0;
            }
        }

        $product->setAttributeActive($attr_id);
        $product->setFreeAttributeActive($freeattributes);
        $qtyInStock = $product->getQtyInStock();
        $pidCheckQtyValue = $product->getPIDCheckQtyValue();

        $new_product = 1;
        if ($updateqty){
        foreach ($this->products as $key => $value){
            //if ($value['product_id'] == $product_id){
            if ($value['product_id'] == $product_id && $value['attributes'] == $attr_serialize && $value['freeattributes']==$free_attr_serialize){
                $product_in_cart = $this->products[$key]['quantity'];
                $save_quantity = $product_in_cart + $quantity;

                $sum_quantity = $save_quantity;
                foreach ($this->products as $key2 => $value2){
                    if ($key==$key2) continue;
                    if ($value2['pid_check_qty_value'] == $pidCheckQtyValue){
                        $sum_quantity += $value2["quantity"];
                        $product_in_cart += $value2["quantity"];
                    }
                }

                if ($config->max_count_order_one_product && $sum_quantity > $config->max_count_order_one_product){
                    $errors['103'] = sprintf(_WOP_SHOP_ERROR_MAX_COUNT_ORDER_ONE_PRODUCT, $config->max_count_order_one_product);
                    if ($displayErrorMessage){
                        addMessage($errors['103'], 'error');
                        trigger_error ($errors['103']);
						$cart_error->add(103,$errors['103']);
                    }
                    return 0;
                }
                if ($config->min_count_order_one_product && $sum_quantity < $config->min_count_order_one_product){
                    $errors['104'] = sprintf(_WOP_SHOP_ERROR_MIN_COUNT_ORDER_ONE_PRODUCT, $config->min_count_order_one_product);
                    if ($displayErrorMessage){
                        addMessage($errors['104'], 'error');
                        trigger_error ($errors['104']);
						$cart_error->add(104,$errors['104']);
                    }
                    return 0;
                }

                if (!$product->unlimited && $config->controler_buy_qty && ($sum_quantity > $qtyInStock)){
                    $balans = $qtyInStock - $product_in_cart;
                    if ($balans < 0) $balans = 0;
                    $errors['105'] = sprintf(_WOP_SHOP_ERROR_EXIST_QTY_PRODUCT_IN_CART, $this->products[$key]['quantity'], $balans);
                    if ($displayErrorMessage){
                        addMessage($errors['105'], 'error');
                        trigger_error ($errors['105']);
						$cart_error->add(105,$errors['105']);
                    }
                    return 0;
                }

                $this->products[$key]['quantity'] = $save_quantity;                
                $this->products[$key]['price'] = $product->getPrice($this->products[$key]['quantity'], 1, 1, 1, $this->products[$key]);
		if ($config->cart_basic_price_show){
                    $this->products[$key]['basicprice'] = $product->getBasicPrice();
                }
				
                if ($usetriggers){
                    do_action_ref_array('onBeforeSaveUpdateProductToCart', array(&$this, &$product, $key, &$errors, &$displayErrorMessage, &$product_in_cart, &$quantity));
                }

                $new_product = 0;
                break;
            }
        }
        }

        if ($new_product){
            $product_in_cart = 0;
            foreach ($this->products as $key2 => $value2){
                if ($value2['pid_check_qty_value'] == $pidCheckQtyValue){
                    $product_in_cart += $value2["quantity"];
                }
            }
            $sum_quantity = $product_in_cart + $quantity;

            if ($config->max_count_order_one_product && $sum_quantity > $config->max_count_order_one_product){
                $errors['106'] = sprintf(_WOP_SHOP_ERROR_MAX_COUNT_ORDER_ONE_PRODUCT, $config->max_count_order_one_product);
                if ($displayErrorMessage){
                    addMessage($errors['106'], 'error');
                    trigger_error ($errors['106']);
					$cart_error->add(106,$errors['106']);
                }
                return 0;
            }
            if ($config->min_count_order_one_product && $sum_quantity < $config->min_count_order_one_product){
                $errors['107'] = sprintf(_WOP_SHOP_ERROR_MIN_COUNT_ORDER_ONE_PRODUCT, $config->min_count_order_one_product);
                if ($displayErrorMessage){
                    trigger_error ($errors['107']);
                    addMessage($errors['107'], 'error');
					$cart_error->add(107,$errors['107']);
                }
                return 0;
            }

            if (!$product->unlimited && $config->controler_buy_qty && ($sum_quantity > $qtyInStock)){
                $balans = $qtyInStock - $product_in_cart;
                if ($balans < 0) $balans = 0;
                $errors['108'] = sprintf(_WOP_SHOP_ERROR_EXIST_QTY_PRODUCT, $balans);
                if ($displayErrorMessage){
                    trigger_error ($errors['108']);
                    addMessage($errors['108'], 'error');
					$cart_error->add(108,$errors['108']);
                }
                return 0;
            }

            $product->getDescription();
            $temp_product['quantity'] = $quantity;
            $temp_product['product_id'] = $product_id;
            $temp_product['category_id'] = $product->getCategory();
            $temp_product['tax'] = $product->getTax();
            $temp_product['tax_id'] = $product->product_tax_id;
            $temp_product['product_name'] = $product->name;
            $temp_product['thumb_image'] = getPatchProductImage ($product->getData('image'), 'thumb');
            $temp_product['delivery_times_id'] = $product->getDeliveryTimeId();
            $temp_product['ean'] = $product->getEan();
            $temp_product['attributes'] = $attr_serialize;
            $temp_product['attributes_value'] = array();
            $temp_product['extra_fields'] = array();
            $temp_product['weight'] = $product->getWeight();
            $temp_product['vendor_id'] = fixRealVendorId($product->vendor_id);
            $temp_product['files'] = json_encode($product->getSaleFiles());
            $temp_product['freeattributes'] = $free_attr_serialize;
            if ($config->show_manufacturer_in_cart){
                $manufacturer_info = $product->getManufacturerInfo();
                $temp_product['manufacturer'] = $manufacturer_info->name;
            }else{
                $temp_product['manufacturer'] = '';
            }
            $temp_product['pid_check_qty_value'] = $pidCheckQtyValue;
            $i = 0;
            if (is_array($attr_id) && count($attr_id)){
                foreach($attr_id as $key=>$value){
                    $attr = Factory::getTable('attribut');
                    $attr_v = Factory::getTable('attributvalue');
                    $temp_product['attributes_value'][$i] = new stdClass();
                    $temp_product['attributes_value'][$i]->attr_id = $key;
                    $temp_product['attributes_value'][$i]->value_id = $value;
                    $temp_product['attributes_value'][$i]->attr = $attr->getName($key);
                    $temp_product['attributes_value'][$i]->value = $attr_v->getName($value);
                    $i++;
                }
            }
            
            if ($config->admin_show_product_extra_field && count($config->getCartDisplayExtraFields())>0){
                $extra_field = $product->getExtraFields(2);                
                $temp_product['extra_fields'] = $extra_field;
            }

            foreach($additional_fields as $k=>$v){
                if ($k!='after_price_calc'){
                    $temp_product[$k] = $v;
                }
            }
            
            if ($usetriggers){
                do_action_ref_array('onBeforeSaveNewProductToCartBPC', array(&$this, &$temp_product, &$product, &$errors, &$displayErrorMessage));
            }

            $temp_product['price'] = $product->getPrice($quantity, 1, 1, 1, $temp_product);
            if ($config->cart_basic_price_show){
                $temp_product['basicprice'] = $product->getBasicPrice();
                $temp_product['basicpriceunit'] = $product->getBasicPriceUnit();
            }
			
            if (is_array($additional_fields['after_price_calc'])){
                foreach($additional_fields['after_price_calc'] as $k=>$v){
                    $temp_product[$k] = $v;
                }
            }
			
            if ($usetriggers){
                do_action_ref_array('onBeforeSaveNewProductToCart', array(&$this, &$temp_product, &$product, &$errors, &$displayErrorMessage));
            }
            $this->products[] = $temp_product;
        }

        $this->loadPriceAndCountProducts();
        $this->reloadRabatValue();
        $this->saveToSession();
        if ($usetriggers){
            do_action_ref_array('onAfterAddProductToCart', array(&$this, &$product_id, &$quantity, &$attr_id, &$freeattributes, &$errors, &$displayErrorMessage) );
        }
        return 1;
    }

    function refresh($quantity){
        $config = Factory::getConfig();

        do_action_ref_array('onBeforeRefreshProductInCart', array(&$quantity, &$this));
                
        if (is_array($quantity) && count($quantity)){
            
            $name = ('name_').$config->cur_lang;
            foreach($quantity as $key=>$value){
                if ($config->use_decimal_qty){
                    $value = floatval(str_replace(",",".",$value));
                    $value = round($value, $config->cart_decimal_qty_precision);
                }else{
                    $value = intval($value);
                }
                if ($value < 0) $value = 0;
                $product = Factory::getTable('product');
                $product->load($this->products[$key]['product_id']);
                $attr = json_decode($this->products[$key]['attributes'], 1);
                $free_attr = json_decode($this->products[$key]['freeattributes'], 1);
                $product->setAttributeActive($attr);
                $product->setFreeAttributeActive($free_attr);
                $qtyInStock = $product->getQtyInStock();
                $checkqty = $value;
				do_action_ref_array('onRefreshProductInCartForeach', array(&$this, &$quantity, &$key, &$product, &$attr, &$free_attr, &$qtyInStock, &$checkqty, &$value));

                foreach($this->products as $key2 => $value2){
                    if ($key2!=$key && $value2['pid_check_qty_value']==$this->products[$key]['pid_check_qty_value']){
                        $checkqty += $value2["quantity"];
                    }
                }
                
                if ($config->max_count_order_one_product && ($checkqty > $config->max_count_order_one_product)){
                    addMessage(sprintf(_WOP_SHOP_ERROR_MAX_COUNT_ORDER_ONE_PRODUCT, $config->max_count_order_one_product), 'error');
                    return 0;
                }
                if ($config->min_count_order_one_product && ($checkqty < $config->min_count_order_one_product)){
                    addMessage(sprintf(_WOP_SHOP_ERROR_MIN_COUNT_ORDER_ONE_PRODUCT, $config->min_count_order_one_product), 'error');
                    return 0;
                }
                if (!$product->unlimited && $config->controler_buy_qty && ($checkqty > $qtyInStock)){
                    addMessage(sprintf(_WOP_SHOP_ERROR_EXIST_QTY_PRODUCT_BASKET, $product->$name, $qtyInStock), 'error');
                    continue;
                }
   
                $this->products[$key]['price'] = $product->getPrice($value, 1, 1, 1, $this->products[$key]);
				if ($config->cart_basic_price_show){
                    $this->products[$key]['basicprice'] = $product->getBasicPrice();
                }
                $this->products[$key]['quantity'] = $value;
                if ($this->products[$key]['quantity'] == 0){
                    unset($this->products[$key]);
                }
                unset($product);
            }
        }
        $this->loadPriceAndCountProducts();
        $this->reloadRabatValue();
        $this->saveToSession();
        do_action_ref_array('onAfterRefreshProductInCart', array(&$quantity, &$this));
        return 1;
    }
    
    function checkListProductsQtyInStore(){
        $config = Factory::getConfig();
		do_action_ref_array('onBeforeCheckListProductsQtyInStore', array(&$this));
        $name = 'name_'.$config->cur_lang;
        $check = 1;
        
        foreach($this->products as $key=>$value){
			if ($value['pid_check_qty_value']=='nocheck') continue;
            $product = Factory::getTable('product');
            $product->load($this->products[$key]['product_id']);
            $attr = json_decode($this->products[$key]['attributes'], 1);
            $product->setAttributeActive($attr);
            $qtyInStock = $product->getQtyInStock();
            $checkqty = $value["quantity"];
			do_action_ref_array('onCheckListProductsQtyInStoreForeach', array(&$this, &$key, &$product, &$attr, &$qtyInStock, &$checkqty));

            foreach($this->products as $key2=>$value2){
                if ($key2!=$key && $value2['pid_check_qty_value']==$this->products[$key]['pid_check_qty_value']){
                    $checkqty += $value2["quantity"];
                }
            }
            
            if (!$product->unlimited && $config->controler_buy_qty && ($checkqty > $qtyInStock)){
                $check = 0;
                addMessage(sprintf(_WOP_SHOP_ERROR_EXIST_QTY_PRODUCT_BASKET, $product->$name, $qtyInStock), 'error');
                continue;
            }
        }
        do_action_ref_array('onAfterCheckListProductsQtyInStore', array(&$this));
    return $check;
    }
    
    function checkCoupon(){
        if (!$this->getCouponId()){
            return 1;
        }
        $coupon = Factory::getTable('coupon');
        $coupon->load($this->getCouponId());
        do_action_ref_array('onBeforeCheckCouponStep5save', array(&$this, &$coupon));
		
        if (!$coupon->coupon_publish || $coupon->used || ($coupon->type == 1 && $coupon->coupon_value < $this->rabatt_value)){
            return 0;
        }else{
            return 1;
        }
    }

    function getWeightProducts(){
        $weight_sum = 0;
        foreach ($this->products as $prod) {
            $weight_sum += $prod['weight'] * $prod['quantity'];
        }
        do_action_ref_array('onGetWeightCartProducts', array(&$this, &$weight_sum));
        return $weight_sum;
    }

    function setRabatt($id, $type, $value) {
        $this->rabatt_id = $id;
        $this->rabatt_type = $type;
        $this->rabatt_value = $value;
        $this->reloadRabatValue();
        $this->saveToSession();
    }
    
    function getSummForCalculePlusPayment(){
        $config = Factory::getConfig();
        $sum = $this->getPriceBruttoProducts();
        if ($this->display_item_shipping){
            $sum += $this->getShippingBruttoPrice();
            $sum += $this->getPackageBruttoPrice();
        }
        return $sum;
    }
    
    function getSummForCalculeDiscount(){
        $config = Factory::getConfig();
        $sum = $this->getPriceProducts();
        if ($config->discount_use_full_sum && $config->display_price_front_current==1){
            $sum = $this->getPriceBruttoProducts();
        }
        if ($config->discount_use_full_sum){
            if ($this->display_item_shipping) {
                $sum += $this->getShippingBruttoPrice();
                $sum += $this->getPackageBruttoPrice();
            }
            if ($this->display_item_payment) $sum += $this->getPaymentBruttoPrice();
        }
        return $sum;
    }
    
    function reloadRabatValue(){
        $config = Factory::getConfig();
        if ($this->rabatt_type == 1){
            $this->rabatt_summ = $this->rabatt_value * $config->currency_value; //value
        } else {
            $this->rabatt_summ = $this->rabatt_value / 100 * $this->getSummForCalculeDiscount(); //percent
        }
        $this->rabatt_summ = round($this->rabatt_summ, 2);
    }
       
    

    function updateDiscountData(){
        $this->reloadRabatValue();
        $this->saveToSession();
    }

    function addLinkToProducts($show_delete = 0, $type="cart") {
        foreach($this->products as $key=>$value){
            $this->products[$key]['href'] = SEFLink('controller=product&task=view&product_id='.$value['product_id'], 1);
            if ($show_delete){
                $this->products[$key]['href_delete'] = SEFLink('controller='.$type.'&task=delete&number_id='.$key);
            }
            if ($type=="wishlist"){
                $this->products[$key]['remove_to_cart'] = SEFLink('controller='.$type.'&task=remove_to_cart&number_id='.$key);
            }
        }
        do_action_ref_array('onAfterAddLinkToProductsCart', array(&$this, &$show_delete, &$type));
    }
    
//    /**
//    * get vendor type
//    * return (1 - multi vendors, 0 - single vendor)
//    */
    function getVendorType(){
        $vendors = array();
        foreach ($this->products as $key => $value){
            $vendors[] = $value['vendor_id'];
        }
        $vendors = array_unique($vendors);
        if (count($vendors)>1){
            return 1;
        }else{
            return 0;
        }
    }
//    
//    /**
//    * get id vendor
//    * reutnr (-1) - if type == multivendors
//    */
    function getVendorId(){
        $vendors = array();
        foreach ($this->products as $key => $value){
            $vendors[] = $value['vendor_id'];
        }
        $vendors = array_unique($vendors);
        if (count($vendors)==0){
            return 0;
        }elseif (count($vendors)>1){
            return -1;
        }else{
            return $vendors[0];
        }
    }
    
    function getDelivery(){
        $deliverytimes = Factory::getAllDeliveryTime();
        $deliverytimesdays = Factory::getAllDeliveryTimeDays();
        $min_id = 0;
        $max_id = 0;
        $min_days = 0;
        $max_days = 0;
        foreach($this->products as $prod){
            if ($prod['delivery_times_id']){
                if ($min_days==0){
                    $min_days = $deliverytimesdays[$prod['delivery_times_id']];
                    $min_id = $prod['delivery_times_id'];
                }
                if ($deliverytimesdays[$prod['delivery_times_id']]<$min_days){
                    $min_days = $deliverytimesdays[$prod['delivery_times_id']];
                    $min_id = $prod['delivery_times_id'];
                }
                if ($deliverytimesdays[$prod['delivery_times_id']]>$max_days){
                    $max_days = $deliverytimesdays[$prod['delivery_times_id']];
                    $max_id = $prod['delivery_times_id'];
                }
            }
        }
        if ($min_id==$max_id){
            $delivery = $deliverytimes[$min_id];
        }else{
            $delivery = $deliverytimes[$min_id]." - ".$deliverytimes[$max_id];
        }
    return $delivery;
    }
    
    function getDeliveryDaysProducts(){
        $deliverytimes = Factory::getAllDeliveryTime();
        $deliverytimesdays = Factory::getAllDeliveryTimeDays();
        $day = 0;
        foreach($this->products as $prod){
            if ($prod['delivery_times_id']){
                if ($deliverytimesdays[$prod['delivery_times_id']]>$day){
                    $day = $deliverytimesdays[$prod['delivery_times_id']];
                }
            }
        }
    return $day;
    }
    
    function getReturnPolicy(){
        $products = array();
        foreach($this->products as $v){
            $products[] = $v['product_id'];
        }
        $products = array_unique($products);
        $statictext = Factory::getTable("statictext");
        $rows = $statictext->getReturnPolicyForProducts($products);
        do_action_ref_array('onAfterCartGetReturnPolicy', array(&$this, &$rows));
    return $rows;
    }
    
    function clear(){
        do_action_ref_array('onBeforeClearCart', array(&$this));
        $session = Factory::getSession();
        $this->products = array();
        $this->rabatt = 0;
        $this->rabatt_value = 0;
        $this->rabatt_type = 0;
        $this->rabatt_summ = 0;
        $this->summ = 0;
        $this->count_product = 0;        
        $this->price_product = 0;        
        $session->set($this->type_cart, "");
        $session->set("pm_method", "");
        $session->set("pm_params", "");
        $session->set("payment_method_id", "");
        $session->set("shipping_method_id", "");
        $session->set("wopshop_price_shipping", "");
        $session->set('checkcoupon', 0);
    }

    function delete($number_id){
        do_action_ref_array('onBeforeDeleteProductInCart', array(&$number_id, &$this) );

        unset($this->products[$number_id]);
        $this->loadPriceAndCountProducts();
        $this->reloadRabatValue();
        $this->saveToSession();

        do_action_ref_array('onAfterDeleteProductInCart', array(&$number_id, &$this) );
    }

    function saveToSession(){
        $session = Factory::getSession();
        $session->set($this->type_cart, serialize($this));        
        $_tempcart = Factory::getModel('tempcart');
        $_tempcart->insertTempCart($this);
        do_action_ref_array('onAfterSaveToSessionCart', array(&$this));        
    }
    
    public function getUrlList(){
		if ($this->type_cart == "wishlist"){
			$url = 'controller=wishlist&task=view';
		} else {
			$url = 'controller=cart&task=view';
		}
		extract(ws_add_trigger(get_defined_vars(), "before"));
		return $url;
	}
}