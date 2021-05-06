<?php
class TaxesWshopAdminModel extends WshopAdminModel {
    public $string;
 
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix.'wshop_taxes';

        parent::__construct();
    }
    function t_getAllTaxes($order = null, $orderDir = null) {
        global $wpdb;
        $ordering = 'tax_name';
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $query = "SELECT * FROM `".$wpdb->prefix."wshop_taxes` ORDER BY ".$ordering;
        return $taxes = $wpdb->get_results( $query, OBJECT);
    }
    function getTaxes(){
        global $wpdb;
        $taxes = $wpdb->get_results( "SELECT * FROM ".$this->table_name." WHERE `tax_publish` > 0 ", OBJECT);
        foreach($taxes as $i=>$v){
            $taxes[$i]->name = $v->tax_name.' ('.$v->tax_value.'%)';
        }
        return $taxes;
    }

    function getAllTaxes($order = null, $orderDir = null){
        global $wpdb;

        $ordering = 'tax_name';
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $query = "SELECT * FROM `".$wpdb->prefix."wshop_taxes` ORDER BY ".$ordering;
		extract(ws_add_trigger(get_defined_vars(), "before"));
        return $wpdb->get_results($query);
    }

    function getExtTaxes($tax_id = 0, $order = null, $orderDir = null) {
        global $wpdb;
        $where = "";
        if ($tax_id) $where = " where ET.tax_id='".$tax_id."'";
        $ordering = 'ET.id';
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $query = "SELECT ET.*, T.tax_name FROM `".$wpdb->prefix."wshop_taxes_ext` as ET left join `".$wpdb->prefix."wshop_taxes` as T on T.tax_id=ET.tax_id ".$where." ORDER BY ".$ordering;
        extract(ws_add_trigger(get_defined_vars(), "before"));
        return $wpdb->get_results($query);
    }
}