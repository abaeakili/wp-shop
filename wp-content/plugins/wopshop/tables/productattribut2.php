<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class ProductAttribut2WshopTable extends WshopTable{
    
    var $id = null;
    var $product_id = null;
    var $attr_id = null;
    var $attr_value_id = null;
    var $price_mod = null;
    var $addprice = null;
    
    var $_price_mod_allow = array("+","-","*","/","=","%");
    
    function __construct(){
        global $wpdb;
        parent::__construct($wpdb->prefix.'wshop_products_attr2', 'id');           
    }
    
    function check(){        
        if (!in_array($this->price_mod, $this->_price_mod_allow)){
             $this->price_mod = $this->_price_mod_allow[0];
        }
        if (!$this->product_id){
            return 0;
        }
        if (!$this->attr_id){
            return 0;
        }
        if (!$this->attr_value_id){
            return 0;
        }
        return 1;
    }
    
    function deleteAttributeForProduct(){
        $query = "DELETE FROM `$this->_tbl` WHERE `product_id` = '".  esc_sql($this->product_id)."'";
        $this->_db->query($query);    
    }
}