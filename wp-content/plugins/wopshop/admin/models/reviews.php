<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class ReviewsWshopAdminModel extends WshopAdminModel {
    public $string;
 
    public function __construct() {
        parent::__construct();
    }

    function getAllReviews($category_id = null, $product_id = null, $limitstart = null, $limit = null, $text_search = null, $result = "list", $vendor_id = 0, $order = null, $orderDir = null) {
		$config = Factory::getConfig();
        $lang = $config->cur_lang;//$lang = get_bloginfo('language');
        global $wpdb;
        $where = "";
        if ($product_id) $where .= " AND pr_rew.product_id='".esc_sql($product_id)."' ";
        if ($vendor_id) $where .= " AND pr.vendor_id='".esc_sql($vendor_id)."' ";

        if($limit > 0) {
            $limit = " LIMIT " . $limitstart . " , " . $limit;
        }
        $where .= ($text_search) ? ( " AND CONCAT_WS('|',pr.`name_".$lang."`,pr.`short_description_".$lang."`,pr.`description_".$lang."`,pr_rew.review, pr_rew.user_name, pr_rew.user_email ) LIKE '%".esc_sql($text_search)."%' " ) : ('');
        $ordering = 'pr_rew.review_id desc';

        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }

        if($category_id) {   
            $query = "select pr.`name_".$lang."` as name,pr_rew.* , DATE_FORMAT(pr_rew.`time`,'%d.%m.%Y') as dateadd 
            from  ".$wpdb->prefix."wshop_products_reviews as pr_rew
            LEFT JOIN ".$wpdb->prefix."wshop_products as pr USING (product_id)
            LEFT JOIN `".$wpdb->prefix."wshop_products_to_categories` AS pr_cat USING (product_id)
            WHERE pr_cat.category_id = '" . esc_sql($category_id) . "' ".$where." ORDER BY ". $ordering ." ". $limit;
        }else {
            $query = "select pr.`name_".$lang."` as name,pr_rew.*, DATE_FORMAT(pr_rew.`time`,'%d.%m.%Y') as dateadd 
            from  ".$wpdb->prefix."wshop_products_reviews as pr_rew
            LEFT JOIN ".$wpdb->prefix."wshop_products  as pr USING (product_id)            
            WHERE 1 ".$where." ORDER BY ". $ordering ." ". $limit;
        }
		extract(ws_add_trigger(get_defined_vars(), "before"));
        if ($result=="list"){
            return $wpdb->get_results($query);
        }else{
            return count($wpdb->get_results($query));
        }
    }
    
    function getReview($id){
        global $wpdb;
        $config = Factory::getConfig();
        $lang = $config->cur_lang;
        $query = "select pr_rew.*, pr.`name_".$lang."` as name from ".$wpdb->prefix."wshop_products_reviews as pr_rew LEFT JOIN ".$wpdb->prefix."wshop_products  as pr USING (product_id)  where pr_rew.review_id = '$id'";
        return $wpdb->get_row($query);
    }
    
    function getProdNameById($id){
		global $wpdb;
        $config = Factory::getConfig();
        $lang = $config->cur_lang;   
        $query = "select pr.`name_".$lang."` as name from ".$wpdb->prefix."wshop_products  as pr where pr.product_id = '$id' LIMIT 1";
        return $wpdb->get_row($query);
    }
    
    function deleteReview($id){
        global $wpdb;
        $wpdb->delete( $wpdb->prefix."wshop_products_reviews", array( 'review_id' => $id ) );
        return 1;
    }
}