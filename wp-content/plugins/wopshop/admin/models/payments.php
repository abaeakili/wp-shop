<?php
class PaymentsWshopAdminModel extends WshopAdminModel {
    protected $table_name;
    
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix.'wshop_payment_method';
        parent::__construct();
    }
    
    function getAllPaymentMethods($publish = 1, $order = null, $orderDir = null) {
        global $wpdb;
        $query_where = ($publish)?("WHERE payment_publish = '1'"):("");
        $ordering = 'payment_ordering';
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        //$query = "SELECT *, `name_".$this->lang."` as payment_name, `description_".$this->lang."` as description FROM ".$this->table_name." ".$query_where." ORDER BY ".$ordering;
        $query = "SELECT payment_id, `name_".$this->lang."` as name, `description_".$this->lang."` as description , payment_code, payment_class, scriptname, payment_publish, payment_ordering, payment_params, payment_type FROM `".$this->table_name."`
                  $query_where
                  ORDER BY ".$ordering;
		extract(ws_add_trigger(get_defined_vars(), "before"));
        return $wpdb->get_results($query);
    }     
    
    
    function load ($id=null){
        global $wpdb;
        if (!$id)
            return false;
        $query = "SELECT *, `name_".$this->lang."` as name FROM ".$this->table_name." WHERE payment_id  = ". esc_sql($id);
        return  $wpdb->get_row($query);
    }
            
    function getAllCurrencies($publish = 1, $order = null, $orderDir = null) {
        global $wpdb;
        if ($publish == 0)
            $query_where = "WHERE currency_publish = '0'";        
        if ($publish == 1)
            $query_where = "WHERE currency_publish > '0'";
        if ($publish == 2)
            $query_where = "WHERE currency_publish >= '0'";
        if ($order && $orderDir){
            $ordering =" ORDER BY ".$order." ".$orderDir;
        }
        //$result = $wpdb->get_results( "SELECT * FROM $this->table_name");
        $result = $wpdb->get_results( "SELECT * FROM $this->table_name $query_where $ordering");
        return $result;
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
    
    function insert($post){
        global $wpdb;
        return $wpdb->insert( 
            $this->table_name, 
            esc_sql($post) 
        );
        //return $wpdb->insert_id;
    }
    
    function update($post, $payment_id){
        global $wpdb;
        return $wpdb->update( 
            $this->table_name, 
            $post, 
            array( 
                'payment_id' => $payment_id
            ) 
        );
//        $wpdb->show_errors();
//        $wpdb->print_error();  
    }
    
    function getMaxOrdering(){
        global $wpdb; 
        $query = "select max(payment_ordering) from ".$this->table_name;
        return $wpdb->get_var($query);
    }    
    
    function reorderPayment($payment_ordering) {
        global $wpdb;       
        $query = "UPDATE $this->table_name SET `payment_ordering` = `payment_ordering` + 1 WHERE `payment_ordering` > '" . $payment_ordering . "'";
        $wpdb->query($query);   
    }    

    function getListNamePaymens($publish = 1){
        $_list = $this->getAllPaymentMethods($publish);
        $list = array();
        foreach($_list as $v){
            $list[$v->payment_id] = $v->name;
        }
        return $list;
    }
}