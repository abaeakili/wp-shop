<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class ImportExportWshopAdminModel extends WshopAdminModel{
    
    function getList() {
        global $wpdb;
        $query = "SELECT * FROM `".$wpdb->prefix."wshop_import_export` ORDER BY name";
		extract(ws_add_trigger(get_defined_vars(), "before"));
        return $wpdb->get_results($query);
    }
}