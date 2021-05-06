<?php

class DeliveryTimesWshopTable extends WshopTable {
    
    function __construct(){
        global $wpdb;
        parent::__construct( $wpdb->prefix.'wshop_delivery_times', 'id');
    }
    
    function getName() {
        $config = Factory::getConfig();
        $name = 'name_'.$config->cur_lang;
        return $this->$name;
    }
    
    function getDeliveryTimes(){
        $config = Factory::getConfig();      
        $query = "SELECT id, `name_".$config->cur_lang."` as name FROM `$this->_tbl` ORDER BY name";
        return $this->_db->get_results($query);
    }
}