<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class ProductFilesWshopTable extends WshopTable {
    
    var $id = null;
    var $product_id = null;
    var $demo = null;
    var $demo_descr = null;
    var $file = null;
    var $file_descr = null;
    var $ordering = null;
    
    function __construct(){
        global $wpdb;
        parent::__construct($wpdb->prefix.'wshop_products_files', 'id');         
    }
}