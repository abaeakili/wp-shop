<?php
class SeoWshopTable extends WshopTable{
    
    function __construct(){
        global $wpdb;
        parent::__construct($wpdb->prefix.'wshop_config_seo', 'id');
    }
    
    function loadData($alias){
        $config = Factory::getConfig();
        $query = "SELECT id, alias, `title_".$config->cur_lang."` as title, `keyword_".$config->cur_lang."` as keyword, `description_".$config->cur_lang."` as description FROM `$this->_tbl` where alias='".  esc_sql($alias)."'";;
    
        return $this->_db->get_row($query);
    }
}