<?php
if ( ! defined( 'ABSPATH' ) ) {
 exit; // Exit if accessed directly
}

class UnitsWshopAdminModel extends WshopAdminModel {
    public $string;
 
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix.'wshop_unit';

        parent::__construct();
    }
  
    function getUnits($order = null, $orderDir = null){
        global $wpdb;

        $ordering = "name";
        
        $query = "SELECT id, `name_".$this->lang."` as name FROM `".$this->table_name."` ORDER BY ".$ordering;
        return $wpdb->get_results($query, OBJECT);
    }
}
