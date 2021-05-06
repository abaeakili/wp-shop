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

/**
 * WOPshop Configuration Class
 *
 * @class wshopConfiguration
 */
class ConfigurationWshopTable extends WshopTable {

    public function __construct(){
        global $wpdb;
        parent::__construct($wpdb->prefix.'wshop_config', 'id');          
    }
    
        
    function transformPdfParameters() {
        if (is_array($this->pdf_parameters)){
            $this->pdf_parameters = implode(":",$this->pdf_parameters);
        }
    }

    public function loadCurrencyValue(){
        $application = Factory::getApplication();
        $session = Factory::getSession();
        $id_currency_session = $session->get('wshop_id_currency');
        $id_currency = Request::getInt('id_currency');
        $main_currency = $this->mainCurrency;
        if ($this->default_frontend_currency) {
            $main_currency = $this->default_frontend_currency;
        }
        
        if ($session->get('wshop_id_currency_orig') && $session->get('wshop_id_currency_orig') != $main_currency) {
            $id_currency_session = 0;
            $session->set('wshop_update_all_price', 1);
        }

        if (!$id_currency && $id_currency_session){
            $id_currency = $id_currency_session;
        }

        $session->set('wshop_id_currency_orig', $main_currency);

        if ($id_currency){
            $this->cur_currency = $id_currency;
        } else {
            $this->cur_currency = $main_currency;
        }
        
        if (!$application->isAdmin()){
            $session->set('wshop_id_currency', $this->cur_currency);
        }
        
        $all_currency = Factory::getAllCurrency();
        $current_currency = $all_currency[$this->cur_currency];
        
        if (!isset($current_currency)){
            $current_currency = array_shift($all_currency);
        }
        
        if (!$current_currency->currency_value){
            $current_currency->currency_value = 1;
        }
        
        $this->currency_value = $current_currency->currency_value;
        $this->currency_code = $current_currency->currency_code;
        $this->currency_code_iso = $current_currency->currency_code_iso;
    }
    
    public function getDisplayPriceFront(){
        $display_price = $this->display_price_front;

        if ($this->use_extend_display_price_rule > 0){
            $adv_user = Factory::getUserShop();
            $country_id = $adv_user->country;
            $client_type = $adv_user->client_type;
            
            if (!$adv_user->user_id){
                $adv_user = Factory::getUserShopGuest();
                $country_id = $adv_user->country;
                $client_type = $adv_user->client_type;
            }
            
            if (!$country_id){
                $country_id = $this->default_country;
            }
            
            if ($country_id){
                $configDisplayPrice = Factory::getTable('configDisplayPrice');
                $rows = $configDisplayPrice->getList();
                foreach($rows as $v){
                    if (in_array($country_id, $v->countries)){
                        if ($client_type == 2){
                            $display_price = $v->display_price_firma;
                        }else{
                            $display_price = $v->display_price;
                        }
                    }
                }
            }
        }
        
        return $display_price;
    }
    
    public function getListFieldsRegister(){
        $config = new stdClass();
        include(WOPSHOP_PLUGIN_DIR."/lib/default_config.php");
        if ($this->fields_register != ""){
            $data = json_decode($this->fields_register, 1);
        } else {
            $data = array();
        }

        foreach($fields_client as $type=>$_v){
            foreach($fields_client[$type] as $k=>$v){
                if (!isset($data[$type][$v])){
                    $data[$type][$v] = array('display'=>0,'require'=>0);                    
                }
                if (!isset($data[$type][$v]['display'])) $data[$type][$v]['display'] = 0;
                if (!isset($data[$type][$v]['require'])) $data[$type][$v]['require'] = 0;
            }
        }
        
        return $data;
    }
    
    function getEnableDeliveryFiledRegistration($type='address'){
        $tmp_fields = $this->getListFieldsRegister();
        $config_fields = (array)$tmp_fields[$type];
        $count = 0;
        foreach($config_fields as $k=>$v){
            if (substr($k, 0, 2)=="d_" && $v['display']==1) $count++;
        }
        
        return ($count>0);
    }
    
    function getProductListDisplayExtraFields(){
        if ($this->product_list_display_extra_fields != ""){
            return json_decode($this->product_list_display_extra_fields, 1);
        }else{
            return array();
        }
    }

    function setProductListDisplayExtraFields($data){
        if (is_array($data)){
            $this->product_list_display_extra_fields = json_encode($data);
        }else{
            $this->product_list_display_extra_fields = json_encode(array());
        }
    }

    function getFilterDisplayExtraFields(){
        if ($this->filter_display_extra_fields != ""){
            return json_decode($this->filter_display_extra_fields, 1);
        }else{
            return array();
        }
    }
    
    function setFilterDisplayExtraFields($data){
        if (is_array($data)){
            $this->filter_display_extra_fields = json_encode($data);
        }else{
            $this->filter_display_extra_fields = json_encode(array());
        }
    }
    
    function getProductHideExtraFields(){
        if ($this->product_hide_extra_fields != ""){
            return json_decode($this->product_hide_extra_fields, 1);
        } else {
            return array();
        }
    }
    
    function setProductHideExtraFields($data){
        if (is_array($data)){
            $this->product_hide_extra_fields = json_encode($data);
        } else {
            $this->product_hide_extra_fields = json_encode(array());
        }
    }
    
    function getCartDisplayExtraFields(){
        if ($this->cart_display_extra_fields != ""){
            return json_decode($this->cart_display_extra_fields, 1);
        }else{
            return array();
        }
    }
    
    function setCartDisplayExtraFields($data){
        if (is_array($data)){
            $this->cart_display_extra_fields = json_encode($data);
        }else{
            $this->cart_display_extra_fields = json_encode(array());
        }
    }
    
    function updateNextOrderNumber(){
        global $wpdb; 
        $table = $wpdb->prefix.'wshop_config';
        $wpdb->query("update $table set next_order_number=next_order_number+1");
    }
    
    function getNextOrderNumber(){
        global $wpdb; 
        $table = $wpdb->prefix.'wshop_config';
        $query = "select next_order_number from $table where id=".intval($this->id);
        return $wpdb->get_var($query);
    }

    public function loadOtherConfig(){
        if ($this->other_config != ""){
            $tmp = json_decode($this->other_config);

            foreach ($tmp as $key => $value){
                $this->$key = $value;
            }
        }
    }

    public function loadLang(){
        $this->cur_lang = getWPLanguageTag();
    }
    
    function setLang($lang){
        $this->cur_lang = $lang;
    }
    
    public function getLang(){
        return $this->cur_lang;
    }
    
    public function parseConfigVars(){
        list($this->pdf_header_width, $this->pdf_header_height, $this->pdf_footer_width, $this->pdf_footer_height) = explode(":", $this->pdf_parameters);
        
        if (!$this->allow_reviews_prod){
            unset($this->sorting_products_field_select[5]);
            unset($this->sorting_products_name_select[5]);
            unset($this->sorting_products_field_s_select[5]);
            unset($this->sorting_products_name_s_select[5]);
        }
        
        if ($this->product_count_related_in_row < 1) {
            $this->product_count_related_in_row = 1;
        }
        
        if ($this->user_as_catalog){
			$this->show_buy_in_category = 0;
		}
        
        if (!$this->stock){
			$this->hide_product_not_avaible_stock = 0;
			$this->hide_buy_not_avaible_stock = 0;
			$this->hide_text_product_not_available = 1;
			$this->product_list_show_qty_stock = 0;
			$this->product_show_qty_stock = 0;
		}
        
        if ($this->hide_product_not_avaible_stock || $this->hide_buy_not_avaible_stock){
			$this->controler_buy_qty = 1;
		} else {
			$this->controler_buy_qty = 0;
		}
        
        $this->display_price_front_current = $this->getDisplayPriceFront(); // 0 - Brutto, 1 - Netto
        
        if ($this->template==""){
			$this->template = "default";
		}
        
        if ($this->show_product_code || $this->show_product_code_in_cart){
			$this->show_product_code_in_order = 1;
		} else {
			$this->show_product_code_in_order = 0;
		}
        
        if ($this->admin_show_vendors == 0){
			$this->vendor_order_message_type = 0; //0 - none, 1 - mesage, 2 - order if not multivendor
			$this->admin_not_send_email_order_vendor_order = 0;
			$this->product_show_vendor = 0;
			$this->product_show_vendor_detail = 0;
		}
        
        $this->copyrightText = '<div style="padding-top:20px; text-align:right;"><a style="font-size: 9px;" target="_blank" href="http://www.wop-agentur.com">'.$this->getCopyrightText().'</a></div>';
        
        if ($this->image_resize_type == 0){
			$this->image_cut = 1;
			$this->image_fill = 2;
		} elseif ($this->image_resize_type == 1){
			$this->image_cut = 0;
			$this->image_fill = 2;
		} else {
			$this->image_cut = 0;
			$this->image_fill = 0;
		}
        
        if (!$this->tax){
			$this->show_tax_in_product = 0;
			$this->show_tax_product_in_cart = 0;
			$this->hide_tax = 1;
		}
        
        if (!$this->admin_show_delivery_time){
			$this->show_delivery_time = 0;
			$this->show_delivery_time_checkout = 0;
			$this->show_delivery_time_step5 = 0;
			$this->display_delivery_time_for_product_in_order_mail = 0;
			$this->show_delivery_date = 0;
		}
        
        if (!$this->admin_show_product_basic_price){
			$this->cart_basic_price_show = 0;
		}
        
        if (!$this->admin_show_weight){
			$this->product_show_weight = 0;
			$this->product_list_show_weight = 0;
		}
        
        $this->use_ssl = intval($this->use_ssl);
    }
    
    private function getCopyrightText(){
        $key = strlen(getHttpHost()) % 4;
        $copyrightTexts = $this->getCopyrightTexts();
        return $copyrightTexts[$key];
    }
    
    private function getCopyrightTexts(){
        return array('Copyright MAXXmarketing GmbH', 'Copyright www.wop-agentur.com', 'Company MAXXmarketing GmbH', 'Webseite www.wop-agentur.com');
    }
}