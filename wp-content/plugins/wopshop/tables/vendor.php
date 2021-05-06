<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class VendorWshopTable extends WshopTable{
    function __construct(){
        global $wpdb;
        parent::__construct($wpdb->prefix.'wshop_vendors', 'id');
    }
    
    function loadMain(){
        $query = "SELECT id FROM ".$this->_db->prefix."wshop_vendors WHERE `main`=1";
        $id = intval($this->_db->get_row($query));        
        $this->load($id);
    }
    
    function loadFull($id){
        if ($id){
            $this->load($id);
        }else{
            $this->loadMain();
        }
    }
    

	function check(){            
	    if(trim($this->f_name) == '') {	    	
		    $this->setError(_WOP_SHOP_REGWARN_NAME);
		    return false;
	    }
        
        if( (trim($this->email == "")) || !is_email($this->email)) {
            $this->setError(_WOP_SHOP_REGWARN_MAIL);
            return false;
        }
        if ($this->user_id){
            $query = "SELECT id FROM ".$this->_db->prefix."wshop_vendors WHERE `user_id`='".esc_sql($this->user_id)."' AND id != '".(int)$this->id."'";
            $xid = intval($this->_db->get_var($query));
            if ($xid){
                $this->setError(sprintf(_WOP_SHOP_ERROR_SET_VENDOR_TO_MANAGER, $this->user_id));
                return false;
            }
        }
        
	return true;
	}
	
    function getAllVendors($publish=1, $limitstart, $limit) {
        $where = "";
        if (isset($publish)){
            $where = "and `publish`='".esc_sql($publish)."'";
        }
        $query = "SELECT * FROM `".$this->_db->prefix."wshop_vendors` where 1 ".$where." ORDER BY shop_name LIMIT ".$limitstart.", ".$limit;       
        return $this->_db->get_results($query);
    }
    
    function getCountAllVendors($publish=1){ 
        $where = "";
        if (isset($publish)){
            $where = "and `publish`='".esc_sql($publish)."'";
        }
        $query = "SELECT COUNT(id) FROM `".$this->_db->prefix."wshop_vendors` where 1 ".$where;
        return $this->_db->get_var($query);
    }
    
    function getProducts($filters, $order = null, $orderby = null, $limitstart = 0, $limit = 0){
        $config = Factory::getConfig();
        $lang = Factory::getLang();
        $adv_query = ""; $adv_from = ""; $adv_result = $this->getBuildQueryListProductDefaultResult();
        $this->getBuildQueryListProduct("vendor", "list", $filters, $adv_query, $adv_from, $adv_result);
        
        if ($this->main){
            $query_vendor_id = "(prod.vendor_id = '".$this->id."' OR prod.vendor_id ='0')";
        }else{
            $query_vendor_id = "prod.vendor_id = '".$this->id."'";
        }
        $order_query = $this->getBuildQueryOrderListProduct($order, $orderby, $adv_from);

        do_action_ref_array( 'onBeforeQueryGetProductList', array("vendor", &$adv_result, &$adv_from, &$adv_query, &$order_query, &$filters) );
        
        $query = "SELECT $adv_result FROM `".$this->_db->prefix."wshop_products` AS prod
                  LEFT JOIN `".$this->_db->prefix."wshop_products_to_categories` AS pr_cat USING (product_id)
                  LEFT JOIN `".$this->_db->prefix."wshop_categories` AS cat ON pr_cat.category_id = cat.category_id                  
                  $adv_from
                  WHERE ".$query_vendor_id." AND prod.product_publish = '1' AND cat.category_publish='1' ".$adv_query."
                  GROUP BY prod.product_id ".$order_query;
       if ($limit){
		   $query .= " LIMIT ".$limitstart.", ".$limit;
       }
       $products = $this->_db->get_results($query);
       $products = listProductUpdateData($products);
       return $products;
    }    
   
    function getCountProducts($filters) {
        $сonfig = Factory::getConfig();
        $adv_query = ""; $adv_from = ""; $adv_result = "";
        $this->getBuildQueryListProduct("vendor", "count", $filters, $adv_query, $adv_from, $adv_result);
        
        if ($this->main){
            $query_vendor_id = "(prod.vendor_id = '".$this->id."' OR prod.vendor_id ='0')";
        }else{
            $query_vendor_id = "prod.vendor_id = '".$this->id."'";
        }
        
        do_action_ref_array( 'onBeforeQueryCountProductList', array("vendor", &$adv_result, &$adv_from, &$adv_query, &$filters) );
         
        $query = "SELECT COUNT(distinct prod.product_id) FROM `".$this->_db->prefix."wshop_products` as prod
                  LEFT JOIN `".$this->_db->prefix."wshop_products_to_categories` AS pr_cat USING (product_id)
                  LEFT JOIN `".$this->_db->prefix."wshop_categories` AS cat ON pr_cat.category_id = cat.category_id
                  $adv_from
                  WHERE ".$query_vendor_id." AND prod.product_publish = '1' AND cat.category_publish='1' ".$adv_query;
		return $this->_db->get_var($query);
    }

    
}