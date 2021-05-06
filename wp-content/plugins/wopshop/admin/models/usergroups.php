<?php
class UserGroupsWshopAdminModel extends WshopAdminModel {
    public $string;
 
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix.'wshop_usergroups';
        parent::__construct();
    }

    function getAllUsergroups_($order=null, $orderDir=null){
        global $wpdb;

        $ordering = "usergroup_id";
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }

        $query = "SELECT * FROM `".$wpdb->prefix."wshop_usergroups` ORDER BY ".$ordering;
        return $wpdb->get_results($query);
    }
    function getAllUsergroups($order = null, $orderDir = null){
        $ordering = "usergroup_id";
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        global $wpdb;
        $query = "SELECT * FROM ".$this->table_name." ORDER BY ".$ordering;
		extract(ws_add_trigger(get_defined_vars(), "before"));
        return $wpdb->get_results($query, OBJECT);
    }
    function resetDefaultUsergroup(){
        global $wpdb;
        $query = "SELECT `usergroup_id` FROM `".$this->table_name."` WHERE `usergroup_is_default`= '1'";
        $usergroup_default = $wpdb->get_var($query);
        $query = "UPDATE `".$this->table_name."` SET `usergroup_is_default` = '0'";
		extract(ws_add_trigger(get_defined_vars(), "before"));
        $wpdb->get_results($query);
        return $usergroup_default;
    }
    function setDefaultUsergroup($usergroup_id){
        global $wpdb;
        $wpdb->update( $this->table_name, array( 'usergroup_is_default' => esc_sql('1') ), array( 'usergroup_id' => esc_sql($usergroup_id) ));
    }
    
    function getAllUsergroupsCount($search = null, $publish=''){
        global $wpdb;

        $where = 'WHERE `publish` != "-1" ';
        if($publish != '') $where.= ' AND `publish` = '.$publish;
        if($search){
            $where.= " AND `usergroup_name` LIKE '%".$search."%'";
        }
        $usergroups = $wpdb->get_var( "SELECT COUNT(*) FROM ".$this->table_name." ".$where);
        //$wpdb->show_errors();        $wpdb->print_error(); 
        return $usergroups;
    }
    
    function getCountUsergroups($publish = ''){
        global $wpdb;

        $where = 'WHERE `publish` != "-1" ';
        
        if($publish != '') $where.= ' AND `publish` = '.$publish;

        $count = $wpdb->get_var( "SELECT COUNT(*) FROM ".$this->table_name." ".$where);
        
        //$wpdb->show_errors();
        //$wpdb->print_error(); 
        return $count;
    }

    function UsergroupsActionPublish($action = '1', $rows = array()){
        global $wpdb;

        if(is_array($rows)){
            foreach($rows as $index=>$row){
                $default = $wpdb->get_var( "SELECT usergroup_is_default FROM ".$this->table_name." WHERE usergroup_id = ".esc_sql($row));
                if($default and $action != 1){
                    return 'error';
                }else{
                    $wpdb->update( $this->table_name, array( 'publish' => esc_sql($action) ), array( 'usergroup_id' => esc_sql($row) ), array( '%s', '%d' ), array( '%d' ) );
                    //$wpdb->show_errors();
                    //$wpdb->print_error();
                    return 'success';
                }
            }
        }
        addMessage(_WOP_SHOP_ERROR_BIND, 'error');
    }
    function UsergroupsActionDelete($rows = array()){
        global $wpdb;

        if(is_array($rows)){
            foreach($rows as $index=>$row){
                $default = $wpdb->get_var( "SELECT usergroup_is_default FROM ".$this->table_name." WHERE usergroup_id = ".esc_sql($row));
                if($default){
                    addMessage(_WOP_SHOP_ERROR_DELETE_USERGROUP_FAVORITE, 'error'); 
                }else{
                    $wpdb->update( $this->table_name, array( 'publish' => -1 ), array( 'usergroup_id' => esc_sql($row) ), array( '%s', '%d' ), array( '%d' ) );
                    //$wpdb->show_errors();
                    //$wpdb->print_error();
                    addMessage(_WOP_SHOP_ACTION_USERGROUP_DELETED); 
                }
            }
        }else{
            addMessage(_WOP_SHOP_ERROR_BIND, 'error');
        }
    }
    
    function UsergroupsActionSetDefault($rows = array()){
        global $wpdb;

        if(is_array($rows)){
            foreach($rows as $index=>$row){
                $publish = $wpdb->get_var( "SELECT publish FROM ".$this->table_name." WHERE usergroup_id = ".esc_sql($row));
                if($publish != '1'){
                    addMessage(_WOP_SHOP_ERROR_SET_USERGROUP_FAVORITE, 'error'); 
                }else{
                    $wpdb->query("UPDATE `".$this->table_name."` SET `usergroup_is_default` = '0'");
                    $wpdb->update( $this->table_name, array( 'usergroup_is_default' => 1 ), array( 'usergroup_id' => esc_sql($row) ), array( '%s', '%d' ), array( '%d' ) );
                    //$wpdb->show_errors();
                    //$wpdb->print_error();
                    addMessage(_WOP_SHOP_ACTION_USERGROUP_SETFAVORITE);
                }
            }
        }else{
            addMessage(_WOP_SHOP_ERROR_BIND, 'error');
        }
        
    }
    function getDataUsergroups($usergroup_id){
        global $wpdb;

        $where = 'WHERE `usergroup_id` = '.esc_sql($usergroup_id);

        $usergroups = $wpdb->get_row( "SELECT *, `usergroup_name` as name FROM ".$this->table_name." ".$where , OBJECT);
        //$wpdb->show_errors();
        //$wpdb->print_error(); 
        return $usergroups;
    }
    
    function UsergroupUpdate($post, $usergroup_id){
        global $wpdb;
        if((int)$usergroup_id > 0){
            $wpdb->update( 
                $this->table_name, 
                $post, 
                array( 
                    'usergroup_id' => $usergroup_id
                )
            );
            //$wpdb->show_errors();
            //$wpdb->print_error(); 
            addMessage(_WOP_SHOP_ACTION_USERGROUP_UPDATE);
        }else{
            addMessage(_WOP_SHOP_ERROR_BIND, 'error');
        }
    }
    
    function UsergroupInsert($post){
        global $wpdb;

        $count = $wpdb->get_var( "SELECT COUNT(*) FROM ".$this->table_name);
        
        $post['ordering'] = $count+1;
        $wpdb->insert( 
            $this->table_name, 
            $post
        );
        //$wpdb->show_errors();
        //$wpdb->print_error(); 
        addMessage(_WOP_SHOP_ACTION_USERGROUP_INSERT);
    }
}