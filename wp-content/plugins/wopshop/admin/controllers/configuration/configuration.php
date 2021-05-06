<?php
class ConfigurationWshopAdminController extends WshopAdminController {
    
    const CONFIGURATION_ID = 1;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function display() {
        $view = $this->getView('configuration');
        $submenu = getItemsConfigPanelMenu();
        $view->items = $submenu;
        $view->display();
    }
    
    public function adminfunction() {
        $config = Factory::getConfig();
        $shop_register_type = array();
        $shop_register_type[] = HTML::_('select.option', 0, "-", 'id', 'name' );
        $shop_register_type[] = HTML::_('select.option', 1, _WOP_SHOP_MEYBY_SKIP_REGISTRATION, 'id', 'name' );
        $shop_register_type[] = HTML::_('select.option', 2, _WOP_SHOP_WITHOUT_REGISTRATION, 'id', 'name' );
        $lists['shop_register_type'] = HTML::_('select.genericlist', $shop_register_type, 'shop_user_guest','class = "inputbox" size = "1"','id','name', $config->shop_user_guest);
        
        $opt = array();
        $opt[] = HTML::_('select.option', 0, _WOP_SHOP_NORMAL, 'id', 'name');
        $opt[] = HTML::_('select.option', 1, _WOP_SHOP_DEVELOPER, 'id', 'name');
        $lists['shop_mode'] = HTML::_('select.genericlist', $opt, 'shop_mode','class = "inputbox"','id','name', $config->shop_mode);

        $view = $this->getView('configuration');
        $view->setLayout("adminfunction");
        $view->assign("lists", $lists);
        $view->assign("config", $config);
		do_action_ref_array('onBeforeEditConfigAdminFunction', array(&$view));
        $view->display();
    }
    
    public function general() {
        $config = Factory::getConfig();
        $model = $this->getModel('configuration');
        $langanguages = $model->getListLanguages();
        $lists['languages'] = HTML::_('select.genericlist', $langanguages, 'defaultLanguage', '', 'language', 'name', $config->defaultLanguage);
        
        $display_price_list = array();
        $display_price_list[] = HTML::_('select.option', 0, _WOP_SHOP_PRODUCT_BRUTTO_PRICE, 'id', 'name');
        $display_price_list[] = HTML::_('select.option', 1, _WOP_SHOP_PRODUCT_NETTO_PRICE, 'id', 'name');
        
        $lists['display_price_admin'] = HTML::_('select.genericlist', $display_price_list, 'display_price_admin', '', 'id', 'name', $config->display_price_admin);
        $lists['display_price_front'] = HTML::_('select.genericlist', $display_price_list, 'display_price_front', '', 'id', 'name', $config->display_price_front);
        $lists['template'] = getShopTemplatesSelect($config->template);

        $view = $this->getView('configuration');
        $view->setLayout("general");
        $view->assign("lists", $lists);
        $view->assign("config", $config);
		do_action_ref_array('onBeforeEditConfigGeneral', array(&$view));
        $view->display();
    }
    
    public function catprod() {
        $config = Factory::getConfig();

        $displayprice = array();
        $displayprice[] = HTML::_('select.option', 0, _WOP_SHOP_YES, 'id', 'value');
        $displayprice[] = HTML::_('select.option', 1, _WOP_SHOP_NO, 'id', 'value');
        $displayprice[] = HTML::_('select.option', 2, _WOP_SHOP_ONLY_REGISTER_USER, 'id', 'value');
        $lists['displayprice'] = HTML::_('select.genericlist', $displayprice, 'displayprice','','id','value', $config->displayprice);

        $catsort = array();
        $catsort[] = HTML::_('select.option', 1, _WOP_SHOP_SORT_MANUAL, 'id','value');
        $catsort[] = HTML::_('select.option', 2, _WOP_SHOP_SORT_ALPH, 'id','value');
        $lists['category_sorting'] = HTML::_('select.genericlist', $catsort, 'category_sorting','','id','value', $config->category_sorting);
        $lists['manufacturer_sorting'] = HTML::_('select.genericlist', $catsort, 'manufacturer_sorting','','id','value', $config->manufacturer_sorting);

        $sortd = array();
        $sortd[] = HTML::_('select.option', 0, _WOP_SHOP_A_Z, 'id','value');
        $sortd[] = HTML::_('select.option', 1, _WOP_SHOP_Z_A, 'id','value');
        $lists['product_sorting_direction'] = HTML::_('select.genericlist', $sortd, 'product_sorting_direction','','id','value', $config->product_sorting_direction);

        $opt = array();
        $opt[] = HTML::_('select.option', 'V.value_ordering', _WOP_SHOP_SORT_MANUAL, 'id','value');
        $opt[] = HTML::_('select.option', 'value_name', _WOP_SHOP_SORT_ALPH, 'id','value');
        $opt[] = HTML::_('select.option', 'PA.price', _WOP_SHOP_SORT_PRICE, 'id','value');
        $opt[] = HTML::_('select.option', 'PA.ean', _WOP_SHOP_EAN_PRODUCT, 'id','value');
        $opt[] = HTML::_('select.option', 'PA.count', _WOP_SHOP_QUANTITY_PRODUCT, 'id','value');
        $opt[] = HTML::_('select.option', 'PA.product_attr_id', _WOP_SHOP_SPECIFIED_IN_PRODUCT, 'id','value');
        $lists['attribut_dep_sorting_in_product'] = HTML::_('select.genericlist', $opt, 'attribut_dep_sorting_in_product','','id','value', $config->attribut_dep_sorting_in_product);

        $opt = array();
        $opt[] = HTML::_('select.option', 'V.value_ordering', _WOP_SHOP_SORT_MANUAL, 'id','value');
        $opt[] = HTML::_('select.option', 'value_name', _WOP_SHOP_SORT_ALPH, 'id','value');
        $opt[] = HTML::_('select.option', 'addprice', _WOP_SHOP_SORT_PRICE, 'id','value');
        $opt[] = HTML::_('select.option', 'PA.id', _WOP_SHOP_SPECIFIED_IN_PRODUCT, 'id','value');
        $lists['attribut_nodep_sorting_in_product'] = HTML::_('select.genericlist', $opt, 'attribut_nodep_sorting_in_product','','id','value', $config->attribut_nodep_sorting_in_product);

        $select = array();

        foreach ($config->sorting_products_name_select as $key => $value) {
            $select[] = HTML::_('select.option', $key, $value, 'id', 'value');
        }
        $lists['product_sorting'] = HTML::_('select.genericlist',$select, "product_sorting", '', 'id','value', $config->product_sorting);

        if ($config->admin_show_product_extra_field){
            $_productfields = $this->getModel("productFields");
            $rows = $_productfields->getList();

            $lists['product_list_display_extra_fields'] = HTML::_('select.genericlist', $rows, "product_list_display_extra_fields[]", ' size="10" multiple = "multiple" ', 'id','name', $config->getProductListDisplayExtraFields());
            $lists['filter_display_extra_fields'] = HTML::_('select.genericlist', $rows, "filter_display_extra_fields[]", ' size="10" multiple = "multiple" ', 'id','name', $config->getFilterDisplayExtraFields());
            $lists['product_hide_extra_fields'] = HTML::_('select.genericlist', $rows, "product_hide_extra_fields[]", ' size="10" multiple = "multiple" ', 'id','name', $config->getProductHideExtraFields());
            $lists['cart_display_extra_fields'] = HTML::_('select.genericlist', $rows, "cart_display_extra_fields[]", ' size="10" multiple = "multiple" ', 'id','name', $config->getCartDisplayExtraFields());
        }

        $_units = $this->getModel("units");
        $list_units = $_units->getUnits();
        $lists['units'] = HTML::_('select.genericlist',$list_units, "main_unit_weight", '', 'id','name', $config->main_unit_weight);

        $view = $this->getView('configuration');
        $view->setLayout("categoryproduct");
        $view->assign("lists", $lists);
        $view->assign("config", $config); 
		do_action_ref_array('onBeforeEditConfigCatProd', array(&$view));
        $view->display();
    } 

    public function checkout() {
        $config = Factory::getConfig();
        $_orders = $this->getModel("orders");
        $order_status = $_orders->getAllOrderStatus();
        $lists['status'] = HTML::_('select.genericlist', $order_status,'default_status_order','class = "inputbox" size = "1"','status_id','name', $config->default_status_order);
        $currency_code = getMainCurrencyCode();        
        $_countries = $this->getModel("countries");
        $countries = $_countries->getAllCountries(0);
        $first = HTML::_('select.option', 0,_WOP_SHOP_SELECT,'country_id','name' );
        array_unshift($countries,$first);
        $lists['default_country'] = HTML::_('select.genericlist', $countries, 'default_country','class = "inputbox" size = "1"','country_id','name', $config->default_country);
        
        $vendor_order_message_type = array();
        $vendor_order_message_type[] = HTML::_('select.option', 0, _WOP_SHOP_NOT_SEND_MESSAGE, 'id', 'name' );
        $vendor_order_message_type[] = HTML::_('select.option', 1, _WOP_SHOP_WE_SEND_MESSAGE, 'id', 'name' );
        $vendor_order_message_type[] = HTML::_('select.option', 2, _WOP_SHOP_WE_SEND_ORDER, 'id', 'name' );
        $vendor_order_message_type[] = HTML::_('select.option', 3, _WOP_SHOP_WE_ALWAYS_SEND_ORDER, 'id', 'name' );
        $lists['vendor_order_message_type'] = HTML::_('select.genericlist', $vendor_order_message_type, 'vendor_order_message_type','class = "inputbox" size = "1"','id','name', $config->vendor_order_message_type);

        $option = array();
        $option[] = HTML::_('select.option', 0, _WOP_SHOP_STEP_3_4, 'id', 'name');
        $option[] = HTML::_('select.option', 1, _WOP_SHOP_STEP_4_3, 'id', 'name');
        $lists['step_4_3'] = HTML::_('select.genericlist', $option, 'step_4_3','class = "inputbox"','id','name', $config->step_4_3);
        
        $view = $this->getView('configuration');
        $view->assign("config", $config); 
        $view->assign("lists", $lists);
        $view->setLayout('checkout');
		do_action_ref_array('onBeforeEditConfigCheckout', array(&$view));
        $view->display();
    }

    public function fieldregister() {
        $config = Factory::getConfig();
        $view = $this->getView("configuration");
        $view->setLayout("fieldregister");
        include($config->path.'lib/default_config.php');

        $current_fields = $config->getListFieldsRegister();
        $view->assign("fields", $fields_client);
        $view->assign("current_fields", $current_fields);
        $view->assign("fields_sys", $fields_client_sys);
		do_action_ref_array('onBeforeEditConfigFieldRegister', array(&$view));
        $view->display();
    } 

    public function currency() {
        $config = Factory::getConfig();
        $_currencies = $this->getModel("currencies");
        $currencies = $_currencies->getAllCurrencies();
        $lists['currencies'] = HTML::_('select.genericlist', $currencies,'mainCurrency','class = "inputbox" size = "1"','currency_id','currency_name',$config->mainCurrency);
        $i = 0;
        foreach ($config->format_currency as $key => $value) {
            $currenc[$i] = new stdClass();
            $currenc[$i]->id_cur = $key;
            $currenc[$i]->format = $value;
            $i++;
        }
        $lists['format_currency'] = HTML::_('select.genericlist', $currenc,'currency_format','class = "inputbox" size = "1"','id_cur','format',$config->currency_format);

        $view = $this->getView('configuration');
        $view->setLayout('currency');
        $view->assign("lists", $lists);
        $view->assign("config", $config);
		do_action_ref_array('onBeforeEditConfigCurrency', array(&$view));
        $view->display();
    }
    
    public function image() {    
        $config = Factory::getConfig();
        
        $resize_type = array();
        $resize_type[] = HTML::_('select.option', 0, _WOP_SHOP_CUT, 'id', 'name' );
        $resize_type[] = HTML::_('select.option', 1, _WOP_SHOP_FILL, 'id', 'name' );
        $resize_type[] = HTML::_('select.option', 2, _WOP_SHOP_STRETCH, 'id', 'name' );
        $select_resize_type = HTML::_('select.genericlist', $resize_type, 'image_resize_type','class = "inputbox" size = "1"','id','name', $config->image_resize_type);
        
        $view = $this->getView('configuration');
        $view->setLayout('image');
        $view->assign("config", $config); 
        $view->assign("select_resize_type", $select_resize_type);
		do_action_ref_array('onBeforeEditConfigImage', array(&$view));
        $view->display();
    } 

//    function statictext() {
//        global $config;
//        $model = $this->getModel('configuration');
//        $config = $model->getConfig();
//        $view = $this->getView('configuration');
//        $view->setLayout('liststatictext');
//        $view->assign("config", $config);
//        $view->display();
//    } 

    public function seo() {
        $config = Factory::getConfig();
        $_seo = Factory::getAdminModel("seo");
        $rows = $_seo->getList();
        $view = $this->getView('configuration');
        $view->setLayout('listseo');
        //$view->assign("etemplatevar", '');
        $view->assign("rows", $rows); 
		do_action_ref_array('onBeforeDisplaySeo', array(&$view));
        $view->display();
    } 

    public function seoedit(){
        $id = Request::getInt("id");

        $seo = Factory::getTable("seo");
        $seo->load($id);

        $_lang = $this->getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;

        
        $view = $this->getView("configuration");
        $view->setLayout("editseo");
        $view->assign('row', $seo);
        //$view->assign('etemplatevar', '');
        $view->assign('languages', $languages);
        $view->assign('multilang', $multilang);
		
        do_action_ref_array('onBeforeDisplaySeoEdit', array(&$view));
        $view->display();
    } 

    public function saveseo(){
        $id = Request::getInt("id");
        $post = Request::get("post");
        do_action_ref_array( 'onBeforeSaveConfigSeo', array(&$post) );
        
        $seo = Factory::getTable("seo");
        $seo->load($id);
        $seo->bind($post);        
        if (!$id){
            $seo->ordering = null;
            $seo->ordering = $seo->getNextOrder();            
        }        
        $result = $seo->store($post);
		do_action_ref_array( 'onAfterSaveConfigSeo', array(&$seo) );
        /*$app= Factory::getApplication();
        $id = Request::getInt("id");
        $post = Request::get("post");
        unset($post['submit']);
        unset($post['id']);
        $_seo = Factory::getAdminModel("seo");
        $result = $_seo->update($post, $id);*/
        if ($result){
            $this->setRedirect('admin.php?page=configuration&task=seo', _WOP_SHOP_CONFIG_SUCCESS, 'updated');
        } else{
            $this->setRedirect('admin.php?page=configuration&task=seoedit&id='.$id, _WOP_SHOP_ERROR_CONFIG, 'error');
        }
    }

    public function storeinfo() {
        $config = Factory::getConfig();
        global $wpdb;
        //$vendor = $wpdb->get_row( "SELECT * FROM ".$wpdb->prefix."wshop_vendors");
        $vendor = Factory::getTable('vendor');
        $vendor->loadMain();

    	$_countries = $this->getModel("countries");
        $countries = $_countries->getAllCountries(0);
        $first = HTML::_('select.option', 0,_WOP_SHOP_SELECT,'country_id','name' );
        array_unshift($countries, $first);
        $lists['countries'] = HTML::_('select.genericlist', $countries, 'country', 'class = "inputbox"', 'country_id', 'name', $vendor->country);

        $nofilter = array();
        //FilterOutput::objectHTMLSafe( $vendor, ENT_QUOTES, $nofilter);

    	$view=$this->getView("configuration");
        $view->setLayout("storeinfo");
        $view->assign("lists", $lists); 
        $view->assign("vendor", $vendor);
        $view->assign("config", $config);
		do_action_ref_array('onBeforeEditConfigStoreInfo', array(&$view));
        $view->display();
    } 

    public function otherconfig(){
        $wconfig = Factory::getConfig();
        $config = new stdClass();
        include($wconfig->path.'lib/default_config.php');
        $tax_rule_for = array();
        $tax_rule_for[] = HTML::_('select.option', 0, _WOP_SHOP_FIRMA_CLIENT, 'id', 'name' );
        $tax_rule_for[] = HTML::_('select.option', 1, _WOP_SHOP_VAT_NUMBER, 'id', 'name' );
        $lists['tax_rule_for'] = HTML::_('select.genericlist', $tax_rule_for, 'ext_tax_rule_for','class = "inputbox" size = "1"','id','name', $config->ext_tax_rule_for);

        $view=$this->getView("configuration");
        $view->setLayout("otherconfig");
        $view->assign("other_config", $other_config);
        $view->assign("other_config_checkbox", $other_config_checkbox);
        $view->assign("other_config_select", $other_config_select);
        $view->assign("config", $wconfig);
        $view->assign("lists", $lists);
        do_action_ref_array('onBeforeEditConfigOtherConfig', array(&$view));
        $view->display();
    }
    
    public function save(){
        $config = Factory::getConfig();
        $tab = Request::getInt('tabs');
		
        switch ($tab){
            case 1: $layout = "general"; break;
            case 2: $layout = "currency"; break;
            case 3: $layout = "image"; break;
            case 5: $layout = "storeinfo"; break;
            case 6: $layout = "catprod"; break;
            case 7: $layout = "checkout"; break;
            case 8: $layout = "adminfunction"; break;
            case 9: $layout = "fieldregister"; break;
            case 10: $layout = "otherconfig"; break;
            case 11: $layout = "permalinks"; break;
        }

        global $wpdb;

        $post = Request::get("post");
		do_action_ref_array('onBeforeSaveConfig', array(&$post, &$extconf));
        //$row = $wpdb->get_row( "SELECT * FROM ".$wpdb->prefix.'wshop_config');
        //$row_id = $row->id;

        //general
        $array = array('display_price_admin', 'display_price_front','use_ssl','savelog','savelogpaymentdata');
        if ($tab == 1){
            foreach ($array as $key => $value) {
                if (!isset($post[$value])) {
                    $post[$value] = 0;
                }
            }
        }

        if ($tab == 5){
            $vendor = Factory::getTable('vendor');
            $post = Request::get("post");
            $vendor->id = $post['vendor_id'];
            $vendor->main = 1;
            $vendor->bind($post);
            $vendor->store();
            /*$fields = $wpdb->get_results("SHOW FIELDS FROM ".$wpdb->prefix."wshop_vendors");
            $obj = array();
            foreach($fields as $index=>$field){
                $filed_name = $field->Field;
                if(isset($post[$filed_name])) $obj[$filed_name] = $post[$filed_name];
            }
            $obj['main'] = 1;
            $wpdb->update(
                $wpdb->prefix."wshop_vendors",
                $obj, 
                array(
                    'id' => $post['vendor_id']
                )
            );*/
            //$wpdb->show_errors(); $wpdb->print_error();
        }
        //category/product
        $array = array('show_buy_in_category','show_tax_in_product','show_tax_product_in_cart','show_plus_shipping_in_product','hide_product_not_avaible_stock','hide_buy_not_avaible_stock','show_sort_product','show_count_select_products','show_delivery_time','demo_type','product_show_manufacturer_logo','product_show_weight',
                       'product_attribut_first_value_empty', 'show_hits', 'allow_reviews_prod', 'allow_reviews_only_registered','hide_text_product_not_available','use_plugin_content', 'product_list_show_weight', 'product_list_show_manufacturer','show_product_code','product_list_show_min_price', 'show_product_list_filters',
                       'product_list_show_vendor','product_show_vendor','product_show_vendor_detail','product_show_button_back','product_list_show_product_code','radio_attr_value_vertical','attr_display_addprice','product_list_show_price_description','display_button_print','product_list_show_price_default');
        if ($tab == 6){
            foreach ($array as $key => $value) {
                if (!isset($post[$value])) $post[$value] = 0;
            }
            $result = new stdClass();
            if ($config->other_config != ''){
                $result = json_decode($config->other_config);
            }

            //$config = new stdClass();
            include($config->path.'lib/default_config.php');

            foreach($catprod_other_config as $k){
                $result->$k = $post[$k];
            }
            $post['other_config'] = json_encode($result);
        }

        //case
        $array = array('hide_shipping_step', 'hide_payment_step', 'order_send_pdf_client','order_send_pdf_admin','hide_tax', 'show_registerform_in_logintemplate','sorting_country_in_alphabet','show_weight_order', 'discount_use_full_sum','show_cart_all_step_checkout',"show_product_code_in_cart",'show_return_policy_in_email_order',
                        'client_allow_cancel_order', 'admin_not_send_email_order_vendor_order','not_redirect_in_cart_after_buy','calcule_tax_after_discount');
        if ($tab == 7){
            if (!$post['next_order_number']){
                unset($post['next_order_number']);
            }
            foreach($array as $key=>$value){
                if (!isset($post[$value])) $post[$value] = 0;
            }
            $result = new stdClass();
            if ($config->other_config!=''){
                $result = json_decode($config->other_config);
            }
            //$conf = new stdClass();
            include($config->path.'lib/default_config.php');
            foreach($checkout_other_config as $k){
                $result->$k = $post[$k];
            }
            $post['other_config'] = json_encode($result);
        }
        //shop function
        $array = array('without_shipping', 'without_payment', 'enable_wishlist', 'shop_user_guest','user_as_catalog', 'use_rabatt_code', 'admin_show_product_basic_price','admin_show_attributes','admin_show_delivery_time','admin_show_languages','use_different_templates_cat_prod','admin_show_product_video','admin_show_product_related','admin_show_product_files','admin_show_product_bay_price','admin_show_product_basic_price', 'admin_show_product_labels', 'admin_show_product_extra_field','admin_show_vendors','admin_show_freeattributes','use_extend_attribute_data');
        if ($tab == 8){
            foreach ($array as $key => $value) {
                if (!isset($post[$value])) $post[$value] = 0;
            }

            $post['without_shipping'] = intval(!$post['without_shipping']);
            $post['without_payment'] = intval(!$post['without_payment']);
            
            $result = new stdClass();
            if ($config->other_config!=''){
                $result = json_decode($config->other_config);
            }
            //$config = new stdClass();
            include($config->path.'lib/default_config.php');
            foreach($adminfunction_other_config as $k){
                $result->$k = $post[$k];
            }
            $post['other_config'] = json_encode($result);
        }

        if ($tab == 9){
            //$config = new stdClass();
            include($config->path.'lib/default_config.php');
            foreach($fields_client_sys as $k=>$v){
                if(is_array($v))
                foreach($v as $v2){
                    //$post['field'][$k][$v2]['require'] = 1;
                    //$post['field'][$k][$v2]['display'] = 1;
                }
            }

            if(is_array($post['field']))
            foreach($post['field'] as $k=>$v){
                foreach($v as $k2=>$v2){
                    if (!$post['field'][$k][$k2]['display']){
                        $post['field'][$k][$k2]['require'] = 0;
                    }
                }
            }

            $post['fields_register'] = json_encode($post['field']);
        }

        if ($tab == 10){
            $result = new stdClass();
            //$config = new stdClass();
            include($config->path.'lib/default_config.php');

            if ($config->other_config != ''){
                $result = json_decode($config->other_config);
            }

            if(is_array($other_config))
            foreach ($other_config as $k) {
                $result->$k = $post[$k];
            }

            $post['other_config'] = json_encode($result);
        }
        
        if ($tab != 4){
            $configuration = Factory::getTable('configuration');
            $configuration->load(self::CONFIGURATION_ID);
            if (!$configuration->bind($post)) {
                $this->setRedirect('admin.php?page=configuration', _WOP_SHOP_ERROR_BIND);
                return 0;
            }

            if ($tab == 6 && $config->admin_show_product_extra_field){
                if (!isset($post['product_list_display_extra_fields'])) $post['product_list_display_extra_fields'] = array();
                if (!isset($post['filter_display_extra_fields'])) $post['filter_display_extra_fields'] = array();
                if (!isset($post['product_hide_extra_fields'])) $post['product_hide_extra_fields'] = array();
                if (!isset($post['cart_display_extra_fields'])) $post['cart_display_extra_fields'] = array();
                $configuration->setProductListDisplayExtraFields($post['product_list_display_extra_fields']);
                $configuration->setFilterDisplayExtraFields($post['filter_display_extra_fields']);
                $configuration->setProductHideExtraFields($post['product_hide_extra_fields']);
                $configuration->setCartDisplayExtraFields($post['cart_display_extra_fields']);
            }

            $configuration->transformPdfParameters();

            if (!$configuration->store()) {
                $this->setRedirect('admin.php?page=configuration&task='.$layout, _WOP_SHOP_ERROR_SAVE_DATABASE);
                return 0;
            }            
        }
        
        if ($tab == 11){
            $config->shop_base_page = $configuration->shop_base_page;
            flush_rewrite_rules();
        }

        if (isset($_FILES['header'])){
            if ($_FILES['header']['size']){
                @unlink($config->path."/assets/images/header.jpg");
                move_uploaded_file( $_FILES['header']['tmp_name'],$config->path."/assets/images/header.jpg");
            }
        }

        if (isset($_FILES['footer'])){
            if ($_FILES['footer']['size']){
                @unlink($config->path."/assets/images/footer.jpg");
                move_uploaded_file( $_FILES['footer']['tmp_name'],$config->path."images/footer.jpg");
            }
        }

        if (isset($post['update_count_prod_rows_all_cats']) && $tab == 6 && $post['update_count_prod_rows_all_cats']){
            $count_products_to_page = intval($post['count_products_to_page']);
            $count_products_to_row = intval($post['count_products_to_row']);
            
            $wpdb->query("UPDATE ".$wpdb->prefix."wshop_categories SET products_page = ".$count_products_to_page.", products_row = ".$count_products_to_row);
            $wpdb->query("UPDATE ".$wpdb->prefix."wshop_manufacturers SET products_page = ".$count_products_to_page.", products_row = ".$count_products_to_row);
            
        }
        
		do_action_ref_array('onAfterSaveConfig', array());
        $this->setRedirect('admin.php?page=configuration&task='.$layout, _WOP_SHOP_CONFIG_SUCCESS);
    }
    
    public function preview_pdf(){
        $config = Factory::getConfig();
        $config->currency_code = "USD";
        $file_generete_pdf_order = $config->file_generete_pdf_order;		
        $order = Factory::getTable('order');
        $order->firma_name = "Firma";
        $order->f_name = "Fname";
        $order->l_name = 'Lname';
        $order->street = 'Street';
        $order->zip = "Zip"; 
        $order->city = "City";
        $order->country = "Country";
        $order->order_number = outputDigit(0,8);
        $order->order_date = strftime($config->store_date_format, time());
        $order->products = array();
        $prod = new stdClass();
        $prod->product_name = "Product name";
        $prod->product_ean = "12345678";
        $prod->product_quantity = 1;
        $prod->product_item_price = 125;
        $prod->product_tax = 19;
        $order->products[] = $prod;
        $order->order_subtotal = 125;
        $order->order_shipping = 20;        
        $display_price = $config->display_price_front;
        if ($display_price==0){
            $order->display_price = 0;
            $order->order_tax_list = array(19 => 23.15);
            $order->order_total = 145;
        }else{
            $order->display_price = 1;
            $order->order_tax_list = array(19 => 27.55);
            $order->order_total = 172.55;
        }
        do_action_ref_array('onBeforeCreateDemoPreviewPdf', array(&$order, &$file_generete_pdf_order));
        require_once($file_generete_pdf_order);
        $order->pdf_file = generatePdf($order, $config);
        $config->pdf_orders_live_path."/".$order->pdf_file; 
        //header("Location: ".$config->pdf_orders_live_path."/".$order->pdf_file);
        $this->setRedirect($config->pdf_orders_live_path."/".$order->pdf_file);
        die();
    }
    
    public function permalinks() {
        $config = Factory::getConfig();
        $pages = get_pages();
        
        $firstPage = array();
        $firstPage[0] = new stdClass();
        $firstPage[0]->ID = 0;
        $firstPage[0]->post_title = "-";       
        
        $lists['shopBasePages'] = HTML::_('select.genericlist', array_merge($firstPage, $pages), 'shop_base_page', 'class="inputbox"', 'ID', 'post_title', $config->shop_base_page);

        $view = $this->getView('configuration');
        $view->setLayout("permalinks");
        $view->assign("lists", $lists);
        $view->assign("config", $config);
		do_action_ref_array('onBeforeEditConfigPermalinks', array(&$view));
        $view->display();
    }
}