<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class OrderItemWshopTable extends WshopTable {

    var $order_item_id = null;
    var $order_id = null;
    var $product_id = null;
    var $product_ean = null;
    var $product_name = null;
    var $product_quantity = null;
    var $product_item_price = null;
    var $product_tax = null;
    var $product_attributes = null;
    var $files = null;
    var $weight = null;
    var $thumb_image = null;
    var $vendor_id = null;

    function __construct(){
        global $wpdb;
        parent::__construct($wpdb->prefix.'wshop_order_item', 'order_item_id');        
    }
}