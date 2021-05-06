<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class ProductWshopTable extends WshopTable{

    function __construct(){
        global $wpdb;
        parent::__construct($wpdb->prefix.'wshop_products', 'product_id');
    }
    
    function setAttributeActive($attribs){
        $config = Factory::getConfig();
        $this->attribute_active = $attribs;
        if (is_array($this->attribute_active) && count($this->attribute_active)){
            $this->attribute_active_data = new stdClass();
            $allattribs = Factory::getAllAttributes(1);
            $dependent_attr = array();
            $independent_attr = array();
            foreach($attribs as $k=>$v){
                if ($allattribs[$k]->independent==0){
                    $dependent_attr[$k] = $v;
                }else{
                    $independent_attr[$k] = $v;
                }
            }
            
            if (count($dependent_attr)){
                $where = "";
                foreach($dependent_attr as $k=>$v){
                    $where.=" and PA.attr_".$k." = '".$this->_db->escape($v)."' ";
                }
                $query = "select PA.* from `".$this->_db->prefix."wshop_products_attr` as PA where PA.product_id = '".  esc_sql($this->product_id)."' ".$where; 
                $this->attribute_active_data = $this->_db->get_row($query);
                if ($config->use_extend_attribute_data && $this->attribute_active_data->ext_attribute_product_id){
                    $this->attribute_active_data->ext_data = $this->getExtAttributeData($this->attribute_active_data->ext_attribute_product_id);
                }
            }
            
            if (count($independent_attr)){
				if (!isset($this->attribute_active_data)) $this->attribute_active_data = new stdClass();
                if (!isset($this->attribute_active_data->price)) $this->attribute_active_data->price = $this->product_price;
                foreach($independent_attr as $k=>$v){
                    $query = "select addprice, price_mod from ".$this->_db->prefix."wshop_products_attr2 where product_id='".  esc_sql($this->product_id)."' and attr_id='". esc_sql($k)."' and attr_value_id='".esc_sql($v)."'";
                    $attr_data2 = $this->_db->get_row($query);
                    if (count($attr_data2) > 0){
                        if ($attr_data2->price_mod=="+"){
                            $this->attribute_active_data->price += $attr_data2->addprice;
                        }elseif ($attr_data2->price_mod=="-"){
                            $this->attribute_active_data->price -= $attr_data2->addprice;
                        }elseif ($attr_data2->price_mod=="*"){
                            $this->attribute_active_data->price *= $attr_data2->addprice;
                        }elseif ($attr_data2->price_mod=="/"){
                            $this->attribute_active_data->price /= $attr_data2->addprice;
                        }elseif ($attr_data2->price_mod=="%"){
                            $this->attribute_active_data->price *= $attr_data2->addprice/100;
                        }elseif ($attr_data2->price_mod=="="){
                            $this->attribute_active_data->price =  $attr_data2->addprice;
                        }
                    }
                }
            }
        }else{
            $this->attribute_active_data = NULL;
        }			
		do_action_ref_array('onAfterSetAttributeActive', array(&$attribs, &$this));
    }
    
    function setFreeAttributeActive($freattribs){
        $this->free_attribute_active = $freattribs;
    }
    
    function getData($field){
        if (isset($this->attribute_active_data->ext_data) && isset($this->attribute_active_data->ext_data->$field)){
            return $this->attribute_active_data->ext_data->$field;
        }else{
            return $this->$field;
        }
    }
    
    //get require attribute
    function getRequireAttribute(){
        $require = array();
        $config = Factory::getConfig();
        if (!$config->admin_show_attributes) return $require;

        $allattribs = Factory::getAllAttributes(2);
        $dependent_attr = $allattribs['dependent'];
        $independent_attr = $allattribs['independent'];
        
        if (count($dependent_attr)){
            $prodAttribVal = $this->getAttributes();
            if (count($prodAttribVal)){
                $prodAtrtib = $prodAttribVal[0];
                foreach($dependent_attr as $attrib){
                    $field = "attr_".$attrib->attr_id;
                    if ($prodAtrtib->$field) $require[] = $attrib->attr_id;
                }
            }
        }
        
        if (count($independent_attr)){
            $prodAttribVal2 = $this->getAttributes2();
            foreach($prodAttribVal2 as $attrib){
                if (!in_array($attrib->attr_id, $require)){
                    $require[] = $attrib->attr_id;
                }
            }
        }

        return $require;
    }
    
    //get dependent attributs
    function getAttributes(){
        $query = "SELECT * FROM `".$this->_db->prefix."wshop_products_attr` WHERE product_id = '".$this->product_id."' ORDER BY product_attr_id";
        return $this->_db->get_results($query);
    }
    
    //get independent attributs
    function getAttributes2(){
        $query = "SELECT * FROM `".$this->_db->prefix."wshop_products_attr2` WHERE product_id = '".$this->product_id."' ORDER BY id";
        do_action_ref_array('onAfterQueryGetAttributes2', array(&$query));
        return $this->_db->get_results($query);
    }   
    
    //get attrib values
    function getAttribValue($attr_id, $other_attr = array(), $onlyExistProduct = 0){
        $config = Factory::getConfig();
        $allattribs = Factory::getAllAttributes(1);
        if ($allattribs[$attr_id]->independent==0){
            $where = "";
            foreach($other_attr as $k=>$v){
                $where.=" and PA.attr_$k='$v'";
            }
            if ($onlyExistProduct) $where.=" and PA.count>0 ";
            $sorting = $config->attribut_dep_sorting_in_product;
            if ($sorting=="") $sorting = "V.value_ordering";
            $field = "attr_".$attr_id;
            $query = "SELECT distinct PA.$field as val_id, V.`name_".$config->cur_lang."` as value_name, V.image
                      FROM `".$this->_db->prefix."wshop_products_attr` as PA INNER JOIN ".$this->_db->prefix."wshop_attr_values as V ON PA.$field=V.value_id
                      WHERE PA.product_id = '".$this->product_id."' ".$where."
                      ORDER BY ".$sorting;
        }else{
            $sorting = $config->attribut_nodep_sorting_in_product;
            if ($sorting=="") $sorting = "V.value_ordering";
            $query = "select PA.attr_value_id as val_id, V.`name_".$config->cur_lang."` as value_name, V.image, price_mod, addprice 
                      from ".$this->_db->prefix."wshop_products_attr2 as PA INNER JOIN ".$this->_db->prefix."wshop_attr_values as V ON PA.attr_value_id=V.value_id
                      where PA.product_id = '".$this->product_id."' and PA.attr_id='".$attr_id."'
                      ORDER BY ".$sorting;
        }
        return $this->_db->get_results($query);
    }
    
    function getAttributesDatas($selected = array()){
        $config = Factory::getConfig();
        $data = array('attributeValues'=>array());
        $requireAttribute = $this->getRequireAttribute();
        $actived = array();
        foreach($requireAttribute as $attr_id){
            $options = $this->getAttribValue($attr_id, $actived, $config->hide_product_not_avaible_stock);
            $data['attributeValues'][$attr_id] = $options;
            if (!$config->product_attribut_first_value_empty){
                $actived[$attr_id] = $options[0]->val_id;
            }
            if (isset($selected[$attr_id])){
                $testActived = 0;
                foreach($options as $tmp) if ($tmp->val_id==$selected[$attr_id]) $testActived = 1;
                if ($testActived){
                    $actived[$attr_id] = $selected[$attr_id];
                }
            }
        }
        if (count($requireAttribute) == count($actived)){
            $data['attributeActive'] = $actived;
        }else{
            $data['attributeActive'] = array();
        }
        $data['attributeSelected'] = $actived;
    return $data;
    }
    
    function getPIDCheckQtyValue(){
        if (isset($this->attribute_active_data->product_attr_id)){
            return "A:".$this->attribute_active_data->product_attr_id;
        }else{
            return "P:".$this->product_id;
        }
    }
    
    function getListFreeAttributes(){
		$config = Factory::getConfig();   
        $lang = $config->cur_lang; //$lang = get_bloginfo('language');
        $query = "SELECT FA.id, FA.required, FA.`name_".$lang."` as name, FA.`description_".$lang."` as description, FA.type FROM `".$this->_db->prefix."wshop_products_free_attr` as PFA left join `".$this->_db->prefix."wshop_free_attr` as FA on FA.id=PFA.attr_id
                  WHERE PFA.product_id = '".esc_sql($this->product_id)."' order by FA.ordering";
        $this->freeattributes = $this->_db->get_results($query);
        return $this->freeattributes;
    }
    
    /**
    * use after getListFreeAttributes()
    */
    function getRequireFreeAttribute(){
        $rows = array();
        if ($this->freeattributes){
            foreach($this->freeattributes as $k=>$v){
                if ($v->required){
                    $rows[] = $v->id;
                }
            }
        }
    return $rows;
    }
//
    function getCategories($type_result = 0){
        if (!isset($this->product_categories)){
            $query = "SELECT * FROM `".$this->_db->prefix."wshop_products_to_categories` WHERE product_id='".esc_sql($this->product_id)."'";
            $this->product_categories = $this->_db->get_results($query);
        }
        if ($type_result==1){
            $cats = array();
            foreach($this->product_categories as $v){
                $cats[] = $v->category_id;
            }
            return $cats;
        }else{
            return $this->product_categories;
        }
    }

    function getName() {
        $config = Factory::getConfig();
        $name = 'name_'.$config->cur_lang;
        return $this->$name;
    }

    function getPriceWithParams(){
        if (isset($this->attribute_active_data->price)){
            return $this->attribute_active_data->price;
        }else{
            return $this->product_price;
        }
    }
    
    function getEan(){   
        if (isset($this->attribute_active_data->ean)){
            return $this->attribute_active_data->ean;
        }else{
            return $this->product_ean;
        }
    }
    
    function getQty(){
        if ($this->unlimited) return 1;
        if (isset($this->attribute_active_data->count)){
            return $this->attribute_active_data->count;
        }else{
            return $this->product_quantity;
        }
    }
    
    function getWeight(){
        if (isset($this->attribute_active_data) && isset($this->attribute_active_data->weight)){
            return $this->attribute_active_data->weight;
        }else{
            return $this->product_weight;
        }
    }
    
    function getWeight_volume_units(){
        if (isset($this->attribute_active_data->weight_volume_units) && $this->attribute_active_data->weight_volume_units > 0){
            return $this->attribute_active_data->weight_volume_units;
        }else{
            return $this->weight_volume_units;
        }
    }
    
    function getQtyInStock(){
        if ($this->unlimited) return 1;
        $qtyInStock = floatval($this->getQty());
        if ($qtyInStock < 0) $qtyInStock = 0;
    return $qtyInStock;
    }
    
    function getOldPrice(){
        if (isset($this->attribute_active_data->old_price)){
            return $this->attribute_active_data->old_price;
        }else{
            return $this->product_old_price;
        }    
    }

    function getImages(){
        if (isset($this->attribute_active_data->ext_data) && $this->attribute_active_data->ext_data){
            $list = $this->attribute_active_data->ext_data->getImages();
            if (count($list)){
                return $list;
            }
        }
		
        $query = "SELECT I.*, IF(P.image=I.image_name,0,1) as sort FROM `".$this->_db->prefix."wshop_products_images` as I left join `".$this->_db->prefix."wshop_products` as P on P.product_id=I.product_id
                 WHERE I.product_id = '".esc_sql($this->product_id)."' ORDER BY sort, I.ordering";
        $list = $this->_db->get_results($query);
        foreach($list as $k=>$v){
            $title = $v->name;
            if (!$title){
                $title = $this->getName();
            }
            $list[$k]->_title = $title;
            $list[$k]->image_thumb = getPatchProductImage($v->image_name, 'thumb');
            $list[$k]->image_full = getPatchProductImage($v->image_name, 'full');
        }
        if (isset($this->attribute_active_data->ext_data) && $this->attribute_active_data->ext_data){
            $list = $this->attribute_active_data->ext_data->getImages();
            if (count($list)){
                return $list;
            }
        }
		
        $query = "SELECT I.*, IF(P.image=I.image_name,0,1) as sort FROM `".$this->_db->prefix."wshop_products_images` as I left join `".$this->_tbl."` as P on P.product_id=I.product_id
                 WHERE I.product_id = '".esc_sql($this->product_id)."' ORDER BY sort, I.ordering";
        $list = $this->_db->get_results($query);
        foreach($list as $k=>$v){
            $title = $v->image_name;
            if (!$title){
                $title = $this->getName();
            }
            $list[$k]->_title = $title;
            $list[$k]->image_thumb = getPatchProductImage($v->image_name, 'thumb');
            $list[$k]->image_full = getPatchProductImage($v->image_name, 'full');
        }
    return $list;
    }

    function getVideos(){
        $config = Factory::getConfig();
        if (!$config->admin_show_product_video) return array();
        
        $query = "SELECT  video_name, video_id, video_preview, video_code FROM `".$this->_db->prefix."wshop_products_videos` WHERE product_id = '".esc_sql($this->product_id)."'";
        return $this->_db->get_results($query);
    }
    
    function getFiles(){
        $config = Factory::getConfig();
        if (!$config->admin_show_product_files) return array();
        if (isset($this->attribute_active_data->ext_data) && $this->attribute_active_data->ext_data){
            $list = $this->attribute_active_data->ext_data->getFiles();
            if (count($list)){
                return $list;
            }
        }
        $query = "SELECT * FROM `".$this->_db->prefix."wshop_products_files` WHERE product_id = '".esc_sql($this->product_id)."' order by `ordering` ";
        return $this->_db->get_results($query);
    }
   
    function getDemoFiles(){
        $config = Factory::getConfig();
        if (!$config->admin_show_product_files) return array();
		$list = array();
        if (isset($this->attribute_active_data) && isset($this->attribute_active_data->ext_data) && $this->attribute_active_data->ext_data){
			$list = $this->attribute_active_data->ext_data->getDemoFiles();
		}
        $query = "SELECT * FROM `".$this->_db->prefix."wshop_products_files` WHERE product_id = '".esc_sql($this->product_id)."' and demo!='' order by `ordering` ";
	$list0 = $this->_db->get_results($query);
        return array_merge($list0, $list);
    }
    
     function getSaleFiles(){
        $config = Factory::getConfig();
        if (!$config->admin_show_product_files) return array();
		$list = array();
        if (isset($this->attribute_active_data->ext_data) && $this->attribute_active_data->ext_data){
			$list = $this->attribute_active_data->ext_data->getSaleFiles();
		}
        $query = "SELECT id, file, file_descr FROM `".$this->_db->prefix."wshop_products_files` WHERE product_id = '".esc_sql($this->product_id)."' and file!='' order by `ordering` ";
	$list0 = $this->_db->get_results($query);
        return array_merge($list0, $list);
    }
    
    function getManufacturerInfo(){
        $manufacturers = Factory::getAllManufacturer();
        if ($this->product_manufacturer_id && isset($manufacturers[$this->product_manufacturer_id])){
            return $manufacturers[$this->product_manufacturer_id];
        }else{
            return null;
        }
    }
    
    function getVendorInfo(){
        $vendors = Factory::getAllVendor();
        if (isset($vendors[$this->vendor_id])){
            return $vendors[$this->vendor_id];
        }else{
            return null;
        }
    }

    /**
    * get first catagory for product
    */    
    function getCategory() {
        $user = Factory::getUser();
        //$groups = implode(',', $user->getAuthorisedViewLevels());
        //$adv_query =' AND cat.access IN ('.$groups.')';
        $adv_query =' ';
        $query = "SELECT pr_cat.category_id FROM `".$this->_db->prefix."wshop_products_to_categories` AS pr_cat
                LEFT JOIN `".$this->_db->prefix."wshop_categories` AS cat ON pr_cat.category_id = cat.category_id
                WHERE pr_cat.product_id = '".  esc_sql($this->product_id)."' AND cat.category_publish='1' ".$adv_query." LIMIT 0,1";
        $this->category_id = $this->_db->get_var($query);
        return $this->category_id;
    }
    
    function getFullQty(){
        if ($this->unlimited) return 1;
        $query = "select count(*) as countattr, SUM(count) AS qty from `_products_attr` where product_id='".  esc_sql($this->product_id)."'";
        $tmp = $this->_db->get_row($query);
        if ($tmp->countattr>0){
            return $tmp->qty;
        }else{
            return $this->product_quantity;
        }
    }
    
    function getMinimumPrice(){
        $config = Factory::getConfig();
        global $wpdb;
        $min_price = $this->product_price;

        $query = "select count(*) as countattr, MIN(price) AS min_price from `".$wpdb->prefix."wshop_products_attr` where product_id='".esc_sql($this->product_id)."'";
        $tmp = $wpdb->get_results($query);
        if ($tmp->countattr>0){
            $min_price = $tmp->min_price;
        }
        
        $query = "select * from `".$wpdb->prefix."wshop_products_attr2` where product_id='".esc_sql($this->product_id)."'";
        $product_attr_ind = $wpdb->get_results($query);
        if ($product_attr_ind){
            $tmpprice = array();
            foreach($product_attr_ind as $key=>$val){
                if ($val->price_mod=="+"){
                    $tmpprice[] = $min_price + $val->addprice;
                }elseif ($val->price_mod=="-"){
                    $tmpprice[] = $min_price - $val->addprice;
                }elseif ($val->price_mod=="*"){
                    $tmpprice[] = $min_price * $val->addprice;
                }elseif ($val->price_mod=="/"){
                    $tmpprice[] = $min_price / $val->addprice;
                }elseif ($val->price_mod=="%"){
                    $tmpprice[] = $min_price * $val->addprice / 100;
                }elseif ($val->price_mod=="="){
                    $tmpprice[] = $val->addprice;
                }
            }
            $min_price = min($tmpprice);
        }

        $query = "select MAX(discount) as max_discount from `".$wpdb->prefix."wshop_products_prices` where product_id='".esc_sql($this->product_id)."'";
        $max_discount = $wpdb->get_var($query);
        if ($max_discount){
            if ($config->product_price_qty_discount == 1){
                $min_price = $min_price - $max_discount;
            }else{
                $min_price = $min_price - ($min_price * $max_discount / 100);
            }
        }
        return $min_price;
    }
    
    function getExtendsData() {
        $this->getRelatedProducts();
        $this->getDescription();
        $this->getTax();
        $this->getPricePreview();
        $this->getDeliveryTime();
    }
    
    function getDeliveryTimeId($globqty = 0){
        $config = Factory::getConfig();
        if ($globqty){
            $qty = $this->product_quantity;
        }else{
            $qty = $this->getQty();
        }
        if ($config->hide_delivery_time_out_of_stock && $qty<=0){
            $this->delivery_times_id = 0;
        }
        return $this->delivery_times_id;
    }
    
    function getDeliveryTime($globqty = 0){
        $config = Factory::getConfig();
        $dti = $this->getDeliveryTimeId($globqty);
        if ($config->show_delivery_time && $dti){
            $deliveryTimes = Factory::getTable('deliveryTimes');
            $deliveryTimes->load($dti);
            $this->delivery_time = $deliveryTimes->getName();
        }else{
            $this->delivery_time = "";
        }
        return $this->delivery_time;
    }

    function getDescription() {
        $config = Factory::getConfig();
        $name = 'name_'.$config->cur_lang;
        $short_description = 'short_description_'.$config->cur_lang;
        $description = 'description_'.$config->cur_lang;
        $meta_title = 'meta_title_'.$config->cur_lang;
        $meta_keyword = 'meta_keyword_'.$config->cur_lang;
        $meta_description = 'meta_description_'.$config->cur_lang;
               
        $this->name = $this->$name;
        $this->short_description = $this->$short_description;
        $this->description = $this->$description;
        $this->meta_title = $this->$meta_title;
        $this->meta_keyword = $this->$meta_keyword;
        $this->meta_description = $this->$meta_description;
    }
    
    function getPricePreview(){
        $this->getPrice(1, 1, 1, 1);
        if ($this->product_is_add_price){
            $this->product_add_prices = array_reverse($this->product_add_prices);
        }
        $this->updateOtherPricesIncludeAllFactors();
    }
    
	function getUseUserDiscount(){
        $config = Factory::getConfig();
        if ($config->user_discount_not_apply_prod_old_price && $this->product_old_price>0){
            return 0;
        }else{
            return 1;
        }
    }
	
    function getPrice($quantity=1, $enableCurrency=1, $enableUserDiscount=1, $enableParamsTax=1, $cartProduct=array()){
        $config = Factory::getConfig();
		$this->product_price_wp = $this->product_price;
        $this->product_price_calculate = $this->getPriceWithParams();
        do_action_ref_array('onBeforeCalculatePriceProduct', array(&$quantity, &$enableCurrency, &$enableUserDiscount, &$enableParamsTax, &$this, &$cartProduct));

        if ($this->product_is_add_price){
            $this->getAddPrices();
        }else{
            $this->product_add_prices = array();
        }
        
        if ($quantity && $this->product_is_add_price){
            foreach($this->product_add_prices as $key=>$value){
                if (($quantity >= $value->product_quantity_start && $quantity <= $value->product_quantity_finish) || ($quantity >= $value->product_quantity_start && $value->product_quantity_finish==0)){
                    $this->product_price_calculate = $value->price;
					$this->product_price_wp = $value->price_wp;
                    break;
                } 
            }
        }
        
        if ($enableCurrency){
            $this->product_price_calculate = getPriceFromCurrency($this->product_price_calculate, $this->currency_id);
			$this->product_price_wp = getPriceFromCurrency($this->product_price_wp, $this->currency_id);
			
        }
        
        if ($enableParamsTax){
            $this->product_price_calculate = getPriceCalcParamsTax($this->product_price_calculate, $this->product_tax_id);
			$this->product_price_wp = getPriceCalcParamsTax($this->product_price_wp, $this->product_tax_id);
        }
        
        if ($enableUserDiscount){
            $userShop = Factory::getUserShop();
            if ($userShop->percent_discount && $this->getUseUserDiscount()){
                $this->product_price_default = $this->product_price_calculate;
				$this->product_price_calculate = getPriceDiscount($this->product_price_calculate, $userShop->percent_discount);
				$this->product_price_wp = getPriceDiscount($this->product_price_wp, $userShop->percent_discount);
            }
        }
        $this->product_price_calculate1 = $this->product_price_calculate;
        do_action_ref_array('onCalculatePriceProduct', array($quantity, $enableCurrency, $enableUserDiscount, $enableParamsTax, &$this, &$cartProduct) );
        $this->product_price_calculate0 = $this->product_price_calculate;
        if ($config->price_product_round){
            $this->product_price_calculate = round($this->product_price_calculate, $config->decimal_count);
        }
        return $this->product_price_calculate;
    }
    
    function getPriceCalculate(){
        return $this->product_price_calculate;
    }
    
    function getBasicPriceInfo(){
        $this->product_basic_price_show = $this->weight_volume_units!=0;
        if (!$this->product_basic_price_show) return 0;
        $config = Factory::getConfig();
        $units = Factory::getAllUnits();
        $unit = $units[$this->basic_price_unit_id];        
        if ($config->calc_basic_price_from_product_price){
            $this->product_basic_price_wvu = $this->weight_volume_units;
        }else{
            $this->product_basic_price_wvu = $this->getWeight_volume_units();
        }
        $this->product_basic_price_weight = $this->product_basic_price_wvu / $unit->qty;
        if ($config->calc_basic_price_from_product_price){
            $this->product_basic_price_calculate = $this->product_price_wp / $this->product_basic_price_weight;
        }else{
            $this->product_basic_price_calculate = $this->product_price_calculate1 / $this->product_basic_price_weight;
        }
        $this->product_basic_price_unit_name = $unit->name;
        $this->product_basic_price_unit_qty = $unit->qty;
        do_action_ref_array('onAfterGetBasicPriceInfoProduct', array(&$this));
        return 1;
    }
    
	function getBasicPrice(){
        if (!isset($this->product_basic_price_wvu)) $this->getBasicPriceInfo();
        return $this->product_basic_price_calculate;
    }
	
	function getBasicWeight(){
        if (!isset($this->product_basic_price_wvu)) $this->getBasicPriceInfo();
        return $this->product_basic_price_weight;
    }
	
	function getBasicPriceUnit(){
        if (!isset($this->product_basic_price_wvu)) $this->getBasicPriceInfo();
        return $this->product_basic_price_unit_name;
    }
	
    function getAddPrices(){
        $config = Factory::getConfig();
        $productprice = Factory::getTable('productprice');
        $this->product_add_prices = $productprice->getAddPrices($this->product_id);
        
        $price = $this->getPriceWithParams();
		$price_wp = $this->product_price;
        foreach($this->product_add_prices as $k=>$v){
            if ($config->product_price_qty_discount == 1){
                $this->product_add_prices[$k]->price = $price - $v->discount; //discount value
				$this->product_add_prices[$k]->price_wp = $price_wp - $v->discount;
            }else{
                $this->product_add_prices[$k]->price = $price - ($price * $v->discount / 100); //discount percent
				$this->product_add_prices[$k]->price_wp = $price_wp - ($price_wp * $v->discount / 100);
            }
        }
        
        if (!$this->add_price_unit_id) $this->add_price_unit_id = $config->product_add_price_default_unit;
        $units = Factory::getAllUnits();
        $unit = $units[$this->add_price_unit_id];
        $this->product_add_price_unit = $unit->name;
        if ($this->product_add_price_unit=="") $this->product_add_price_unit=JSHP_ST_;
        do_action_ref_array('onAfterGetAddPricesProduct', array(&$this));
    }
    
    function getTax(){
        
        $taxes = Factory::getAllTaxes();
        $this->product_tax = $taxes[$this->product_tax_id];
        do_action_ref_array('onBeforeGetTaxProduct', array(&$this));
        return $this->product_tax;
    }
    
    function updateOtherPricesIncludeAllFactors(){
        $config = Factory::getConfig();
        $userShop = Factory::getUserShop();
        
        $this->product_old_price = $this->getOldPrice();
        $this->product_old_price = getPriceFromCurrency($this->product_old_price, $this->currency_id);
        $this->product_old_price = getPriceCalcParamsTax($this->product_old_price, $this->product_tax_id);
        if ($this->getUseUserDiscount()){
			$this->product_old_price = getPriceDiscount($this->product_old_price, $userShop->percent_discount);
		}
		
        if (is_array($this->product_add_prices)){
            foreach ($this->product_add_prices as $key=>$value){
                $this->product_add_prices[$key]->price = getPriceFromCurrency($this->product_add_prices[$key]->price, $this->currency_id);
                $this->product_add_prices[$key]->price = getPriceCalcParamsTax($this->product_add_prices[$key]->price, $this->product_tax_id);
				if ($this->getUseUserDiscount()){
					$this->product_add_prices[$key]->price = getPriceDiscount($this->product_add_prices[$key]->price, $userShop->percent_discount);
				}
            }
        }
        do_action_ref_array('updateOtherPricesIncludeAllFactors', array(&$this) );
    }
    
    function getExtraFields($type = 1){
        $_cats = $this->getCategories();
        $cats = array();
        foreach($_cats as $v){
            $cats[] = $v->category_id;
        }
        
        $fields = array();
        $config = Factory::getConfig();
        $hide_fields = $config->getProductHideExtraFields();
        $cart_fields = $config->getCartDisplayExtraFields();
        $fieldvalues = Factory::getAllProductExtraFieldValue();
        $listfield = Factory::getAllProductExtraField();
        foreach($listfield as $val){
            if ($type==1 && in_array($val->id, $hide_fields)) continue;
            if ($type==2 && !in_array($val->id, $cart_fields)) continue;
            
            if ($val->allcats){
                $fields[] = $val;
            }else{
                $insert = 0;
                foreach($cats as $cat_id){
                    if (in_array($cat_id, $val->cats)) $insert = 1;
                }
                if ($insert){
                    $fields[] = $val;
                }
            }
        }
       
        $rows = array();
        foreach($fields as $field){
            $field_id = $field->id;
            $field_name = "extra_field_".$field_id;
            if ($field->type==0){
                if ($this->$field_name!=0){
                    $listid = explode(',', $this->$field_name);
                    $tmp = array();
                    foreach($listid as $extrafiledvalueid){
                        $tmp[] = $fieldvalues[$extrafiledvalueid];
                    }
                    $extra_field_value = implode($config->multi_charactiristic_separator, $tmp);
                    $rows[] = array("id"=>$field_id, "name"=>$listfield[$field_id]->name, "description"=>$listfield[$field_id]->description, "value"=>$extra_field_value, "groupname"=>$listfield[$field_id]->groupname);
                }
            }else{
                if ($this->$field_name!=""){
                    $rows[] = array("id"=>$field_id, "name"=>$listfield[$field_id]->name, "description"=>$listfield[$field_id]->description, "value"=>$this->$field_name, "groupname"=>$listfield[$field_id]->groupname);
                }
            }
        }
        $grname = '';
        foreach($rows as $k=>$v){
            if ($v['groupname']!=$grname){
                $grname = $v['groupname'];
                $rows[$k]['grshow'] = 1;
            }else{
                $rows[$k]['grshow'] = 0;
            }
        }        
        return $rows;
    }
    
    function getRelatedProducts(){
        $config = Factory::getConfig();
        if (!$config->admin_show_product_related){
            $this->product_related = array();
            return $this->product_related;
        }

        $adv_query = ""; $adv_from = ""; $adv_result = $this->getBuildQueryListProductDefaultResult();
        $filters = array();
        $this->getBuildQueryListProductSimpleList("related", null, $filters, $adv_query, $adv_from, $adv_result);
        $order_query = "order by relation.id";
        do_action_ref_array('onBeforeQueryGetProductList', array("related_products", &$adv_result, &$adv_from, &$adv_query, &$order_query, &$filters) );

        $query = "SELECT $adv_result FROM `".$this->_db->prefix."wshop_products_relations` AS relation
                INNER JOIN `".$this->_db->prefix."wshop_products` AS prod ON relation.product_related_id = prod.product_id
                LEFT JOIN `".$this->_db->prefix."wshop_products_to_categories` AS pr_cat ON pr_cat.product_id = relation.product_related_id
                LEFT JOIN `".$this->_db->prefix."wshop_categories` AS cat ON pr_cat.category_id = cat.category_id
                $adv_from
                WHERE relation.product_id = '" . esc_sql($this->product_id) . "' AND cat.category_publish='1' AND prod.product_publish = '1' ".$adv_query." group by prod.product_id ".$order_query;
        $this->product_related = $this->_db->get_results($query);
        foreach($this->product_related as $key=>$value) {
            $this->product_related[$key]->product_link = SEFLink('controller=product&task=view&product_id='.$value->product_id, 1);
        }
        $this->product_related = listProductUpdateData($this->product_related, 1);
        return $this->product_related;
    }
    
    function getLastProducts($count, $array_categories = null, $filters = array()){
        $config = Factory::getConfig();

        $adv_query = ""; $adv_from = ""; $adv_result = $this->getBuildQueryListProductDefaultResult();
        $this->getBuildQueryListProductSimpleList("last", $array_categories, $filters, $adv_query, $adv_from, $adv_result);
        $order_query = "ORDER BY prod.product_id";

        do_action_ref_array('onBeforeQueryGetProductList', array("last_products", &$adv_result, &$adv_from, &$adv_query, &$order_query, &$filters));
 
        $query = "SELECT $adv_result FROM `".$this->_db->prefix."wshop_products` AS prod
                  INNER JOIN `".$this->_db->prefix."wshop_products_to_categories` AS pr_cat ON pr_cat.product_id = prod.product_id
                  LEFT JOIN `".$this->_db->prefix."wshop_categories` AS cat ON pr_cat.category_id = cat.category_id
                  $adv_from
                  WHERE prod.product_publish = '1' AND cat.category_publish='1' ".$adv_query."
                  GROUP BY prod.product_id $order_query DESC LIMIT ".$count;
        $products = $this->_db->get_results($query);
        $products = listProductUpdateData($products);
        return $products;
    }

    function getRandProducts($count, $array_categories = null, $filters = array()){
        $config = Factory::getConfig();
        global $wpdb;

        $adv_query = ""; $adv_from = ""; $adv_result = $this->getBuildQueryListProductDefaultResult();
        $this->getBuildQueryListProductSimpleList("rand", $array_categories, $filters, $adv_query, $adv_from, $adv_result);

        do_action_ref_array( 'onBeforeQueryGetProductList', array("rand_products", &$adv_result, &$adv_from, &$adv_query, &$order_query, &$filters) );
        
        $query = "SELECT count(distinct prod.product_id) FROM `".$this->_db->prefix."wshop_products` AS prod
                  INNER JOIN `".$this->_db->prefix."wshop_products_to_categories` AS pr_cat ON pr_cat.product_id = prod.product_id
                  LEFT JOIN `".$this->_db->prefix."wshop_categories` AS cat ON pr_cat.category_id = cat.category_id
                  $adv_from                  
                  WHERE prod.product_publish = '1' AND cat.category_publish='1' ".$adv_query;
        $totalrow = $wpdb->get_var($query);
        $totalrow = $totalrow - $count;
        if ($totalrow < 0) $totalrow = 0;
        $limitstart = rand(0, $totalrow);
        
        $order = array();
        $order[] = "name asc";
        $order[] = "name desc";
        $order[] = "prod.product_price asc";
        $order[] = "prod.product_price desc";
        $orderby = $order[rand(0,3)];
                 
        $query = "SELECT $adv_result FROM `".$this->_db->prefix."wshop_products` AS prod
                  INNER JOIN `".$this->_db->prefix."wshop_products_to_categories` AS pr_cat ON pr_cat.product_id = prod.product_id
                  LEFT JOIN `".$this->_db->prefix."wshop_categories` AS cat ON pr_cat.category_id = cat.category_id
                  $adv_from
                  WHERE prod.product_publish = '1' AND cat.category_publish='1' ".$adv_query."
                  GROUP BY prod.product_id order by ".$orderby." LIMIT ".$limitstart.", ".$count;
        $products = $wpdb->get_results($query);
        $products = listProductUpdateData($products);        
        return $products;
    }    
    
    function getBestSellers($count, $array_categories = null, $filters = array()){
        $config = Factory::getConfig();
        global $wpdb;

        $adv_query = ""; $adv_from = ""; $adv_result = $this->getBuildQueryListProductDefaultResult();
        $this->getBuildQueryListProductSimpleList("best", $array_categories, $filters, $adv_query, $adv_from, $adv_result);

        do_action_ref_array( 'onBeforeQueryGetProductList', array("bestseller_products", &$adv_result, &$adv_from, &$adv_query, &$order_query, &$filters) );
 
        $query = "SELECT SUM(OI.product_quantity) as max_num, $adv_result FROM ".$wpdb->prefix."wshop_order_item AS OI
                  INNER JOIN `".$wpdb->prefix."wshop_products` AS prod   ON prod.product_id=OI.product_id
                  INNER JOIN `".$wpdb->prefix."wshop_products_to_categories` AS pr_cat ON pr_cat.product_id = prod.product_id
                  LEFT JOIN `".$wpdb->prefix."wshop_categories` AS cat ON pr_cat.category_id = cat.category_id
                  $adv_from
                  WHERE prod.product_publish = '1' AND cat.category_publish='1' ".$adv_query."
                  GROUP BY prod.product_id
                  ORDER BY max_num desc LIMIT ".$count;
        $products = $this->_db->get_results($query);

        $products = listProductUpdateData($products);

        return $products;
    }
    
    function getProductLabel($label_id, $count, $array_categories = null, $filters = array(), $order_query = "ORDER BY name"){
        $config = Factory::getConfig();
        global $wpdb;

        $adv_query = ""; $adv_from = ""; $adv_result = $this->getBuildQueryListProductDefaultResult();
        $this->getBuildQueryListProductSimpleList("label", $array_categories, $filters, $adv_query, $adv_from, $adv_result);
        if ($label_id){
            $adv_query .= " AND prod.label_id='".esc_sql($label_id)."'";
        }

        do_action_ref_array('onBeforeQueryGetProductList', array("label_products", &$adv_result, &$adv_from, &$adv_query, &$order_query, &$filters));
 
        $query = "SELECT $adv_result FROM `".$wpdb->prefix."wshop_products` AS prod
                  INNER JOIN `".$wpdb->prefix."wshop_products_to_categories` AS pr_cat ON pr_cat.product_id = prod.product_id
                  LEFT JOIN `".$wpdb->prefix."wshop_categories` AS cat ON pr_cat.category_id = cat.category_id
                  $adv_from
                  WHERE prod.product_publish = '1' and prod.label_id!=0 AND cat.category_publish='1' ".$adv_query."
                  GROUP BY prod.product_id $order_query LIMIT ".$count;
        $products = $this->_db->get_results($query);
        $products = listProductUpdateData($products);
        return $products;
    }
    
    function getTopRatingProducts($count, $array_categories = null, $filters = array()){
        $config = Factory::getConfig();
        global $wpdb;

        $adv_query = ""; $adv_from = ""; $adv_result = $this->getBuildQueryListProductDefaultResult();
        $this->getBuildQueryListProductSimpleList("toprating", $array_categories, $filters, $adv_query, $adv_from, $adv_result);
 
        do_action_ref_array('onBeforeQueryGetProductList', array("top_rating_products", &$adv_result, &$adv_from, &$adv_query, &$order_query, &$filters));
 
        $query = "SELECT $adv_result FROM `".$wpdb->prefix."wshop_products` AS prod
                  INNER JOIN `".$wpdb->prefix."wshop_products_to_categories` AS pr_cat ON pr_cat.product_id = prod.product_id
                  LEFT JOIN `".$wpdb->prefix."wshop_categories` AS cat ON pr_cat.category_id = cat.category_id
                  $adv_from
                  WHERE prod.product_publish = '1' AND cat.category_publish='1' ".$adv_query."
                  GROUP BY prod.product_id ORDER BY prod.average_rating desc LIMIT ".$count;
        $products = $this->_db->get_results($query);
        $products = listProductUpdateData($products);
        return $products;
    }
    
    function getTopHitsProducts($count, $array_categories = null, $filters = array()){
        $config = Factory::getConfig();
        global $wpdb;

        $adv_query = ""; $adv_from = ""; $adv_result = $this->getBuildQueryListProductDefaultResult();

        $this->getBuildQueryListProductSimpleList("tophits", $array_categories, $filters, $adv_query, $adv_from, $adv_result);
        do_action_ref_array( 'onBeforeQueryGetProductList', array("top_hits_products", &$adv_result, &$adv_from, &$adv_query, &$order_query, &$filters));
        $query = "SELECT $adv_result FROM `".$wpdb->prefix."wshop_products` AS prod
                  INNER JOIN `".$wpdb->prefix."wshop_products_to_categories` AS pr_cat ON pr_cat.product_id = prod.product_id
                  LEFT JOIN `".$wpdb->prefix."wshop_categories` AS cat ON pr_cat.category_id = cat.category_id
                  $adv_from
                  WHERE prod.product_publish = '1' AND cat.category_publish='1' ".$adv_query."
                  GROUP BY prod.product_id ORDER BY prod.hits desc LIMIT ".$count;
        $products = $this->_db->get_results($query);
        $products = listProductUpdateData($products);
        return $products;   
    }
    
    function getAllProducts($filters, $order = null, $orderby = null, $limitstart = 0, $limit = 0){
        $config = Factory::getConfig();
//        $lang = Factory::getLang();
        $adv_query = ""; $adv_from = ""; $adv_result = $this->getBuildQueryListProductDefaultResult();
        $this->getBuildQueryListProduct("products", "list", $filters, $adv_query, $adv_from, $adv_result);
        $order_query = $this->getBuildQueryOrderListProduct($order, $orderby, $adv_from);
 
        do_action_ref_array( 'onBeforeQueryGetProductList', array("all_products", &$adv_result, &$adv_from, &$adv_query, &$order_query, &$filters) );
        
        $query = "SELECT $adv_result FROM `$this->_tbl` AS prod
                  LEFT JOIN `".$this->_db->prefix."wshop_products_to_categories` AS pr_cat USING (product_id)
                  LEFT JOIN `".$this->_db->prefix."wshop_categories` AS cat ON pr_cat.category_id = cat.category_id
                  $adv_from
                  WHERE prod.product_publish = '1' AND cat.category_publish='1' ".$adv_query."
                  GROUP BY prod.product_id ".$order_query;
        if ($limit){
            $query .= " LIMIT $limitstart, $limit";
        }
        $products = $this->_db->get_results($query);
        $products = listProductUpdateData($products);
        return $products;
    }    
    
    function getCountAllProducts($filters) {
        $config = Factory::getConfig();
        $adv_query = ""; $adv_from = ""; $adv_result = "";
        $this->getBuildQueryListProduct("products", "count", $filters, $adv_query, $adv_from, $adv_result);

        do_action_ref_array( 'onBeforeQueryCountProductList', array("all_products", &$adv_result, &$adv_from, &$adv_query, &$filters) );
        
        $query = "SELECT COUNT(distinct prod.product_id) FROM `$this->_tbl` as prod
                  LEFT JOIN `".$this->_db->prefix."wshop_products_to_categories` AS pr_cat USING (product_id)
                  LEFT JOIN `".$this->_db->prefix."wshop_categories` AS cat ON pr_cat.category_id = cat.category_id
                  $adv_from
                  WHERE prod.product_publish = '1' AND cat.category_publish='1' ".$adv_query;
        return $this->_db->get_var($query);
    }

    function getReviews($limitstart = 0, $limit = 20) {
        $query = "SELECT * FROM `".$this->_db->prefix."wshop_products_reviews` WHERE product_id = '".$this->_db->escape($this->product_id)."' and publish='1' order by review_id desc";
        if ($limit){
            $query .= " LIMIT $limitstart, $limit";
        }
        $rows = $this->_db->get_results($query);
        do_action_ref_array('onAfterGetReviewsProduct', array(&$this, &$rows, &$limitstart, &$limit));
        return $rows;
    }
    
    function getReviewsCount(){
        $query = "SELECT count(review_id) FROM `".$this->_db->prefix."wshop_products_reviews` WHERE product_id = '".$this->_db->escape($this->product_id)."' and publish='1'";
        $row = $this->_db->get_var($query);
        do_action_ref_array('onAfterGetReviewsCountProduct', array(&$this, &$row));
        return $row;
    }

    function getAverageRating() {
        $query = "SELECT ROUND(AVG(mark),2) FROM `".$this->_db->prefix."wshop_products_reviews` WHERE product_id = '".$this->_db->escape($this->product_id)."' and mark > 0 and publish='1'";
        $row = $this->_db->get_var($query);
        do_action_ref_array('onAfterGetAverageRatingProduct', array(&$this, &$row));
        return $row;
    }
    
    function loadAverageRating(){
        $this->average_rating = $this->getAverageRating();
        if (!$this->average_rating) $this->average_rating = 0;
    }
    
    function loadReviewsCount(){
        $this->reviews_count = $this->getReviewsCount();
    }
    
    function getExtAttributeData($pid){
        $product = Factory::getTable('product');
        $product->load($pid);
    return $product;
    }
    
    function getBuildSelectAttributes($attributeValues, $attributeActive){
        $config = Factory::getConfig();
        if (!$config->admin_show_attributes) return array();
        $attrib = Factory::getAllAttributes();
		$userShop = Factory::getUserShop();
        $selects = array();

        foreach($attrib as $k=>$v){
            $attr_id = $v->attr_id;
            if (isset($attributeValues[$attr_id]) && $attributeValues[$attr_id]){
                if (isset($attributeActive[$attr_id])){
                    $_firstval = $attributeActive[$attr_id];
                }else{
                    $_firstval = 0;
                }
                $selects[$attr_id] = new stdClass();
                $selects[$attr_id]->attr_id = $attr_id;
                $selects[$attr_id]->attr_name = $v->name;
                $selects[$attr_id]->attr_description = $v->description;
                $selects[$attr_id]->groupname = $v->groupname;
                $selects[$attr_id]->firstval = $_firstval;
                $options = $attributeValues[$attr_id];
                $attrimage = array();
                foreach($options as $k2=>$v2){
                    $attrimage[$v2->val_id] = $v2->image;
					$addPrice = $v2->addprice;
                    $addPrice = getPriceFromCurrency($addPrice, $this->currency_id);
                    $addPrice = getPriceCalcParamsTax($addPrice, $this->product_tax_id);
                    if ($userShop->percent_discount){
                        $addPrice = getPriceDiscount($addPrice, $userShop->percent_discount);
                    }

                    $options[$k2]->addprice = $addPrice;
                }

                if ($v->attr_type==1){
                // attribut type select
                
                    if ($config->attr_display_addprice){
                        foreach($options as $k2=>$v2){
                            if (($v2->price_mod=="+" || $v2->price_mod=="-" || $config->attr_display_addprice_all_sign) && $v2->addprice>0){
                                $ext_price_info = " (".$v2->price_mod.formatprice($v2->addprice).")";
                                $options[$k2]->value_name .=$ext_price_info;
                            }
                        }
                    }

                    if ($config->product_attribut_first_value_empty){
                        $first = array();
                        $first[] = HTML::_('select.option', '0', _WOP_SHOP_SELECT, 'val_id','value_name');
                        $options = array_merge($first, $options);
                    }
                    
                    if (isset($attributeActive[$attr_id]) && isset($attrimage[$attributeActive[$attr_id]])){
                        $_active_image = $attrimage[$attributeActive[$attr_id]];
                    }else{
                        $_active_image = '';
                    }
                    if (isset($attributeActive[$attr_id])){
                        $_select_active = $attributeActive[$attr_id];
                    }else{
                        $_select_active = '';
                    }
                    $selects[$attr_id]->selects = HTML::_('select.genericlist', $options, 'wshop_attr_id['.$attr_id.']','class = "inputbox" size = "1" onchange="setAttrValue(\''.$attr_id.'\', this.value);"','val_id','value_name', $_select_active)."<span class='prod_attr_img'>".$this->getHtmlDisplayProdAttrImg($attr_id, $_active_image)."</span>";
                    $selects[$attr_id]->selects = str_replace(array("\n","\r","\t"), "", $selects[$attr_id]->selects);
                }else{
                // attribut type radio
                
                    foreach($options as $k2=>$v2){
                        if ($v2->image) $options[$k2]->value_name = "<img src='".$config->image_attributes_live_path."/".$v2->image."' alt='' /> ".$v2->value_name;
                    }

                    if ($config->attr_display_addprice){
                        foreach($options as $k2=>$v2){
                            if (($v2->price_mod=="+" || $v2->price_mod=="-" || $config->attr_display_addprice_all_sign) && $v2->addprice>0){
                                $ext_price_info = " (".$v2->price_mod.formatprice($v2->addprice).")";
                                $options[$k2]->value_name .=$ext_price_info;
                            }
                        }
                    }

                    $radioseparator = '';
                    if ($config->radio_attr_value_vertical) $radioseparator = "<br/>"; 
                    foreach($options as $k2=>$v2){
                        $options[$k2]->value_name = "<span class='radio_attr_label'>".$v2->value_name."</span>";
                    }

                    $selects[$attr_id]->selects = sprintRadioList($options, 'wshop_attr_id['.$attr_id.']','onclick="setAttrValue(\''.$attr_id.'\', this.value);"','val_id','value_name', $attributeActive[$attr_id], $radioseparator);
                    $selects[$attr_id]->selects = str_replace(array("\n","\r","\t"), "", $selects[$attr_id]->selects);
                }
                do_action_ref_array('onBuildSelectAttribute', array(&$attributeValues, &$attributeActive, &$selects, &$options, &$attr_id, &$v));
            }
        }
        $grname = '';
        foreach($selects as $k=>$v){
            if ($v->groupname!=$grname){
                $grname = $v->groupname;
                $selects[$k]->grshow = 1;
            }else{
                $selects[$k]->grshow = 0;
            }
        }
    return $selects;
    }

    function getHtmlDisplayProdAttrImg($attr_id, $img){
        $config = Factory::getConfig();
        if ($img){
            $path = $config->image_attributes_live_path;
        }else{
            $path = $config->live_path."assets/images";
            $img = "blank.gif";
        }
        $urlimg = $path."/".$img;
        
        $html = '<img id="prod_attr_img_'.$attr_id.'" src="'.$urlimg.'" alt="" />';
        return $html;
    }
}