<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class ShippingMethodPriceWshopTable extends WshopTable {

    function __construct(){
        global $wpdb;
        parent::__construct($wpdb->prefix.'wshop_shipping_method_price', 'sh_pr_method_id');         
    }
    
	function getPricesWeight($sh_pr_method_id, $id_country, &$cart){
        $config = Factory::getConfig();

        $query = "SELECT (sh_pr_weight.shipping_price + sh_pr_weight.shipping_package_price) AS shipping_price, sh_pr_weight.shipping_weight_from, sh_pr_weight.shipping_weight_to, sh_price.shipping_tax_id
                  FROM `$this->_tbl` AS sh_price
                  INNER JOIN `".$this->_db->prefix."wshop_shipping_method_price_weight` AS sh_pr_weight ON sh_pr_weight.sh_pr_method_id = sh_price.sh_pr_method_id
                  INNER JOIN `".$this->_db->prefix."wshop_shipping_method_price_countries` AS sh_pr_countr ON sh_pr_weight.sh_pr_method_id = sh_pr_countr.sh_pr_method_id
                  WHERE sh_price.sh_pr_method_id = '" . esc_sql($sh_pr_method_id) . "'AND sh_pr_countr.country_id = '" . esc_sql($id_country) . "' 
                  ORDER BY sh_pr_weight.shipping_weight_from";
        $list = $this->_db->get_results($query);
        foreach($list as $k=>$v){
            $list[$k]->shipping_price = $list[$k]->shipping_price * $jconfig->currency_value;            
            $list[$k]->shipping_price = getPriceCalcParamsTax($list[$k]->shipping_price, $list[$k]->shipping_tax_id, $cart->products);
        }
        return $list; 
    }

    function getPrices($orderdir = "asc") {
        $query = "SELECT * FROM `".$this->_db->prefix."wshop_shipping_method_price_weight` AS sh_price
                  WHERE sh_price.sh_pr_method_id = '" . $this->_db->escape($this->sh_pr_method_id) . "'
                  ORDER BY sh_price.shipping_weight_from ".$orderdir;
        return $this->_db->get_results($query);
    }

    function getCountries() {
        $config = Factory::getConfig();
        $query = "SELECT sh_country.country_id, countries.`name_".$config->cur_lang."` as name
                  FROM `".$this->_db->prefix."wshop_shipping_method_price_countries` AS sh_country
                  INNER JOIN `".$this->_db->prefix."wshop_countries` AS countries ON countries.country_id = sh_country.country_id
                  WHERE sh_country.sh_pr_method_id = '" . esc_sql($this->sh_pr_method_id) . "'";       
        return $this->_db->get_results($query);
    }

    function getTax(){        
        $taxes = Factory::getAllTaxes();        
        return $taxes[$this->shipping_tax_id];
    }
    
    function getTaxPackage(){
        $taxes = Factory::getAllTaxes();
        return $taxes[$this->package_tax_id];
    }
    
    function getGlobalConfigPriceNull($cart){
        $config = Factory::getConfig();
        return ($cart->getSum() >= ($config->summ_null_shipping * $config->currency_value) && $config->summ_null_shipping > 0);
    }

    function calculateSum(&$cart){
        $config = Factory::getConfig();
        if ($this->getGlobalConfigPriceNull($cart)){
            return 0;
        }

        $price = $this->shipping_stand_price;
        $package = $this->package_stand_price;
        $prices = array('shipping'=>$price,'package'=>$package);

        $extensions = Factory::getShippingExtList($this->shipping_method_id);
        foreach($extensions as $extension){
            if (isset($extension->exec->version) && $extension->exec->version==2){
                $prices = $extension->exec->getPrices($cart, $this->getParams(), $prices, $extension, $this);
            }else{
                $price = $extension->exec->getPrice($cart, $this->getParams(), $price, $extension, $this);
                $prices = array('shipping'=>$price,'package'=>$package);
            }
        }

        $prices['shipping'] = $prices['shipping'] * $config->currency_value;
        $prices['shipping'] = getPriceCalcParamsTax($prices['shipping'], $this->shipping_tax_id, $cart->products);
        $prices['package'] = $prices['package'] * $config->currency_value;
        $prices['package'] = getPriceCalcParamsTax($prices['package'], $this->package_tax_id, $cart->products);
    return $prices;
    }

    function calculateTax($sum){
        $config = Factory::getConfig();
        $pricetax = getPriceTaxValue($sum, $this->getTax(), $config->display_price_front_current);
        return $pricetax;
    }
    function calculateTaxPackage($sum){
        $config = Factory::getConfig();
        $pricetax = getPriceTaxValue($sum, $this->getTaxPackage(), $config->display_price_front_current);
        return $pricetax;
    }
    
    function getShipingPriceForTaxes($price, $cart){
        if ($this->shipping_tax_id==-1){
            $prodtaxes = getPriceTaxRatioForProducts($cart->products);
            $prices = array();
            foreach($prodtaxes as $k=>$v){
                $prices[$k] = $price*$v;
            }
        }else{
            $prices = array();
            $prices[$this->getTax()] = $price;
        }
    return $prices;
    }
    
    function getPackegePriceForTaxes($price, $cart){
        if ($this->package_tax_id==-1){
            $prodtaxes = getPriceTaxRatioForProducts($cart->products);
            $prices = array();
            foreach($prodtaxes as $k=>$v){
                $prices[$k] = $price*$v;
            }
        }else{
            $prices = array();
            $prices[$this->getTaxPackage()] = $price;
        }
    return $prices;
    }

    function calculateShippingTaxList($price, $cart){
        $config = Factory::getConfig();
        if ($this->shipping_tax_id==-1){
            $prodtaxes = getPriceTaxRatioForProducts($cart->products);
            $prices = array();
            foreach($prodtaxes as $k=>$v){
                $prices[] = array('tax'=>$k, 'price'=>$price*$v);
            }
            $taxes = array();
            if ($config->display_price_front_current==0){
                foreach($prices as $v){
                    $taxes[$v['tax']] = $v['price']*$v['tax']/(100+$v['tax']);
                }
            }else{
                foreach($prices as $v){
                    $taxes[$v['tax']] = $v['price']*$v['tax']/100;
                }
            }    
        }else{
            $taxes = array();
            $taxes[$this->getTax()] = $this->calculateTax($price);
        }
    return $taxes;
    }
    
    function calculatePackageTaxList($price, $cart){
        $config = Factory::getConfig();
        if ($this->package_tax_id==-1){
            $prodtaxes = getPriceTaxRatioForProducts($cart->products);
            $prices = array();
            foreach($prodtaxes as $k=>$v){
                $prices[] = array('tax'=>$k, 'price'=>$price*$v);
            }
            $taxes = array();
            if ($config->display_price_front_current==0){
                foreach($prices as $v){
                    $taxes[$v['tax']] = $v['price']*$v['tax']/(100+$v['tax']);
                }
            }else{
                foreach($prices as $v){
                    $taxes[$v['tax']] = $v['price']*$v['tax']/100;
                }
            }    
        }else{
            $taxes = array();
            $taxes[$this->getTaxPackage()] = $this->calculateTaxPackage($price);
        }
    return $taxes;
    }
    
    function isCorrectMethodForCountry($id_country) {
        $query = "SELECT `sh_method_country_id` FROM `".$this->_db->prefix."wshop_shipping_method_price_countries` WHERE `country_id` = '".  esc_sql($id_country)."' AND `sh_pr_method_id` = '".  esc_sql($this->sh_pr_method_id)."'";
        $sh_method_country_id = $this->_db->get_var($query);
//        return ($this->_db->getNumRows())?(1):(0);
        return (count($sh_method_country_id));
    }
    
    function setParams($params){
        $this->params = json_encode($params);
    }
    
    function getParams(){
        if ($this->params==""){
            return array();
        }else{
            return json_decode($this->params, 1);
        }
    }
}