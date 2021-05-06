<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class ProductOptionWshopTable extends WshopTable{

    function __construct(){
        global $wpdb;
        parent::__construct($wpdb->prefix.'wshop_products_option', 'id');        
    }
    
    function getProductOption($product_id, $key){
        $query = "SELECT `value` FROM `$this->_tbl` WHERE product_id = '".  esc_sql($product_id)."' AND `key`='".  esc_sql($key)."' ";
        $db->setQuery($query);
        return $this->_db->get_row($query);
    }
    
    function getProductOptions($product_id){
        $query = "SELECT `key`, `value` FROM `$this->_tbl` WHERE product_id='".  esc_sql($product_id)."'";
        $list = $this->_db->get_results($query);
        $rows = array();
        foreach($list as $k=>$v){
            $rows[$v->key] = $v->value;
        }
    return $rows;
    }
    
    function getProductOptionList($array_product_id, $key, $setforallproducts = 1){
        if (!count($array_product_id)){
            return array();
        }
        $ids = implode(',', $array_product_id);
        $query = "SELECT `product_id`, `value` FROM `$this->_tbl` WHERE product_id IN (".  esc_sql($ids).") AND `key`='".  esc_sql($key)."' ";
        $list = $this->_db->get_results($query);
        $rows = array();
        foreach($array_product_id as $pid){
            if (isset($list[$pid])){
                $rows[$pid] = $list[$pid]->value;
            }else{
                if ($setforallproducts){
                    $rows[$pid] = '';
                }
            }
        }
        return $rows;
    }
}