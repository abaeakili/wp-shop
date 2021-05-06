<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class IeSimpleExport extends IeController{
    
    function view(){
        $config = Factory::getConfig();
        $ie_id = Request::getInt("ie_id");
        $_importexport = Factory::getTable('Importexport'); 
        $_importexport->load($ie_id);
        $name = $_importexport->get('name');
        $ie_params_str = $_importexport->get('params');
        $ie_params = parseParamsToArray($ie_params_str);
        //if ( ! function_exists( 'list_files' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );
        //$files = list_files($config->importexport_path.$_importexport->alias);
        $files = get_list_files($config->importexport_path.$_importexport->alias,  '.csv');
   
        $count = count($files);
                          
        
        include(dirname(__FILE__)."/list_csv.php");  
    }

    function save(){
        $mainframe = Factory::getApplication();
        
        include_once(WOPSHOP_PLUGIN_DIR."lib/csv.io.class.php");
        
        $ie_id = Request::getInt("ie_id");
        if (!$ie_id) $ie_id = $this->get('ie_id');
        
        $_importexport = Factory::getTable('Importexport'); 
        $_importexport->load($ie_id);
        $alias = $_importexport->alias;
        $_importexport->set('endstart', time());        
        $params = Request::getVar("params");        
        if (is_array($params)){        
            $paramsstr = parseArrayToParams($params);
            $_importexport->set('params', $paramsstr);
        }                
        $_importexport->store();
        
        $ie_params_str = $_importexport->get('params');
        $ie_params = parseParamsToArray($ie_params_str);
        
        $config = Factory::getConfig();
        global $wpdb;
        
        $query = "SELECT prod.product_id, prod.product_ean, prod.product_quantity, prod.product_date_added, prod.product_price, tax.tax_value as tax, prod.`name_".$config->cur_lang."` as name, prod.`short_description_".$config->cur_lang."` as short_description,  prod.`description_".$config->cur_lang."` as description, cat.`name_".$config->cur_lang."` as cat_name
                  FROM `".$wpdb->prefix."wshop_products` AS prod
                  LEFT JOIN `".$wpdb->prefix."wshop_products_to_categories` AS categ USING (product_id)
                  LEFT JOIN `".$wpdb->prefix."wshop_categories` as cat on cat.category_id=categ.category_id
                  LEFT JOIN `".$wpdb->prefix."wshop_taxes` AS tax ON tax.tax_id = prod.product_tax_id              
                  GROUP BY prod.product_id";
        $products = $wpdb->get_results($query);
        
        $data = array();
        $head = array("product_id","ean","qty","date","price","tax","category","name","short_description","description");
        $data[] = $head;
        
        foreach($products as $prod){
            $row = array();
            $row[] = $prod->product_id;
            $row[] = $prod->product_ean;
            $row[] = $prod->product_quantity;
            $row[] = $prod->product_date_added;
            $row[] = $prod->product_price;        
            $row[] = $prod->tax;
            $row[] = utf8_decode($prod->cat_name);
            $row[] = utf8_decode($prod->name);
            $row[] = utf8_decode($prod->short_description);
            $row[] = utf8_decode($prod->description);
            $data[] = $row; 
        }
        
        
        $filename = $config->importexport_path.$alias."/".$ie_params['filename'].".csv";
        
        $csv = new csv();
        $csv->write($filename, $data);
                
        if (!Request::getInt("noredirect")){
            addMessage(_WOP_SHOP_COMPLETED);
            $mainframe->redirect("admin.php?page=options&tab=importexport&task=view&ie_id=".$ie_id);
        }
    }

    function filedelete(){
        $mainframe = Factory::getApplication();
        $config = Factory::getConfig();
        $ie_id = Request::getInt("ie_id");
        $_importexport = Factory::getTable('Importexport'); 
        $_importexport->load($ie_id);
        $alias = $_importexport->alias;
        $file = Request::getVar("file");
        $filename = $config->importexport_path.$alias."/".$file;
        @unlink($filename);
        $mainframe->redirect("admin.php?page=options&tab=importexport&task=view&ie_id=".$ie_id);
    }
    
}
?>