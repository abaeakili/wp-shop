<?php
class ManufacturersWshopAdminModel extends WshopAdminModel {
    public $string;
 
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix.'wshop_manufacturers';

        parent::__construct();
    }
    function getAllManufacturers($publish=0, $order=null, $orderDir=null){
        global $wpdb;
        $query_where = ($publish)?(" WHERE manufacturer_publish = '1'"):("");  
        $queryorder = '';        
        if ($order && $orderDir){
            $queryorder = "order by ".$order." ".$orderDir;
        }
        $field = 'name_'.$this->lang;
        $query = "SELECT manufacturer_id, manufacturer_url, manufacturer_logo, manufacturer_publish, ordering, `".$field."` as name FROM `".$this->table_name."` $query_where ".$queryorder;
		extract(ws_add_trigger(get_defined_vars(), "before"));
        return $wpdb->get_results($query);
    }

    
    
    
	function getListManufacturers(){
        global $wpdb;
        $name_table = $wpdb->prefix.'wshop_manufacturers';
        return $wpdb->get_results( "SELECT *, `name_".$this->lang."` as manufacturer_name FROM ".$name_table." WHERE `manufacturer_publish` = '1' ORDER BY `ordering` asc");
        //return array();
    }
    
    function getAllManufacturersCount($search = null, $publish=''){
        global $wpdb;

        $where = 'WHERE 1=1';//`manufacturer_publish` != "-1" ';
        if($publish != '') $where.= ' AND `manufacturer_publish` = '.$publish;
        if($search){
            $where.= " AND `name_".$this->lang."` LIKE '%".$search."%'";
        }
		extract(ws_add_trigger(get_defined_vars(), "before"));
        $manufacturers = $wpdb->get_var( "SELECT COUNT(*) FROM ".$this->table_name." ".$where);
        //$wpdb->show_errors();        $wpdb->print_error(); 
        return $manufacturers;
    }
    
    function getCountManufacturers($publish = ''){
        global $wpdb;

        $where = 'WHERE `manufacturer_publish` != "-1" ';
        
        if($publish != '') $where.= ' AND `manufacturer_publish` = '.$publish;

        $count = $wpdb->get_var( "SELECT COUNT(*) FROM ".$this->table_name." ".$where);
        
        //$wpdb->show_errors();
        //$wpdb->print_error(); 
        return $count;
    }

    function ManufacturersActionPublish($action = '1', $rows = array()){
        global $wpdb;

        if(is_array($rows)){
            foreach($rows as $index=>$row){
                $wpdb->update( $this->table_name, array( 'manufacturer_publish' => esc_sql($action) ), array( 'manufacturer_id' => esc_sql($row) ), array( '%s', '%d' ), array( '%d' ) );
                //$wpdb->show_errors();
                //$wpdb->print_error();
            }
            return 'success';
        }else{
            addMessage(_WOP_SHOP_ERROR_BIND, 'error');
        }
    }
    function getDataManufacturer($manufacturer_id){
        global $wpdb;

        $where = 'WHERE `manufacturer_id` = '.esc_sql($manufacturer_id);

        $manufacturer = $wpdb->get_row( "SELECT *, `name_".$this->lang."` as name FROM ".$this->table_name." ".$where , OBJECT);
        //$wpdb->show_errors();
        //$wpdb->print_error(); 
        return $manufacturer;
    }
    
    function ManufacturerUpdate($post, $manufacturer_id){
        global $wpdb;
        $wpdb->update( 
            $this->table_name, 
            $post, 
            array( 
                'manufacturer_id' => $manufacturer_id
            )
        );
        //$wpdb->show_errors();
        //$wpdb->print_error(); 
        //die();
    }
    
    function ManufacturerInsert($post){
        global $wpdb;

        $count = $wpdb->get_var( "SELECT COUNT(*) FROM ".$this->table_name);
        
        $post['ordering'] = $count+1;
        $wpdb->insert( 
            $this->table_name, 
            $post
        );
        return $wpdb->insert_id;
        //$wpdb->show_errors();
        //$wpdb->print_error(); 
    }


    function _getAllManufacturers($publish=0, $order=null, $orderDir=null){
        global $wpdb;
        $query_where = ($publish)?(" WHERE manufacturer_publish = '1'"):("");  
		$queryorder = '';		
        if ($order && $orderDir){
            $queryorder = "order by ".$order." ".$orderDir;
        }
        $query = "SELECT manufacturer_id, manufacturer_url, manufacturer_logo, manufacturer_publish, ordering, `name_".$this->lang."` as name FROM `".$wpdb->prefix."wshop_manufacturers` $query_where ".$queryorder;
		extract(ws_add_trigger(get_defined_vars(), "before"));
        return $wpdb->get_results($query, OBJECT);
    }
    function getList(){
        $config = Factory::getConfig();
        if ($config->manufacturer_sorting==2){
            $morder = 'name';
        }else{
            $morder = 'ordering';
        }
    return $this->_getAllManufacturers(1, $morder, 'asc');
    } 
}