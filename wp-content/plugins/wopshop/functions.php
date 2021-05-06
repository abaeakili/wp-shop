<?php
/**
* @version      1.0.0 01.06.2016
* @author       MAXXmarketing GmbH
* @package      WOPshop
* @copyright    Copyright (C) 2010 http://www.wop-agentur.com. All rights reserved.
* @license      GNU/GPL
*/

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
function quickiconButton( $link, $image, $text ){
?>
    <div style="float:left;">
        <div class="icon">
            <a href="<?php echo $link?>">
                <img src="<?php print WOPSHOP_PLUGIN_URL?>assets/images/<?php print $image?>" alt="">
                <span><?php echo $text?></span>
            </a>
        </div>
    </div>

<?php
}

function ws_add_trigger($vars = array(), $name = ''){
    list(,$caller) = debug_backtrace();
	$trigger_name = 'on'.ucfirst($caller['class']).ucfirst($caller['function']).ucfirst($name);
    do_action_ref_array($trigger_name, array(&$caller['object'], &$vars));
    return $vars;
}

function SEFLink($url, $useDefaultPage = 0, $redirect = 0, $ssl = null, $langPrefix = null){
    $xhtml = $redirect ? false : true;
    $router = new WshopRouter();
    $url = $router->build($url, $ssl, $langPrefix);

    if ($xhtml) {
        return htmlspecialchars($url);
    }

    return $url;
}

function xhtmlUrl($url, $filter=1){
    if ($filter){
        $url = jsFilterUrl($url);
    }
    $url = str_replace("&","&amp;",$url);    

    return $url;
}

function jsFilterUrl($url){
    $url = strip_tags($url);
return $url;
}

function getQuerySortDirection($fieldnum, $ordernum){
    $dir = "ASC";
    if ($ordernum) {
        $dir = "DESC";
        if ($fieldnum==5 || $fieldnum==6) $dir = "ASC";
    } else {
        $dir = "ASC";
        if ($fieldnum==5 || $fieldnum==6) $dir = "DESC";
    }
return $dir;
}

function getImgSortDirection($fieldnum, $ordernum){
    if ($ordernum) {
        $image = 'arrow_down.gif';
    } else {
        $image = 'arrow_up.gif';
    }
return $image;
}

function getBuildFilterListProduct($contextfilter, $no_filter = array()){
    $config = Factory::getConfig();
    $mainframe =Factory::getApplication();
    
    $category_id = Request::getInt('category_id');
    $manufacturer_id = Request::getInt('manufacturer_id');
    $label_id = Request::getInt('label_id');
    $vendor_id = Request::getInt('vendor_id');
    $freeatribute_id = Request::getInt('freeatribute_id');
    $price_from = saveAsPrice(Request::getVar('price_from'));
    $price_to = saveAsPrice(Request::getVar('price_to'));
    
    $categorys = $mainframe->getUserStateFromRequest( $contextfilter.'categorys', 'categorys', array());
    $categorys = filterAllowValue($categorys, "int+");
    $tmpcd = getListFromStr(Request::getVar('category_id'));    
    if (is_array($tmpcd) && !$categorys) $categorys = $tmpcd;
    
    $manufacturers = $mainframe->getUserStateFromRequest( $contextfilter.'manufacturers', 'manufacturers', array());
    $manufacturers = filterAllowValue($manufacturers, "int+");
    $tmp = getListFromStr(Request::getVar('manufacturer_id'));    
    if (is_array($tmp) && !$manufacturers) $manufacturers = $tmp;
    
    $labels = $mainframe->getUserStateFromRequest( $contextfilter.'labels', 'labels', array());
    $labels = filterAllowValue($labels, "int+");
    $tmplb = getListFromStr(Request::getVar('label_id'));    
    if (is_array($tmplb) && !$labels) $labels = $tmplb;
    
    $vendors = $mainframe->getUserStateFromRequest( $contextfilter.'vendors', 'vendors', array());
    $vendors = filterAllowValue($vendors, "int+");
    $tmp = getListFromStr(Request::getVar('vendor_id'));    
    if (is_array($tmp) && !$vendors) $vendors = $tmp;
    
    if ($config->admin_show_product_extra_field){
        $extra_fields = $mainframe->getUserStateFromRequest( $contextfilter.'extra_fields', 'extra_fields', array());
        $extra_fields = filterAllowValue($extra_fields, "array_int_k_v+");
    }
    $fprice_from = $mainframe->getUserStateFromRequest( $contextfilter.'fprice_from', 'fprice_from');
    $fprice_from = saveAsPrice($fprice_from);
    if (!$fprice_from) $fprice_from = $price_from;
    $fprice_to = $mainframe->getUserStateFromRequest( $contextfilter.'fprice_to', 'fprice_to');
    $fprice_to = saveAsPrice($fprice_to);
    if (!$fprice_to) $fprice_to = $price_to;

    $filters = array();
    $filters['categorys'] = $categorys;
    $filters['manufacturers'] = $manufacturers;
    $filters['price_from'] = $fprice_from;
    $filters['price_to'] = $fprice_to;
    $filters['labels'] = $labels;
    $filters['vendors'] = $vendors;
    if ($config->admin_show_product_extra_field){
        $filters['extra_fields'] = $extra_fields;
    }
    if ($category_id && !$filters['categorys']){
        $filters['categorys'][] = $category_id;
    }
    if ($manufacturer_id && !$filters['manufacturers']){
        $filters['manufacturers'][] = $manufacturer_id;
    }
    if ($label_id && !$filters['labels']){
        $filters['labels'][] = $label_id;
    }
    if ($freeatribute_id && !$filters['freeatributes']){
        $filters['freeatributes'] = $freeatribute_id;
    }
    if ($vendor_id && !$filters['vendors']){
        $filters['vendors'][] = $vendor_id;
    }
    if (is_array($filters['vendors'])){
        $main_vendor = Factory::getMainVendor();
        foreach($filters['vendors'] as $vid){
            if ($vid == $main_vendor->id){
                $filters['vendors'][] = 0;
            }
        }
    }
    foreach($no_filter as $filterkey){
        unset($filters[$filterkey]);
    }
return $filters;
}

function getListFromStr($stelist){
    if (preg_match('/\,/', $stelist)){
        return filterAllowValue(explode(',',$stelist), 'int+');
    }else{
        return null;
    }
}

function filterAllowValue($data, $type){
    
    if ($type=="int+"){
        if (is_array($data)){
            foreach($data as $k=>$v){
                $v = intval($v);
                if ($v>0){
                    $data[$k] = $v;
                }else{
                    unset($data[$k]);
                }
            }
        }
    }
    
    if ($type=="array_int_k_v+"){
        if (is_array($data)){
            foreach($data as $k=>$v){
                $k = intval($k);
                if (is_array($v)){
                    foreach($v as $k2=>$v2){
                        $k2 = intval($k2);
                        $v2 = intval($v2);
                        if ($v2>0){
                            $data[$k][$k2] = $v2;
                        }else{
                            unset($data[$k][$k2]);
                        }
                    }
                }
            }
        }
    }
    
    return $data;
}

function listProductUpdateData($products, $setUrl = 0) {
    $config = Factory::getConfig();
    $userShop = Factory::getUserShop();
    $taxes = Factory::getAllTaxes();
    if ($config->product_list_show_manufacturer){
        $manufacturers = Factory::getAllManufacturer();
    }
    if ($config->show_delivery_time){
        $deliverytimes = Factory::getAllDeliveryTime();
    }
    if ($config->product_list_show_vendor){
        $vendors = Factory::getAllVendor();
    }    

    $image_path = $config->image_product_live_path;
    $noimage = $config->noimage;

    foreach ($products as $key => $value) {
        do_action_ref_array('onListProductUpdateDataProduct', array(&$products, &$key, &$value));
        
        $use_userdiscount = 1;
        if ($config->user_discount_not_apply_prod_old_price && $products[$key]->product_old_price > 0){
            $use_userdiscount = 0;
        }
        $products[$key]->_original_product_price = $products[$key]->product_price;
        $products[$key]->product_price_wp = $products[$key]->product_price;
        $products[$key]->product_price_default = 0;
        if ($config->product_list_show_min_price) {
            if ($products[$key]->min_price > 0){
                $products[$key]->product_price = $products[$key]->min_price;
            }
        }
        $products[$key]->show_price_from = 0;
        if ($config->product_list_show_min_price && $value->different_prices) {
            $products[$key]->show_price_from = 1;
        }

        $products[$key]->product_price = getPriceFromCurrency($products[$key]->product_price, $products[$key]->currency_id);
        $products[$key]->product_old_price = getPriceFromCurrency($products[$key]->product_old_price, $products[$key]->currency_id);
        $products[$key]->product_price_wp = getPriceFromCurrency($products[$key]->product_price_wp, $products[$key]->currency_id);
        $products[$key]->product_price = getPriceCalcParamsTax($products[$key]->product_price, $products[$key]->tax_id);
        $products[$key]->product_old_price = getPriceCalcParamsTax($products[$key]->product_old_price, $products[$key]->tax_id);
        $products[$key]->product_price_wp = getPriceCalcParamsTax($products[$key]->product_price_wp, $products[$key]->tax_id);

        if ($userShop->percent_discount && $use_userdiscount) {
            $products[$key]->product_price_default = $products[$key]->_original_product_price;
            $products[$key]->product_price_default = getPriceFromCurrency($products[$key]->product_price_default, $products[$key]->currency_id);
            $products[$key]->product_price_default = getPriceCalcParamsTax($products[$key]->product_price_default, $products[$key]->tax_id);

            $products[$key]->product_price = getPriceDiscount($products[$key]->product_price, $userShop->percent_discount);
            $products[$key]->product_old_price = getPriceDiscount($products[$key]->product_old_price, $userShop->percent_discount);
            $products[$key]->product_price_wp = getPriceDiscount($products[$key]->product_price_wp, $userShop->percent_discount);
        }    
        
        
        if ($config->list_products_calc_basic_price_from_product_price) {
            $products[$key]->basic_price_info = getProductBasicPriceInfo($value, $products[$key]->product_price_wp);
        } else {
            $products[$key]->basic_price_info = getProductBasicPriceInfo($value, $products[$key]->product_price);
        }

        if ($value->tax_id) {
            $products[$key]->tax = $taxes[$value->tax_id];
        }

        if ($config->product_list_show_manufacturer && $value->product_manufacturer_id && isset($manufacturers[$value->product_manufacturer_id])) {
            $products[$key]->manufacturer = $manufacturers[$value->product_manufacturer_id];
        } else {
            $products[$key]->manufacturer = new stdClass();
            $products[$key]->manufacturer->name = '';
        }
        if ($config->admin_show_product_extra_field){
            $products[$key]->extra_field = getProductExtraFieldForProduct($value);
        } else {
            $products[$key]->extra_field = '';
        }
        if ($config->product_list_show_vendor){
            $vendordata = $vendors[$value->vendor_id];
            $vendordata->products = SEFLink("controller=vendor&task=products&vendor_id=".$vendordata->id,1);
            $products[$key]->vendor = $vendordata;
        }else{
            $products[$key]->vendor = '';
        }
        if ($config->hide_delivery_time_out_of_stock && $products[$key]->product_quantity <= 0) {
            $products[$key]->delivery_times_id = 0;
            $value->delivery_times_id = 0;
        }
        if ($config->show_delivery_time && $value->delivery_times_id) {
            $products[$key]->delivery_time = $deliverytimes[$value->delivery_times_id];
        } else {
            $products[$key]->delivery_time = '';
        }
        $products[$key]->_display_price = getDisplayPriceForProduct($products[$key]->product_price);
        if (!$products[$key]->_display_price) {
            $products[$key]->product_old_price = 0;
            $products[$key]->product_price_default = 0;
            $products[$key]->basic_price_info['price_show'] = 0;
            $products[$key]->tax = 0;
            $config->show_plus_shipping_in_product = 0;
        }
        if ($config->product_list_show_qty_stock) {
            $products[$key]->qty_in_stock = getDataProductQtyInStock($products[$key]);
        }
        $image = getPatchProductImage($products[$key]->image);
        $products[$key]->product_name_image = $products[$key]->image;
        $products[$key]->product_thumb_image = $image;
        if (!$image)
            $image = $noimage;
        $products[$key]->image = $image_path . "/" . $image;
        $products[$key]->template_block_product = "product.php";
        if (!$config->admin_show_product_labels)
            $products[$key]->label_id = null;
        if ($products[$key]->label_id) {
            $image = getNameImageLabel($products[$key]->label_id);
            if ($image) {
                $products[$key]->_label_image = $config->image_labels_live_path . "/" . $image;
            }
            $products[$key]->_label_name = getNameImageLabel($products[$key]->label_id, 2);
        }
        if ($config->display_short_descr_multiline) {
            $products[$key]->short_description = nl2br($products[$key]->short_description);
        }
    }

    if ($setUrl) {
        addLinkToProducts($products);
    }
    
    do_action_ref_array('onListProductUpdateData', array(&$products));
    
    return $products;
}

function getProductExtraFieldForProduct($product){
    $fields = Factory::getAllProductExtraField();
    $fieldvalues = Factory::getAllProductExtraFieldValue();
    $displayfields = Factory::getDisplayListProductExtraFieldForCategory($product->category_id);
    $rows = array();
    foreach($displayfields as $field_id){
        $field_name = "extra_field_".$field_id;
        if ($fields[$field_id]->type==0){
            if ($product->$field_name!=0){
                $listid = explode(',', $product->$field_name);
                $tmp = array();
                foreach($listid as $extrafiledvalueid){
                    $tmp[] = $fieldvalues[$extrafiledvalueid];
                }
                $extra_field_value = implode(", ", $tmp);
                $rows[$field_id] = array("name"=>$fields[$field_id]->name, "description"=>$fields[$field_id]->description, "value"=>$extra_field_value);
            }
        }else{
            if ($product->$field_name!=""){
                $rows[$field_id] = array("name"=>$fields[$field_id]->name, "description"=>$fields[$field_id]->description, "value"=>$product->$field_name);
            }
        }
    }
return $rows;
}

function replaceNbsp($string) {
return (str_replace(" ","_",$string));
}

function replaceToNbsp($string) {
return (str_replace("_"," ",$string));
}

function getTextNameArrayValue($names, $values){
    $return = '';
    foreach ($names as $key=>$value){
        $return .= $names[$key].": ".$values[$key]."\n";
    }
    return $return;
}

function addLinkToProducts(&$products){
    $config = Factory::getConfig();
    
    foreach($products as $key=>$value){
        $products[$key]->product_link = SEFLink('controller=product&task=view&product_id='.$products[$key]->product_id);
        $products[$key]->buy_link = '';
        if ($config->show_buy_in_category && $products[$key]->_display_price){
            if (!($config->hide_buy_not_avaible_stock && ($products[$key]->product_quantity <= 0))){
                $products[$key]->buy_link = SEFLink('controller=cart&task=add&product_id='.$products[$key]->product_id);
            }
        }
    }
}

function getPriceDiscount($price, $discount){
    return $price - ($price*$discount/100);
}

function getMessageJson(){
   global $cart_error;
   $rows = array();
	if ( $cart_error->get_error_code() ) {
		foreach( $cart_error->get_error_messages() as $error ){
			$rows[] = array("message"=>$error);
		}
	}
	$session = Factory::getSession();
	$session->set('application.queue', null);
	return json_encode($rows);
}

function getOkMessageJson($cart){
	global $cart_error;
    if ( $cart_error->get_error_code() ) {
        return getMessageJson(); 
    }else{
        return json_encode($cart);
    }
}

function getCalculateDeliveryDay($day, $date=null){
    if (!$date){
        $date = getJsDate();
    }
    $time = intval(strtotime($date) + $day*86400);
return date('Y-m-d H:i:s', $time);
}

function getProductBySlug($alias) {
    global $wpdb;
    $config = Factory::getConfig();
    $lang = $config->cur_lang;
    $query = "SELECT product_id FROM `".$wpdb->prefix.'wshop_products'."` WHERE `alias_".$lang."` = '".esc_sql($alias)."'";
    $id = $wpdb->get_var($query);
    return ($id) ? $id : $alias;
}

function insertValueInArray($value, &$array) {
    if ($key = array_search($value, $array)) return $key;
    $array[$value] = $value;
    asort($array);
    return $key-1;
}

function willBeUseFilter($filters){
    $res = 0;    
    if (isset($filters['price_from']) && $filters['price_from']>0) $res = 1;
    if (isset($filters['price_to']) && $filters['price_to']>0) $res = 1;
    if (isset($filters['categorys']) && count($filters['categorys'])>0) $res = 1;
    if (isset($filters['manufacturers']) && count($filters['manufacturers'])>0) $res = 1;
    if (isset($filters['vendors']) && count($filters['vendors'])>0) $res = 1;    
    if (isset($filters['labels']) && count($filters['labels'])>0) $res = 1;
    if (isset($filters['extra_fields']) && count($filters['extra_fields'])>0) $res = 1;
return $res;
}

function getQueryListProductsExtraFields(){
    $query = "";
    $list = getAllProductExtraField();
    $config_list = getProductListDisplayExtraFields();
    foreach($list as $v){
        if (in_array($v->id, $config_list)){
            $query .= ", prod.`extra_field_".$v->id."` ";
        }
    }

    return $query;
}

function getAllProductExtraField(){
static $list;
    if (!is_array($list)){
        $productfield = Factory::getTable('productfield');
        $list = $productfield->getList();
    }
return $list;
}

function getProductListDisplayExtraFields(){
    $config = Factory::getConfig();
    if ($config->product_list_display_extra_fields!=""){
        return json_decode($config->product_list_display_extra_fields, 1);
    }else{
        return array();
    }
}

function getDataProductQtyInStock($product){
    $qty = $product->product_quantity;
    if ($product instanceof ProductWshopTable){
        $qty = $product->getQty();
    }

    $qty_in_stock = array(
        "qty" => floatval($qty), 
        "unlimited" => $product->unlimited
    );
    
    if ($qty_in_stock['qty'] < 0) {
        $qty_in_stock['qty'] = 0;
    }

    return $qty_in_stock;
}

function sprintQtyInStock($qty_in_stock){
    if (!is_array($qty_in_stock)){
        return $qty_in_stock;
    }else{
        if ($qty_in_stock['unlimited']){
            return _WOP_SHOP_UNLIMITED;
        }else{
            return $qty_in_stock['qty'];
        }
    }
}

/**
* check date Format date yyyy-mm-dd
*/
function checkMyDate($date) {
    if (trim($date)=="") return false;
    $arr = explode("-",$date);
return checkdate($arr[1],$arr[2],$arr[0]);
}

function getProductBasicPriceInfo($obj, $price){
    $config = Factory::getConfig();
    $price_show = $obj->weight_volume_units!=0;

    if (!$config->admin_show_product_basic_price || $price_show==0){
        return array("price_show"=>0);
    }

    $units = Factory::getAllUnits();
    $unit = $units[$obj->basic_price_unit_id];
    $basic_price = $price / $obj->weight_volume_units * $unit->qty;

    return array("price_show"=>$price_show, "basic_price"=>$basic_price, "name"=>$unit->name, "unit_qty"=>$unit->qty);
}

function getDisplayPriceForProduct($price){
    $config = Factory::getConfig();
    $user = Factory::getUser();
    $display_price = 1;
    if ($config->displayprice==1){
        $display_price = 0;
    }elseif($config->displayprice==2 && !$user->id){
        $display_price = 0;
    }
    if ($display_price && $price==0 && $config->user_as_catalog){
        $display_price = 0;
    }
    
    return $display_price;
}

function getDisplayPriceShop(){
    $config = Factory::getConfig();
    $user = Factory::getUser();

    $display_price = 1;
    if ($config->displayprice == 1){
        $display_price = 0;
    } else if ($config->displayprice == 2 && !$user->user_id){
        $display_price = 0;
    }

    return $display_price;
}

function getPriceTaxRatioForProducts($products, $group = 'tax'){
    $prodtaxes = array();
    foreach($products as $k => $v){
        if (!isset($prodtaxes[$v[$group]])) {
            $prodtaxes[$v[$group]] = 0;
        }
        $prodtaxes[$v[$group]] += $v['price'] * $v['quantity'];
    }

    $sumproducts = array_sum($prodtaxes);        
    foreach($prodtaxes as $k => $v){
		if ($sumproducts > 0){
			$prodtaxes[$k] = $v / $sumproducts;
		} else {
			$prodtaxes[$k] = 0;
		}
    }

    return $prodtaxes;
}

function getFixBrutopriceToTax($price, $tax_id){
    $config = Factory::getConfig();
    if ($config->no_fix_brutoprice_to_tax==1){
        return $price;
    }
    $taxoriginal = Factory::getAllTaxesOriginal();
    $taxes = Factory::getAllTaxes();
    $tax = $taxes[$tax_id];
    $tax2 = $taxoriginal[$tax_id];
    if ($tax!=$tax2){
        $price = $price / (1 + $tax2 / 100);
        $price = $price * (1+$tax/100);    
    }
return $price;
}

function getPriceTaxValue($price, $tax, $price_netto = 0){
    if ($price_netto==0){
        $tax_value = $price * $tax / (100 + $tax);
    }else{
        $tax_value = $price * $tax / 100;
    }
return $tax_value;
}

function checkUserLogin(){
    $config = Factory::getConfig();
    $user = wp_get_current_user();
    header("Cache-Control: no-cache, must-revalidate");
    
    if (!$user->ID) {
        $application = Factory::getApplication();
        $return = base64_encode($_SERVER['REQUEST_URI']);
        $session = Factory::getSession();
        $session->set("return", $return);
        
        $application->redirect(SEFLink('controller=user&task=login', 1, 1, $config->use_ssl));
        exit();
    }
}

function updateAllprices( $ignore = array() ){
    $cart = Factory::getModel('cart');
    $cart->load();
    $cart->updateCartProductPrice();
    
    $sh_pr_method_id = $cart->getShippingPrId();
    if ($sh_pr_method_id){
        $shipping_method_price = Factory::getTable('shippingmethodprice');
        $shipping_method_price->load($sh_pr_method_id);
        $prices = $shipping_method_price->calculateSum($cart);
        $cart->setShippingsDatas($prices, $shipping_method_price);
    }
    $payment_method_id = $cart->getPaymentId();
    if ($payment_method_id){
        $paym_method = Factory::getTable('paymentmethod');
        $paym_method->load($payment_method_id);
        $paym_method->setCart($cart);
        $price = $paym_method->getPrice();
        $cart->setPaymentDatas($price, $paym_method);
    }
    
    $cart = Factory::getModel('cart');
    $cart->load('wishlist');
    $cart->updateCartProductPrice();   
}

function setNextUpdatePrices(){
    $session =Factory::getSession();
    $session->set('wshop_update_all_price', 1);
}

function sprintAtributeInCart($atribute){
    $html = "";
    if (count($atribute)) $html .= '<div class="list_attribute">';
    foreach($atribute as $attr){
        do_action_ref_array('beforeSprintAtributeInCart', array(&$attr) );
        $html .= '<p class="wshop_cart_attribute"><span class="name">'.$attr->attr.'</span>: <span class="value">'.$attr->value.'</span></p>';
    }
    if (count($atribute)) $html .= '</div>';
return $html;
}

function sprintFreeAtributeInCart($freeatribute){
    $html = "";
    if (count($freeatribute)) $html .= '<div class="list_free_attribute">';
    foreach($freeatribute as $attr){
        do_action_ref_array('beforeSprintFreeAtributeInCart', array(&$attr) );
        $html .= '<p class="wshop_cart_attribute"><span class="name">'.$attr->attr.'</span>: <span class="value">'.$attr->value.'</span></p>';
    }
    if (count($freeatribute)) $html .= '</div>';
return $html;
}

function sprintFreeExtraFiledsInCart($extra_fields){
    $html = "";
    if (count($extra_fields)) $html .= '<div class="list_extra_field">';
    foreach($extra_fields as $f){
        do_action_ref_array('beforeSprintExtraFieldsInCart', array(&$f) );
        $html .= '<p class="wshop_cart_extra_field"><span class="name">'.$f['name'].'</span>: <span class="value">'.$f['value'].'</span></p>';
    }
    if (count($extra_fields)) $html .= '</div>';
return $html;
}

function searchChildCategories($category_id,$all_categories,&$cat_search) {
    foreach ($all_categories as $all_cat) {
        if($all_cat->category_parent_id == $category_id) {
            searchChildCategories($all_cat->category_id, $all_categories, $cat_search);
            $cat_search[] = $all_cat->category_id;
        }
    }
}

function getDBFieldNameFromConfig($name){
    $config = Factory::getConfig();
    $tmp = explode('.', $name);
    if (count($tmp)>1){
        $res = $tmp[0].'.';
        $field = $tmp[1];
    }else{
        $res = '';
        $field = $tmp[0];
    }
    $tmp2 = explode(':', $field);
    if (count($tmp2)>1 && $tmp2[0]=='ml'){
        $res .= '`'.$tmp2[1].'_'.$config->cur_lang.'`';
    }else{
        $res .= '`'.$field.'`';
    }
return $res;
}

function showMarkStar($rating){
    $config = Factory::getConfig();
    $count = floor($config->max_mark / $config->rating_starparts);
    $width = $count * 16;
    $rating = round($rating);
    $width_active = intval($rating * 16 / $config->rating_starparts);
    $html = "<div class='stars_no_active' style='width:".$width."px'>";
    $html .= "<div class='stars_active' style='width:".$width_active."px'>";
    $html .= "</div>";
    $html .= "</div>";
return $html;
}

function sprintRadioList($list, $name, $params, $key, $val, $actived = null, $separator = ' '){
    $html = "";
    $id = str_replace("[","",$name);
    $id = str_replace("]","",$id);
    foreach($list as $obj){
        $id_text = $id.$obj->$key;
        if ($obj->$key == $actived) $sel = ' checked="checked"'; else $sel = '';
        $html.='<span class="input_type_radio"><input type="radio" name="'.$name.'" id="'.$id_text.'" value="'.$obj->$key.'"'.$sel.' '.$params.'> <label for="'.$id_text.'">'.$obj->$val."</label></span>".$separator;
    }
return $html;
}

function saveToLog($file, $text){
    $config = Factory::getConfig();
    if (!$config->savelog) {
        return 0;
    }
    if ($file == 'paymentdata.log' && !$config->savelogpaymentdata) {
        return 0;
    }
    $f = fopen($config->log_path.$file, "a+");
    fwrite($f, date('Y-m-d H:i:s')." ".$text."\r\n");
    fclose($f);
    
    return 1;
}

function json_value_encode($val, $textfix = 0){
    if ($textfix){
        $val = str_replace(array("\n","\r","\t"), "", $val);
    }
    $val = str_replace('"', '\"', $val);
    return $val;
}

function getPriceCalcParamsTax($price, $tax_id, $products = array()) {
    $config = Factory::getConfig();
    $taxes = Factory::getAllTaxes();
    if ($tax_id == -1) {
        $prodtaxes = getPriceTaxRatioForProducts($products);
    }
    if ($config->display_price_admin == 0 && $tax_id > 0) {
        $price = getFixBrutopriceToTax($price, $tax_id);
    }
    if ($config->display_price_admin == 0 && $tax_id == -1) {
        $prices = array();
        $prodtaxesid = getPriceTaxRatioForProducts($products, 'tax_id');
        foreach ($prodtaxesid as $k => $v) {
            $prices[$k] = getFixBrutopriceToTax($price * $v, $k);
        }
        $price = array_sum($prices);
    }
    if ($tax_id > 0) {
        $tax = $taxes[$tax_id];
    } elseif ($tax_id == -1) {
        $prices = array();
        foreach ($prodtaxes as $k => $v) {
            $prices[] = array('tax' => $k, 'price' => $price * $v);
        }
    } else {
        $taxlist = array_values($taxes);
        $tax = $taxlist[0];
    }
    if ($config->display_price_admin == 1 && $config->display_price_front_current == 0) {
        if ($tax_id == -1) {
            $price = 0;
            foreach ($prices as $v) {
                $price+= $v['price'] * (1 + $v['tax'] / 100);
            }
        } else {
            $price = $price * (1 + $tax / 100);
        }
    }
    if ($config->display_price_admin == 0 && $config->display_price_front_current == 1) {
        if ($tax_id == -1) {
            $price = 0;
            foreach ($prices as $v) {
                $price+= $v['price'] / (1 + $v['tax'] / 100);
            }
        } else {
            $price = $price / (1 + $tax / 100);
        }
    }
    return $price;
}

function getPriceFromCurrency($price, $currency_id = 0, $current_currency_value = 0) {
    $config = Factory::getConfig();
    if ($currency_id) {
        $all_currency = Factory::getAllCurrency();
        $value = $all_currency[$currency_id]->currency_value;
        if (!$value)
            $value = 1;
        $pricemaincurrency = $price / $value;
    }else {
        $pricemaincurrency = $price;
    }
    if (!$current_currency_value) {
        $current_currency_value = $config->currency_value;
    }
    return $pricemaincurrency * $current_currency_value;
}

function displayMainPanelIco(){
    $menu = array();
    $menu['categories'] = array(_WOP_SHOP_MENU_CATEGORIES, admin_url( 'admin.php?page=categories'), 'icons/categories.png', 1);
    $menu['products'] = array(_WOP_SHOP_MENU_PRODUCTS, 'admin.php?page=products', 'icons/products.png', 1);
    $menu['orders'] = array( _WOP_SHOP_MENU_ORDERS, 'admin.php?page=orders', 'icons/orders.png', 1);
    $menu['users'] = array(_WOP_SHOP_MENU_CLIENTS, 'admin.php?page=clients', 'icons/clients.png', 1);
    $menu['options'] = array(_WOP_SHOP_MENU_OTHER, 'admin.php?page=options', 'icons/options.png', 1);
    $menu['config'] = array( _WOP_SHOP_MENU_CONFIG, 'admin.php?page=configuration', 'icons/configurations.png', 1 );
    $menu['update'] = array(_WOP_SHOP_PANEL_UPDATE, 'admin.php?page=update', 'icons/install.png', 1);
    $menu['info'] = array(_WOP_SHOP_MENU_INFO, 'admin.php?page=aboutus', 'icons/about_us.png', 1);
	do_action_ref_array('onBeforeAdminMainPanelIcoDisplay', array(&$menu));

    foreach($menu as $item){
        if ($item[3]){
            quickiconButton($item[1], $item[2], $item[0]);            
        }
    }
}

function addMessage($message, $type = 'updated') {
    Factory::getApplication()->enqueueMessage($message, $type);    
}

function getStateFromRequest($key, $request, $default=null) {
    $app = Factory::getApplication();
    return $app->getUserStateFromRequest($key, $request, $default);
}

function HTML_getSelect($type, $arr, $order, $ext, $key, $value, $selected){
    if($type = 'select'){
        if(is_array($arr) and count($arr) > 0){
            $select = '<select id="'.$order.'" '.$ext.' name="'.$order.'">';
            foreach ($arr as $index=>$data){
                if($data->$key == $selected) $sell = ' selected="selected" '; else $sell = '';
                $select.= '<option '.$sell.' value="'.$data->$key.'">'.$data->$value.'</option>';
            }
            $select.= '</select>';
            return $select;
        }else{
            return 0;
        }
    }
}
    
function getBooleanlist($name, $attribs = array(), $selected = null, $yes = _WOP_SHOP_YES, $no = _WOP_SHOP_NO, $id = false)
{
    $obj = new stdClass();
    $obj->value = 0;
    $obj->text = $no;
    $arr[] = $obj;
    unset($obj);
    $obj = new stdClass();
    $obj->value = 1;
    $obj->text = $yes;
    $arr[] = $obj;    
        return getRadiolist($arr, $name, $attribs, 'value', 'text', (int) $selected, $id); 
}

function getRadiolist($data, $name, $attribs = null, $optKey = 'value', $optText = 'text', $selected = null, $idtag = false)
{
    $id_text = $idtag ? $idtag : $name;

    $html = '<div class="controls">';

    foreach ($data as $obj)
    {
            $k = $obj->$optKey;
            $t = $obj->$optText;
            $id = (isset($obj->id) ? $obj->id : null);

            $extra = '';
            $id = $id ? $obj->id : $id_text . $k;

            if (is_array($selected))
            {
                    foreach ($selected as $val)
                    {
                            $k2 = is_object($val) ? $val->$optKey : $val;

                            if ($k == $k2)
                            {
                                    $extra .= ' selected="selected" ';
                                    break;
                            }
                    }
            }
            else
            {
                    $extra .= ((string) $k == (string) $selected ? ' checked="checked" ' : '');
            }

            $html .= "\n\t" . '<label for="' . $id . '" id="' . $id . '-lbl" class="radio">';
            $html .= "\n\t\n\t" . '<input type="radio" name="' . $name . '" id="' . $id . '" value="' . $k . '" ' . $extra
                    . $attribs . ' >' . $t;
            $html .= "\n\t" . '</label>';
    }

    $html .= "\n";
    $html .= '</div>';
    $html .= "\n";

    return $html;
}

function getCheckbox($args = array())
{
    $default = array(
        'name' => '',
        'value' => '',
        'checked' => false,
        'attributes' => ''
    );
    $p = wp_parse_args($args, $default);
    return sprintf('<input type="checkbox" name="%s" value="%s" %s %s>', esc_attr($p['name']), esc_attr($p['value']), $p['checked'] == true ? 'checked="checked"' : null, $p['attributes']);
}

function callback_checkbox( $args ) {

    $value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );

    $html = sprintf( '<input type="hidden" name="%1$s[%2$s]" value="off" />', $args['section'], $args['id'] );
    $html .= sprintf( '<input type="checkbox" class="checkbox" id="%1$s[%2$s]" name="%1$s[%2$s]" value="on"%4$s />', $args['section'], $args['id'], $value, checked( $value, 'on', false ) );
    $html .= sprintf( '<label for="%1$s[%2$s]"> %3$s</label>', $args['section'], $args['id'], $args['desc'] );

    echo $html;
}

function getItemsOptionPanelMenu(){
    $config = Factory::getConfig();
    $menu = array();
    $menu['manufacturers'] = array(_WOP_SHOP_MENU_MANUFACTURERS, admin_url( 'admin.php?page=options&tab=manufacturers'), 'icons/manufacturers.png', 1);
    $menu['coupons'] = array(_WOP_SHOP_MENU_COUPONS, admin_url( 'admin.php?page=options&tab=coupons'), 'icons/coupons.png', $config->use_rabatt_code);
    $menu['currencies'] = array(_WOP_SHOP_PANEL_CURRENCIES, 'admin.php?page=options&tab=currencies', 'icons/currencies.png', 1);
    $menu['taxes'] = array(_WOP_SHOP_PANEL_TAXES, 'admin.php?page=options&tab=taxes', 'icons/taxes.png', $config->tax);
    $menu['payments'] = array( _WOP_SHOP_PANEL_PAYMENTS, 'admin.php?page=options&tab=payments', 'icons/payments.png', $config->without_payment==0);
    $menu['shippings'] = array(_WOP_SHOP_PANEL_SHIPPINGS, 'admin.php?page=options&tab=shippings', 'icons/shippings_methods.png', $config->without_shipping==0);
    $menu['shippingsprices'] = array(_WOP_SHOP_PANEL_SHIPPINGS_PRICES, 'admin.php?page=options&tab=shippingsprices', 'icons/shipping_prices.png', $config->without_shipping==0);
    $menu['deliverytimes'] = array(_WOP_SHOP_PANEL_DELIVERY_TIME, 'admin.php?page=options&tab=deliverytimes', 'icons/delivery_time.png', $config->admin_show_delivery_time);
    $menu['orderstatus'] = array(_WOP_SHOP_PANEL_ORDER_STATUS, 'admin.php?page=options&tab=orderstatus', 'icons/order_status.png', 1);
    $menu['countries'] = array( _WOP_SHOP_PANEL_COUNTRIES, 'admin.php?page=options&tab=countries', 'icons/countries.png', 1 );
    $menu['attributes'] = array(_WOP_SHOP_PANEL_ATTRIBUTES, 'admin.php?page=options&tab=attributes', 'icons/attributes.png', $config->admin_show_attributes);
    $menu['freeattributes'] = array(_WOP_SHOP_PANEL_FREEATTRIBUTES, 'admin.php?page=options&tab=freeattributes', 'icons/attributes.png', $config->admin_show_freeattributes);
	$menu['units'] = array(_WOP_SHOP_PANEL_UNITS_MEASURE, 'admin.php?page=options&tab=units', 'icons/units.png', $config->admin_show_units);
    $menu['usergroups'] = array(_WOP_SHOP_PANEL_USERGROUPS, 'admin.php?page=options&tab=usergroups', 'icons/user_groups.png', 1);
	$menu['vendors'] = array(_WOP_SHOP_VENDORS, 'admin.php?page=options&tab=vendors', 'icons/vendors.png', 1);
    $menu['reviews'] = array(_WOP_SHOP_PANEL_REVIEWS, 'admin.php?page=options&tab=reviews', 'icons/comments.png', 1);
    $menu['labels'] = array(_WOP_SHOP_PANEL_PRODUCT_LABELS, 'admin.php?page=options&tab=productlabels', 'icons/labels.png', $config->admin_show_product_labels);
    $menu['productfields'] = array(_WOP_SHOP_PANEL_PRODUCT_EXTRA_FIELDS, 'admin.php?page=options&tab=productfields', 'icons/characteristics.png', $config->admin_show_product_extra_field);
    $menu['languages'] = array(_WOP_SHOP_PANEL_LANGUAGES, 'admin.php?page=options&tab=languages', 'icons/languages.png', $config->admin_show_languages);
    $menu['importexport'] = array(_WOP_SHOP_PANEL_IMPORT_EXPORT, 'admin.php?page=options&tab=importexport', 'icons/import_export.png', 1);
    $menu['addons'] = array(_WOP_SHOP_ADDONS, 'admin.php?page=options&tab=addons', 'icons/general.png', 1);
    
    do_action_ref_array('onBeforeAdminOptionPanelMenuDisplay', array(&$menu));
    
    return $menu; 
}

function getItemsConfigPanelMenu(){    
    $menu = array();
    $menu['adminfunction'] = array( _WOP_SHOP_SHOP_FUNCTION, 'admin.php?page=configuration&task=adminfunction', 'shop_function.png', 1 );
    $menu['general'] = array(_WOP_SHOP_GENERAL_PARAMETERS, admin_url( 'admin.php?page=configuration&task=general'), 'general.png', 1);
    $menu['catprod'] = array(_WOP_SHOP_CAT_PROD, admin_url( 'admin.php?page=configuration&task=catprod'), 'category_product.png', 1);
    $menu['checkout'] = array(_WOP_SHOP_CHECKOUT, 'admin.php?page=configuration&task=checkout', 'checkout.png', 1);
    $menu['fieldregister'] = array(_WOP_SHOP_REGISTER_FIELDS, 'admin.php?page=configuration&task=fieldregister', 'fields_registration.png', 1);
    $menu['currency'] = array( _WOP_SHOP_PANEL_CURRENCIES, 'admin.php?page=configuration&task=currency', 'currencies.png', 1);
    $menu['image'] = array(_WOP_SHOP_IMAGE_VIDEO_PARAMETERS, 'admin.php?page=configuration&task=image', 'image_video.png', 1);
    $menu['statictext'] = array(_WOP_SHOP_STATIC_TEXT, 'admin.php?page=configuration&tab=statictext', 'static_text.png', 1);
    $menu['seo'] = array(_WOP_SHOP_SEO, 'admin.php?page=configuration&task=seo', 'seo.png', 1);
    $menu['storeinfo'] = array(_WOP_SHOP_STORE_INFO, 'admin.php?page=configuration&task=storeinfo', 'shop_info.png', 1);
    $menu['permalinks'] = array(_WOP_SHOP_PERMALINKS, 'admin.php?page=configuration&task=permalinks', 'permalinks.png', 1);
    $menu['otherconfig'] = array(_WOP_SHOP_OC, 'admin.php?page=configuration&task=otherconfig', 'other_configuration.png', 1);
    do_action_ref_array( 'onBeforeAdminConfigPanelMenuDisplay', array(&$menu) );
    
    return $menu;
}

function displaySubmenuOptions($active=""){
    include(WOPSHOP_PLUGIN_ADMIN_DIR."/views/panel/tmpl/options_submenu.php");
}

function displaySubmenuConfigs($active=""){
    include(WOPSHOP_PLUGIN_ADMIN_DIR."/views/configuration/tmpl/submenu.php");
}

function getEnableDeliveryFiledRegistration($type='address'){
    $config = Factory::getConfig();
    $tmp_fields = $config->getListFieldsRegister();
    $config_fields = (array)$tmp_fields[$type];
    $count = 0;
    foreach($config_fields as $k=>$v){
        if (substr($k, 0, 2)=="d_" && $v['display']==1) $count++;
    }
    return ($count>0);
}

function getShopTemplatesSelect($default){
    $config = Factory::getConfig();
    $temp = array();
    $dir = $config->template_path;
    $dh = opendir($dir);
    while(($file = readdir($dh)) !== false){        
        if (is_dir($dir.$file) && $file!="." && $file!=".." && $file!='addons'){
            $temp[] = $file;
        }
    }
    closedir($dh);
    $list = array();
    foreach($temp as $val){
        $list[] = HTML::_('select.option', $val, $val, 'id', 'value');
    }
    return HTML::_('select.genericlist', $list, "template",'class = "inputbox" size = "1"','id','value', $default);
}

function getTemplates($type, $default, $first_empty = 0){
    $name = $type."_template";
    $folder = $type;

    $config = Factory::getConfig();
    $temp = array();
    $dir = $config->template_path.$config->template."/".$folder."/";
    $dh = opendir($dir);
    while (($file = readdir($dh)) !== false) {
        if (preg_match("/".$type."_(.*)\.php/", $file, $matches)){
            $temp[] = $matches[1];
        }
    }
    closedir($dh);
    $list = array();
    if ($first_empty){
        $list[] = HTML::_('select.option', -1, "- - -", 'id', 'value');
    }
    foreach($temp as $val){
        $list[] = HTML::_('select.option', $val, $val, 'id', 'value');
    }
    
    return HTML::_('select.genericlist', $list, $name,'class = "inputbox" size = "1"','id','value', $default);
}

function buildTreeCategory($publish = 1, $is_select = 1, $access = 1) {
    $config = Factory::getConfig();
    global $wpdb;
    $lang = $config->getLang();
    $user = Factory::getUser();
    $where = array();
    if ($publish){
        $where[] = "category_publish = '1'";
    }
    //if ($access){
    //    $groups = implode(',', $user->getAuthorisedViewLevels());
    //    $where[] =' access IN ('.$groups.')';
    //}
    $add_where = "";
    if (count($where)){
        $add_where = " where ".implode(" and ", $where);
    }
    $query = "SELECT `name_".$lang."` as name, category_id, category_parent_id, category_publish FROM `".$wpdb->prefix."wshop_categories`
                  ".$add_where." ORDER BY category_parent_id, ordering";
    $all_cats = $wpdb->get_results($query);

    $categories = array();
        if(count($all_cats)) {
        foreach ($all_cats as $key => $value) {
            if(!$value->category_parent_id){
                recurseTree($value, 0, $all_cats, $categories, $is_select);
            }
        }
    }
    return $categories;
}

function recurseTree($cat, $level, $all_cats, &$categories, $is_select) {
    $probil = '';
    if($is_select) {
        for ($i = 0; $i < $level; $i++) {
            $probil .= '-- ';
        }
        $cat->name = ($probil . $cat->name);
        $categories[] = HTML::_('select.option', $cat->category_id, $cat->name,'category_id','name' );
    } else {
        $cat->level = $level;
        $categories[] = $cat;
    }
    foreach ($all_cats as $categ) {
        if($categ->category_parent_id == $cat->category_id) {
            recurseTree($categ, ++$level, $all_cats, $categories, $is_select);
            $level--;
        }
    }
    return $categories;
}

function formatprice($price, $currency_code = null, $currency_exchange = 0, $style_currency = 0) {
    $config = Factory::getConfig();
    //global $config;

    if ($currency_exchange){
        $price = $price * $config->currency_value;
    }
    if ($config->formatprice_style_currency_span && $style_currency!=-1){
        $style_currency = 1;
    }
    if (!$currency_code) $currency_code = $config->currency_code;
    $price = number_format($price, $config->decimal_count, $config->decimal_symbol, $config->thousand_separator);
    if ($style_currency==1) $currency_code = '<span class="currencycode">'.$currency_code.'</span>';
    
    $return = str_replace("Symb", $currency_code, str_replace("00", $price, $config->format_currency[$config->currency_format]));
	extract(ws_add_trigger(get_defined_vars(), "after"));
    return $return;
}

function toSelect($array, $value, $name, $title, $selected){
    if(is_array($array) and count($array) > 0){
        $result = '<select name="'.$title.'" id="'.$title.'" class="inputbox">';
        foreach($array as $index=>$data){
            if($data->$value == $selected) $sell = ' selected= "selected" '; else $sell = '';
            $result.= '<option '.$sell.' value="'.$data->$value.'">'.$data->$name.'</option>';
        }
        $result.= '</select>';
        return $result;
    }else{
        return '';
    }
}

function CurrencyName($currency_id){
    global $wpdb;
    $name_table = $wpdb->prefix.'wshop_currencies';
    return $wpdb->get_var( "SELECT currency_name FROM ".$name_table." WHERE `currency_id` = ".$currency_id);
}

function getListTaxes(){
    global $wpdb;
    $name_table = $wpdb->prefix.'wshop_taxes';
    return $wpdb->get_results( "SELECT * FROM ".$name_table." WHERE `tax_publish` = '1' ORDER BY `ordering` asc");
}

function toList($array, $value, $name, $selected){
    if(is_array($array) and count($array) > 0){
        $result = '<select name="'.$name.'" id="'.$name.'" class="inputbox">';
        foreach($array as $index=>$data){
            if($data->$value == $selected) $sell = ' selected= "selected" '; else $sell = '';
            $result.= '<option '.$sell.' value="'.$data->$value.'">'.$data->$name.'</option>';
        }
        $result.= '</select>';
        return $result;
    }else{
        return '';
    }
}
    
function getOrderstatuses(){
    global $wpdb;
    $config = Factory::getConfig();
    $lang = $config->getLang();
    return $wpdb->get_results("SELECT *, `name_".$lang."` as name FROM ".$wpdb->prefix.'wshop_order_status');
}

function toArray($arr, $key, $name){
    $result = array();
    if(is_array($arr)){
        foreach($arr as $index=>$data){
            $result[$data->$key] = $data->$name;
        }
    }
    return $result;
}

function sprintUnitWeight(){
    $config = Factory::getConfig();
    global $wpdb;
    $name_table = $wpdb->prefix.'wshop_unit';
    return $wpdb->get_var( "SELECT `name_".get_bloginfo('language')."` FROM ".$name_table." WHERE `id` = ".$config->main_unit_weight);
}

function saveAsPrice($val){
    $val = str_replace(",",".",$val);
    preg_match('/-?[0-9]+(\.[0-9]+)?/', $val, $matches);
return floatval($matches[0]);
}

function splitValuesArrayObject($array_object,$property_name) {
    $return = '';
	if (is_array($array_object)){
		foreach($array_object as $key=>$value){
	        $return .= $array_object[$key]->$property_name.', ';
	    }
	    $return = "( ".substr($return,0,strlen($return) - 2)." )";
    }
    return $return;
}

//function getNameImageLabel($id, $type = 1){
//    global $config;
//    //static $listLabels;
//        if (!$config->admin_show_product_labels) return "";
//        //if (!is_array($listLabels)){
//            $listLabels = Factory::getListLabels();
//        //}
//        $obj = $listLabels[$id];
//        if ($type==1)
//            return $obj->image;
//        else
//            return $obj->name;
//}

function getNameImageLabel($id, $type = 1){
    $config = Factory::getConfig();
    //global $config;
    global $wpdb;
    static $listLabels;
    if (!$config->admin_show_product_labels) return "";
    if (!is_array($listLabels)){
        $query = "SELECT id, image, `name_".$config->cur_lang."` as name FROM `".$wpdb->prefix."wshop_product_labels` ORDER BY name";
        $list = $wpdb->get_results($query);
        $rows = array();
        foreach($list as $row){
            $rows[$row->id] = $row;
        }
        $listLabels = $rows;
    }
    $obj = $listLabels[$id];
    if ($type==1)
        return $obj->image;
    else
        return $obj->name;
}

function orderBlocked($order){
    if (!$order->order_created && time()-strtotime($order->order_date)<3600){
        return 1;
    }else{
        return 0;
    }
}

function getMainCurrencyCode(){
    $config = Factory::getConfig();
    //global $config;
    global $wpdb;
    
    return $wpdb->get_var( "SELECT currency_code FROM ".$wpdb->prefix."wshop_currencies WHERE `currency_id` = ".$config->main_unit_weight);
}

function getIdVendorForCUser(){
/*	static $id; die();
        echo $id;
        $config = Factory::getConfig();
	//global $config;
	
	//$user = get_current_user_id();
	//echo $config->admin_show_vendors; die();
	
    if (!$config->admin_show_vendors) return 0;
    if (!isset($id)){
        $user = JFactory::getUser();
        $adminaccess = $user->authorise('core.admin', 'com_jshopping');
        if ($adminaccess){
            $id = 0;    
        }else{
            $vendors = $this->getModel("vendors");    
            $id = $vendors->getIdVendorForUserId($user->id);
        }
		die('asdasdasdrfrfrfr');
    }
    return $id; */
}

function datenull($date){
	return (substr($date,0,1)=="0");
}

function getDisplayDate($date, $format='%d.%m.%Y'){
    if (datenull($date)){
        return '';
    }
    $adate = array(substr($date, 0, 4), substr($date, 5, 2), substr($date, 8, 2));
    $str = str_replace(array("%Y","%m","%d"), $adate, $format);
	return $str;
}

function displayTotalCartTaxName($display_price = null){
    $config = Factory::getConfig();
    //global $config;
    if (!isset($display_price)) {
        $display_price = $config->display_price_front_current;
    }
    if ($display_price==0){
        return _WOP_SHOP_INC_TAX;
    }else{
        return _WOP_SHOP_PLUS_TAX;
    }
}

function loadCurrencyValue(){
    $config = Factory::getConfig();
    $session = Factory::getSession();
    $id_currency_session = $session->get('wshop_id_currency');
    $id_currency = Request::getInt('id_currency');
    $main_currency = $config->mainCurrency;
    if ($config->default_frontend_currency) $main_currency = $config->default_frontend_currency;

    if ($session->get('wshop_id_currency_orig') && $session->get('wshop_id_currency_orig') != $main_currency) {
        $id_currency_session = 0;
        $session->set('wshop_update_all_price', 1);
    }

    if (!$id_currency && $id_currency_session){
        $id_currency = $id_currency_session;
    }

    $session->set('wshop_id_currency_orig', $main_currency);

    if ($id_currency){
        $config->cur_currency = $id_currency;
    }else{
        $config->cur_currency = $main_currency;
    }
    $session->set('wshop_id_currency', $config->cur_currency);
    $all_currency = Factory::getAllCurrency();
    $current_currency = $all_currency[$config->cur_currency];
    if (!$current_currency->currency_value) $current_currency->currency_value = 1;
    $config->currency_value = $current_currency->currency_value;
    $config->currency_code = $current_currency->currency_code;
    $config->currency_code_iso = $current_currency->currency_code_iso;
}

function getJsDateDB($str, $format='%d.%m.%Y'){
    $f = str_replace(array("%d","%m","%Y"), array('dd','mm','yyyy'), $format);
    $pos = array(strpos($f, 'y'),strpos($f, 'm'),strpos($f, 'd'));
    $date = substr($str, $pos[0], 4).'-'.substr($str, $pos[1], 2).'-'.substr($str, $pos[2], 2);
    return $date;
}

function getJsDate($date = 'now', $format='Y-m-d H:i:s', $local = true){
    $date = Factory::getDate($date);
    return $date->format($format, $local);
}

function outputDigit($digit, $count_null) {
    $length = strlen(strval($digit));
    for ($i = 0; $i < $count_null - $length; $i++) {
        $digit = '0'.$digit;
    }
    return $digit;
}

function getCorrectedPriceForQueryFilter($price){
    $config = Factory::getConfig();

    $taxes = Factory::getAllTaxes();
    $taxlist = array_values($taxes);
    $tax = $taxlist[0];

    if ($config->display_price_admin == 1 && $config->display_price_front_current == 0){
        $price = $price / (1 + $tax / 100);
    }
    if ($config->display_price_admin == 0 && $config->display_price_front_current == 1){
        $price = $price * (1 + $tax / 100);
    }
    
    $price = $price / $config->currency_value;
    return $price;
}

function getPatchProductImage($name, $prefix = '', $patchtype = 0){
    $config = Factory::getConfig();
    if ($name==''){
        return '';
    }
    if ($prefix!=''){
        $name = $prefix."_".$name;
    }
    if ($patchtype==1){
        $name = $config->image_product_live_path."/".$name;
    }
    if ($patchtype==2){
        $name = $config->image_product_path."/".$name;
    }
return $name;
}
function formatweight($val, $unitid = 0, $show_unit = 1){
    $config = Factory::getConfig();
    if (!$unitid){
        $unitid = $config->main_unit_weight;
    }
    $units = Factory::getAllUnits();
    $unit = $units[$unitid];
    if ($show_unit){
        $sufix = " ".$unit->name;
    }else{
        $sufix = "";
    }
    $val = floatval($val);
    return str_replace(".", $config->decimal_symbol, $val).$sufix;
}
function formatEPrice($price){
    $config = Factory::getConfig();
    return number_format($price, $config->product_price_precision, '.', '');
}
function formatdate($date, $showtime = 0){
    $config = Factory::getConfig();
    $format = $config->store_date_format;
    if ($showtime) $format = $format." %H:%M:%S";
    return strftime($format, strtotime($date));
}
function fixRealVendorId($id){
    if ($id==0){
        $mainvendor = Factory::getMainVendor();
        $id = $mainvendor->id;
    }
return $id;
}
function formatqty($val){
    return floatval($val);
}
function sprintAtributeInOrder($atribute, $type="html"){   
    do_action_ref_array('beforeSprintAtributeInOrder', array(&$atribute, $type));
    if ($type=="html"){
        $html = nl2br($atribute);
    }else{
        $html = $atribute;
    }
return $html;
}
function sprintFreeAtributeInOrder($freeatribute, $type="html"){
    do_action_ref_array('beforeSprintFreeAtributeInOrder', array(&$freeatribute, $type));
    if ($type=="html"){
        $html = nl2br($freeatribute);
    }else{
        $html = $freeatribute;
    }
return $html;
}
function sprintExtraFiledsInOrder($extra_fields, $type="html"){
    do_action_ref_array('beforeSprintExtraFieldsInOrder', array(&$extra_fields, $type));
    if ($type=="html"){
        $html = nl2br($extra_fields);
    }else{
        $html = $extra_fields;
    }
return $html;
}
function formattax($val){
    $config = Factory::getConfig();
    $val = floatval($val);
    return str_replace(".", $config->decimal_symbol, $val);
}

add_action('wp_ajax_modal_insert_product_to_order', 'modal_insert_product_to_order_callback');
function modal_insert_product_to_order_callback(){
    $e_name = Request::getInt('e_name');
    WshopAdminRouter::route('admin.php?page=products&tab=productlistselectable&task=display&e_name='.$e_name);
    wp_die();
}
add_action('wp_ajax_modal_insert_product_to_order_json', 'modal_insert_product_to_order_json_callback');
function modal_insert_product_to_order_json_callback(){
    $pid = Request::getInt('product_id');
    $currency_id = Request::getInt('currency_id');
    //echo 'admin.php?page=products&task=loadproductinfo&product_id='.$pid.'&currency_id='.$currency_id;
    WshopAdminRouter::route('admin.php?page=products&task=loadproductinfo&product_id='.$pid.'&currency_id='.$currency_id);
    wp_die();
}
add_action('wp_ajax_userinfo_json', 'userinfo_json_callback');
function userinfo_json_callback(){
    $uid = Request::getInt('user_id');
    WshopAdminRouter::route('admin.php?page=clients&task=get_userinfo&user_id='.$uid);
    wp_die();
}
add_action('wp_ajax_product_cat_attr', 'product_cat_attr_callback');
function product_cat_attr_callback(){
    //$product_id = Request::getInt('product_id');
    //$cat_id = Request::getVar('cat_id');
    //$cats = implode(',', $cat_id);
    //WshopAdminRouter::route('admin.php?page=products&task=product_extra_fields&product_id='.$product_id.'&cat_id='.$cats);
    WshopAdminRouter::route('admin.php?page=products&task=product_extra_fields');
    wp_die();
}
add_action('wp_ajax_product_delete_file', 'product_delete_file');
function product_delete_file(){
    //$product_id = Request::getInt('product_id');
    //$cat_id = Request::getVar('cat_id');
    //$cats = implode(',', $cat_id);
    //WshopAdminRouter::route('admin.php?page=products&task=product_extra_fields&product_id='.$product_id.'&cat_id='.$cats);
    WshopAdminRouter::route('admin.php?page=products&task=delete_file');
    wp_die();
}
add_action('wp_ajax_product_delete_foto', 'product_delete_foto');
function product_delete_foto(){
    //$product_id = Request::getInt('product_id');
    //$cat_id = Request::getVar('cat_id');
    //$cats = implode(',', $cat_id);
    //WshopAdminRouter::route('admin.php?page=products&task=product_extra_fields&product_id='.$product_id.'&cat_id='.$cats);
    WshopAdminRouter::route('admin.php?page=products&task=delete_foto');
    wp_die();
}
add_action('wp_ajax_search_related', 'product_search_related');
function product_search_related(){
    //$product_id = Request::getInt('product_id');
    //$cat_id = Request::getVar('cat_id');
    //$cats = implode(',', $cat_id);
    //WshopAdminRouter::route('admin.php?page=products&task=product_extra_fields&product_id='.$product_id.'&cat_id='.$cats);
    WshopAdminRouter::route('admin.php?page=products&task=search_related');
    wp_die();
}
add_action('wp_ajax_printOrder', 'printOrder');
function printOrder(){
    $product_id = Request::getInt('order_id');
    //$cat_id = Request::getVar('cat_id');
    //$cats = implode(',', $cat_id);
    //WshopAdminRouter::route('admin.php?page=products&task=product_extra_fields&product_id='.$product_id.'&cat_id='.$cats);
    WshopAdminRouter::route('admin.php?page=orders&task=printOrder&order_id='.$order_id);
    wp_die();
}
add_action('wp_ajax_delete_video', 'delete_video');
function delete_video(){
    $id = Request::getInt('id');
    WshopAdminRouter::route('admin.php?page=products&task=delete_video&id='.$id);
    wp_die();
}
add_action('wp_ajax_setmenu', 'setmenu');
function setmenu(){
    $url = Request::getVar('url');
    echo SEFLink($url);
    wp_die();
}
add_action('wp_ajax_category_parent_sorting', 'category_parent_sorting');
function category_parent_sorting(){
    $catid = Request::getInt('catid');
    WshopAdminRouter::route('admin.php?page=categories&task=sorting_cats_html&catid='.$catid);
    wp_die();
}

function getPageHeaderOfParams(&$params){
    $header = "";
    //if (@$params->get('show_page_heading') && @$params->get('page_heading')){
    if ($params->show_page_heading && $params->page_heading){
        $header = $params->page_heading;
    }
return $header;
}

function splitSql($sql){
    $start = 0;
    $open = false;
    $char = '';
    $end = strlen($sql);
    $queries = array();

    for ($i = 0; $i < $end; $i++)
    {
        $current = substr($sql, $i, 1);

        if (($current == '"' || $current == '\''))
        {
            $n = 2;

            while (substr($sql, $i - $n + 1, 1) == '\\' && $n < $i)
            {
                $n++;
            }

            if ($n % 2 == 0)
            {
                if ($open)
                {
                    if ($current == $char)
                    {
                            $open = false;
                            $char = '';
                    }
                }
                else
                {
                    $open = true;
                    $char = $current;
                }
            }
        }

        if (($current == ';' && !$open) || $i == $end - 1)
        {
            $queries[] = substr($sql, $start, ($i - $start + 1));
            $start = $i + 1;
        }
    }

    return $queries;
}
function getProductById($product_id){
    global $wpdb;
    $config = Factory::getConfig();
    $lang = $config->cur_lang;
    $query = "SELECT `alias_".$lang."` FROM `".$wpdb->prefix.'wshop_products'."` WHERE product_id = ".esc_sql($product_id);
    $alias = $wpdb->get_var($query);
    return ($alias) ? $alias : $product_id;
}
function parseParamsToArray($string) {
    $temp = explode("\n",$string);
    foreach ($temp as $key => $value) {
        if(!$value) continue;
        $temp2 = explode("=",$value);
        $array[$temp2[0]] = $temp2[1];
    }
    return $array;
}
function parseArrayToParams($array) {
    $str = '';
    foreach ($array as $key => $value) {
        $str .= $key."=".$value."\n";
    }
    return $str;
}
function get_list_files( $folder = '', $filter = '.', $levels = 1 ) {
	if ( empty($folder) )
		return false;

	if ( ! $levels )
		return false;

	$files = array();
	if ( $dir = @opendir( $folder ) ) {
		while (($file = readdir( $dir ) ) !== false ) {
			if ( in_array($file, array('.', '..') ) )
				continue;
			if ( is_dir( $folder . '/' . $file ) ) {
				$files2 = list_files( $folder . '/' . $file, $levels - 1);
				if ( $files2 )
					$files = array_merge($files, $files2 );
				else
					$files[] = $folder . '/' . $file . '/';
			} else {
                            if (preg_match("/$filter/", $file))
				//$files[] = $folder . '/' . $file;
                                $files[] = $file;
			}
		}
	}
	@closedir( $dir );
	return $files;
}

//php 5.4
//function slugify($string) {
//    $string = transliterator_transliterate("Any-Latin; NFD; [:Nonspacing Mark:] Remove; NFC; [:Punctuation:] Remove; Lower();", $string);
//    $string = preg_replace('/[-\s]+/', '-', $string);
//    return trim($string, '-');
//}

function updateCountExtTaxRule(){
    global $wpdb;
    $query = "SELECT count(id) FROM `".$wpdb->prefix."wshop_taxes_ext`";
    $count = $wpdb->get_var($query);

    $query = "update ".$wpdb->prefix."wshop_config set use_extend_tax_rule='".$count."' where id='1'";
    $wpdb->query( $query );
}

function replaceWWW($str){
    return str_replace("www.","",$str);
}

function getHttpHost(){
    return $_SERVER["HTTP_HOST"];
}

function compareX64($a, $b){
    return base64_encode($a) == $b;
}

function productTaxInfo($tax, $display_price = null){
    if (!isset($display_price)) {
        $config = Factory::getConfig();
        $display_price = $config->display_price_front_current;
    }
    if ($display_price==0){
        return sprintf(_WOP_SHOP_INC_PERCENT_TAX, formattax($tax));
    }else{
        return sprintf(_WOP_SHOP_PLUS_PERCENT_TAX, formattax($tax));
    }
}

function sprintBasicPrice($prod){
    if (is_object($prod)) $prod = (array)$prod;
    do_action_ref_array('beforeSprintBasicPrice', array(&$prod));
    $html = '';
    if ($prod['basicprice']>0){
        $html = formatprice($prod['basicprice'])." / ".$prod['basicpriceunit'];
    }
return $html;
}

function getWPLanguageTag(){
    //$language = get_bloginfo('language');
    $language = get_locale();

    return str_replace('_', '-', $language);
}

function getTreeCategory($publish = 1, $access = 1){
    $config = Factory::getConfig();
    global $wpdb;
    $user = Factory::getUser();
    $lang = Factory::getLang();
    $where = array();
    if ($publish){
        $where[] = "category_publish = '1'";
    }
//    if ($access){
//        $groups = implode(',', $user->getAuthorisedViewLevels());
//        $where[] =' access IN ('.$groups.')';
//    }
    $add_where = "";
    if (count($where)){
        $add_where = " where ".implode(" and ", $where);
    }
    $query = "SELECT `".$lang->get('name')."` as name, category_id, category_parent_id FROM `".$wpdb->prefix."wshop_categories` ".$add_where." ORDER BY category_parent_id, ordering";
    $allcats = $wpdb->get_results($query);        
    $cats = _getCategoryParent($allcats, 0);
    _getResortCategoryTree($cats, $allcats);
    
return $cats;
}
function _getCategoryParent($cat, $parent){
    $res = array();
    foreach($cat as $obj){
        if ($obj->category_parent_id == $parent) $res[] = $obj;
    } 
return $res;
}

function _getResortCategoryTree(&$cats, $allcats){
    foreach($cats as $k=>$v){
        $cats_sub = _getCategoryParent($allcats, $v->category_id);
        if (count($cats_sub)){
            _getResortCategoryTree($cats_sub, $allcats);
        }
        $cats[$k]->subcat = $cats_sub;
    }
}

/*function productTaxInfo($tax, $display_price = null){
    if (!isset($display_price)) {
        $config = Factory::getConfig();
        $display_price = $config->display_price_front_current;
    }
    if ($display_price==0){
        return sprintf(_WOP_SHOP_INC_PERCENT_TAX, formattax($tax));
    }else{
        return sprintf(_WOP_SHOP_PLUS_PERCENT_TAX, formattax($tax));
    }
}
 * 
 */