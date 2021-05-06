<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class AddonsWshopAdminModel extends WshopAdminModel {
    
    public function getList($details = 0){
        global $wpdb;
        
        $query = "SELECT * FROM `".$wpdb->prefix."wshop_addons`";
        extract(ws_add_trigger(get_defined_vars(), "before"));
        $addons = $wpdb->get_results($query);

        if ($details){
            foreach($addons as $k=>$v){
                if (file_exists(WOPSHOP_PLUGIN_DIR."/site/addons/".$v->alias."/config.tmpl.php")){
                    $addons[$k]->config_file_exist = 1;
                } else {
                    $addons[$k]->config_file_exist = 0;
                }
                
                if (file_exists(WOPSHOP_PLUGIN_DIR."/site/addons/".$v->alias."/info.tmpl.php")){
                    $addons[$k]->info_file_exist = 1;
                } else {
                    $addons[$k]->info_file_exist = 0;
                }
                
                if (file_exists(WOPSHOP_PLUGIN_DIR."/site/addons/".$v->alias."/version.tmpl.php")){
                    $addons[$k]->version_file_exist = 1;
                } else {
                    $addons[$k]->version_file_exist = 0;
                }
            }
        }
        
        return $addons;
    }
    
    public function save(array $post){
        $row = Factory::getTable('addon');
        $params = $post['params'];
        if (!is_array($params)){
            $params = array();
        }

        do_action_ref_array('onBeforeSaveAddons', array(&$params, &$post, &$row));
        $row->bind($post);
        $row->setParams($params);
        $row->store();
		do_action_ref_array('onAfterSaveAddons', array(&$params, &$post, &$row));
        return $row;
    }
    
    public function delete($id){
        $text = '';
        do_action_ref_array('onBeforeRemoveAddons', array(&$id));
        $row = Factory::getTable('addon');
        $row->load($id);
        if (file_exists(WOPSHOP_PLUGIN_DIR."/site/addons/".$row->alias."/uninstall.php")){
            include WOPSHOP_PLUGIN_DIR."/site/addons/".$row->alias."/uninstall.php";
        }
        $row->delete();
        do_action_ref_array('onAfterRemoveAddons', array(&$id, &$text));
        if ($text){
            addMessage($text);
        }
    }
    
    protected function getTableName(){
        if (empty($this->tablename)){
            $this->tablename = 'addon';
        }
        
        return parent::getTableName();
    }
}