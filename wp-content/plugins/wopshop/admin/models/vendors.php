<?php
/**
* @version      1.0.0 30.01.2017
* @author       MAXXmarketing GmbH
* @package      WOPshop
* @copyright    Copyright (C) 2010 http://www.wop-agentur.com. All rights reserved.
* @license      GNU/GPL
*/

class VendorsWshopAdminModel extends WshopAdminModel {
    public $string;
 
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'wshop_vendors';
        parent::__construct();
    }

function getNamesVendors() {
        global $wpdb;
        $query = "SELECT id, f_name, l_name FROM `{$wpdb->prefix}wshop_vendors` ORDER BY f_name, l_name DESC";
        return $wpdb->get_results($query);
        $wpdb->flush();
    }    	

    function getAllVendors($limitstart, $limit, $text_search="") {
        global $wpdb;
        $where = "";
        if ($text_search){
            $search = esc_sql($text_search);            
            $where .= " and (f_name like '%".$search."%' or l_name like '%".$search."%' or email like '%".$search."%') "; 			
        } 
        $query = "SELECT * FROM `{$wpdb->prefix}wshop_vendors` where 1 ".$where." ORDER BY id DESC LIMIT " . $limitstart . ", " . $limit . " ";
        return $wpdb->get_results($query);
        $wpdb->flush();
    }

    function getCountAllVendors($text_search = "") {
        global $wpdb;
        $where = "";
        if ($text_search){
            $search = esc_sql($text_search);            
            $where .= " and (f_name like '%".$search."%' or l_name like '%".$search."%' or email like '%".$search."%') "; 	
        }
        $query = "SELECT COUNT(id) FROM `{$wpdb->prefix}wshop_vendors` where 1 " . $where . " ORDER BY id DESC";
        return $wpdb->get_var($query);
        $wpdb->flush();
    }
    
    function getAllVendorsNames($main_id_null = 0){
        global $wpdb;
        $query = "SELECT id, concat(f_name, ' ', l_name) as name, `main` FROM `{$wpdb->prefix}wshop_vendors` ORDER BY name";
        $rows = $wpdb->get_results($query);
        if ($main_id_null){
            foreach($rows as $k=>$v){
                if ($v->main) { $rows[$k]->id = 0; }
            }
        }
        return $rows;
        $wpdb->flush();
    }
    
    function getIdVendorForUserId($id){
        global $wpdb;
        $query = $wpdb->prepare("SELECT id FROM `{$wpdb->prefix}wshop_vendors` where user_id='%d'", $id);
        return $wpdb->get_var($query);
        $wpdb->flush();
    }
}