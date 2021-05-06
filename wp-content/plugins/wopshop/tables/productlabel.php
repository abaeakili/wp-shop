<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class ProductLabelWshopTable extends WshopTable {
    var $ordering = null;

    function __construct(){
        global $wpdb;
        parent::__construct($wpdb->prefix.'wshop_product_labels', 'id');
    }

    function getListLabels(){
		$config = Factory::getConfig();
		$lang = $config->cur_lang;
		$query = "SELECT id, image, `name_".$lang."` as name FROM `$this->_tbl` ORDER BY name";
		$list = $this->_db->get_results($query);
		$rows = array();
		foreach($list as $row){
			$rows[$row->id] = $row;
		}
	return $rows;
    }
}