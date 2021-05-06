<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class OrderStatusWshopTable extends WshopTable {
    
    function __construct(){
        global $wpdb;
        parent::__construct($wpdb->prefix.'wshop_order_status', 'status_id');          
    }
    
    function getName($status_id) {
        $config = Factory::getConfig();
        $query = "SELECT `name_".$config->cur_lang."` as name FROM `$this->_tbl` WHERE status_id = '" . esc_sql($status_id) . "'";
        return $this->_db->get_var($query);
    }    
}