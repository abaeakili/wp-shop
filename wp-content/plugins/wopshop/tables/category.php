<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class CategoryWshopTable extends WshopTable{
    
    function __construct(){
        global $wpdb;
        parent::__construct($wpdb->prefix.'wshop_categories', 'category_id');   
    }
    
    function getSubCategories($parentId, $order = 'id', $ordering = 'asc', $publish = 0) {
        $config = Factory::getConfig();
//        $user = Factory::getUser();
        $add_where = ($publish)?(" AND category_publish = '1' "):("");
//        $groups = implode(',', $user->getAuthorisedViewLevels());
//        $add_where .=' AND access IN ('.$groups.')';
        if ($order=="id") $orderby = "category_id";
        if ($order=="name") $orderby = "`name_".$config->cur_lang."`";
        if ($order=="ordering") $orderby = "ordering";
        if (!$orderby) $orderby = "ordering";
        
        $query = "SELECT `name_".$config->cur_lang."` as name,`description_".$config->cur_lang."` as description,`short_description_".$config->cur_lang."` as short_description, category_id, category_publish, ordering, category_image FROM `$this->_tbl`
                   WHERE category_parent_id = '".  esc_sql($parentId)."' ".$add_where."
                   ORDER BY ".$orderby." ".$ordering;
        $categories = $this->_db->get_results($query);
        foreach($categories as $key=>$value){
            $categories[$key]->category_link = SEFLink('controller=category&task=view&category_id='.$categories[$key]->category_id);
        }        
        return $categories;
    }

    function getName() {
        $config = Factory::getConfig();
        $name = 'name_'.$config->cur_lang;
        return $this->$name;
    }
    
    function getDescription(){
        
        if (!$this->category_id){
            $this->getDescriptionMainPage();
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

    function getTreeChild() {
        $category_parent_id = $this->category_parent_id;
        $i = 0;
        $list_category = array();
        $list_category[$i] = new stdClass();
        $list_category[$i]->category_id = $this->category_id;
        $list_category[$i]->name = $this->name;
        $i++;
        while($category_parent_id) {
            $category = Factory::getTable('category');
            $category->load($category_parent_id);
            $list_category[$i] = new stdClass();
            $list_category[$i]->category_id = $category->category_id;
            $list_category[$i]->name = $category->getName();
            $category_parent_id = $category->category_parent_id;
            $i++;
        }
        $list_category = array_reverse($list_category);
        return $list_category;
    }

    function getAllCategories($publish = 1, $access = 1) {
//        $user = Factory::getUser();
        $where = array();
        if ($publish){
            $where[] = "category_publish = '1'";
        }
//        if ($access){
//            $groups = implode(',', $user->getAuthorisedViewLevels());
//            $where[] =' access IN ('.$groups.')';
//        }
        $add_where = "";
        if (count($where)){
            $add_where = " where ".implode(" and ", $where);
        }
        $query = "SELECT category_id, category_parent_id FROM `$this->_tbl` ".$add_where." ORDER BY ordering";
        return $this->_db->get_results($query);
    }

    function getChildCategories($order='id', $ordering='asc', $publish=1){
        return $this->getSubCategories($this->category_id, $order, $ordering, $publish);
    }

    function getSisterCategories($order, $ordering = 'asc', $publish = 1) {
        return $this->getSubCategories($this->category_parent_id, $order, $ordering, $publish);
    }

    function getTreeParentCategories($publish = 1, $access = 1){
//        $user = Factory::getUser();
        $cats_tree = array(); 
        $category_parent = $this->category_id;
        $where = array();
        if ($publish){
            $where[] = "category_publish = '1'";
        }
//        if ($access){
//            $groups = implode(',', $user->getAuthorisedViewLevels());
//            $where[] =' access IN ('.$groups.')';
//        }
        $add_where = "";
        if (count($where)){
            $add_where = "and ".implode(" and ", $where);
        }
        while($category_parent) {
            $cats_tree[] = $category_parent;
            $query = "SELECT category_parent_id FROM `$this->_tbl` WHERE category_id = '".  esc_sql($category_parent)."' ".$add_where;
            $rows = $this->_db->get_results($query);
            $category_parent = $rows[0]->category_parent_id;
        }
        return array_reverse($cats_tree);
    }

    function getProducts($filters, $order = null, $orderby = null, $limitstart = 0, $limit = 0) {
        $config = Factory::getConfig();        
        $adv_query = ""; $adv_from = ""; $adv_result = $this->getBuildQueryListProductDefaultResult();
        $this->getBuildQueryListProduct("category", "list", $filters, $adv_query, $adv_from, $adv_result);
        $order_query = $this->getBuildQueryOrderListProduct($order, $orderby, $adv_from);

        do_action_ref_array('onBeforeQueryGetProductList', array("category", &$adv_result, &$adv_from, &$adv_query, &$order_query, &$filters) );

        $query = "SELECT $adv_result FROM `".$this->_db->prefix."wshop_products` AS prod
                  LEFT JOIN `".$this->_db->prefix."wshop_products_to_categories` AS pr_cat USING (product_id)
                  $adv_from
                  WHERE pr_cat.category_id = '".  esc_sql($this->category_id)."' AND prod.product_publish = '1' ".$adv_query." ".$order_query;

        if ($limit)
            $query .= "  LIMIT $limitstart, $limit";

        $products = $this->_db->get_results($query);
        $products = listProductUpdateData($products);
        return $products;
    }

    function getCountProducts($filters){
        $config = Factory::getConfig();
        $adv_query = ""; $adv_from = ""; $adv_result = "count(*)";
        $this->getBuildQueryListProduct("category", "count", $filters, $adv_query, $adv_from, $adv_result);

        do_action_ref_array( 'onBeforeQueryCountProductList', array("category", &$adv_result, &$adv_from, &$adv_query, &$filters) );

        $query = "SELECT $adv_result FROM `".$this->_db->prefix."wshop_products_to_categories` AS pr_cat
                  INNER JOIN `".$this->_db->prefix."wshop_products` AS prod ON pr_cat.product_id = prod.product_id
                  $adv_from 
                  WHERE pr_cat.category_id = '".  esc_sql($this->category_id)."' AND prod.product_publish = '1' ".$adv_query;
        return $this->_db->get_var($query);
    }
    
    function getDescriptionMainPage(){
        $statictext = Factory::getTable("statictext");
        $row = $statictext->loadData("home");
        $this->description = $row->text;
        
        $seo = Factory::getTable("seo");
        $row = $seo->loadData("category");
        $this->meta_title = $row->title;
        $this->meta_keyword = $row->keyword;
        $this->meta_description = $row->description;
    }
    
    /**
    * get List Manufacturer for this category
    */
    function getManufacturers(){
        $config = Factory::getConfig();
        $user = Factory::getUser();
        $adv_query = "";
//        $groups = implode(',', $user->getAuthorisedViewLevels());
//        $adv_query .=' AND prod.access IN ('.$groups.')';
        if ($config->hide_product_not_avaible_stock){
            $adv_query .= " AND prod.product_quantity > 0";
        }
        if ($config->manufacturer_sorting==2){
            $order = 'name';
        }else{
            $order = 'man.ordering';
        }
        $query = "SELECT distinct man.manufacturer_id as id, man.`name_".$config->cur_lang."` as name FROM `".$this->_db->prefix."wshop_products` AS prod
                  LEFT JOIN `".$this->_db->prefix."wshop_products_to_categories` AS categ USING (product_id)
                  LEFT JOIN `".$this->_db->prefix."wshop_manufacturers` as man on prod.product_manufacturer_id=man.manufacturer_id 
                  WHERE categ.category_id = '".  esc_sql($this->category_id)."' AND prod.product_publish = '1' AND prod.product_manufacturer_id!=0 ".$adv_query." order by ".$order;
        $list = $this->_db->get_results($query);
        return $list;
           
    }    
}
?>