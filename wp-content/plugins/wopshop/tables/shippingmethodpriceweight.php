<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class ShippingMethodPriceWeightWshopTable extends WshopTable {
    
	var $sh_pr_weight_id = null;
	var $sh_pr_method_id = null;
	var $shipping_price = null;
	var $shipping_package_price = null;
	var $shipping_weight_to = null;
	var $shipping_weight_fron = null;
    
    function __construct(){
		global $wpdb;
        parent::__construct( $wpdb->prefix.'wshop_shipping_method_price_weight', 'sh_pr_weight_id');
    }
}