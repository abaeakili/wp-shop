<?php
class CouponsWshopAdminModel extends WshopAdminModel {
    public $string;
 
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix.'wshop_coupons';

        parent::__construct();
    }

    function getAllCoupons($limitstart, $limit, $order = null, $orderDir = null) {
        global $wpdb;
        $queryorder = 'ORDER BY C.used, C.coupon_id desc';
        if ($order && $orderDir){
            $queryorder = "ORDER BY ".$order." ".$orderDir;
        }
        $query = "SELECT C.*, U.f_name, U.l_name FROM `".$wpdb->prefix."wshop_coupons` as C left join ".$wpdb->prefix."wshop_users as U on C.for_user_id=U.user_id ".$queryorder;
        if($limit) $query.= ' LIMIT '.$limitstart.', '.$limit;

        extract(ws_add_trigger(get_defined_vars(), "before"));
        return $wpdb->get_results($query, OBJECT);
    }
    function getCountCoupons(){
        global $wpdb;
        $query = "SELECT count(*) FROM `".$wpdb->prefix."wshop_coupons`";
		extract(ws_add_trigger(get_defined_vars(), "before"));
        return $wpdb->get_var($query);
    }
}