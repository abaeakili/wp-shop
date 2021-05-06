<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class AliasWshopAdminModel extends WshopAdminModel {
    public $string;
 
    public function __construct() {
        parent::__construct();
    }

    function checkExistAlias1Group($alias, $lang, $category_id, $manufacture_id){
        global $wpdb;
        $query = "select category_id as id from ".$wpdb->prefix."wshop_categories where `alias_".$lang."` = '".esc_sql($alias)."' and category_id!='".esc_sql($category_id)."' 
                  union
                  select manufacturer_id as id from ".$wpdb->prefix."wshop_manufacturers where `alias_".$lang."` = '".esc_sql($alias)."' and manufacturer_id!='".esc_sql($manufacture_id)."'
                  ";
        $res = $wpdb->get_var($query);
//        $reservedFirstAlias = JSFactory::getReservedFirstAlias();
//        if ($res || in_array($alias, $reservedFirstAlias)){
        if ($res > 0){
            return 0;//error
        }else{
            return 1;//ok
        }
    }
    
    function checkExistAlias2Group($alias, $lang, $product_id){
        global $wpdb;
        $query = "select product_id from ".$wpdb->prefix."wshop_products where `alias_".$lang."` = '".esc_sql($alias)."' and product_id!='".esc_sql($product_id)."'";
        $res = (int)$wpdb->get_var($query);
        if ($res){
            return 0;//error
        }else{
            return 1;//ok
        }
    }
}