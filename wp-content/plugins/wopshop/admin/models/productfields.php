<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class ProductFieldsWshopAdminModel extends WshopAdminModel {

    function getList($groupordering = 0, $order = null, $orderDir = null, $filter=array()){
        global $wpdb;
        $lang_name = 'name_'.$this->lang;
        $lang_description = 'description_'.$this->lang;
        $ordering = "F.ordering";
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        if ($groupordering){
            $ordering = "G.ordering, ".$ordering;
        }        
        $where = '';
	$_where = array();		
	if ($filter['group']){
            $_where[] = " F.group = '".esc_sql($filter['group'])."' ";    
        }		
	if ($filter['text_search']){
            $text_search = $filter['text_search'];
            $word = addcslashes(esc_sql($text_search), "_%");
            $_where[]=  "(LOWER(F.`".$lang_name."`) LIKE '%" . $word . "%' OR LOWER(F.`".$lang_description."`) LIKE '%" . $word . "%' OR F.id LIKE '%" . $word . "%')";
        }		
	if (count($_where)>0){
            $where = " WHERE ".implode(" AND ",$_where);
	}
        $query = "SELECT F.id, F.`".$lang_name."` as name, F.`".$lang_description."` as description, F.allcats, F.type, F.cats, F.ordering, F.`group`, G.`".$lang_name."` as groupname, multilist FROM `".$wpdb->prefix."wshop_products_extra_fields` as F left join `".$wpdb->prefix."wshop_products_extra_field_groups` as G on G.id=F.group ".$where." order by ".$ordering;
		extract(ws_add_trigger(get_defined_vars(), "before"));
        return $wpdb->get_results($query, OBJECT);
    }
}
?>