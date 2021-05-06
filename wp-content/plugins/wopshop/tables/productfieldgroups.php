<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class ProductFieldGroupsWshopTable extends WshopTable{
    
    function __construct(  ) {
        
        global $wpdb;
        parent::__construct( $wpdb->prefix . 'wshop_products_extra_field_groups', 'id' );
        
    }
    
    function getList(  ) {
        
        $config = Factory::getConfig(  );
        $lang   = Factory::getLang(  );
        $query  = "SELECT id, `" . $lang->get("name") . "` as name, ordering FROM `{$this->_db->prefix}wshop_products_extra_field_groups` order by ordering";
        
        return $this->_db->get_results( $query );
        
    }
    
}