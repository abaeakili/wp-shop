<?php
if ( ! defined( 'ABSPATH' ) ) {
 exit; // Exit if accessed directly
}

class ProductLabelsWshopAdminModel extends WshopAdminModel {
    public $string;
 
    public function __construct() {
            global $wpdb;
        $this->table_name = $wpdb->prefix.'wshop_product_labels';

        parent::__construct();
    }
  
    function getAllProductlabels($search = null, $publish='', $order='ordering', $orderDir='asc', $start, $limit){
        if($order == 'name') $order = esc_sql('`name_'.$this->lang.'`');
        global $wpdb;

        $where = 'WHERE `label_publish` != "-1" ';
        if($publish != '') $where.= ' AND `label_publish` = '.$publish;
        if($search){
            $where.= " AND `name_".$this->lang."` LIKE '%".$search."%'";
        }
        $productlabels = $wpdb->get_results( "SELECT *, `name_".$this->lang."` as name FROM ".$this->table_name." ".$where." ORDER BY $order $orderDir LIMIT $start,$limit" , OBJECT);
        //$wpdb->show_errors();
        //$wpdb->print_error(); 
        return $productlabels;
    }
    function getAllProductlabelsCount($search = null, $publish=''){
        global $wpdb;

        $where = 'WHERE `label_publish` != "-1" ';
        if($publish != '') $where.= ' AND `label_publish` = '.$publish;
        if($search){
            $where.= " AND `name_".$this->lang."` LIKE '%".$search."%'";
        }
        $productlabels = $wpdb->get_var( "SELECT COUNT(*) FROM ".$this->table_name." ".$where);
        //$wpdb->show_errors();
        //$wpdb->print_error(); 
        return $productlabels;
    }
    
    function getCountProductlabels($publish = ''){
        global $wpdb;

        $where = 'WHERE `label_publish` != "-1" ';
        
        if($publish != '') $where.= ' AND `label_publish` = '.$publish;

        $count = $wpdb->get_var( "SELECT COUNT(*) FROM ".$this->table_name." ".$where);
        
        //$wpdb->show_errors();
        //$wpdb->print_error(); 
        return $count;
    }

    function ProductlabelsActionPublish($action = '1', $rows = array()){
        global $wpdb;

        if(is_array($rows)){
            foreach($rows as $index=>$row){
                $wpdb->update( $this->table_name, array( 'label_publish' => esc_sql($action) ), array( 'id' => esc_sql($row) ), array( '%s', '%d' ), array( '%d' ) );
                //$wpdb->show_errors();
                //$wpdb->print_error();
            }
            return 'success';
        }else{
            addMessage(_WOP_SHOP_ERROR_BIND, 'error');
        }
    }
    function getDataProductlabel($id){
        global $wpdb;

        $where = 'WHERE `id` = '.esc_sql($id);

        $productlabel = $wpdb->get_row( "SELECT *, `name_".$this->lang."` as name FROM ".$this->table_name." ".$where , OBJECT);
        //$wpdb->show_errors();
        //$wpdb->print_error(); 
        return $productlabel;
    }
    
    function ProductlabelUpdate($post, $id){
        global $wpdb;
        $wpdb->update( 
            $this->table_name, 
            $post, 
            array( 
                'id' => $id
            )
        );
        //$wpdb->show_errors();
        //$wpdb->print_error(); 
    }
    
    function ProductlabelInsert($post){
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

    
    
    
    
    
    function getList($order = null, $orderDir = null){
        global $wpdb;

        $ordering = "name";
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $query = "SELECT id, image, `name_".$this->lang."` as name FROM `".$this->table_name."` ORDER BY ".$ordering;
        //$wpdb->show_errors();
        //$wpdb->print_error();
        return $wpdb->get_results($query, OBJECT);
    }
}
