<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
abstract class ShippingExtRoot{
    
    /**
    * Show form Shipping price
    * 
    * @param mixed $params - shipping price params
    * @param mixed $shipping_ext_row - exstension row
    * @param mixed $template - template view object
    */
    abstract function showShippingPriceForm($params, &$shipping_ext_row, &$template);
    
    /**
    * show form config
    * 
    * @param mixed $config - config extension
    * @param mixed $shipping_ext - object wshopShippingExt
    * @param mixed $template - template view object
    */    
    abstract function showConfigForm($config, &$shipping_ext, &$template);
    
    /**
    * calculate price for shipping and package
    * 
    * @param mixed $cart - cart object
    * @param mixed $params - shipping price params
    * @param mixed $prices - prices before
    * @param mixed $shipping_ext_row - exstension row
    * @param mixed $shipping_method_price - object wshopShippingMethodPrice
    */
    /*
    abstract function getPrice($cart, $params, $price, &$shipping_ext_row, &$shipping_method_price);//deprecated
    abstract function getPrices($cart, $params, $prices, &$shipping_ext_row, &$shipping_method_price);
    */
}