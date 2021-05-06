<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class ReviewWshopTable extends WshopTable {
    public function __construct() {
        global $wpdb;
        parent::__construct($wpdb->prefix.'wshop_products_reviews', 'review_id'); 
    }

    function getAllowReview($type = null) {
        $config = Factory::getConfig();
		$user_id = get_current_user_id();

        if ($type){
            if(!$config->allow_reviews_manuf) {
                return 0;
            }
        } else {
            if(!$config->allow_reviews_prod) {
                return 0;
            }
        }
        if($config->allow_reviews_only_registered && !$user_id) {
            return -1;
        }
        return 1;
    }
	
    function getText() {
        $config = Factory::getConfig();
        // Not logged in
        if($this->getAllowReview() == -1) {
            return _WOP_SHOP_REVIEW_NOT_LOGGED;
        } else {
            return '';
        }
    }	
}