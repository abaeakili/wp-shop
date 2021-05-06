<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class ConfigDisplayPriceWshopTable extends WshopTable {

    var $id = null;
    var $zones = null;
    var $display_price = null;
    var $display_price_firma = null;
    
    function __construct(){
        global $wpdb;
        parent::__construct($wpdb->prefix.'wshop_config_display_prices', 'id');          
    }
    
    function setZones($zones){
        $this->zones = json_encode($zones);
    }
    
    function getZones(){
        if ($this->zones !="" ){
            return json_decode($this->zones, 1);
        }else{
            return array();
        }
    }
    
    function getList(){        
        $query = "SELECT * FROM `$this->_tbl`";
        $list = $this->_db->get_results($query);
        foreach($list as $k=>$v){
            $list[$k]->countries = json_decode($v->zones, 1);
        }
        return $list;
    }
}