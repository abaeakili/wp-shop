<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class UserGroupWshopTable extends WshopTable{

    function __construct(){
        global $wpdb;
        parent::__construct($wpdb->prefix.'wshop_usergroups', 'usergroup_id');
    }
     
    function getDefaultUsergroup(){
        $query = "SELECT `usergroup_id` FROM `$this->_tbl` WHERE `usergroup_is_default`= '1'";
        return $this->_db->get_var($query);
    }
    
    function getList(){
        $config = Factory::getConfig();

        $lang = $config->cur_lang;
        $query = "SELECT *, `name_".$lang."` as name, `description_".$lang."` as description FROM `".$this->_tbl."`";
        $list = $this->_db->get_results($query);
        foreach($list as $k=>$v){
            if ($v->name==''){
                $list[$k]->name = $v->usergroup_name;
            }
        }
        return $list;
    }
}