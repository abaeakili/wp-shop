<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class ManufacturerWshopTable extends WshopTable{

    function __construct(){
        global $wpdb;
        parent::__construct($wpdb->prefix.'wshop_manufacturers', 'manufacturer_id');          
    }

	function getAllManufacturers($publish = 0, $order = "ordering", $dir ="asc" ) {
            $config = Factory::getConfig();
        if ($order=="id") $orderby = "manufacturer_id";
        if ($order=="name") $orderby = "name";
        if ($order=="ordering") $orderby = "ordering";
        if (!$orderby) $orderby = "ordering"; 
		$query_where = ($publish)?("WHERE manufacturer_publish = '1'"):("");
		$query = "SELECT manufacturer_id, manufacturer_url, manufacturer_logo, manufacturer_publish, `name_".$config->cur_lang."` as name, `description_".$config->cur_lang."` as description,  `short_description_".$config->cur_lang."` as short_description
				  FROM `$this->_tbl` $query_where ORDER BY ".$orderby." ".$dir;
		$list = $this->_db->get_results($query);
		
		foreach($list as $key=>$value){
            $list[$key]->link = SEFLink('controller=manufacturer&task=view&manufacturer_id='.$list[$key]->manufacturer_id);
        }		
		return $list;
	}
    
    function getList(){
        $config = Factory::getConfig();
        if ($config->manufacturer_sorting==2){
            $morder = 'name';
        }else{
            $morder = 'ordering';
        }
    return $this->getAllManufacturers(1, $morder, 'asc');
    }
	
	function getName() {
        $config = Factory::getConfig();
        $name = 'name_'.$config->cur_lang;
        return $this->$name;
    }
    
    function getDescription(){
        
        if (!$this->manufacturer_id){            
            return 1; 
        }
        
        $config = Factory::getConfig();
        $name = 'name_'.$config->cur_lang;        
        $description = 'description_'.$config->cur_lang;
        $short_description = 'short_description_'.$config->cur_lang;
        $meta_title = 'meta_title_'.$config->cur_lang;
        $meta_keyword = 'meta_keyword_'.$config->cur_lang;
        $meta_description = 'meta_description_'.$config->cur_lang;
        
        $this->name = $this->$name;
        $this->description = $this->$description;
        $this->short_description = $this->$short_description;
        $this->meta_title = $this->$meta_title;
        $this->meta_keyword = $this->$meta_keyword;
        $this->meta_description = $this->$meta_description;
    }
	
	function getProducts($filters, $order = null, $orderby = null, $limitstart = 0, $limit = 0){
        $config = Factory::getConfig();
        $adv_query = ""; $adv_from = ""; $adv_result = $this->getBuildQueryListProductDefaultResult();
        $this->getBuildQueryListProduct("manufacturer", "list", $filters, $adv_query, $adv_from, $adv_result);
        $order_query = $this->getBuildQueryOrderListProduct($order, $orderby, $adv_from);
        do_action_ref_array( 'onBeforeQueryGetProductList', array("manufacturer", &$adv_result, &$adv_from, &$adv_query, &$order_query, &$filters) );
        
        $query = "SELECT $adv_result FROM `".$this->_db->prefix."wshop_products` AS prod
                  LEFT JOIN `".$this->_db->prefix."wshop_products_to_categories` AS pr_cat USING (product_id)
                  LEFT JOIN `".$this->_db->prefix."wshop_categories` AS cat ON pr_cat.category_id = cat.category_id                  
                  $adv_from
                  WHERE prod.product_manufacturer_id = '".$this->manufacturer_id."' AND prod.product_publish = '1' AND cat.category_publish='1' ".$adv_query."
                  GROUP BY prod.product_id ".$order_query;
        if ($limit)
            $query .= "  LIMIT $limitstart, $limit";
        $products = $this->_db->get_results($query);
        $products = listProductUpdateData($products);
        return $products;
    }    
	
	function getCountProducts($filters) {
		$config = Factory::getConfig();
        $adv_query = ""; $adv_from = ""; $adv_result = "";
        $this->getBuildQueryListProduct("manufacturer", "count", $filters, $adv_query, $adv_from, $adv_result);

        do_action_ref_array( 'onBeforeQueryCountProductList', array("manufacturer", &$adv_result, &$adv_from, &$adv_query, &$filters) );
        
		$query = "SELECT COUNT(distinct prod.product_id) FROM `".$this->_db->prefix."wshop_products` as prod
                  LEFT JOIN `".$this->_db->prefix."wshop_products_to_categories` AS pr_cat USING (product_id)
                  LEFT JOIN `".$this->_db->prefix."wshop_categories` AS cat ON pr_cat.category_id = cat.category_id
                  $adv_from
                  WHERE prod.product_manufacturer_id = '".$this->manufacturer_id."' AND prod.product_publish = '1' AND cat.category_publish='1' ".$adv_query;
		return $this->_db->get_var($query);
	}
    
    /**
    * get List category
    */
    function getCategorys(){
        $config = Factory::getConfig();
//        $user = Factory::getUser();
        $adv_query = "";
//        $groups = implode(',', $user->getAuthorisedViewLevels());
//        $adv_query .=' AND prod.access IN ('.$groups.') AND cat.access IN ('.$groups.')';
        if ($config->hide_product_not_avaible_stock){
            $adv_query .= " AND prod.product_quantity > 0";
        }
        $query = "SELECT distinct cat.category_id as id, cat.`name_".$config->cur_lang."` as name FROM `".$this->_db->prefix."wshop_products` AS prod
                  LEFT JOIN `".$this->_db->prefix."wshop_products_to_categories` AS categ USING (product_id)
                  LEFT JOIN `".$this->_db->prefix."wshop_categories` as cat on cat.category_id=categ.category_id
                  WHERE prod.product_publish = '1' AND prod.product_manufacturer_id='".  esc_sql($this->manufacturer_id)."' AND cat.category_publish='1' ".$adv_query." order by name";

        $list = $this->_db->get_results($query);        
        return $list;
    }
}