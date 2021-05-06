<?php

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class ProductWshopController extends WshopController {
    public function __construct() {
        parent::__construct();
		do_action_ref_array('onConstructWshopControllerProduct', array(&$this));
    }
    
    public function view() {
        $config = Factory::getConfig();
        $mainframe = Factory::getApplication();
        $session =Factory::getSession();
		$user = Factory::getUser();
        $back_value = $session->get('product_back_value');
        
        Factory::loadJsFilesLightBox();
        $product_id = Request::getVar('product_id');
        $attr = Request::getVar("attr");
        if (!isset($back_value['pid'])) $back_value = array('pid'=>null, 'attr'=>null, 'qty'=>null);
        if ($back_value['pid']!=$product_id) $back_value = array('pid'=>null, 'attr'=>null, 'qty'=>null);
        if (!is_array($back_value['attr'])) $back_value['attr'] = array();
        if (count($back_value['attr'])==0 && is_array($attr)) $back_value['attr'] = $attr;
		
		do_action_ref_array('onBeforeLoadProduct', array(&$product_id, &$back_value));
        do_action_ref_array('onBeforeLoadProductList', array());		

        $product = Factory::getTable('product');
        $product->load($product_id);        

        if (!($product) || $product->product_publish==0){
            echo _WOP_SHOP_PAGE_NOT_FOUND;
            return ;
        } 
        $product->hit();
        
        $listcategory = $product->getCategories(1);
        if (!getDisplayPriceForProduct($product->product_price)){
            $config->attr_display_addprice = 0;
        }
    
        $all_attr_values = array(); // delete
        
        
        if (!$product->_display_price) $hide_buy = 1;        
        $default_count_product = 1;
        if ($config->min_count_order_one_product>1){
            $default_count_product = $config->min_count_order_one_product;
        }
        if ($back_value['qty']){
            $default_count_product = $back_value['qty'];
        }        
        if (!getDisplayPriceForProduct($product->product_price)){
            $config->attr_display_addprice = 0;
        }
        
        $attributesDatas = $product->getAttributesDatas($back_value['attr']);

        $product->setAttributeActive($attributesDatas['attributeActive']);
        $attributeValues = $attributesDatas['attributeValues'];
        $attributes = $product->getBuildSelectAttributes($attributeValues, $attributesDatas['attributeSelected']);

        if (count($attributes)){
            $_attributevalue = Factory::getTable('attributvalue');
            $all_attr_values = $_attributevalue->getAllAttributeValues();
        }else{
            $all_attr_values = array();
        }
        $session->set('product_back_value',array());
        $product->getExtendsData();        
        
        //$category->load($category_id);
        //$category->name = $category->getName();
//        if ($category->category_publish==0 || $product->product_publish==0 || !in_array($product->access, $user->getAuthorisedViewLevels()) || !in_array($category_id, $listcategory)){
//            JError::raiseError( 404, _WOP_SHOP_PAGE_NOT_FOUND);
//            return;
//        }        
        if ($product->meta_title=="") $product->meta_title = $product->name;
        $this->addMetaTag('description', $product->meta_description);
        $this->addMetaTag('keyword', $product->meta_keyword);
        $this->addMetaTag('title', $product->meta_title);

        if ($config->product_show_manufacturer_logo || $config->product_show_manufacturer){
            $product->manufacturer_info = $product->getManufacturerInfo();
            if (!isset($product->manufacturer_info)){
                $product->manufacturer_info = new stdClass();
                $product->manufacturer_info->manufacturer_logo = '';
                $product->manufacturer_info->name = '';
            }
        }else{
            $product->manufacturer_info = new stdClass();
            $product->manufacturer_info->manufacturer_logo = '';
            $product->manufacturer_info->name = '';
        }
		
        if ($config->product_show_vendor){
            $vendorinfo = $product->getVendorInfo();
            $vendorinfo->urllistproducts = SEFLink("controller=vendor&task=products&vendor_id=".$vendorinfo->id,1);
            $vendorinfo->urlinfo = SEFLink("controller=vendor&task=info&vendor_id=".$vendorinfo->id,1);
            $product->vendor_info = $vendorinfo;
        }else{
            $product->vendor_info = null;
        }  		
		
		
        if ($config->product_show_qty_stock){
            $product->qty_in_stock = getDataProductQtyInStock($product);
        }        
        $product->product_basic_price_unit_qty = 1;
        if ($config->admin_show_product_basic_price){
            $product->getBasicPriceInfo();
        }else{
            $product->product_basic_price_show = 0;
        }        
        
        do_action_ref_array('onBeforeDisplayProductList', array(&$product->product_related));
        
        $view_name = "product";
        $view = $this->getView($view_name);
        if ($product->product_template=="") $product->product_template = "default";
        $view->setLayout("product_".$product->product_template);
        
        $_review = Factory::getTable('review');
        if (($allow_review = $_review->getAllowReview()) > 0) {
            $arr_marks = array();
            $arr_marks[] = HTML::_('select.option',  '0', _WOP_SHOP_NOT, 'mark_id', 'mark_value' );
            for ($i = 1; $i <= $config->max_mark; $i++) {
                $arr_marks[] = HTML::_('select.option', $i, $i, 'mark_id', 'mark_value' );
            }
            $text_review = '';
            $select_review = HTML::_('select.genericlist', $arr_marks, 'mark', 'class="inputbox" size="1"','mark_id', 'mark_value' );
        } else {
            $select_review = '';
            $text_review = $_review->getText();
        }
        
        if ($allow_review){
            Factory::loadJsFilesRating();
        }
        
        
        if ($config->admin_show_product_extra_field){
            $product->extra_field = $product->getExtraFields();
        }else{
            $product->extra_field = null;
        }
        
        if ($config->admin_show_freeattributes){
            $product->getListFreeAttributes();
            foreach($product->freeattributes as $k=>$v){
                if (!isset($back_value['freeattr'][$v->id])) $back_value['freeattr'][$v->id] = '';
                $product->freeattributes[$k]->input_field = '<input type="text" class="inputbox" size="40" name="freeattribut['.$v->id.']" value="'.$back_value['freeattr'][$v->id].'" />';
            }
            $attrrequire = $product->getRequireFreeAttribute();
            $product->freeattribrequire = count($attrrequire);
        }else{
            $product->freeattributes = null;
            $product->freeattribrequire = 0;
        }
        
        
        if ($config->product_show_qty_stock){
            $product->qty_in_stock = getDataProductQtyInStock($product);
        }
        if (!$config->admin_show_product_labels) $product->label_id = null;
        if ($product->label_id){
            $image = getNameImageLabel($product->label_id);
            if ($image){
                $product->_label_image = $config->image_labels_live_path."/".$image;
            }
            $product->_label_name = getNameImageLabel($product->label_id, 2);
        }
        $hide_buy = 0;
        if ($config->user_as_catalog) $hide_buy = 1;
        if ($config->hide_buy_not_avaible_stock && $product->product_quantity <= 0) $hide_buy = 1;
        
        $available = "";
        if ( ($product->getQty() <= 0) && $product->product_quantity >0 ){
            $available = _WOP_SHOP_PRODUCT_NOT_AVAILABLE_THIS_OPTION;
        }elseif ($product->product_quantity <= 0){
            $available = _WOP_SHOP_PRODUCT_NOT_AVAILABLE;
        }        

        $product->_display_price = getDisplayPriceForProduct($product->getPriceCalculate());
        if (!$product->_display_price){
            $product->product_old_price = 0;
            $product->product_price_default = 0;
            $product->product_basic_price_show = 0;
            $product->product_is_add_price = 0;
            $product->product_tax = 0;
            $config->show_plus_shipping_in_product = 0;
        }
        
        if (!$product->_display_price) $hide_buy = 1;        
        
        $default_count_product = 1;
        if ($config->min_count_order_one_product>1){
            $default_count_product = $config->min_count_order_one_product;
        }
        if ($back_value['qty']){
            $default_count_product = $back_value['qty'];
        }

        if (trim($product->description)=="") $product->description = $product->short_description;
        $product->hide_delivery_time = 0;
        if (!$product->getDeliveryTimeId()){
            $product->hide_delivery_time = 1;
        }
        $product->button_back_js_click = "history.go(-1);";
        if ($session->get('wshop_end_page_list_product') && $config->product_button_back_use_end_list){
            $product->button_back_js_click = "location.href='".$session->get('wshop_end_page_list_product')."';";
        }
		
        $displaybuttons = '';
        if ($config->hide_buy_not_avaible_stock && $product->getQty() <= 0) $displaybuttons = 'display:none;';        
        $product_images = $product->getImages();        
        $product_videos = $product->getVideos();
        $product_demofiles = $product->getDemoFiles();
        
        do_action_ref_array('onBeforeDisplayProduct', array(&$product, &$view, &$product_images, &$product_videos, &$product_demofiles) );
        
        $view->assign('config', $config);
        $view->assign('image_path', $config->live_path.'/images');
        $view->assign('noimage', $config->noimage);
        $view->assign('image_product_path', $config->image_product_live_path);
        $view->assign('video_product_path', $config->video_product_live_path);
        $view->assign('video_image_preview_path', $config->video_product_live_path);
        $view->assign('product', $product);
        //$view->assign('category_id', $category_id);
        $view->assign('images', $product_images);
        $view->assign('videos', $product_videos);
        $view->assign('demofiles', $product_demofiles);
        $view->assign('attributes', $attributes);
        $view->assign('all_attr_values', $all_attr_values);
        $view->assign('related_prod', $product->product_related);
        $view->assign('path_to_image', $config->live_path . '/assets/images/');
        $view->assign('live_path', URI::root());
        $view->assign('urlupdateprice', SEFLink('controller=product&task=ajax_attrib_select_and_price&product_id='.$product_id.'&ajax=1'));
        $view->assign('enable_wishlist', $config->enable_wishlist);
        $view->assign('action', SEFLink('controller=cart&task=add'));

        if ($allow_review){
            $context = "wshop.list.front.product.review";
            $limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', 20, 'int');
            $limitstart = Request::getInt('limitstart');
            $total =  $product->getReviewsCount();
            $view->assign('reviews', $product->getReviews($limitstart, $limit));
            $pagination = new Pagination($total, $limitstart, $limit);
			$pagination->setAdditionalUrlParam('product_id', $product_id);
            $pagenav = $pagination->getPagesLinks();
            $view->assign('pagination', $pagenav);
              $view->assign('pagination_obj', $pagination);
            $view->assign('display_pagination', $pagenav!="");
        }        
        $view->assign('allow_review', $allow_review);
        $view->assign('select_review', $select_review);
        $view->assign('text_review', $text_review);
        $view->assign('stars_count', floor($config->max_mark / $config->rating_starparts));
        $view->assign('parts_count', $config->rating_starparts);
        $view->assign('user', $user);
        $view->assign('shippinginfo', SEFLink($config->shippinginfourl,1));
        $view->assign('hide_buy', $hide_buy);
        $view->assign('available', $available);                
        $view->assign('default_count_product', $default_count_product);
        $view->assign('folder_list_products', "list_products");
        $view->assign('back_value', $back_value);
        $view->assign('displaybuttons', $displaybuttons);
        do_action_ref_array('onBeforeDisplayProductView', array(&$view));
        $view->display();
		do_action_ref_array('onAfterDisplayProduct', array(&$product));
    }
    
    public function ajax_attrib_select_and_price(){
        $config = Factory::getConfig();
                
        $product_id = Request::getInt('product_id');
        $change_attr = Request::getInt('change_attr');
        if ($config->use_decimal_qty){
            $qty = floatval(str_replace(",",".",Request::getVar('qty',1)));
        }else{
            $qty = Request::getInt('qty',1);
        }
        if ($qty < 0) $qty = 0;
        $attribs = Request::getVar('attr');
        if (!is_array($attribs)) $attribs = array();
        $freeattr = Request::getVar('freeattr');
        if (!is_array($freeattr)) $freeattr = array();
       
        do_action_ref_array('onBeforeLoadDisplayAjaxAttrib', array(&$product_id, &$change_attr, &$qty, &$attribs, &$freeattr));
        
        $product = Factory::getTable('product'); 
        $product->load($product_id);
		do_action_ref_array('onBeforeLoadDisplayAjaxAttrib2', array(&$product));
        
        $attributesDatas = $product->getAttributesDatas($attribs);
        $product->setAttributeActive($attributesDatas['attributeActive']);
        $attributeValues = $attributesDatas['attributeValues'];
        $product->setFreeAttributeActive($freeattr);
        
        $attributes = $product->getBuildSelectAttributes($attributeValues, $attributesDatas['attributeSelected']);

        $rows = array();
        foreach($attributes as $k=>$v){            
            $rows["id_".$k] = $v->selects;
        }

        $pricefloat = $product->getPrice($qty, 1, 1, 1);
        $price = formatprice($pricefloat);
        $available = intval($product->getQty() > 0);
		$displaybuttons = intval(intval($product->getQty() > 0) || $config->hide_buy_not_avaible_stock==0);
        $ean = $product->getEan();
        $weight = formatweight($product->getWeight());
        $basicprice = formatprice($product->getBasicPrice());
        
        $rows["price"] = $price;
        $rows["pricefloat"] = $pricefloat;
        $rows["available"] = $available;
        $rows["ean"] = $ean;
        if ($config->admin_show_product_basic_price){
            $rows["basicprice"] = $basicprice;
        }
        if ($config->product_show_weight){
            $rows["weight"] = $weight;
        }
        if ($config->product_list_show_price_default && $product->product_price_default>0){
            $rows["pricedefault"] = formatprice($product->product_price_default);
        }
        if ($config->product_show_qty_stock){
            $qty_in_stock = getDataProductQtyInStock($product);
            $rows["qty"] = sprintQtyInStock($qty_in_stock);
        }
		
        $product->updateOtherPricesIncludeAllFactors();

        if (is_array($product->product_add_prices)){
            foreach($product->product_add_prices as $k=>$v){
                $rows["pq_".$v->product_quantity_start] = formatprice($v->price).$v->ext_price;
            }
        }
        if ($product->product_old_price){
            $old_price = formatprice($product->product_old_price);
            $rows["oldprice"] = $old_price;
        }
		$rows["displaybuttons"] = $displaybuttons;
        if ($config->hide_delivery_time_out_of_stock){
            $rows["showdeliverytime"] = $product->getDeliveryTimeId();            
        }
        
        if ($config->use_extend_attribute_data){
            $template_path = $config->template_path.$config->template."/product";
            $images = $product->getImages();
            $videos = $product->getVideos();
            $demofiles = $product->getDemoFiles();

            if (!file_exists($template_path."/block_image_thumb.php")){
                $tmp = array();
                foreach($images as $img){
					$tmp[] = $img->image_name;
				}
                $displayimgthumb = intval((count($images)>1) || (count($videos) && count($images)));
                $rows['images'] = $tmp;
				$rows['displayimgthumb'] = $displayimgthumb;
            }
            
            $view_name = "product";
            $view = $this->getView($view_name);

            $view->setLayout("demofiles");
            $view->assign('config', $config);
            $view->assign('demofiles', $demofiles);
            $demofiles = $view->loadTemplate();
            $rows['demofiles'] = $demofiles;

            if (file_exists($template_path."/block_image_thumb.php")){
                $product->getDescription();
                
                $view_name = "product";
                $view->setLayout("block_image_thumb");
                $view->assign('config', $config);            
                $view->assign('images', $images);            
                $view->assign('videos', $videos);            
                $view->assign('image_product_path', $config->image_product_live_path);            
                do_action_ref_array('onBeforeDisplayProductViewBlockImageThumb', array(&$view));
                $block_image_thumb = $view->loadTemplate();
                
                $view_name = "product";
                $view->setLayout("block_image_middle");
                $view->assign('config', $config);            
                $view->assign('images', $images);            
                $view->assign('videos', $videos);            
                $view->assign('product', $product);            
                $view->assign('noimage', $config->noimage);            
                $view->assign('image_product_path', $config->image_product_live_path);
                $view->assign('path_to_image', $config->live_path.'/assets/images/');
                do_action_ref_array('onBeforeDisplayProductViewBlockImageMiddle', array(&$view));
                $block_image_middle = $view->loadTemplate();

                $rows["block_image_thumb"] = $block_image_thumb;
                $rows["block_image_middle"] = $block_image_middle;
            }
        }

        do_action_ref_array('onBeforeDisplayAjaxAttrib', array(&$rows, &$product) );
        echo json_encode($rows);
        die();
    } 
    
    function showmedia(){
        $config = Factory::getConfig();
        $media_id = Request::getInt('media_id');
        $file = Factory::getTable('productfiles');
        $file->load($media_id);

        $scripts_load = '<script type="text/javascript" src="'.WOPSHOP_PLUGIN_URL.'assets/js/jquery/jquery.min.js"></script>';
        $scripts_load .= '<script type="text/javascript" src="'.WOPSHOP_PLUGIN_URL.'assets/js/jquery/jquery.media.js"></script>';

        $view_name = "product";
        $view = $this->getView($view_name);
        $view->setLayout("playmedia");
        $view->assign('config', $config);
        $view->assign('filename', $file->demo);
        $view->assign('description', $file->demo_descr);
        $view->assign('scripts_load', $scripts_load);
        do_action_ref_array('onBeforeDisplayProductShowMediaView', array(&$view) );
        $view->display(); 
        die();
    }
    
    public function getfile(){
        $config = Factory::getConfig();
        $user = Factory::getUser();

        $id = Request::getInt('id'); 
        $oid = Request::getInt('oid');
        $hash = Request::getVar('hash');
        $rl = Request::getInt('rl');

        $order = Factory::getTable('order');
        $order->load($oid);
        if ($order->file_hash!=$hash){
            die("Error download file");
            return 0;
        }

        if (!in_array($order->order_status, $config->payment_status_enable_download_sale_file)){
            echo (_WOP_SHOP_FOR_DOWNLOAD_ORDER_MUST_BE_PAID);
            return 0;
        }

        if ($rl == 1){
            //fix for IE
            $newurl = SEFLink('controller=product&task=getfile&oid='.$oid.'&id='.$id.'&hash='.$hash, false); 
            print "<script type='text/javascript'>location.href='".$newurl."';</script>";
            die();
        }
		
        if ($config->user_registered_download_sale_file && $order->user_id>0 && $order->user_id!=$user->id){
            checkUserLogin();
        }

        if ($config->max_day_download_sale_file && (time() > ($order->getStatusTime()+(86400*$config->max_day_download_sale_file))) ){
            echo (_WOP_SHOP_TIME_DOWNLOADS_FILE_RESTRICTED);
            return 0; 
        }
        
        $items = $order->getAllItems();
        $filesid = array();
        if ($config->order_display_new_digital_products){
            $product = Factory::getTable('product');
            foreach($items as $item){
                $product->product_id = $item->product_id;
                $product->setAttributeActive(json_decode($item->attributes, 1));
                $files = $product->getSaleFiles();
                foreach($files as $_file){
                    $filesid[] = $_file->id;
                }
            }
        }else{
            foreach($items as $item){
                $arrayfiles = json_decode($item->files);
                foreach($arrayfiles as $_file){
                    $filesid[] = $_file->id;
                }
            }
        }
        
        if (!in_array($id, $filesid)){
            die("Error download file");
            return 0;
        }
        
        $stat_download = $order->getFilesStatDownloads();
        
        if ($config->max_number_download_sale_file>0 && $stat_download[$id]['download'] >= $config->max_number_download_sale_file){
            echo (_WOP_SHOP_NUMBER_DOWNLOADS_FILE_RESTRICTED);
            return 0;
        }
        
        $file = Factory::getTable('productFiles');
        $file->load($id);

        do_action_ref_array('onAfterLoadProductFile', array(&$file, &$order));
        $downloadFile = $file->file;
        if ($downloadFile==""){
            die("Error download file");
            return 0;
        }
        $file_name = $config->files_product_path."/".$downloadFile;
        if (!file_exists($file_name)){
            die("Error. File not exist");
            return 0;
        }
        
        $stat_download[$id]['download'] = intval($stat_download[$id]['download']) + 1;
        $stat_download[$id]['time'] = getJsDate();
        
        $order->setFilesStatDownloads($stat_download);
        $order->store();
        
        ob_end_clean();
        @set_time_limit(0);
        $fp = fopen($file_name, "rb");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-Type: application/octet-stream");
        header("Content-Length: " . (string)(filesize($file_name)));
        header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
        header("Content-Transfer-Encoding: binary");

        while( (!feof($fp)) && (connection_status()==0) ){
            print(fread($fp, 1024*8));
            flush();
        }
        fclose($fp);
        die();
    }
	
    function reviewsave(){
        $config = Factory::getConfig();
        $user = Factory::getUser(); 
        $post = Request::get('post');
        $backlink = Request::getVar('back_link');
        $product_id = Request::getInt('product_id');
        
        $review = Factory::getTable('review');
        
        if ($review->getAllowReview() <= 0) {
			addMessage($review->getText(), 'error');
            $this->setRedirect($backlink);
            return 0;
        }
                
        $review->bind($post);
        $review->time = getJsDate();
        $review->user_id = $user->id;
        $review->ip = $_SERVER['REMOTE_ADDR'];
        if ($config->display_reviews_without_confirm){
            $review->publish = 1;    
        }

        do_action_ref_array( 'onBeforeSaveReview', array(&$review) );

        $review->store();

        do_action_ref_array( 'onAfterSaveReview', array(&$review) );

        $product = Factory::getTable('product');
        $product->load($product_id);
        $product->loadAverageRating();
        $product->loadReviewsCount();
        $product->store();
		$name = 'name_'.$config->cur_lang;
        $view_name = "product";
        $view = $this->getView($view_name);
        $view->setLayout("commentemail");
        $view->assign('product_name', $product->$name);
        $view->assign('user_name', $review->user_name);
        $view->assign('user_email', $review->user_email);
        $view->assign('mark', $review->mark);
        $view->assign('review', $review->review);
        $message = $view->loadTemplate();		
		$subject = _WOP_SHOP_NEW_COMMENT;
		$to = get_option('admin_email');
		$headers[] = 'From: '.  get_bloginfo().' <'.get_option('admin_email') . ">\r\n";
		$headers[] = 'Content-Type: text/html; charset=UTF-8';                   
		wp_mail( $to, $subject, $message, $headers);		

        if ($config->display_reviews_without_confirm){
			addMessage(_WOP_SHOP_YOUR_REVIEW_SAVE_DISPLAY);
            $this->setRedirect($backlink);
        }else{
			addMessage(_WOP_SHOP_YOUR_REVIEW_SAVE);
            $this->setRedirect($backlink);
        }
    }	
}