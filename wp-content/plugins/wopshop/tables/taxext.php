<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class TaxExtWshopTable extends WshopTable {    
    var $id = null;
    var $tax_id = null;
    var $zones = null;
    var $tax = null;
    var $firma_tax = null;    
    
    function __construct(){
        global $wpdb;
        parent::__construct($wpdb->prefix.'wshop_taxes_ext', 'id');
    }
    
    function setZones($zones){
        $this->zones = json_encode($zones);
    }
    
    function getZones(){
        if ($this->zones != ""){
            return json_decode($this->zones, 1);
        }else{
            return array();
        }
    }
}