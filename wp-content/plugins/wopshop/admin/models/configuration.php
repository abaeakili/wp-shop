<?php
class ConfigurationWshopAdminModel extends WshopAdminModel {
    public $table_name;
 
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix.'wshop_config';

        parent::__construct();
    }
    
    function getAllOrderstatus(){
        global $wpdb;
        $orderstatus = $wpdb->get_results( "SELECT *, `name_".$this->lang."` as name FROM ".$wpdb->prefix."wshop_order_status");
        return $orderstatus;
    }
    
    function getAllCountries(){
        global $wpdb;
        $countries = $wpdb->get_results( "SELECT *, `name_".$this->lang."` as name FROM ".$wpdb->prefix."wshop_countries");
        return $countries;
    }
    
    function getStaticTextList($use_for_return_policy = 0){
        global $wpdb;
        $where = $use_for_return_policy?' WHERE use_for_return_policy=1 ':'';
        $query = "SELECT id, alias, use_for_return_policy FROM `".$wpdb->prefix."wshop_config_statictext` ".$where." ORDER BY id";
        return $wpdb->get_results($query);
    }    

    function update($post){
        global $wpdb;
        $wpdb->update( 
            $this->table_name, 
            $post, 
            array('id' => $post['id']) 
        );
//        $wpdb->show_errors();
//        $wpdb->print_error(); 
//        die();
    }
}