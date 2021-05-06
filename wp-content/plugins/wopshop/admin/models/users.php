<?php
class UsersWshopAdminModel extends WshopAdminModel {
    public $string;
 
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix.'wshop_usergroups';
        parent::__construct();
    }

    function getAllUsers($limitstart, $limit, $text_search="", $order = null, $orderDir = null) {
        global $wpdb;
        $where = "";
        $queryorder = "";
        if ($text_search){
            $search = esc_sql($text_search);
            $where .= " and (U.u_name like '%".$search."%' or U.f_name like '%".$search."%' or U.l_name like '%".$search."%' or U.email like '%".$search."%' or U.firma_name like '%".$search."%'  or U.d_f_name like '%".$search."%'  or U.d_l_name like '%".$search."%'  or U.d_firma_name like '%".$search."%' or U.number='".$search."') ";
        }
        if ($order && $orderDir){
            $queryorder = "order by ".$order." ".$orderDir;
        }
        $query = "SELECT U.number, U.u_name, U.f_name, U.l_name, U.email, U.user_id, UG.usergroup_name FROM `".$wpdb->prefix."wshop_users` AS U
                 INNER JOIN `".$wpdb->prefix."users` AS UM ON U.user_id = UM.id
                 left join ".$wpdb->prefix."wshop_usergroups as UG on UG.usergroup_id=U.usergroup_id
                 where 1 ".$where." ".$queryorder.' LIMIT '.$limitstart.', '.$limit;
		extract(ws_add_trigger(get_defined_vars(), "before"));
        return $wpdb->get_results($query);
    }

    function getCountAllUsers($text_search=""){
        global $wpdb;
        $where = "";
        if ($text_search){
            $search = esc_sql($text_search);
            $where .= " and (U.u_name like '%".$search."%' or U.f_name like '%".$search."%' or U.l_name like '%".$search."%' or U.email like '%".$search."%' or U.firma_name like '%".$search."%'  or U.d_f_name like '%".$search."%'  or U.d_l_name like '%".$search."%'  or U.d_firma_name like '%".$search."%' or U.number='".$search."') ";
        }
        $query = "SELECT COUNT(U.user_id) FROM `".$wpdb->prefix."wshop_users` AS U
                 INNER JOIN `".$wpdb->prefix."users` AS UM ON U.user_id = UM.id where 1 ".$where;
		extract(ws_add_trigger(get_defined_vars(), "before"));
        return $wpdb->get_var($query);
    }
	
	function getUsers(){
		global $wpdb;
		$query = "SELECT U.`user_id`, concat(U.`f_name`,' ',U.`l_name`) as `name`
				  FROM `".$wpdb->prefix."wshop_users` as U INNER JOIN `".$wpdb->prefix."users` AS UM ON U.user_id=UM.id
				  ORDER BY U.`f_name`, U.`l_name`";
		extract(ws_add_trigger(get_defined_vars(), "before"));
		return $wpdb->get_results($query);
	}
}
?>