<?php
if ( ! defined( 'ABSPATH' ) ) {
 exit; // Exit if accessed directly
}
class FreeAttributWshopAdminModel extends WshopAdminModel {
    public $string;

    public function __construct() {
        parent::__construct();
    }
    function getNameAttrib($id) {
        global $wpdb;
		$config = Factory::getConfig();
        $lang = $config->cur_lang; //get_bloginfo('language');
        $query = "SELECT `name_".$lang."` as name FROM `".$wpdb->prefix."wshop_free_attr` WHERE id = '".sql_esc($id)."'";
		extract(ws_add_trigger(get_defined_vars(), "before"));
        return $wpdb->get_var($query);
    }

    function getAll($order = null, $orderDir = null) {
        $config = Factory::getConfig();
        $lang = $config->cur_lang; //get_bloginfo('language');
        global $wpdb;
        $ordering = 'ordering';
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $query = "SELECT id, `name_".$lang."` as name, ordering, required FROM `".$wpdb->prefix."wshop_free_attr` ORDER BY ".$ordering;
		extract(ws_add_trigger(get_defined_vars(), "before"));
        return $wpdb->get_results($query, OBJECT);
    }
}
