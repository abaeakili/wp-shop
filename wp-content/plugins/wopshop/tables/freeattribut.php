<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class FreeAttributWshopTable extends WshopTable{

    function __construct(){
        global $wpdb;
        parent::__construct($wpdb->prefix.'wshop_free_attr', 'id');        
    }

    function getName($id) {
        $config = Factory::getConfig();
        $query = "SELECT `name_".$config->cur_lang."` as name FROM `$this->_tbl` WHERE `id` = '".  esc_sql($id)."'";
        return $this->_db->get_var($query);
    }

    function getAll() {
        $config = Factory::getConfig();
        $query = "SELECT id, `name_".$config->cur_lang."` as name, required, ordering FROM `$this->_tbl` ORDER BY `ordering`";
        return $this->_db->get_results($query);
    }

    function getAllNames(){
        $rows = array();
        $config = Factory::getConfig();
        $query = "SELECT id, `name_".$config->cur_lang."` as name FROM `$this->_tbl` ORDER BY `ordering`";
        $list = $this->_db->get_results($query);       
        foreach($list as $v){
            $rows[$v->id] = $v->name;
        }
        return $rows;
    }
}