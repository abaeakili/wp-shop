<?php
if ( ! defined( 'ABSPATH' ) ) {
 exit; // Exit if accessed directly
}
class AttributWshopAdminModel extends WshopAdminModel {
    public $string;

    public function __construct() {
        parent::__construct();
    }
    function getAttributes($product_id){
        global $wpdb;
        $query = "SELECT * FROM `".$wpdb->prefix."wshop_products_attr` WHERE product_id = '".$product_id."' ORDER BY product_attr_id";
        return $wpdb->get_results($query, OBJECT);
    }
    function getAttributes2($product_id){
        global $wpdb;
        $query = "SELECT * FROM `".$wpdb->prefix."wshop_products_attr2` WHERE product_id = '".$product_id."' ORDER BY id";
        return $wpdb->get_results($query, OBJECT);
    } 
    
    function getNameAttribut($attr_id) {
        global $wpdb;
        $query = "SELECT `name_".$this->lang."` as name FROM `".$wpdb->prefix."wshop_attr` WHERE attr_id = '".esc_sql($attr_id)."'";
        return $wpdb->get_var($query);
    }
    
    function getAllAttributes($result = 0, $categorys = null, $order = null, $orderDir = null){
        global $wpdb;
        $config = Factory::getConfig();

        $ordering = "A.attr_ordering asc";
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $query = "SELECT A.attr_id, A.`name_".$this->lang."` as name, A.attr_type, A.attr_ordering, A.independent, A.allcats, A.cats, G.`name_".$this->lang."` as groupname
                  FROM `".$wpdb->prefix."wshop_attr` as A left join `".$wpdb->prefix."wshop_attr_groups` as G on A.`group`=G.id
                  ORDER BY ".$ordering;
		extract(ws_add_trigger(get_defined_vars(), "before"));
        $list = $wpdb->get_results($query);

        if (is_array($categorys) && count($categorys)){
            foreach($list as $k=>$v){
                if (!$v->allcats){
                    if ($v->cats != ""){
                        $cats = json_decode($v->cats, 1);
                    }else{
                        $cats = array();
                    }
                    $enable = 0;
                    foreach($categorys as $cid){
                        if (is_array($cats) > 0 and in_array($cid, $cats)) $enable = 1;
                    }
                    if (!$enable){
                        unset($list[$k]);
                    }
                }
            } 
        }
        
        if ($result==0){
            return $list;
        }
        if ($result==1){
            $attributes_format1 = array();
            foreach($list as $v){
                $attributes_format1[$v->attr_id] = $v;
            }
            return $attributes_format1;
        }
        if ($result==2){
            $attributes_format2 = array();
            $attributes_format2['independent']= array();
            $attributes_format2['dependent']= array();
            foreach($list as $v){
                if ($v->independent) $key_dependent = "independent"; else $key_dependent = "dependent";
                $attributes_format2[$key_dependent][$v->attr_id] = $v;
            }
            return $attributes_format2;
        }
    }
    
    function deleteAttributeForProduct($product_id){
        global $wpdb;
        $wpdb->delete( $wpdb->prefix."wshop_products_attr2", array( 'product_id' => $product_id ) );
        //$wpdb->show_errors(); $wpdb->print_error();
    }
}
