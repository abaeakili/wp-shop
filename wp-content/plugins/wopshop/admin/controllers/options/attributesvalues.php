<?php
class AttributesValuesWshopAdminController extends WshopAdminController {
    function __construct() {
        parent::__construct();
    }
   
    function display($cachable = false, $urlparams = false){        
        global $wpdb;
        $config = Factory::getConfig();

        $attr_id = Request::getVar("attr_id");
        $context = "admin.attributesvalues.";
        $filter_order = getStateFromRequest($context.'filter_order', 'filter_order', 'value_ordering');
        $filter_order_Dir = getStateFromRequest($context.'filter_order_dir', 'filter_order_Dir', 'asc');

        $attributValues = $this->getModel("AttributValue");
	$rows = $attributValues->getAllValues($attr_id, $filter_order, $filter_order_Dir);
        $attr_name = $attributValues->getNameValue($attr_id);

        $actions = array(
            'delete' => _WOP_SHOP_DELETE
        );
        $bulk = $attributValues->getBulkActions($actions);
        
        if($filter_order_Dir == 'asc') $filter_order_Dir = 'desc'; else $filter_order_Dir = 'asc';

	$view=$this->getView("attributesvalues");
        $view->setLayout("list");
        $view->assign('rows', $rows);
        $view->assign('attr_id', $attr_id);
        $view->assign('config', $config);
        $view->assign('bulk', $bulk);
        $view->assign('attr_name', $attr_name);
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);
		do_action_ref_array('onBeforeDisplayAttributesValues', array(&$view));
        $view->display();
    }

    function edit(){
        global $wpdb;

        $config = Factory::getConfig();
        
        //$value_id = (int)$_REQUEST['value_id'];
        //$attr_id = (int)$_REQUEST['attr_id'];

        $value_id = Request::getInt("value_id");
        $attr_id = Request::getInt("attr_id");

        $attributValue = Factory::getTable('attributvalue');
        $attributValue->load($value_id);
        
        //$query = "SELECT * FROM `".$wpdb->prefix."wshop_attr_values` WHERE `value_id` = '".esc_sql($value_id)."'";
        //$attributValue = $wpdb->get_row($query);

        $_lang = $this->getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;

        //FilterOutput::objectHTMLSafe($attributValue, ENT_QUOTES);

        $view=$this->getView("attributesvalues");
        $view->setLayout("edit");
        $view->assign('attributValue', $attributValue);
        $view->assign('attr_id', $attr_id);
        $view->assign('config', $config);
        $view->assign('languages', $languages);
        $view->assign('multilang', $multilang);
		do_action_ref_array('onBeforeEditAtributesValues', array(&$view));
	$view->display();
    }

    function save(){
        if ( !empty($_POST) && check_admin_referer('attributesvalues_edit','name_of_nonce_field') )
        {
        $config = Factory::getConfig();
        require_once ($config->path.'lib/uploadfile.class.php');
        global $wpdb;
	$value_id = Request::getInt("value_id");
        $attr_id = Request::getInt("attr_id");
        $post = Request::get("post");
        $attributValue = Factory::getTable('attributvalue');
        do_action_ref_array( 'onBeforeSaveAttributValue', array(&$post) );
        $upload = new UploadFile($_FILES['image']);
        $upload->setAllowFile(array('jpeg','jpg','gif','png'));
        $upload->setDir($config->image_attributes_path);
        $upload->setFileNameMd5(0);
        $upload->setFilterName(1);
        if ($upload->upload()){
            if ($post['old_image']){
                @unlink($config->image_attributes_path."/".$post['old_image']);
            }
            $post['image'] = $upload->getName();
            @chmod($config->image_attributes_path."/".$post['image'], 0777);
        }else{
            if ($upload->getError() != 4){
                addMessage(_WOP_SHOP_ERROR_UPLOADING_IMAGE, 'error');
                //saveToLog("error.log", "SaveAttributeValue - Error upload image. code: ".$upload->getError());
            }
        }

        if (!$value_id){
            $query = "SELECT MAX(value_ordering) AS value_ordering FROM `".$wpdb->prefix."wshop_attr_values` where attr_id='".esc_sql($attr_id)."'";
            $row = $wpdb->get_results($query, OBJECT);
            $post['value_ordering'] = $row[0]->value_ordering + 1;
        }
        
        if (!$attributValue->bind($post)) {
            addMessage(_WOP_SHOP_ERROR_BIND, 'error');
            $this->setRedirect("admin.php?page=options&tab=attributesvalues&attr_id=".$attr_id);
            return 0;
        }
                
        if (!$attributValue->store()) {
            addMessage(_WOP_SHOP_ERROR_SAVE_DATABASE, 'error');
            $this->setRedirect("admin.php?page=options&tab=attributesvalues&attr_id=".$attr_id);
            return 0;
        }
        do_action_ref_array( 'onAfterSaveAttributValue', array(&$attributValue) );
        $this->setRedirect("admin.php?page=options&tab=attributesvalues&attr_id=".$attr_id);
        }
    }

    function delete(){
        $cid = Request::getVar("rows");
	$attr_id = Request::getInt("attr_id");
        $config = Factory::getConfig();
        global $wpdb;
        do_action_ref_array( 'onBeforeRemoveAttributValue', array(&$cid) );
        $text = '';
        foreach ($cid as $key => $value){
            $query = "SELECT image FROM `".$wpdb->prefix."wshop_attr_values` WHERE value_id = '".esc_sql($value)."'";
            $image = $wpdb->get_var($query);

            @unlink($config->image_attributes_path."/".$image);

            $wpdb->delete($wpdb->prefix."wshop_attr_values", array( 'value_id' => $value ) );
            $text = _WOP_SHOP_ATTRIBUT_VALUE_DELETED;
        }
        do_action_ref_array( 'onAfterRemoveAttributValue', array(&$cid) );
        $this->setRedirect("admin.php?page=options&tab=attributesvalues&attr_id=".$attr_id, $text);
    }
}