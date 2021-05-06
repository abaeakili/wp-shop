<?php
/**
* @version      1.0.0 01.06.2016
* @author       MAXXmarketing GmbH
* @package      WOPshop
* @copyright    Copyright (C) 2010 http://www.wop-agentur.com. All rights reserved.
* @license      GNU/GPL
* @class        Factory
*/

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

abstract class Factory  {
    
    const CONFIGURATION_ID = 1;
    
    public static $application = null;
    public static $cache = null;
    public static $config = null;
    public static $dates = array();
    public static $session = null;
    public static $table = null;
    
    public static function isW() {
        return defined('ABSPATH');
    }
	
    public static function getApplication($id = null, $config = array()) {
        if (!self::$application) {
            self::$application = Application::getInstance($id, $config);
        }

        return self::$application;
    }
    
    public static function getWPost($id) {
        $post = get_post($id);

        return $post;
    }

    public static function getSession($options = array()) {
        if (!self::$session) {
            self::$session = self::createSession($options);
        }

        return self::$session;
    }

    public static function getUri($uri = 'SERVER') {
        return Uri::getInstance($uri);
    }

    protected static function createSession($options = array()) {
        $options['name']    = '_wopshop';
        $options['expire']  = 172800; //wordpress 48 hours 
        //$options['expire']  = 900;

        $session = Session::getInstance($options);
        if ($session->getState() == 'expired') {
            $session->restart();
        }

        return $session;
    }
    
    public static function getTable($type, $prefix = 'WshopTable') {
        do_action_ref_array( 'onWSFactoryGetTable', array( &$type, &$prefix ) );
        $table = WshopTable::getInstance($type, $prefix);
        do_action_ref_array( 'onAfterWSFactoryGetTable', array( &$table, &$type, &$prefix ) );
        return $table;
    }
    
    public static function getView($name){
        if (file_exists(WOPSHOP_PLUGIN_DIR ."site/views/".strtolower($name)."/view.php")){
            include_once(WOPSHOP_PLUGIN_DIR ."site/views/".strtolower($name)."/view.php");
            $viewname = $name."WshopView";
            
            if (class_exists($viewname)){
                $obj = new $viewname($name);
                return $obj;
            } else {
                echo('No View Class found');
            }            
        } else {
           echo('No View file found'); 
        }        
    } 
    
    
    public static function getModel($name){
        do_action_ref_array( 'onWSFactoryGetModel', array( &$name ) );
        if (file_exists(WOPSHOP_PLUGIN_DIR ."site/models/".strtolower($name).".php")){
            include_once(WOPSHOP_PLUGIN_DIR ."site/models/".strtolower($name).".php");
            $modelname = $name."WshopModel";
            if (class_exists($modelname)){
                $obj = new $modelname();
                
                do_action_ref_array( 'onAfterWSFactoryGetModel', array( &$obj, &$name ) );
                
                return $obj;                   
            }         
        }
    }    

    public static function getAdminModel($name){
        if (file_exists(WOPSHOP_PLUGIN_DIR ."admin/models/".strtolower($name).".php")){
            include_once(WOPSHOP_PLUGIN_DIR ."admin/models/".strtolower($name).".php");
            $modelname = $name."WshopAdminModel";
            if (class_exists($modelname)){
                $obj = new $modelname();
                return $obj;
            }         
        }
    }    
    
    public static function getAllCurrency(){
        global $wpdb;
        static $list;
            if (!is_array($list)){
                $query = "SELECT * FROM `".$wpdb->prefix."wshop_currencies` WHERE `currency_publish` = '1'";
                $_list = $wpdb->get_results($query);
                $list = array();
                foreach($_list as $row){
                    $list[$row->currency_id] = $row;
                }
            }
        return $list;
        }
        
    public static function reloadConfigFieldTLF(){
        $config = Factory::getConfig();
        $reload = array('user_field_client_type','user_field_title','sorting_products_name_select','sorting_products_name_s_select','count_product_select');
        foreach($reload as $field){
            $tmp = $config->$field;
            foreach($tmp as $k=>$v){
                if (defined($v)) $tmp[$k] = constant($v);
            }
            $config->$field = $tmp;
        }
    }        

    public static function getAllTaxes(){
        $config = Factory::getConfig();
        static $rows;
            if (!is_array($rows)){           
                $rows = Factory::getAllTaxesOriginal();
                if ($config->use_extend_tax_rule){
                    $country_id = 0;
                    $adv_user = Factory::getUserShop();
                    $country_id = $adv_user->country;
                    if ($config->tax_on_delivery_address && $adv_user->delivery_adress && $adv_user->d_country){
                        $country_id = $adv_user->d_country;
                    }
                    $client_type = $adv_user->client_type;
                    $enter_tax_id = $adv_user->tax_number!="";
                    if (!$country_id){
                        $adv_user = Factory::getUserShopGuest();
                        $country_id = $adv_user->country;
                        if ($config->tax_on_delivery_address && $adv_user->delivery_adress && $adv_user->d_country){
                            $country_id = $adv_user->d_country;
                        }
                        $client_type = $adv_user->client_type;
                        $enter_tax_id = $adv_user->tax_number!="";
                    }
                    if ($country_id){
                        $_tax = Factory::getTable('tax');
                        $_rowsext = $_tax->getExtTaxes();
                        do_action_ref_array('beforeGetAllTaxesRowsext', array(&$_rowsext, &$country_id, &$adv_user, &$rows) );
                        foreach($_rowsext as $v){
                            if(is_array($v->countries))
                            if (in_array($country_id, $v->countries)){
                                if ($config->ext_tax_rule_for==1){
                                    if ($enter_tax_id){
                                        $rows[$v->tax_id] = $v->firma_tax;
                                    }else{
                                        $rows[$v->tax_id] = $v->tax;
                                    }    
                                }else{
                                    if ($client_type==2){
                                        $rows[$v->tax_id] = $v->firma_tax;
                                    }else{
                                        $rows[$v->tax_id] = $v->tax;
                                    }
                                }
                            }
                        }
                        do_action_ref_array('afterGetAllTaxesRowsext', array(&$_rowsext, &$country_id, &$adv_user, &$rows) );
                        unset($_rowsext);
                    }
                }
            do_action_ref_array('afterGetAllTaxes', array(&$rows) );
            }
        return $rows;
    }  
    
    public static function getAllTaxesOriginal(){
        global $wpdb;
        static $rows;
            if (!is_array($rows)){
                $query = "SELECT * FROM `".$wpdb->prefix."wshop_taxes`";
                $_rows = $wpdb->get_results($query);
                $rows = array();
                foreach($_rows as $row){
                    $rows[$row->tax_id] = $row->tax_value;
                }
            }
        return $rows;
    }
    
    public static function getAllUnits(){
        global $wpdb;
        $config = Factory::getConfig();
        static $rows;
            if (!is_array($rows)){
                $query = "SELECT *, `name_".$config->cur_lang."` as name FROM `".$wpdb->prefix."wshop_unit`";
                $list = $wpdb->get_results($query);
                $rows = array();
                foreach($list as $row){
                     $rows[$row->id] = $row;
                }                         
            }
        return $rows;       
    }
    
    public static function getListLabels(){
        global $wpdb;
        static $rows;
            if (!is_array($rows)){
                //$query = "SELECT id, image, `name_".get_bloginfo('language')."` as name FROM `".$wpdb->prefix."wshop_productlabels` ORDER BY name";
                $query = "SELECT * FROM `".$wpdb->prefix."wshop_productlabels` WHERE `label_publish` = '1'";
                $rows = $wpdb->get_results($query);
            }
        return $rows;
    }
    
    public static function getAllManufacturer(){
        global $wpdb;
        $config = Factory::getConfig();
        static $rows;
            if (!is_array($rows)){
                $adv_result = "manufacturer_id as id, `name_".$config->cur_lang."` as name, manufacturer_logo, manufacturer_url";
                $query = "select ".$adv_result." from `".$wpdb->prefix."wshop_manufacturers` where manufacturer_publish='1'";
                $_rows = $wpdb->get_results($query);
                $rows = array();
                foreach($_rows as $row){
                    $rows[$row->id] = $row;
                }
                unset($_rows);
            }
        return $rows;
    }    

    public static function loadJQuery(){
        static $load;
        
        if (!$load){
            wp_enqueue_script('jquery');
//            wp_enqueue_script('wshopJquery', WOPSHOP_PLUGIN_URL.'assets/js/jquery/jquery.min.js');
            $load = 1;
        }
    }
    
    public static function loadJsFiles(){
        static $load;
        
        if (!$load){
            $config = Factory::getConfig();
            if ($config->load_javascript){
                wp_enqueue_script('jquery.media.js', WOPSHOP_PLUGIN_URL.'assets/js/jquery/jquery.media.js', array('jquery'));
                wp_enqueue_script('functions.js', WOPSHOP_PLUGIN_URL.'assets/js/system/functions.js', array('jquery'));
                wp_enqueue_script('validateform.js', WOPSHOP_PLUGIN_URL.'assets/js/system/validateForm.js', array('jquery'));
            }
            
            $load = 1;
        }
    }
    
    public static function loadJsFilesRating(){
        static $load;
        
        if (!$load){
            $config = Factory::getConfig();
            if ($config->load_javascript){
				wp_enqueue_script('jquery.MetaData.js', WOPSHOP_PLUGIN_URL.'assets/js/jquery/jquery.MetaData.js', array('jquery'));
				wp_enqueue_script('jquery.rating.pack.js', WOPSHOP_PLUGIN_URL.'assets/js/jquery/jquery.rating.pack.js', array('jquery'));
				wp_enqueue_style('jquery.rating.css', WOPSHOP_PLUGIN_URL.'assets/css/jquery.rating.css');
            }
            
            $load = 1;
        }
    }
    
    public static function loadDatepicker(){
        static $load;
        
        if (!$load){
            wp_enqueue_script('jquery-ui-datepicker');            
            $load = 1;
        }
    }
	
    public static function loadCssFiles(){
        static $load;
        
        if (!$load){
            $config = Factory::getConfig();
            if ($config->load_css) {
                wp_enqueue_style($config->template.'.css', WOPSHOP_PLUGIN_URL.'assets/css/'.$config->template.'.css');

                if (file_exists( WOPSHOP_PLUGIN_URL.'assets/css/'.$config->template.'custom.css')){
                    wp_enqueue_style('custom.css', WOPSHOP_PLUGIN_URL.'assets/css/'.$config->template.'custom.css');
                }
				wp_enqueue_style('jquery.ui.css', WOPSHOP_PLUGIN_URL.'assets/css/jquery.ui.css');
            }
            
            $load = 1;
        }
    }
    
    public static function loadJsBootstrap(){
        static $load;
        
        if (!$load){
            wp_enqueue_style('bootstrap.min.css', WOPSHOP_PLUGIN_URL.'assets/css/bootstrap.min.css');
            wp_enqueue_script('bootstrap.min.js', WOPSHOP_PLUGIN_URL.'assets/js/bootstrap/bootstrap.min.js', array('jquery'));
            $load = 1;
        }
    }
    
     public static function InitJsBootstrap(){
        static $load;
        
        if (!$load){
            wp_enqueue_script('bootstrap.init.js', WOPSHOP_PLUGIN_URL.'assets/js/bootstrap/bootstrap.init.js', array('jquery'));
            $load = 1;
        }
    }
    
    public static function loadJsFilesLightBox(){
        static $load;
        
        if (!$load){
            $config = Factory::getConfig();
            if (!$config->load_jquery_lightbox){
                return 0;
            }
            
            wp_enqueue_script('jquery.lightbox.js', WOPSHOP_PLUGIN_URL.'assets/js/jquery/jquery.lightbox-0.5.js', array('jquery'));
            wp_enqueue_style('lightbox.css', WOPSHOP_PLUGIN_URL.'assets/css/jquery.lightbox-0.5.css');
            
            $load = 1;
        }
    }
    
    public static function getAllCountries($publish = 1){
        global $wpdb;
        $config = Factory::getConfig();
        $language = Factory::getLang();

        $where = $publish ? " WHERE country_publish = 1 " : " ";
        $ordering = "ordering";
        if ($config->sorting_country_in_alphabet) {
            $ordering = "name";
        }

        $query = "SELECT country_id, `".$language->get('name')."` as name FROM `".$wpdb->prefix."wshop_countries` ".$where." ORDER BY ".$ordering;
        return $wpdb->get_results($query);        
    }    
    
    public static function getDate($time = 'now', $tzOffset = null){
        $config = Factory::getConfig();
        //global $config;
        static $classname;
        static $mainLocale;

        $locale = $config->cur_lang;

        if (!isset($classname) || $locale != $mainLocale)
        {
                // Store the locale for future reference
                $mainLocale = $locale;

                if ($mainLocale !== false)
                {
                        $classname = str_replace('-', '_', $mainLocale) . 'Date';

                        if (!class_exists($classname))
                        {
                                // The class does not exist, default to JDate
                                $classname = 'Date';
                        }
                }
                else
                {
                        // No tag, so default to JDate
                        $classname = 'Date';
                }
        }

        $key = $time . '-' . ($tzOffset instanceof DateTimeZone ? $tzOffset->getName() : (string) $tzOffset);

        if (!isset(self::$dates[$classname][$key]))
        {
                self::$dates[$classname][$key] = new $classname($time, $tzOffset);
        }

        $date = clone self::$dates[$classname][$key];

        return $date;
    }

    public static function getUser() {
        $user = wp_get_current_user();
        if ($user->ID){
            $adv_user = Factory::getUserShop();
        }else{
            $adv_user = Factory::getUserShopGuest();    
        }
        return $adv_user;
    }
    
    public static function getUserShopGuest(){
    static $userguest;
        if (!is_object($userguest)){
            include(WOPSHOP_PLUGIN_DIR . "site/models/userguest.php");
            $userguest = new WshopUserGust();
            $userguest->load();
            $userguest->percent_discount = 0;
        }
    return $userguest;
    }    
    
    public static function getUserShop(){
        static $usershop;

        if (!is_object($usershop)){
            $user = wp_get_current_user();
            $usershop = Factory::getTable('usershop');
            
            if ($user->ID){
                if (!$usershop->isUserInShop($user->ID)) {
                    $usershop->addUserToTableShop($user);
                }
                $usershop->load($user->ID);
                $usershop->percent_discount = $usershop->getDiscount();
            } else {
                $usershop->percent_discount = 0;
            }
            do_action_ref_array('onAfterGetUserShopFactory', array(&$usershop));
        }
        
        return $usershop;
    }
    
    public static function getConfig(){
        static $config;
        
        if (!is_object($config)){
            $config = Factory::getTable('configuration');
            $config->load(self::CONFIGURATION_ID);

            require WOPSHOP_PLUGIN_DIR . "lib/default_config.php";
            if (file_exists(WOPSHOP_PLUGIN_DIR . "lib/user_config.php")){
				require WOPSHOP_PLUGIN_DIR . "lib/user_config.php";
			}
            do_action_ref_array('onBeforeLoadWshopConfig', array($config));
            $config->loadOtherConfig();
            $config->loadCurrencyValue();
            // TODO loadFrontLand
            //$config->loadFrontLand();
            $config->loadLang();
            $config->parseConfigVars();
			do_action_ref_array('onLoadWshopConfig', array(&$config));
        }
        
        return $config;
    }    
    
    public static function getAllProductExtraField(){
        global $wpdb;
        static $list;
        $config = Factory::getConfig();
//            if (!is_array($list)){
//                $query = "SELECT * FROM `".$wpdb->prefix."wshop_products_extra_fields`";
//                $_list = $wpdb->get_results($query);
//                $list = array();
//                foreach($_list as $row){
//                    $list[$row->id] = $row;
//                }
//            }
//        return $list;        
        if (!is_array($list)){
            $ordering = "F.ordering";
            $query = "SELECT F.id, F.`name_".$config->cur_lang."` as name, F.`description_".$config->cur_lang."` as description, F.allcats, F.type, F.cats, F.ordering, F.`group`, G.`name_".$config->cur_lang."` as groupname, multilist FROM `".$wpdb->prefix."wshop_products_extra_fields` as F left join `".$wpdb->prefix."wshop_products_extra_field_groups` as G on G.id=F.group order by ".$ordering;
            $rows = $wpdb->get_results($query);
            $list = array();        
            foreach($rows as $k=>$v){
                $list[$v->id] = $v;
                if ($v->allcats){
                    $list[$v->id]->cats = array();
                }else{
                    $list[$v->id]->cats = json_decode($v->cats, 1);
                }            
            }
            unset($rows);
        }
        return $list;
    }

    public static function getAllProductExtraFieldValue(){
    static $list;
        if (!is_array($list)){
            global $wpdb;
            $config = Factory::getConfig();
            $query = "SELECT id, `name_".$config->cur_lang."` as name, field_id FROM `".$wpdb->prefix."wshop_products_extra_field_values` order by ordering";
                $rows = $wpdb->get_results($query);
                $list = array();
                foreach($rows as $k=>$row){
                    $list[$row->id] = $row->name;
                    unset($rows[$k]);    
                }
        }
    return $list;
    }

    public static function getAllProductExtraFieldValueDetail(){
    static $list;
        if (!is_array($list)){
            $productfieldvalue = Factory::getTable('productfieldvalue');
            $list = $productfieldvalue->getAllList(2);
        }
    return $list;
    }
    
    public static function getAllDeliveryTime(){
        global $wpdb;
        static $rows;
        $config = Factory::getConfig();
        if (!is_array($rows)){
            $query = "select id, `name_".$config->cur_lang."` as name from ".$wpdb->prefix."wshop_delivery_times";
            $_rows = $wpdb->get_results($query);
            $rows = array();
            foreach($_rows as $row){
                $rows[$row->id] = $row->name;
            }
            unset($_rows);
        }
        return $rows;
    } 
    
    public static function getAllAttributes($resformat = 0){
    static $attributes;
        if (!is_array($attributes)){
            $_attrib = Factory::getTable("attribut");
            $attributes = $_attrib->getAllAttributes();
        }
        if ($resformat==0){
            return $attributes;
        }
        if ($resformat==1){
            $attributes_format1 = array();
            foreach($attributes as $v){
                $attributes_format1[$v->attr_id] = $v;
            }
            return $attributes_format1;
        }
        if ($resformat==2){
            $attributes_format2 = array();
            $attributes_format2['independent']= array();
            $attributes_format2['dependent']= array();
            foreach($attributes as $v){
                if ($v->independent) $key_dependent = "independent"; else $key_dependent = "dependent";
                $attributes_format2[$key_dependent][$v->attr_id] = $v;
            }
            return $attributes_format2;
        }
    } 
    
    public static function getShippingExtList($for_shipping = 0){
    static $list;
        if (!is_array($list)){
            $config = Factory::getConfig();
            $path = $config->path."shippings";
            $shippingext = Factory::getTable('shippingext');
            $_list = $shippingext->getList(1);
            $list = array();
            foreach($_list as $row){
                $extname = $row->alias;
                $filepatch = $path."/".$extname."/".$extname.".php";
                if (file_exists($filepatch)){
                    include_once($filepatch);
                    $row->exec = new $extname();
                    $list[$row->id] = $row;
                }else{
                    addMessage('Load ShippingExt "'.$extname.'" error.', 'error');
                }
            }
        }
        if ($for_shipping==0){
            return $list;
        }
        $returnlist = array();
        foreach($list as $row){
            if ($row->shipping_method!=""){
                $sm = json_decode($row->shipping_method, 1);
            }else{
                $sm = array();
            }
            if(!isset($sm[$for_shipping])){
                $sm[$for_shipping]=1;
            }
            if ($sm[$for_shipping]!=="0"){
                $returnlist[] = $row;
            }
        }
    return $returnlist;
    }   
    
    public static function getDisplayFilterExtraFieldForCategory($cat_id){
    static $listforcat;
        if (!isset($listforcat[$cat_id])){
            $fields = array();
            $list = Factory::getAllProductExtraField();
            foreach($list as $val){
                if ($val->allcats){
                    $fields[] = $val->id;
                }else{
                    if (in_array($cat_id, $val->cats)) $fields[] = $val->id;
                }
            }
            
            $config = Factory::getConfig();
            $config_list = $config->getFilterDisplayExtraFields();
            foreach($fields as $k=>$val){
                if (!in_array($val, $config_list)) unset($fields[$k]);
            }
            $listforcat[$cat_id] = $fields;
        }
    return $listforcat[$cat_id];
    }
    
    public static function getAllDeliveryTimeDays(){
    static $rows;
        if (!is_array($rows)){
            global $wpdb;
            $query = "select id, days from ".$wpdb->prefix."wshop_delivery_times";
            $_rows = $wpdb->get_results($query);
            $rows = array();
            foreach($_rows as $row){
                $rows[$row->id] = $row->days;
            }
            unset($_rows);
        }
    return $rows;
    }    
    
    public static function getAliasCategory(){
    static $alias;
        if (!is_array($alias)){
            global $wpdb;
            $config = Factory::getConfig();
            $dbquery = "select category_id as id, `alias_".$config->cur_lang."` as alias from ".$wpdb->prefix."wshop_categories where `alias_".$config->cur_lang."`!=''"; 
            $rows = $wpdb->get_results($dbquery);
            $alias = array();
            foreach($rows as $row){
                $alias[$row->id] = $row->alias;
            }
            unset($rows);
        }
    return $alias;
    }
    
    public static function getAliasManufacturer(){
    static $alias;
        if (!is_array($alias)){
            global $wpdb;
            $config = Factory::getConfig();
            $dbquery = "select manufacturer_id as id, `alias_".$config->cur_lang."` as alias from ".$wpdb->prefix."wshop_manufacturers where `alias_".$config->cur_lang."`!=''";
            $rows = $wpdb->get_results($dbquery);
            $alias = array();
            foreach($rows as $row){
                $alias[$row->id] = $row->alias;
            }
            unset($rows);
        }
    return $alias;
    }    

    public static function getDisplayListProductExtraFieldForCategory($cat_id){
    static $listforcat;
        if (!isset($listforcat[$cat_id])){
            $fields = array();
            $list = Factory::getAllProductExtraField();
            foreach($list as $val){
                if ($val->allcats){
                    $fields[] = $val->id;
                }else{
                    if (in_array($cat_id, $val->cats)) $fields[] = $val->id;
                }
            }

            $config = Factory::getConfig();
            $config_list = $config->getProductListDisplayExtraFields();
            foreach($fields as $k=>$val){
                if (!in_array($val, $config_list)) unset($fields[$k]);
            }
            $listforcat[$cat_id] = $fields;
        }
    return $listforcat[$cat_id];
    }    
    
    public static function getLang($langtag = ""){
        static $ml;
        
        if (!is_object($ml) || $langtag != ""){
            $config = Factory::getConfig();
            $ml = new WshopMultiLang();
            if ($langtag == ""){
                $langtag = $config->getLang();
            }

            $ml->setLang($langtag);
            do_action_ref_array('onAfterGetLangFactory', array(&$ml, &$langtag));
        }
        
        return $ml;
    }
    
    public static function loadLanguageFile($langtag = null, $adminFolder = 0){
        if ($langtag === null){
            $langtag = getWPLanguageTag();
        }
        
        if ($adminFolder == 1 || Factory::getApplication()->isAdmin()){
            $folder = 'admin';
        } else {
            $folder = 'site';
        }

        if (file_exists(WOPSHOP_PLUGIN_DIR.$folder.'/lang/override/'.$langtag.'.php')){
            require_once WOPSHOP_PLUGIN_DIR.$folder.'/lang/override/'.$langtag.'.php';
        }
        
        if (file_exists(WOPSHOP_PLUGIN_DIR.$folder.'/lang/'.$langtag.'.php')){
            require_once WOPSHOP_PLUGIN_DIR.$folder.'/lang/'.$langtag.'.php';
        } else {
            require_once WOPSHOP_PLUGIN_DIR.$folder.'/lang/en-GB.php';
        }
        self::reloadConfigFieldTLF();
    }
    
    public static function loadExtLanguageFile($extname, $adminFolder = 0, $langtag = null){
        if ($langtag === null){
            $langtag = getWPLanguageTag();
        }

        $langPath = WOPSHOP_PLUGIN_DIR.'site/lang/';
        if ($adminFolder){
            $langPath = WOPSHOP_PLUGIN_DIR.'admin/lang/';
        }

        if (file_exists($langPath.$extname.'/'.$langtag.'.php')){
            require_once $langPath.$extname.'/'.$langtag.'.php';
        } else {
            require_once $langPath.$extname.'/en-GB.php';
        }
    }
    
    public static function initLanguageFile(){
        static $languageLoadeded = false;
        if ($languageLoadeded === false){
            self::loadLanguageFile();
            $languageLoadeded = true;
        }
    }
	
    public static function getMainVendor(){
    static $row;
        if (!isset($row)){
            $row = Factory::getTable('vendor');
            $row->loadMain();
        }
    return $row;
    }
	
    public static function getAllVendor(){
    static $rows;
        if (!is_array($rows)){
            global $wpdb;
            $query = "select id, shop_name, l_name, f_name from ".$wpdb->prefix."wshop_vendors";
            //$_rows = $wpdb->get_results($query, OBJECT_K);
			$rows = $wpdb->get_results($query, OBJECT_K);
            //$rows = array();
            $mainvendor = Factory::getMainVendor();
            $rows[0] = $mainvendor;
//            foreach($_rows as $row){
//                $rows[$row->id] = $row;
//            }
//            unset($_rows);
        }
    return $rows;
    }	
}
