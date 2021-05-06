<?php
if ( ! defined( 'ABSPATH' ) ) {
 exit; // Exit if accessed directly
}
class AttributesGroupsWshopAdminModel extends WshopAdminModel {
    public $string;

    public function __construct() {
        parent::__construct();
    }
    function getList(){
        global $wpdb;
        $query = "SELECT id, `name_".$this->lang."` as name, ordering FROM `".$wpdb->prefix."wshop_attr_groups` order by ordering";
		extract(ws_add_trigger(get_defined_vars(), "before"));
        return $wpdb->get_results($query);
    }
}
