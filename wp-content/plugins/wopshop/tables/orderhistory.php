<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class OrderHistoryWshopTable extends WshopTable {
    
    var $order_history_id = null;
    var $order_id = null;
    var $order_status_id = null;
    var $status_date_added = null;
    var $customer_notify = null;
    var $comments = null;

    function __construct(){
        global $wpdb;
        parent::__construct($wpdb->prefix.'wshop_order_history', 'order_history_id');        
    }
}