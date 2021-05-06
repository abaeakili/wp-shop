<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class AttributWshopTable extends WshopTable{

    function __construct(){
        global $wpdb;
        parent::__construct($wpdb->prefix.'wshop_attr', 'attr_id');        
    }

    function getName($attr_id){
        $config = Factory::getConfig();
        $query = "SELECT `name_".$config->cur_lang."` as name FROM `$this->_tbl` WHERE attr_id = '".  esc_sql($attr_id)."'";
        return $this->_db->get_var($query);
    }

    function getAllAttributes($groupordering = 1){
        $config = Factory::getConfig();
        $ordering = "A.attr_ordering";
        if ($groupordering){
            $ordering = "G.ordering, A.attr_ordering";
        }        
        $query = "SELECT A.attr_id, A.`name_".$config->cur_lang."` as name, A.`description_".$config->cur_lang."` as description, A.attr_type, A.independent, A.allcats, A.cats, A.attr_ordering, G.`name_".$config->cur_lang."` as groupname
                  FROM `$this->_tbl` as A left join `".$this->_db->prefix."wshop_attr_groups` as G on A.`group`=G.id
                  ORDER BY ".$ordering;
        $rows = $this->_db->get_results($query);
        foreach($rows as $k=>$v){
            if ($v->allcats){
                $rows[$k]->cats = array();
            }else{
                $rows[$k]->cats = json_decode($v->cats, 1);
            }
        }
    return $rows;
    }
    
    function getTypeAttribut($attr_id){
        $query = "select attr_type from $this->_tbl `attr_id`='".  esc_sql($attr_id)."'";
        return $this->_db->get_var($query);
    }
    
    function setCategorys($cats){
        $this->cats = json_encode($cats);
    }
      
    function getCategorys(){
        if ($this->cats != ""){
            return json_decode($this->cats, 1);
        }else{
            return array();
        }
    }

}