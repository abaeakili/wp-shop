<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class ProductsWshopAdminModel extends WshopAdminModel {
    public $table_name;
 
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix.'wshop_products';
        parent::__construct();
    }
    
    function _getAllProductsQueryForFilter($filter){
		$config = Factory::getConfig();
        $lang = $config->cur_lang;
		//$lang = get_bloginfo('language');
        global $wpdb;
        $where = "";
        if (isset($filter['without_product_id']) && $filter['without_product_id']){
            $where .= " AND pr.product_id <> '".esc_sql($filter['without_product_id'])."' ";    
        }
        if (isset($filter['category_id']) && $filter['category_id']){
            $category_id = $filter['category_id'];
            $where .= " AND pr_cat.category_id = '".esc_sql($filter['category_id'])."' ";    
        }
        if (isset($filter['text_search']) && $filter['text_search']){
            $text_search = $filter['text_search'];
            $word = addcslashes(esc_sql($text_search), "_%");
            $where .=  "AND (LOWER(pr.`name_".$lang."`) LIKE '%" . $word . "%' OR LOWER(pr.`short_description_".$lang."`) LIKE '%" . $word . "%' OR LOWER(pr.`description_".$lang."`) LIKE '%" . $word . "%' OR pr.product_ean LIKE '%" . $word . "%' OR pr.product_id LIKE '%" . $word . "%')";
        }
        if (isset($filter['manufacturer_id']) && $filter['manufacturer_id']){
            $where .= " AND pr.product_manufacturer_id = '".esc_sql($filter['manufacturer_id'])."' ";    
        }
        if (isset($filter['label_id']) && $filter['label_id']){
            $where .= " AND pr.label_id = '".esc_sql($filter['label_id'])."' ";    
        }
        if (isset($filter['publish']) && $filter['publish']){
            if ($filter['publish']==1) $_publish = 1; else $_publish = 0;            
            $where .= " AND pr.product_publish = '".esc_sql($_publish)."' ";
        }
        if (isset($filter['vendor_id']) && $filter['vendor_id'] >= 0){
            $where .= " AND pr.vendor_id = '".esc_sql($filter['vendor_id'])."' ";
        }
        extract(ws_add_trigger(get_defined_vars(), "after"));
    return $where;
    }
    
    function _allProductsOrder($order = null, $orderDir = null, $category_id = 0){
        if ($order && $orderDir){
            $fields = array("product_id"=>"pr.product_id", "name"=>"name",'category'=>"namescats","manufacturer"=>"man_name","vendor"=>"v_f_name","ean"=>"ean","qty"=>"pr.unlimited, qty","price"=>"pr.product_price","hits"=>"pr.hits","date"=>"pr.product_date_added", "product_name_image"=>"pr.product_name_image");
            if ($category_id) $fields['ordering'] = "pr_cat.product_ordering";
            if (strtolower($orderDir)!="asc") $orderDir = "desc";
            if ($orderDir=="desc") $fields['qty'] ='pr.unlimited desc, qty';
            if (!$fields[$order]) return "";
            return "order by ".$fields[$order]." ".$orderDir;
        }else{
            return "";
        }
    }
    
    function getAllProducts($filter, $limitstart = null, $limit = null, $order = null, $orderDir = null){
        $config = Factory::getConfig();
		$lang = $config->cur_lang;
        global $wpdb;
        if ($limit > 0){
            $limit = " LIMIT ".$limitstart.", ".$limit;
        }else{
            $limit = "";
        }        
        if (isset($filter['category_id'])) 
            $category_id = $filter['category_id'];
        else 
            $category_id = '';
        
        $where = $this->_getAllProductsQueryForFilter($filter);
        
        $query_filed = ""; $query_join = "";
        if ($config->admin_show_vendors){
            $query_filed .= ", pr.vendor_id, V.f_name as v_f_name, V.l_name as v_l_name";
            $query_join .= " left join `".$wpdb->prefix."wshop_vendors` as V on pr.vendor_id=V.id ";
        }

        if ($category_id) {
            $query = "SELECT pr.product_id, pr.product_publish, pr_cat.product_ordering, pr.`name_".$lang."` as name, pr.`short_description_".$lang."` as short_description, man.`name_".$lang."` as man_name, pr.product_ean as ean, pr.product_quantity as qty, pr.image as image, pr.product_price, pr.currency_id, pr.hits, pr.unlimited, pr.product_date_added, pr.label_id $query_filed FROM `".$wpdb->prefix."wshop_products` AS pr
                      LEFT JOIN `".$wpdb->prefix."wshop_products_to_categories` AS pr_cat USING (product_id)
                      LEFT JOIN `".$wpdb->prefix."wshop_manufacturers` AS man ON pr.product_manufacturer_id=man.manufacturer_id
                      $query_join
                      WHERE pr.parent_id=0 ".$where." ".$this->_allProductsOrder($order, $orderDir, $category_id)." ".$limit;
        }else{
            $spec_where = "GROUP_CONCAT(cat.`name_".$lang."` SEPARATOR '<br>') AS namescats";

            $query = "SELECT pr.product_id, pr.product_publish, pr.`name_".$lang."` as name, pr.`short_description_".$lang."` as short_description, man.`name_".$lang."` as man_name, ".$spec_where.", pr.product_ean as ean, pr.product_quantity as qty, pr.image as image, pr.product_price, pr.currency_id, pr.hits, pr.unlimited, pr.product_date_added, pr.label_id $query_filed FROM `".$wpdb->prefix."wshop_products` AS pr 
                      LEFT JOIN `".$wpdb->prefix."wshop_products_to_categories` AS pr_cat USING (product_id)
                      LEFT JOIN `".$wpdb->prefix."wshop_categories` AS cat ON pr_cat.category_id=cat.category_id
                      LEFT JOIN `".$wpdb->prefix."wshop_manufacturers` AS man ON pr.product_manufacturer_id=man.manufacturer_id
                      $query_join
                      WHERE pr.parent_id=0 ".$where." GROUP BY pr.product_id ".$this->_allProductsOrder($order, $orderDir)." ".$limit;
        }
        do_action_ref_array('onBeforeDisplayListProductsGetAllProducts', array(&$this, &$query, $filter, $limitstart, $limit, $order, $orderDir));
        return $wpdb->get_results( $query , OBJECT);
    }
    
    function getCountAllProducts($filter){
        global $wpdb;
        
        $category_id = $filter['category_id'];
        $where = $this->_getAllProductsQueryForFilter($filter);
        if ($category_id) {
            $query = "SELECT count(pr.product_id) FROM `".$wpdb->prefix."wshop_products` AS pr
                      LEFT JOIN `".$wpdb->prefix."wshop_products_to_categories` AS pr_cat USING (product_id)
                      LEFT JOIN `".$wpdb->prefix."wshop_manufacturers` AS man ON pr.product_manufacturer_id=man.manufacturer_id
                      WHERE pr.parent_id=0 ".$where;
        } else {
            $query = "SELECT count(pr.product_id) FROM `".$wpdb->prefix."wshop_products` AS pr
                      LEFT JOIN `".$wpdb->prefix."wshop_manufacturers` AS man ON pr.product_manufacturer_id=man.manufacturer_id
                      WHERE pr.parent_id=0 ".$where;
        }
		do_action_ref_array('onBeforeDisplayListProductsGetCountAllProducts', array(&$this, &$query, $filter));
        return $wpdb->get_var($query);
    }
    
    function productInCategory($product_id, $category_id) {
        global $wpdb;
        $query = "SELECT prod_cat.category_id FROM `".$wpdb->prefix."wshop_products_to_categories` AS prod_cat
                   WHERE prod_cat.product_id = '".esc_sql($product_id)."' AND prod_cat.category_id = '".esc_sql($category_id)."'";
        $res = $wpdb->get_results($query, OBJECT);
        return count($res);
    }
    
    function getMaxOrderingInCategory($category_id) {
        global $wpdb;
        $query = "SELECT MAX(product_ordering) as k FROM `".$wpdb->prefix."wshop_products_to_categories` WHERE category_id = '".esc_sql($category_id)."'";
        return $wpdb->get_var($query);
    }
    
    function setCategoryToProduct($product_id, $categories = array()){
        global $wpdb;
        if(is_array($categories) and count($categories) > 0)
        foreach($categories as $cat_id){
            if (!$this->productInCategory($product_id, $cat_id)){
                $ordering = $this->getMaxOrderingInCategory($cat_id)+1;
                $wpdb->insert($wpdb->prefix."wshop_products_to_categories", array('product_id' => esc_sql($product_id), 'category_id' => esc_sql($cat_id), 'product_ordering' => esc_sql($ordering)));
            }
        }
        //delete other cat for product
        $query = "select `category_id` from `".$wpdb->prefix."wshop_products_to_categories` where `product_id` = '".esc_sql($product_id)."'";
        $listcat = $wpdb->get_results($query, OBJECT);
        foreach($listcat as $val){
            if (!in_array($val->category_id, $categories)){
                $wpdb->delete( $wpdb->prefix."wshop_products_to_categories", array( 'product_id' => esc_sql($product_id), 'category_id' => esc_sql($val->category_id)));
            }
        }
    }
    
    function getRelatedProducts($product_id){
        global $wpdb;
		$config = Factory::getConfig();
		$lang = $config->cur_lang;
        $lang = get_bloginfo('language');
        $query = "SELECT relation.product_related_id AS product_id, prod.`name_".$lang."` as name, prod.image as image
                FROM `".$wpdb->prefix."wshop_products_relations` AS relation
                LEFT JOIN `".$wpdb->prefix."wshop_products` AS prod ON prod.product_id=relation.product_related_id
                WHERE relation.product_id = '".esc_sql($product_id)."' order by relation.id";
		extract(ws_add_trigger(get_defined_vars(), "before"));
        return $wpdb->get_results($query, OBJECT);
    }
    
    function saveAditionalPrice($product_id, $product_add_discount, $quantity_start, $quantity_finish){
        global $wpdb;
        $wpdb->delete( $wpdb->prefix."wshop_products_prices", array( 'product_id' => $product_id ) );
        $counter = 0;
        if (count($product_add_discount)){
            foreach ($product_add_discount as $key=>$value){
                if ((!$quantity_start[$key] && !$quantity_finish[$key])) continue;

                $wpdb->insert( $wpdb->prefix.'wshop_products_prices', 
                        array(  'product_id' => $product_id, 'discount' => saveAsPrice($product_add_discount[$key]), 'product_quantity_start' => intval($quantity_start[$key]), 'product_quantity_finish' => intval($quantity_finish[$key])));
                $counter++;
            }
        }
        $product = Factory::getTable('product');
        $product->load($product_id);
        $product->product_is_add_price = ($counter>0) ? (1) : (0);
        $product->store();
    }
    
    function saveFreeAttributes($product_id, $attribs){
        global $wpdb;
        $wpdb->delete( $wpdb->prefix.'wshop_products_free_attr', array('product_id' => $product_id));

        if (is_array($attribs)){
            foreach($attribs as $attr_id=>$v){
                $wpdb->insert( $wpdb->prefix.'wshop_products_free_attr', array('product_id' => $product_id, 'attr_id' => $attr_id));
                //$wpdb->show_errors(); $wpdb->print_error();
            }
        }
    }
    
    function saveProductOptions($product_id, $options){
        global $wpdb;
        foreach($options as $key=>$value){
            $wpdb->delete( $wpdb->prefix."wshop_products_option", array( 'product_id' => $product_id, 'key' => $key) );
            $wpdb->insert( $wpdb->prefix."wshop_products_option", array( 'product_id' => $product_id, 'key' => $key, 'value' => $value));
        }
    }
    
    function getMinimalPrice($price, $attrib_prices, $attrib_ind_price_data, $is_add_price, $add_discounts){
        $config = Factory::getConfig();
        
        $minprice = $price;
        if (is_array($attrib_prices)){            
            $minprice = min($attrib_prices);            
        }
        
        if (is_array($attrib_ind_price_data[0])){
            $attr_ind_id = array_unique($attrib_ind_price_data[0]);
            $startprice = $minprice;
            foreach($attr_ind_id as $attr_id){
                $tmpprice = array();
                foreach($attrib_ind_price_data[0] as $k=>$tmp_attr_id){
                    if ($tmp_attr_id==$attr_id){
                        if ($attrib_ind_price_data[1][$k]=="+"){
                            $tmpprice[] = $startprice + $attrib_ind_price_data[2][$k];
                        }elseif ($attrib_ind_price_data[1][$k]=="-"){
                            $tmpprice[] = $startprice - $attrib_ind_price_data[2][$k];
                        }elseif ($attrib_ind_price_data[1][$k]=="*"){
                            $tmpprice[] = $startprice * $attrib_ind_price_data[2][$k];
                        }elseif ($attrib_ind_price_data[1][$k]=="/"){
                            $tmpprice[] = $startprice / $attrib_ind_price_data[2][$k];
                        }elseif ($attrib_ind_price_data[1][$k]=="%"){
                            $tmpprice[] = $startprice * $attrib_ind_price_data[2][$k] / 100;
                        }elseif ($attrib_ind_price_data[1][$k]=="="){
                            $tmpprice[] = $attrib_ind_price_data[2][$k];
                        }
                    }
                }
                $startprice = min($tmpprice);
            }
            $minprice = $startprice;
        }
        
        if ($is_add_price && is_array($add_discounts)){
            $max_discount = max($add_discounts);
            if ($config->product_price_qty_discount == 1){
                $minprice = $minprice - $max_discount; //discount value
            }else{
                $minprice = $minprice - ($minprice * $max_discount / 100); //discount percent
            }            
        }
		extract(ws_add_trigger(get_defined_vars(), "before"));
        return $minprice;
    }
    
    function copyProductBuildQuery($table, $array, $product_id){
        global $wpdb;
        $query = "INSERT INTO `".$wpdb->prefix."wshop_products_".$table."` SET ";
        $array_keys = array('image_id', 'price_id', 'review_id', 'video_id', 'product_attr_id', 'value_id', 'id');
        foreach ($array as $key => $value){
            if (in_array($key, $array_keys)) {
                continue;
            }
            if ($key == 'product_id') {
                $value = $product_id;
            }
            $query .= "`".$key."` = '".esc_sql($value)."', ";
        }
		extract(ws_add_trigger(get_defined_vars(), "before"));
        return $query = substr($query, 0, strlen($query) - 2);
    }
    
    function uploadVideo($product, $product_id, $post){
        $config = Factory::getConfig();
        $image_prev_video = "";
        for($i=0;$i<$config->product_video_upload_count;$i++){
            if (!(isset($post['product_insert_code_'.$i]) && ($post['product_insert_code_'.$i] == 1))) {
                $upload = new UploadFile($_FILES['product_video_'.$i]);
                $upload->setDir($config->video_product_path);
                $upload->setFileNameMd5(0);
                $upload->setFilterName(1);
                if ($upload->upload()){
                    $file_video = $upload->getName();
                    @chmod($config->video_product_path."/".$file_video, 0777);
					
                    $upload2 = new UploadFile($_FILES['product_video_preview_'.$i]);
                    $upload2->setAllowFile(array('jpeg','jpg','gif','png'));
                    $upload2->setDir($config->video_product_path);
                    $upload2->setFileNameMd5(0);
                    $upload2->setFilterName(1);
                    if ($upload2->upload()){
                        $image_prev_video = $upload2->getName();
                        @chmod($config->video_product_path."/".$image_prev_video, 0777);
                    }else{
                        if ($upload2->getError() != 4){
                            addMessage(_WOP_SHOP_ERROR_UPLOADING_VIDEO_PREVIEW);
                                //saveToLog("error.log", "SaveProduct - Error upload video preview. code: ".$upload2->getError());
                        }    
                    }
                        unset($upload2);
                        $this->addToProductVideo($product_id, $file_video, $image_prev_video);
                }else{
                    if ($upload->getError() != 4){
                        addMessage(_WOP_SHOP_ERROR_UPLOADING_VIDEO);
                            //saveToLog("error.log", "SaveProduct - Error upload video. code: ".$upload->getError());
                        }
                }
                unset($upload);
            } else {
                $code_video = Request::getVar('product_video_code_'.$i, null, 'default', 'none', JREQUEST_ALLOWRAW);
                if ($code_video) {
                    $upload2 = new UploadFile($_FILES['product_video_preview_'.$i]);
                    $upload2->setAllowFile(array('jpeg','jpg','gif','png'));
                    $upload2->setDir($config->video_product_path);
                    $upload2->setFileNameMd5(0);
                    $upload2->setFilterName(1);
                    if ($upload2->upload()){
                            $image_prev_video = $upload2->getName();
                            @chmod($config->video_product_path."/".$image_prev_video, 0777);
                    }else{
                        if ($upload2->getError() != 4){
                            addMessage(_WOP_SHOP_ERROR_UPLOADING_VIDEO_PREVIEW);
                            //saveToLog("error.log", "SaveProduct - Error upload video preview. code: ".$upload2->getError());
                        }    
                    }
                    unset($upload2);
                    $this->addToProductVideoCode($product_id, $code_video, $image_prev_video);
                }
            }
        }
    }
    
    function addToProductVideo($product_id, $name_video, $preview_image = '') {
        global $wpdb;
        $wpdb->insert( $wpdb->prefix.'wshop_products_videos', array('product_id' => $product_id, 'video_name' => $name_video, 'video_preview' => $preview_image));
    }
    
    function addToProductVideoCode($product_id, $code_video, $preview_image = '') {
        global $wpdb;
        $wpdb->insert( $wpdb->prefix.'wshop_products_videos', array('product_id' => $product_id, 'video_code' => $code_video, 'video_preview' => $preview_image));
    }
    
    function uploadImages($product, $product_id, $post){
        $config = Factory::getConfig();

        for($i=0; $i<$config->product_image_upload_count; $i++){
            $upload = new UploadFile($_FILES['product_image_'.$i]);
            $upload->setAllowFile(array('jpeg','jpg','gif','png'));
            $upload->setDir($config->image_product_path);
            $upload->setFileNameMd5(0);
            $upload->setFilterName(1);
            if ($upload->upload()){
                $name_image = $upload->getName();
                $name_thumb = 'thumb_'.$name_image;
                $name_full = 'full_'.$name_image;
                @chmod($config->image_product_path."/".$name_image, 0777);

                $path_image = $config->image_product_path."/".$name_image;
                $path_thumb = $config->image_product_path."/".$name_thumb;
                $path_full =  $config->image_product_path."/".$name_full;
                rename($path_image, $path_full);

                if ($config->image_product_original_width || $config->image_product_original_height){
                    if (!ImageLib::resizeImageMagic($path_full, $config->image_product_original_width, $config->image_product_original_height, $config->image_cut, $config->image_fill, $path_full, $config->image_quality, $config->image_fill_color)){
                        addMessage(_WOP_SHOP_ERROR_CREATE_THUMBAIL, 'error');
                        //saveToLog("error.log", "SaveProduct - Error create thumbail");
                        $error = 1;
                    }
                }

                $error = 0;

                if ($post['size_im_product']==3){
                    copy($path_full, $path_thumb);
                    @chmod($path_thumb, 0777);
                }else{
                    if ($post['size_im_product']==1){
                        $product_width_image = $config->image_product_width;
                        $product_height_image = $config->image_product_height;
                    }else{
                        $product_width_image = Request::getInt('product_width_image'); 
                        $product_height_image = Request::getInt('product_height_image');
                    }
                    
                    if ($product_width_image || $product_height_image){
                        if (!ImageLib::resizeImageMagic($path_full, $product_width_image, $product_height_image, $config->image_cut, $config->image_fill, $path_thumb, $config->image_quality, $config->image_fill_color)){
                            addMessage(_WOP_SHOP_ERROR_CREATE_THUMBAIL, 'error');
                            //saveToLog("error.log", "SaveProduct - Error create thumbail");
                            $error = 1;
                        }    
                        @chmod($path_thumb, 0777);
                    }
                }

                if ($post['size_full_product']==3){
                    copy($path_full, $path_image);
                    @chmod($path_image, 0777);
                }else{
                    if ($post['size_full_product']==1){
                        $product_full_width_image = $config->image_product_full_width; 
                        $product_full_height_image = $config->image_product_full_height;
                    }else{
                        $product_full_width_image = Request::getInt('product_full_width_image'); 
                        $product_full_height_image = Request::getInt('product_full_height_image');
                    }

                    if ($product_full_width_image || $product_full_height_image){
                        if (!ImageLib::resizeImageMagic($path_full, $product_full_width_image, $product_full_height_image, $config->image_cut, $config->image_fill, $path_image, $config->image_quality, $config->image_fill_color)){
                            addMessage(_WOP_SHOP_ERROR_CREATE_THUMBAIL, 'error');
                            $error = 1;
                        }    
                        @chmod($path_image, 0777);
                    }
                }

                if (!$error){
                    $this->addToProductImage($product_id, $name_image, $post["product_image_descr_".$i]);
                    do_action_ref_array('onAfterSaveProductImage', array($product_id, $name_image));
                }
            }else{
                if ($upload->getError() != 4){
                    addMessage(_WOP_SHOP_ERROR_UPLOADING_IMAGE, 'error');
                    //saveToLog("error.log", "SaveProduct - Error upload image. code: ".$upload->getError());
                }
            }

            unset($upload);    
        }        
        for($i=0; $i<$config->product_image_upload_count; $i++){
            if ($post['product_folder_image_'.$i] != '') {
                if (file_exists($config->image_product_path .'/'.$post['product_folder_image_'.$i])) {
                    $name_image = $post['product_folder_image_'.$i];
                    $name_thumb = 'thumb_'.$name_image;
                    $name_full = 'full_'.$name_image;
                    $this->addToProductImage($product_id, $name_image, $post["product_image_descr_".$i]);
                    do_action_ref_array('onAfterSaveProductFolerImage', array($product_id, $name_full, $name_image, $name_thumb));
                }
            }
        }
		
        if (!$product->image){
            $list_images = $product->getImages();
            if (count($list_images)){
                $product = Factory::getTable('product');
                $product->load($product_id);
                $product->image = $list_images[0]->image_name;
                $product->store();
            }
        }

        if (isset($post['old_image_descr'])){
            $this->renameProductImageOld($post['old_image_descr'], $post['old_image_ordering']);
        }

    }
    
    function addToProductImage($product_id, $name_image, $image_descr) {
        $image = Factory::getTable('image');
        $image->set("image_id", 0);
        $image->set("product_id", $product_id);
        $image->set("image_name", $name_image);
        $image->set("name", $image_descr);
        $image->set("ordering", $image->getNextOrder("product_id='".intval($product_id)."'"));
        $image->store();
    }

    function renameProductImageOld($image_descr, $image_ordering){
        global $wpdb;
        foreach($image_descr as $id=>$v){
            $wpdb->update( $wpdb->prefix."wshop_products_images", array( 'name' => esc_sql($image_descr[$id]), 'ordering' => esc_sql($image_ordering[$id]) ), array( 'image_id' => esc_sql($id) ));
            //$wpdb->show_errors(); $wpdb->print_error();
        }
    }
    
    function uploadFiles($product, $product_id, $post){
        $config = Factory::getConfig();
        if (!isset($post['product_demo_descr'])) {
            $post['product_demo_descr'] = '';
        }
        if (!isset($post['product_file_descr'])) {
            $post['product_file_descr'] = '';
        }
        if (!isset($post['product_file_sort'])) {
            $post['product_file_sort'] = '';
        }
        
        for ($i = 0; $i < $config->product_file_upload_count; $i++){
            $file_demo = "";
            $file_sale = "";
            if ($config->product_file_upload_via_ftp != 1){
                $upload = new UploadFile($_FILES['product_demo_file_'.$i]);
                $upload->setDir($config->demo_product_path);
                $upload->setFileNameMd5(0);
                $upload->setFilterName(1);
                if ($upload->upload()){
                    $file_demo = $upload->getName();
                    @chmod($config->demo_product_path."/".$file_demo, 0777);
                } else {
                    if ($upload->getError() != 4){
                        addMessage(_WOP_SHOP_ERROR_UPLOADING_FILE_DEMO, 'error');
                        saveToLog("error.log", "SaveProduct - Error upload demo. code: ".$upload->getError());    
                    }    
                }
                unset($upload);
                
                $upload = new UploadFile($_FILES['product_file_'.$i]);
                $upload->setDir($config->files_product_path);
                $upload->setFileNameMd5(0);
                $upload->setFilterName(1);
                if ($upload->upload()){
                    $file_sale = $upload->getName();
                    @chmod($config->files_product_path."/".$file_sale, 0777);
                } else {
                    if ($upload->getError() != 4){
                        addMessage(_WOP_SHOP_ERROR_UPLOADING_FILE_SALE, 'error');
                        saveToLog("error.log", "SaveProduct - Error upload file sale. code: ".$upload->getError());    
                    }    
                }
                unset($upload);
            }
            
            if (!$file_demo && isset($post['product_demo_file_name_'.$i]) && $post['product_demo_file_name_'.$i]){
                $file_demo = $post['product_demo_file_name_'.$i];
            }
            
            if (!$file_sale && isset($post['product_file_name_'.$i]) && $post['product_file_name_'.$i]){
                $file_sale = $post['product_file_name_'.$i];
            }
            
            if ($file_demo != "" || $file_sale != ""){
                $this->addToProductFiles($product_id, $file_demo, $post['product_demo_descr_'.$i], $file_sale, $post['product_file_descr_'.$i], $post['product_file_sort_'.$i]);
            }
        }
        
        //Update description files
        $this->productUpdateDescriptionFiles($post['product_demo_descr'], $post['product_file_descr'], $post['product_file_sort']);
    }

    private function addToProductFiles($product_id, $file_demo, $demo_descr, $file_sale, $file_descr, $sort){
        global $wpdb;
        $wpdb->insert($wpdb->prefix."wshop_products_files", array('product_id' => $product_id, 'demo' => $file_demo, 'demo_descr' => $demo_descr, 'file' => $file_sale, 'file_descr' => $file_descr, 'ordering' => $sort));
    }
    
    private function productUpdateDescriptionFiles($demo_descr, $file_descr, $ordering){
        global $wpdb;
        if (is_array($demo_descr)){
            foreach ($demo_descr as $file_id => $value){
                $wpdb->update($wpdb->prefix."wshop_products_files", array('demo_descr' => $demo_descr[$file_id], 'file_descr' => $file_descr[$file_id], 'ordering' => $ordering[$file_id]), array('id' => $file_id));
            }
        }
    }
    
    function saveAttributes($product, $product_id, $post){
        $productAttribut = Factory::getTable('productattribut');
        $productAttribut->set("product_id", $product_id);
        
        $list_exist_attr = $product->getAttributes();
        if (isset($post['product_attr_id'])){
            $list_saved_attr = $post['product_attr_id'];
        }else{
            $list_saved_attr = array();
        }        
        foreach($list_exist_attr as $v){
            if (!in_array($v->product_attr_id, $list_saved_attr)){
                $productAttribut->deleteAttribute($v->product_attr_id);
            }
        }
        
        if (is_array($post['attrib_price'])){
            foreach($post['attrib_price'] as $k=>$v){
                $a_price = saveAsPrice($post['attrib_price'][$k]);
                $a_old_price = saveAsPrice($post['attrib_old_price'][$k]);
                $a_buy_price = saveAsPrice($post['attrib_buy_price'][$k]);
                $a_count = $post['attr_count'][$k];
                $a_ean = $post['attr_ean'][$k];
                $a_weight_volume_units = $post['attr_weight_volume_units'][$k];
                $a_weight = $post['attr_weight'][$k];
                
                if ($post['product_attr_id'][$k]){
                    $productAttribut->load($post['product_attr_id'][$k]);
                }else{
                    $productAttribut->set("product_attr_id", 0);
                    $productAttribut->set("ext_attribute_product_id", 0);
                }
                $productAttribut->set("price", $a_price);
                $productAttribut->set("old_price", $a_old_price);
                $productAttribut->set("buy_price", $a_buy_price);
                $productAttribut->set("count", $a_count);
                $productAttribut->set("ean", $a_ean);
                $productAttribut->set("weight_volume_units", $a_weight_volume_units);
                $productAttribut->set("weight", $a_weight);
                foreach($post['attrib_id'] as $field_id=>$val){
                    $productAttribut->set("attr_".intval($field_id), $val[$k]);
                }
                do_action_ref_array('onBeforeProductAttributStore', array(&$productAttribut, &$product, &$product_id, &$post));
                if ($productAttribut->check()){
                    $productAttribut->store();
                }
            }
        }        
        
        $productAttribut2 = Factory::getTable('productattribut2');
        $productAttribut2->set("product_id", $product_id);
        $productAttribut2->deleteAttributeForProduct();

        if (is_array($post['attrib_ind_id'])){
            foreach($post['attrib_ind_id'] as $k=>$v){
                $a_id = intval($post['attrib_ind_id'][$k]);
                $a_value_id = intval($post['attrib_ind_value_id'][$k]);
                $a_price = saveAsPrice($post['attrib_ind_price'][$k]);
                $a_mod_price = $post['attrib_ind_price_mod'][$k];

                $productAttribut2->set("id", 0);
                $productAttribut2->set("product_id", $product_id);
                $productAttribut2->set("attr_id", $a_id);
                $productAttribut2->set("attr_value_id", $a_value_id);
                $productAttribut2->set("price_mod", $a_mod_price);
                $productAttribut2->set("addprice", $a_price);
                do_action_ref_array('onBeforeProductAttribut2Store', array(&$productAttribut2, &$product, &$product_id, &$post));
                if ($productAttribut2->check()){
                    $productAttribut2->store();
                }
            }
        }
    }
    
    
    function saveRelationProducts($product, $product_id, $post){
        global $wpdb;

        if ($post['edit']) {
            $wpdb->delete( $wpdb->prefix."wshop_products_relations", array( 'product_id' => $product_id ) );
        }

        $post['related_products'] = array_unique($post['related_products']);
        foreach($post['related_products'] as $key => $value){
            if ($value!=0){
                $wpdb->insert( $wpdb->prefix."wshop_products_relations", array( 'product_id' => $product_id, 'product_related_id' => $value));
            }
        }
    }
    
    function getModPrice($price, $newprice, $mod){
        $result = 0;
        switch($mod){
            case '=':
            $result = $newprice;
            break;
            case '+':
            $result = $price + $newprice;
            break;
            case '-':
            $result = $price - $newprice;
            break;
            case '*':
            $result = $price * $newprice;
            break;
            case '/':
            $result = $price / $newprice;
            break;
            case '%':
            $result = $price * $newprice / 100;
            break;
        }
    return $result;
    }
    
    function updatePriceAndQtyDependAttr($product_id, $post){
        global $wpdb;
        $_adv_query = array();
        if ($post['product_price']!=""){
            $price = saveAsPrice($post['product_price']);
            if ($post['mod_price']=='%') 
                $_adv_query[] = " `price`=`price` * '".$price."' / 100 ";
            elseif($post['mod_price']=='=') 
                $_adv_query[] = " `price`= '".$price."' ";
            else 
                $_adv_query[] = " `price`=`price` ".$post['mod_price']." '".$price."' ";
        }
        
        if ($post['product_old_price']!=""){
            $price = saveAsPrice($post['product_old_price']);
            if ($post['mod_old_price']=='%') 
                $_adv_query[] = " `old_price`=`old_price` * '".$price."' / 100 ";
            elseif($post['mod_old_price']=='=') 
                $_adv_query[] = " `old_price`= '".$price."' ";
            else 
                $_adv_query[] = " `old_price`=`old_price` ".$post['mod_old_price']." '".$price."' ";
        }

        if ($post['product_quantity']!=""){
            $_adv_query[] = " `count`= '".$wpdb->esc_sql($post['product_quantity'])."' ";
        }
        
        if (count($_adv_query)>0){
            $adv_query = implode(" , ", $_adv_query);
			$wpdb->update( $wpdb->prefix."wshop_products_attr",
			$_adv_query,
			array( 'product_id' => $wpdb->esc_sql($product_id) )
			);
        }
    }
    
    public function copyProducts($cid){
        $text = array();
        do_action_ref_array('onBeforeCopyProduct', array(&$cid));
        
        foreach ($cid as $key => $value){
            $product = $this->copyProduct($value);
			do_action_ref_array('onCopyProductEach', array(&$cid, &$key, &$value, &$product));
            $text[] = sprintf(_WOP_SHOP_PRODUCT_COPY_TO, $value, $product->product_id)."<br>";
        }
        
        do_action_ref_array('onAfterCopyProduct', array(&$cid));
        return $text;
    }
    
    public function copyProduct($pid){
        global $wpdb;
        $languages = Factory::getAdminModel("languages")->getAllLanguages(1);
        $tables = array('attr', 'attr2', 'images', 'prices', 'relations', 'to_categories', 'videos', 'files', 'free_attr');
        do_action_ref_array('onBeforeStartCopyProduct', array(&$pid, &$tables, &$languages));

        $product = Factory::getTable('product');
        $product->load($pid);
        $product->product_id = null;
        $product->product_publish = 0;
        foreach($languages as $lang){
            $name_alias = 'alias_'.$lang->language;
            if ($product->$name_alias){
                $product->$name_alias = $product->$name_alias.date('ymdHis');
            }
        }
        $product->product_date_added = getJsDate();
        $product->date_modify = getJsDate();
        $product->average_rating = 0;
        $product->reviews_count = 0;
        $product->hits = 0;
        $product->store();

        $array = array();
        foreach ($tables as $table){
            $query = "SELECT * FROM `".$wpdb->prefix."wshop_products_".$table."` AS prod_table WHERE prod_table.product_id = ".(int)$pid;
            $array[] = $wpdb->get_results($query, ARRAY_A);
        }

        $i = 0;
        foreach ($array as $value2){
            if (count($value2)){
                foreach ($value2 as $value3){
                    $wpdb->query($this->copyProductBuildQuery($tables[$i], $value3, $product->product_id));
                }
            }
            $i++;
        }

        //change order in category
        $query = "SELECT * FROM `".$wpdb->prefix."wshop_products_to_categories` WHERE `product_id` = ".(int)$product->product_id;
        $list = $wpdb->get_results($query);

        foreach ($list as $val){
            $query = "SELECT MAX(product_ordering) AS k FROM `".$wpdb->prefix."wshop_products_to_categories` WHERE `category_id` = ".(int)$val->category_id;
            $ordering = $wpdb->get_var($query) + 1;

            $query = "UPDATE `".$wpdb->prefix."wshop_products_to_categories` SET `product_ordering` = '".$ordering."' WHERE `category_id` = ".(int)$val->category_id." AND `product_id` = ".(int)$product->product_id;
            $wpdb->query($query);
        }

        $query = "UPDATE `".$wpdb->prefix."wshop_products_attr` SET `ext_attribute_product_id` = 0 WHERE `product_id` = ".(int)$product->product_id;
        $wpdb->query($query);

        return $product;
    }
}