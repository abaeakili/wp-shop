<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class ProductFieldValueWshopTable extends WshopTable {

    var $ordering = null;

    function __construct(){
        global $wpdb;
        parent::__construct($wpdb->prefix.'wshop_products_extra_field_values', 'id');
    }

    function getAllList($display = 0){
		$config = Factory::getConfig();
		$lang = $config->cur_lang;		
        $query = "SELECT id, `name_".$lang."` as name, field_id FROM `$this->_tbl` order by ordering";
        if ($display==0){
            return $this->_db->get_results($query);
        }elseif($display==1){
            $rows = $this->_db->get_results($query);
            $list = array();
            foreach($rows as $k=>$row){
                $list[$row->id] = $row->name;
                unset($rows[$k]);    
            }
            return $list;
        }else{
            $rows = $this->_db->get_results($query);
            $list = array();
            foreach($rows as $k=>$row){
                $list[$row->field_id][$row->id] = $row->name;
                unset($rows[$k]);    
            }
            return $list;
        }
    }
}