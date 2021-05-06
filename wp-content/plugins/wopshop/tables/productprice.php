<?php

class ProductPriceWshopTable extends WshopTable{

    var $price_id = null;
    var $product_id = null;
    var $discount = null;
    var $product_quantity_start = null;
    var $product_quantity_finish = null;

    function __construct(){
        global $wpdb;
        parent::__construct($wpdb->prefix.'wshop_products_prices', 'price_id');
    }
    function getAddPrices($product_id){
        global $wpdb;
        $query = "SELECT * FROM `".$wpdb->prefix."wshop_products_prices` WHERE product_id = '".esc_sql($product_id)."' ORDER BY product_quantity_start DESC";
        return $wpdb->get_results($query, OBJECT);
    }
}