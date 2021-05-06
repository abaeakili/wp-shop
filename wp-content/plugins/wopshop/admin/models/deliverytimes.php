<?php
class DeliveryTimesWshopAdminModel extends WshopAdminModel {
    public $string;
 
    public function __construct() {
        parent::__construct();
    }
    
    function getDeliveryTimes($order = null, $orderDir = null){
        global $wpdb;
       
        $ordering = "name";
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $query = "SELECT id, `name_".$this->lang."` as name FROM `".$wpdb->prefix.'wshop_delivery_times'."` ORDER BY ".$ordering;
        extract(ws_add_trigger(get_defined_vars(), "before"));
        return $wpdb->get_results($query, OBJECT);
    }
    
    function getDeliveryTime($id){
        global $wpdb;
       
        $query = "SELECT *, `name_".$this->lang."` as name FROM `".$wpdb->prefix."wshop_delivery_times` WHERE `id` = ".esc_sql($id)." LIMIT 1";
        extract(ws_add_trigger(get_defined_vars(), "before"));
        return $wpdb->get_row($query, OBJECT);
    }
    function DeliverytimesUpdate($post, $id){
        global $wpdb;
        $wpdb->update( 
            $wpdb->prefix."wshop_delivery_times",
            $post, 
            array( 
                'id' => $id
            )
        );
        //$wpdb->show_errors(); $wpdb->print_error();
        addMessage(_WOP_SHOP_ACTION_DELIVERYTIME_UPDATE);
    }
    
    function DeliverytimesInsert($post){
        global $wpdb;

        $wpdb->insert(
            $wpdb->prefix."wshop_delivery_times",
            $post
        );
        //$wpdb->show_errors(); $wpdb->print_error(); 
        addMessage(_WOP_SHOP_ACTION_DELIVERYTIME_INSERT);
    }
    
    function DeliverytimesDelete($rows){
        global $wpdb;
        
        if(is_array($rows)){
            foreach($rows as $i=>$v){
                $wpdb->delete($wpdb->prefix."wshop_delivery_times", array( 'id' => $v ));
                //$wpdb->show_errors(); $wpdb->print_error(); 
            }
        }
        addMessage(_WOP_SHOP_ACTION_DELIVERYTIME_DELETED);
    }
}