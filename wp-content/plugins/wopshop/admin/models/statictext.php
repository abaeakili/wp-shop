<?php
class StaticTextWshopAdminModel extends WshopAdminModel {
    public $string;
 
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix.'wshop_config_statictext';
        parent::__construct();
    }
    function getList($use_for_return_policy = 0){
		$config = Factory::getConfig();
        $lang = $config->cur_lang;
		//$lang = get_bloginfo('language');
        global $wpdb;
        $where = $use_for_return_policy?' WHERE use_for_return_policy=1 ':'';
        $query = "SELECT id, alias, use_for_return_policy FROM `".$this->table_name."` ".$where." ORDER BY id";
		extract(ws_add_trigger(get_defined_vars(), "before"));
        return $wpdb->get_results($query);
    }
    
    function getAllStatictext($search = null, $publish='', $order='id', $orderDir='asc'){
        global $wpdb;

        $where = 'WHERE 1=1 ';
        if($search){
            $where.= " AND `text_".$this->lang."` LIKE '%".$search."%'";
        } 
        $statictext = $wpdb->get_results( "SELECT * FROM ".$this->table_name." ".$where." ORDER BY ".$order." ".$orderDir, OBJECT);
        //$wpdb->show_errors();
        //$wpdb->print_error(); 
        return $statictext;
    }
    
    function getDataStatictext($statictext_id){
        global $wpdb;

        $where = 'WHERE `id` = '.esc_sql($statictext_id);

        $statictext = $wpdb->get_row( "SELECT * FROM ".$this->table_name." ".$where , OBJECT);
        //$wpdb->show_errors();
        //$wpdb->print_error(); 
        return $statictext;
    }
    
    function StatictextUpdate($post, $statictext_id){
        global $wpdb;
        $wpdb->update( 
            $this->table_name, 
            $post, 
            array( 
                'id' => $statictext_id
            )
        );
        //$wpdb->show_errors();
        //$wpdb->print_error(); 
        //die();
    }
}