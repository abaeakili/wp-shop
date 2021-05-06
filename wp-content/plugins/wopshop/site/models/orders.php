<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
class OrdersWshopModel extends WshopModel{
    
    public $type_cart = "cart"; //cart,wishlist
    public $products = array();
    public $count_product = 0;
    public $price_product = 0;
    public $summ = 0;
    public $rabatt_id = 0;
    public $rabatt_value = 0;
    public $rabatt_type = 0;
    public $rabatt_summ = 0;
    
    public function __construct() {
        parent::__construct();
    }

    function getAllOrderStatus($order = null, $orderDir = null) {
        global $wpdb;
        $ordering = "status_id";
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $query = "SELECT status_id, status_code, `name_".$this->lang."` as name FROM `".$wpdb->prefix."wshop_order_status` ORDER BY ".$ordering;
        return $wpdb->get_results($query);
    }
}