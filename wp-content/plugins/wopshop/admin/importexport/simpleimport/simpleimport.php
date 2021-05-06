<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class IeSimpleimport extends IeController{
    
    function view(){
        $config = Factory::getConfig();
        $ie_id = Request::getInt("ie_id");
        $_importexport = Factory::getTable('Importexport'); 
        $_importexport->load($ie_id);
        $name = $_importexport->get('name');                        
        include(dirname(__FILE__)."/form.php");  
    }

    function save(){
        $mainframe = Factory::getApplication();
        $config = Factory::getConfig();
        require_once(WOPSHOP_PLUGIN_DIR.'lib/uploadfile.class.php');
        require_once(WOPSHOP_PLUGIN_DIR."lib/csv.io.class.php");
        
        $ie_id = Request::getInt("ie_id");
        if (!$ie_id) $ie_id = $this->get('ie_id');

        global $wpdb;       
        $_importexport = Factory::getTable('Importexport'); 
        $_importexport->load($ie_id);
        $alias = $_importexport->get('alias');
        $_importexport->set('endstart', time());
        $_importexport->store();
                
        //get list tax
        $query = "SELECT tax_id, tax_value FROM `".$wpdb->prefix."wshop_taxes`";       
        $rows = $wpdb->get_results($query);
        $listTax = array();
        foreach($rows as $row){
            $listTax[intval($row->tax_value)] = $row->tax_id;
        }
        
        //get list category
        $query = "SELECT category_id as id, `name_".$config->cur_lang."` as name FROM `".$wpdb->prefix."wshop_categories`";
        $rows = $wpdb->get_results($query);
        $listCat = array();
        foreach($rows as $row){
            $listCat[$row->name] = $row->id;
        }
        
        $_products = Factory::getAdminModel('products');                
        
        $dir = $config->importexport_path.$alias;
        
        $upload = new UploadFile($_FILES['file']);
        $upload->setAllowFile(array('csv'));
        $upload->setDir($dir);
        if ($upload->upload()){
            $filename = $dir."/".$upload->getName();
            @chmod($filename, 0777);
            $csv = new csv();
            $data = $csv->read($filename);
            if (is_array($data)){                
                foreach($data as $k=>$row){                    
                    if (count($row)<2 || $k==0) continue;
                                        
                    $tax_value = intval($row[5]);                    
                    if (!isset($listTax[$tax_value])){
                        $tax = Factory::getTable('tax');
                        $tax->set('tax_name', $tax_value);
                        $tax->set('tax_value', $tax_value);
                        $tax->store();
                        $listTax[$tax_value] = $tax->get("tax_id");                        
                    }
                    
                    $category_name = $row['6'];
                    if (!isset($listCat[$category_name]) && $category_name!=""){
                        $cat = Factory::getTable("category");
                        $query = "SELECT max(ordering) FROM `".$wpdb->prefix."wshop_categories`";        
                        $ordering = $wpdb->get_var($query) + 1;
                        $cat->set('name_'.$config->cur_lang, $category_name);
                        $cat->set("category_ordertype", 1);
                        $cat->set("products_page", $config->count_products_to_page);
                        $cat->set("products_row", $config->count_products_to_row);
                        $cat->set("category_publish", 0);
                        $cat->set("ordering", $ordering);                        
                        $cat->store();
                        $listCat[$category_name] = $cat->get("category_id");                        
                    }
                    
                    
                    $product = Factory::getTable('product');
                    $product->set("product_ean", $row[1]);
                    $product->set("product_quantity", $row[2]);
                    $product->set("product_date_added", $row[3]);
                    $product->set("product_price", $row[4]);
                    $product->set("min_price", $row[4]);
                    $product->set("product_tax_id", $listTax[$tax_value]);                                        
                    $product->set("currency_id", $config->mainCurrency);
                    $product->set('name_'.$config->cur_lang, utf8_encode($row[7]));
                    $product->set('short_description_'.$config->cur_lang, utf8_encode($row[8]));
                    $product->set('description_'.$config->cur_lang, utf8_encode($row[9]));
                    $product->store();
                    $product_id = $product->get("product_id");
                    $category_id = $listCat[$category_name];
                    if ($category_name!="" && $category_id){
                        $_products->setCategoryToProduct($product_id, array($category_id));
                    }
                    
                    unset($product);
                }
            }
            @unlink($filename);
        }else{
            addMessage(_WOP_SHOP_ERROR_UPLOADING, 'error');
        }
                
        if (!Request::getInt("noredirect")){
            $mainframe->redirect("admin.php?page=options&tab=importexport&task=view&ie_id=".$ie_id, _WOP_SHOP_COMPLETED);
        }
    }
    
}
?>