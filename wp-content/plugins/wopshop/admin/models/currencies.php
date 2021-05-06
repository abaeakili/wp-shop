<?php
class CurrenciesWshopAdminModel extends WshopAdminModel {
    protected $table_name;
    
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix.'wshop_currencies';
        parent::__construct();
    }
  
    function getListCurrencies(){
        global $wpdb;
        $name_table = $wpdb->prefix.'wshop_currencies';
        return $wpdb->get_results( "SELECT * FROM ".$name_table." WHERE `currency_publish` = '1' ORDER BY `currency_ordering` asc");
    }
    function getAllCurrencies($publish = 1, $order = null, $orderDir = null) {
        global $wpdb;
        $query_where = ($publish)?("WHERE currency_publish = '1'"):("");
        $ordering = 'currency_ordering';
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $query = "SELECT * FROM `".$this->table_name."` $query_where ORDER BY ".$ordering;
		extract(ws_add_trigger(get_defined_vars(), "before"));
        return $wpdb->get_results($query);
    }
    
    function getCurrency($currency_id) {
        global $wpdb;
        $query_where = "WHERE currency_id = '$currency_id'";        
        $result = $wpdb->get_row( "SELECT * FROM $this->table_name WHERE currency_id = '$currency_id'");
        return $result;
    }     
    
    
    function getCountCurrencies($publish = 1) {
        global $wpdb;
        if ($publish == 0)
            $query_where = "WHERE currency_publish = '0'";        
        if ($publish == 1)
            $query_where = "WHERE currency_publish > '0'";
        if ($publish == 2)
            $query_where = "WHERE currency_publish >= '0'";
        $result = $wpdb->get_var( "SELECT COUNT(*) FROM $this->table_name $query_where" );
        return $result;

    }     
    
    function insertCurrency($post){
        global $wpdb;
        $wpdb->insert( 
            $this->table_name, 
            $post 
        );
    }
    
    function updateCurrency($post, $currency_id){
        global $wpdb;
        $wpdb->update( 
            $this->table_name, 
            $post, 
            array( 
                'currency_id' => $currency_id
            ) 
        );
    }
    
    function publishCurrency($flag, $currency_ids) {
        global $wpdb;
        if(count($currency_ids)){
            foreach ($currency_ids as $currency_id) {
                $wpdb->query("UPDATE $this->table_name SET `currency_publish` = '" . esc_sql($flag) . "' WHERE `currency_id` = " . esc_sql($currency_id));
            }
        }
        //$wpdb->query("UPDATE $this->table_name SET `currency_publish` = '" . esc_sql($flag) . "' WHERE `currency_id` IN (" . esc_sql($currency_ids) . ") ");
//        $wpdb->show_errors();
//        $wpdb->print_error(); 
//        die();
    } 
    
    function reorderCurrency($currency_ordering) {
        global $wpdb;       
        $query = "UPDATE $this->table_name SET `currency_ordering` = `currency_ordering` + 1 WHERE `currency_ordering` > '" . $currency_ordering . "'";
        $wpdb->query($query);   
    }    
    
}