<?php
class AttributValueWshopTable extends WshopTable{
    
    function __construct(){
        global $wpdb;
        parent::__construct($wpdb->prefix.'wshop_attr_values', 'value_id');
    }
    
    function getName($value_id){
        global $wpdb;
        $config = Factory::getConfig();
        $query = "SELECT `name_".$config->cur_lang."` as name FROM `".$wpdb->prefix."wshop_attr_values` WHERE value_id = '".esc_sql($value_id)."'";
        return $wpdb->get_var($query);
    }
    
    function getAllValues($attr_id) {
        global $wpdb;
        $config = Factory::getConfig();
        $query = "SELECT value_id, image, `name_".$config->cur_lang."` as name, value_ordering, attr_id FROM `".$wpdb->prefix."wshop_attr_values` where attr_id='".$attr_id."' ORDER BY value_ordering, value_id";
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
        $config = Factory::getConfig();
        $query = "SELECT value_id, image, `name_".$config->cur_lang."` as name, attr_id, value_ordering FROM `".$wpdb->prefix."wshop_attr_values` ORDER BY value_ordering, value_id";
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