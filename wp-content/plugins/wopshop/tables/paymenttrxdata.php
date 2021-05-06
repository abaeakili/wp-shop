<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class PaymentTrxDataWshopTable extends WshopTable{
    function __construct(){
        global $wpdb;
        parent::__construct($wpdb->prefix.'wshop_payment_trx_data', 'id');         
    }
}