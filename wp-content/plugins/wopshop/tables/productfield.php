<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class ProductFieldWshopTable extends WshopTable{
    
    function __construct(){
        global $wpdb;
        parent::__construct($wpdb->prefix.'wshop_products_extra_fields', 'id');
    }
    
    /**
    * set categorys
    * 
    * @param array $cats
    */
    function setCategorys($cats){
        $this->cats = json_encode($cats);
    }
    
    /**
    * get gategoryd
    * 
    * @return array
    */    
    function getCategorys(){
        if ($this->cats != ""){
            return json_decode($this->cats, 1);
        }else{
            return array();
        }
    }
    
    function getList($groupordering = 1){
        $config = Factory::getConfig();
        $ordering = "F.ordering";
        if ($groupordering){
            $ordering = "G.ordering, F.ordering";
        } 
        $query = "SELECT F.id, F.`name_".$config->cur_lang."` as name, F.`description_".$config->cur_lang."` as description, F.allcats, F.type, F.cats, F.ordering, F.`group`, G.`name_".$config->cur_lang."` as groupname, multilist FROM `$this->_tbl` as F left join `".$this->_db->prefix."wshop_products_extra_field_groups` as G on G.id=F.group order by ".$ordering;
        $rows = $this->_db->get_results($query);
        $list = array();        
        foreach($rows as $k=>$v){
            $list[$v->id] = $v;
            if ($v->allcats){
                $list[$v->id]->cats = array();
            }else{
                $list[$v->id]->cats = json_decode($v->cats, 1);
            }            
        }
        unset($rows);
        return $list;
    }
}