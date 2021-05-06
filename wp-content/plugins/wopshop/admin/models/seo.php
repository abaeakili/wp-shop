<?php
class SeoWshopAdminModel extends WshopAdminModel {
    public $string;
 
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix.'wshop_config_seo';
        parent::__construct();
    }
    
    function getList(){
        global $wpdb; 
		$config = Factory::getConfig();
        $query = "SELECT id, alias, `title_".$config->cur_lang."` as title, `keyword_".$config->cur_lang."` as keyword, `description_".$config->cur_lang."` as description FROM `".$wpdb->prefix."wshop_config_seo` ORDER BY ordering";
        extract(ws_add_trigger(get_defined_vars(), "before"));
        return $wpdb->get_results($query);
    }  
        
    function load($id){
        global $wpdb;       
        $query = "SELECT * FROM `".$wpdb->prefix."wshop_config_seo` WHERE id = ".esc_sql($id);
        return $wpdb->get_row($query);
    }
    function update($data, $id){
        global $wpdb;
        $result = $wpdb->update($wpdb->prefix."wshop_config_seo", $data, array('id' => $id));
        return $result;
    }    
}