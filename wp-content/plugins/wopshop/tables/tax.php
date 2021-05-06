<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class TaxWshopTable extends WshopTable{

    function __construct(){
        global $wpdb;
        parent::__construct($wpdb->prefix.'wshop_taxes', 'tax_id');
    }
    
    function getAllTaxes(){
		global $wpdb;
		$query = "SELECT tax_id, tax_name, tax_value FROM `".$wpdb->prefix."wshop_taxes`";                
        return $wpdb->get_results($query);
    }
    function getExtTaxes($tax_id = 0){
        global $wpdb;
        $where = "";
        if ($tax_id) $where = " where tax_id='".$tax_id."'";
        $query = "SELECT * FROM `".$wpdb->prefix."wshop_taxes_ext` ".$where;
        $list = $wpdb->get_results($query);
        foreach($list as $k=>$v){
            $list[$k]->countries = json_decode($v->zones, 1);
        }
        return $list;
    }   
}