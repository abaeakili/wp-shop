<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class ProductAttributWshopTable extends WshopTable {
    
    function __construct(){
        global $wpdb;
        parent::__construct($wpdb->prefix.'wshop_products_attr', 'product_attr_id');         
    }
    
    function check(){
        return 1;
    }
    
    function deleteAttributeForProduct(){
        $query = "DELETE FROM `$this->_tbl` WHERE `product_id` = '".  esc_sql($this->product_id)."'";
        $this->_db->query($query);    
    }
    
    function deleteAttribute($id){
        
        $this->load($id);
        if ($this->ext_attribute_product_id){
            $query = "DELETE FROM `".$this->_db->prefix."wshop_products` WHERE `product_id` = '".esc_sql($this->ext_attribute_product_id)."'";
            $this->_db->query($query);
        }
        
        $query = "DELETE FROM `$this->_tbl` WHERE `product_attr_id` = '".esc_sql($id)."'";
        $this->_db->query($query);
    }
}