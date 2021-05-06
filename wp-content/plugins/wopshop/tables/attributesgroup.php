<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class AttributesGroupWshopTable extends WshopTable{
    
    function __construct(){
        global $wpdb;
        parent::__construct($wpdb->prefix.'wshop_attr_groups', 'id', $_db );
    }
    
    function getList(){
        $config = Factory::getConfig();
        $query = "SELECT id, `name_".$config->cur_lang."` as name, ordering FROM `".$this->_tbl."` order by ordering";
        return $this->_db->get_results($query);
    }
}