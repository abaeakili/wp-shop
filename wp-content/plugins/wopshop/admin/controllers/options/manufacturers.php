<?php
class ManufacturersWshopAdminController extends WshopAdminController {
    public function __construct() {
        parent::__construct();
    }

    public function display() {
        $context = "admin.manufacturers.";
        $filter_order = getStateFromRequest($context.'filter_order', 'filter_order', 'ordering');
        $filter_order_Dir = getStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', 'asc');

        $model = $this->getModel("manufacturers");

        $actions = array(
            'delete' => _WOP_SHOP_DELETE,
            'publish' => _WOP_SHOP_ACTION_PUBLISH,
            'unpublish' => _WOP_SHOP_ACTION_UNPUBLISH,
        );
        $bulk = $model->getBulkActions($actions);

        if($filter_order_Dir == 'asc') $filter_order_Dir = 'desc'; else $filter_order_Dir = 'asc';

        $rows = $model->getAllManufacturers(0, $filter_order, $filter_order_Dir);
        $view = $this->getView('manufacturers');
        $view->setLayout("list");
        $view->assign('rows', $rows);
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);
        $view->assign('bulk', $bulk);
		do_action_ref_array('onBeforeDisplayManufacturers', array(&$view));
        $view->display();
    }
    
    public function edit(){
        $man_id = Request::getInt('row');

        /*$model = $this->getModel('manufacturers');
        $manufacturer = $model->getDataManufacturer($id);
        $listLanguages = $model->getListLanguages();*/
        

        $manufacturer = Factory::getTable('manufacturer');
        $manufacturer->load($man_id);
        $edit = $man_id ? 1 : 0;
        
        if (!$man_id){
            $manufacturer->manufacturer_publish = 1;
        }
        
        $_lang = $this->getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages) > 1;
        $nofilter = array();
        //FilterOutput::objectHTMLSafe( $manufacturer, ENT_QUOTES, $nofilter);

        $view = $this->getView('manufacturers');
        $view->setLayout('edit');
        $view->assign('manufacturer', $manufacturer);
        $view->assign('listlanguages', $languages);
        $view->assign('multilang', $multilang);
		do_action_ref_array('onBeforeEditManufacturers', array(&$view));
        $view->display();
    }
    
    public function save(){
        if ( !empty($_POST) && check_admin_referer('manufacturer_edit','name_of_nonce_field') )
        {
        $config = Factory::getConfig();

        require_once ($config->path.'lib/image.lib.php');
        require_once ($config->path.'lib/uploadfile.class.php');
        
        $_alias = $this->getModel("alias");
        global $wpdb;
        $man = Factory::getTable('manufacturer');
        $man_id = Request::getInt("manufacturer_id");

        $post = Request::get("post");
        $_lang = $this->getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        foreach($languages as $lang){
            $post['name_'.$lang->language] = trim($post['name_'.$lang->language]);
            if ($config->create_alias_product_category_auto && $post['alias_'.$lang->language]=="") $post['alias_'.$lang->language] = $post['name_'.$lang->language];
			$post['alias_'.$lang->language] = sanitize_title_with_dashes($post['alias_'.$lang->language]);
            if ($post['alias_'.$lang->language]!="" && !$_alias->checkExistAlias1Group($post['alias_'.$lang->language], $lang->language, 0, $man_id)){
                $post['alias_'.$lang->language] = "";
                addMessage(_WOP_SHOP_ERROR_ALIAS_ALREADY_EXIST, 'error');
            }
            $post['description_'.$lang->language] = Request::getVar('description'.$lang->id,'','post',"string", 2);
            $post['short_description_'.$lang->language] = Request::getVar('short_description_'.$lang->language,'','post',"string", 2);
        }
        
        if (!$post['manufacturer_publish']){
            $post['manufacturer_publish'] = 0;
        }
		do_action_ref_array( 'onBeforeSaveManufacturer', array(&$post) );
        if (!$man->bind($post)) {
            addMessage(_WOP_SHOP_ERROR_BIND, 'error');
            $this->setRedirect("admin.php?page=options&tab=manufacturers");
            return 0;
        }
        
        if (!$man_id){
            $man->ordering = null;
            $man->ordering = $man->getNextOrder();            
        }        
        
        $upload = new UploadFile($_FILES['manufacturer_logo']);
        $upload->setAllowFile(array('jpeg','jpg','gif','png'));
        $upload->setDir($config->image_manufs_path);
        $upload->setFileNameMd5(0);
        $upload->setFilterName(1);
        if ($upload->upload()){            
            if ($post['old_image']){
                @unlink($config->image_manufs_path."/".$post['old_image']);
            }
            $name = $upload->getName();
            @chmod($config->image_manufs_path."/".$name, 0777);
            
            if($post['size_im_category'] < 3){
                if($post['size_im_category'] == 1){
                    $category_width_image = $config->image_category_width; 
                    $category_height_image = $config->image_category_height;
                }else{
                    $category_width_image = Request::getInt('category_width_image'); 
                    $category_height_image = Request::getInt('category_height_image');
                }

                $path_full = $config->image_manufs_path."/".$name;
                $path_thumb = $config->image_manufs_path."/".$name;

                if (!ImageLib::resizeImageMagic($path_full, $category_width_image, $category_height_image, $config->image_cut, $config->image_fill, $path_thumb, $config->image_quality, $config->image_fill_color)) {
                    addMessage(_WOP_SHOP_ERROR_CREATE_THUMBAIL);
                }
                @chmod($config->image_manufs_path."/".$name, 0777);    
                unset($img);
            }
            $man->manufacturer_logo = $name;
        }else{
            if ($upload->getError() != 4){
                addMessage(_WOP_SHOP_ERROR_UPLOADING_IMAGE, 'error');
            }
        }
        if (!$man->store()) {
            addMessage(_WOP_SHOP_ERROR_SAVE_DATABASE, 'error');
            $this->setRedirect("admin.php?page=options&tab=manufacturers");
            return 0;
        }
		do_action_ref_array( 'onAfterSaveManufacturer', array(&$man) );
        $this->setRedirect("admin.php?page=options&tab=manufacturers");
        return;
        }
        $this->setRedirect('admin.php?page=options&tab=manufacturers', _WOP_SHOP_ERROR_SAVE_DATABASE);
        
    }
    
    public function publish(){
        $this->publishManufacturer(1);
    }
    
    public function unpublish(){
        $this->publishManufacturer(0);
    }
    
    private function publishManufacturer($flag) {
        $cid = Request::getVar("rows");
        global $wpdb;
        do_action_ref_array( 'onBeforePublishManufacturer', array(&$cid, &$flag) );
        foreach ($cid as $key => $value) {
            $wpdb->update( $wpdb->prefix."wshop_manufacturers", array('manufacturer_publish'=>$flag), array('manufacturer_id' => esc_sql($value)));
        }
        do_action_ref_array( 'onAfterPublishManufacturer', array(&$cid, &$flag) );
        $this->setRedirect("admin.php?page=options&tab=manufacturers");
    }
    
    public function delete(){
        $cid = Request::getVar("rows");
        global $wpdb;
        $config = Factory::getConfig();
        $text = array();
		do_action_ref_array( 'onBeforeRemoveManufacturer', array(&$cid) );
        foreach ($cid as $key => $value) {
            //$wpdb->delete($wpdb->prefix."wshop_manufacturers", array( 'manufacturer_id' => esc_sql($value) ) );
            $manuf = Factory::getTable('manufacturer');
            $manuf->load($value);
            $manuf->delete();

            $text[]= sprintf(_WOP_SHOP_MANUFACTURER_DELETED, $value);
            if ($manuf->manufacturer_logo){
                @unlink($config->image_manufs_path.'/'.$manuf->manufacturer_logo);
            }         
        }
		do_action_ref_array( 'onAfterRemoveManufacturer', array(&$cid) );
        $this->setRedirect("admin.php?page=options&tab=manufacturers", implode("</li><li>",$text));
    }
    
    public function deleteFoto(){
        $id = Request::getInt("id");
        $config = Factory::getConfig();
        $manuf = Factory::getTable('manufacturer');
        $manuf->load($id);
        @unlink($config->image_manufs_path.'/'.$manuf->manufacturer_logo);
        $manuf->manufacturer_logo = "";
        $manuf->store();        
        die();
    }
	
    function order(){
		$order = Request::getVar("order");
		$cid = Request::getInt("id");
		$number = Request::getInt("number");
		global $wpdb;
		switch ($order) {
			case 'up':
				$query = "SELECT a.manufacturer_id, a.ordering
					   FROM `".$wpdb->prefix."wshop_manufacturers` AS a
					   WHERE a.ordering < '" . $number . "'
					   ORDER BY a.ordering DESC
					   LIMIT 1";
				break;
			case 'down':
				$query = "SELECT a.manufacturer_id, a.ordering
					   FROM `".$wpdb->prefix."wshop_manufacturers` AS a
					   WHERE a.ordering > '" . $number . "'
					   ORDER BY a.ordering ASC
					   LIMIT 1";
		}
		$row = $wpdb->get_row($query);
		$query1 = "UPDATE `".$wpdb->prefix."wshop_manufacturers` AS a
					 SET a.ordering = '" . $row->ordering . "'
					 WHERE a.manufacturer_id = '" . $cid . "'";
		$query2 = "UPDATE `".$wpdb->prefix."wshop_manufacturers` AS a
					 SET a.ordering = '" . $number . "'
					 WHERE a.manufacturer_id = '" . $row->manufacturer_id . "'";
		$wpdb->query($query1);
		$wpdb->query($query2);
		
		$this->setRedirect("admin.php?page=options&tab=manufacturers");		
    }
    
    function saveorder(){
		$cid = Request::getVar("rows");
        $order = Request::getVar('order', array(), 'post', 'array' );                
        foreach($cid as $k=>$id){
            $table = Factory::getTable('manufacturer');
            $table->load($id);
            if ($table->ordering!=$order[$k]){
                $table->ordering = $order[$k];
                $table->store();
            }
        }                
        $this->setRedirect("admin.php?page=options&tab=manufacturers");		
    }	
}