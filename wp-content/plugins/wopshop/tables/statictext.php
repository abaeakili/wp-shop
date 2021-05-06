<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class StaticTextWshopTable extends WshopTable{
    
    function __construct(){
        global $wpdb;
        parent::__construct($wpdb->prefix.'wshop_config_statictext', 'id');        
    }
    
    function loadData($alias){
        $config = Factory::getConfig();        
        $query = "SELECT id, alias, `text_".$config->cur_lang."` as text FROM `$this->_tbl` where alias='".  esc_sql($alias)."'";
        return $this->_db->get_row($query);
    }
    
    function loadDataByIds($list){
        if (!count($list)){
            return array();
        }
        $config = Factory::getConfig();  
        $ids = implode(',', $list);
        $query = "SELECT id, alias, `text_".$config->cur_lang."` as text FROM `$this->_tbl` where id IN (".  esc_sql($ids).")";
        return $this->_db->get_results($query);
    }
    
    function getReturnPolicyForProducts($products){
        $productOption = Factory::getTable('productOption');
        $listrp = $productOption->getProductOptionList($products, 'return_policy');
        $listrp = array_unique($listrp);
        $tmp = $this->loadData('return_policy');
        $defidrp = intval($tmp->id);
        foreach($listrp as $k=>$v){
            if (!$v) $listrp[$k] = $defidrp;
        }
        return $this->loadDataByIds($listrp);
    }
}