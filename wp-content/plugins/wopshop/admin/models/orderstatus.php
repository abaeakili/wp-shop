<?php
class OrderStatusWshopAdminModel extends WshopAdminModel {
    public $string;
 
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix.'wshop_order_status';
        parent::__construct();
    }
  
    function getAllOrderstatus($search = null, $publish='', $order='ordering', $orderDir='asc', $start, $limit){
        global $wpdb;

        $where = 'WHERE `publish` != "-1" ';
        if($publish != '') $where.= ' AND `publish` = '.$publish;
        if($search){
            $where.= " AND `name_".$this->lang."` LIKE '%".$search."%'";
        }
        $orderstatus = $wpdb->get_results( "SELECT *, `name_".$this->lang."` as name FROM ".$this->table_name." ".$where." ORDER BY $order $orderDir LIMIT $start,$limit" , OBJECT);
        //$wpdb->show_errors();
        //$wpdb->print_error(); 
        return $orderstatus;
    }
    
    function getAllOrderstatusCount($search = null, $publish=''){
        global $wpdb;

        $where = 'WHERE `publish` != "-1" ';
        if($publish != '') $where.= ' AND `publish` = '.$publish;
        if($search){
            $where.= " AND `name_".$this->lang."` LIKE '%".$search."%'";
        }
        $orderstatus = $wpdb->get_var( "SELECT COUNT(*) FROM ".$this->table_name." ".$where);
        //$wpdb->show_errors();
        //$wpdb->print_error(); 
        return $orderstatus;
    }
    
    function getCountOrderstatus($publish = ''){
        global $wpdb;

        $where = 'WHERE `publish` != "-1" ';
        
        if($publish != '') $where.= ' AND `publish` = '.$publish;

        $count = $wpdb->get_var( "SELECT COUNT(*) FROM ".$this->table_name." ".$where);
        
        //$wpdb->show_errors();
        //$wpdb->print_error(); 
        return $count;
    }

    function OrderstatusActionPublish($action = '1', $rows = array()){
        global $wpdb;
        if(is_array($rows)){
            foreach($rows as $index=>$row){
                $wpdb->update( $this->table_name, array( 'publish' => esc_sql($action) ), array( 'status_id' => esc_sql($row) ), array( '%s', '%d' ), array( '%d' ) );
                //$wpdb->show_errors();
                //$wpdb->print_error();
                return 'success';
            }
        }else{
            addMessage(_WOP_SHOP_ERROR_BIND, 'error');
        }
    }
    function getDataOrderstatus($status_id){
        global $wpdb;

        $where = 'WHERE `status_id` = '.esc_sql($status_id);

        $orderstatus = $wpdb->get_row( "SELECT *, `name_".$this->lang."` as name FROM ".$this->table_name." ".$where , OBJECT);
        //$wpdb->show_errors();
        //$wpdb->print_error(); 
        return $orderstatus;
    }
    
    function Update($post, $country_id){
        global $wpdb;
        $wpdb->update( 
            $this->table_name, 
            $post, 
            array( 
                'status_id' => $country_id
            )
        );
        //$wpdb->show_errors();
        //$wpdb->print_error(); 
        addMessage(_WOP_SHOP_ACTION_ORDERSTATUS_UPDATE);
    }
    
    function Insert($post){
        global $wpdb;
        $count = $wpdb->get_var( "SELECT COUNT(*) FROM ".$this->table_name);
        
        $post['ordering'] = $count+1;
        $wpdb->insert( 
            $this->table_name, 
            $post
        );
        //$wpdb->show_errors();
        //$wpdb->print_error(); 
        addMessage(_WOP_SHOP_ACTION_ORDERSTATUS_INSERT);
    }
}