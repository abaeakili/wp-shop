<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class ProductFieldValuesWshopAdminModel extends WshopAdminModel {

    function getList($field_id, $order = null, $orderDir = null, $filter=array()){
        global $wpdb;
        
        $ordering = 'ordering';
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $where = '';
        $lang_name = 'name_'.$this->lang;
        if ($filter['text_search']){
            $text_search = $filter['text_search'];
            $word = addcslashes(esc_sql($text_search), "_%");
            $where =  " and (LOWER(`".$lang_name."`) LIKE '%".$word."%' OR id LIKE '%".$word."%')";
        }
        $query = "SELECT id, `".$lang_name."` as name, ordering FROM `".$wpdb->prefix."wshop_products_extra_field_values` where field_id='$field_id' ".$where." order by ".$ordering;
		extract(ws_add_trigger(get_defined_vars(), "before"));
        return $wpdb->get_results($query, OBJECT);
    }
    
    function getAllList($display = 0){
        global $wpdb;
        $lang_name = 'name_'.$this->lang;
        $query = "SELECT id, `".$lang_name."` as name, field_id FROM `".$wpdb->prefix."wshop_products_extra_field_values` order by ordering";
		extract(ws_add_trigger(get_defined_vars(), "before"));
        if ($display==0){
            return $wpdb->get_results($query, OBJECT);
        }elseif($display==1){
            $rows = $wpdb->get_results($query, OBJECT);
            $list = array();
            foreach($rows as $k=>$row){
                $list[$row->id] = $row->name;
                unset($rows[$k]);    
            }
            return $list;
        }else{
            $rows = $wpdb->get_results($query, OBJECT);
            $list = array();
            foreach($rows as $k=>$row){
                $list[$row->field_id][$row->id] = $row->name;
                unset($rows[$k]);    
            }
            return $list;
        }
    }
}

?>