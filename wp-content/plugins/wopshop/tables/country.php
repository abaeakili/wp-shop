<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class CountryWshopTable extends WshopTable {
    
    var $ordering = null;

    function __construct(){
        global $wpdb;
        parent::__construct($wpdb->prefix.'wshop_countries', 'country_id');        
    }

    function getAllCountries($publish = 1){
        $config = Factory::getConfig();
        $where = ($publish)?(" WHERE country_publish = '1' "):(" ");
        $ordering = "ordering";
        if ($config->sorting_country_in_alphabet) $ordering = "name";
        $query = "SELECT country_id, `name_".$config->cur_lang."` as name FROM `$this->_tbl` ".$where." ORDER BY ".$ordering;
        return $this->_db->get_results($query);
    }
}