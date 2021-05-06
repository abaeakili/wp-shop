<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class CurrencyWshopTable extends WshopTable {
    function __construct(){
        global $wpdb;
        parent::__construct($wpdb->prefix.'wshop_currencies', 'currency_id');
    }
    function getAllCurrencies($publish = 1) {
        $query_where = ($publish)?("WHERE currency_publish = '1'"):("");
        $query = "SELECT currency_id, currency_name, currency_code, currency_code_iso, currency_value FROM `$this->_tbl` $query_where ORDER BY currency_ordering";
	return $this->_db->get_results($query, OBJECT);
    }
}