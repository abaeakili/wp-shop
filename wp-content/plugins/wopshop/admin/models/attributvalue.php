<?php
if ( ! defined( 'ABSPATH' ) ) {
 exit; // Exit if accessed directly
}
class AttributValueWshopAdminModel extends WshopAdminModel {
    public $string;

    public function __construct() {
        parent::__construct();
    }

    function getNameValue($value_id) {
        global $wpdb;
        $query = "SELECT `name_".$this->lang."` as name FROM `".$wpdb->prefix."wshop_attr_values` WHERE value_id = '".esc_sql($value_id)."'";
        return $wpdb->get_var($query);
    }
    function getAllValues($attr_id, $order = null, $orderDir = null) {
        global $wpdb;
        $ordering = 'value_ordering, value_id';
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $query = "SELECT value_id, image, `name_".$this->lang."` as name, attr_id, value_ordering FROM `".$wpdb->prefix."wshop_attr_values` where attr_id='".$attr_id."' ORDER BY ".$ordering;
		extract(ws_add_trigger(get_defined_vars(), "before"));
        return $wpdb->get_results($query);
    }
    /**
    * get All Atribute value
    * @param $resulttype (0 - ObjectList, 1 - array {id->name}, 2 - array(id->object) )
    * 
    * @param mixed $resulttype
    */
    function getAllAttributeValues($resulttype=0){
        global $wpdb;
        $query = "SELECT value_id, image, `name_".$this->lang."` as name, attr_id, value_ordering FROM `".$wpdb->prefix."wshop_attr_values` ORDER BY value_ordering, value_id";
		extract(ws_add_trigger(get_defined_vars(), "before"));
        $attribs = $wpdb->get_results($query);

        if ($resulttype==2){
            $rows = array();
            foreach($attribs as $k=>$v){
                $rows[$v->value_id] = $v;    
            }
            return $rows;
        }elseif ($resulttype==1){
            $rows = array();
            foreach($attribs as $k=>$v){
                $rows[$v->value_id] = $v->name;    
            }
            return $rows;
        }else{
            return $attribs;
        }        
    }
}
