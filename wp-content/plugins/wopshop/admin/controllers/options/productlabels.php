<?php
class ProductLabelsWshopAdminController extends WshopAdminController {
    function __construct() {
        parent::__construct();
    }
   
    function display() {
        $config = Factory::getConfig();

        $context = "admin.productlabels.";
        $filter_order = getStateFromRequest($context.'filter_order', 'filter_order', "name");
        $filter_order_Dir = getStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc");

        $_productLabels = $this->getModel("productLabels");
        $rows = $_productLabels->getList($filter_order, $filter_order_Dir);

        if($filter_order_Dir == 'asc') $filter_order_Dir = 'desc'; else $filter_order_Dir = 'asc';

        $actions = array(
            'delete' => _WOP_SHOP_DELETE
        );
        $bulk = $_productLabels->getBulkActions($actions);

        $view=$this->getView("productlabels");
        $view->setLayout("list");		
        $view->assign('bulk', $bulk);
        $view->assign('rows', $rows);
        $view->assign('config', $config);
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);       
        do_action_ref_array('onBeforeDisplayProductLabels', array(&$view));		
        $view->display();
    }
    function edit(){
        $config = Factory::getConfig();
        $id = Request::getInt("row");
        $productLabel = Factory::getTable('productlabel');
        $productLabel->load($id);
        $edit = ($id)?(1):(0);
        $_lang = $this->getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
        //FilterOutput::objectHTMLSafe($productLabel, ENT_QUOTES);

        $view=$this->getView("productlabels", 'html');
        $view->setLayout("edit");
        $view->assign('productLabel', $productLabel);
        $view->assign('config', $config);
        $view->assign('edit', $edit);
        $view->assign('languages', $languages);
        $view->assign('multilang', $multilang);
        do_action_ref_array('onBeforeEditProductLabels', array(&$view));
        $view->display();
    }
    function save(){
        if ( !empty($_POST) && check_admin_referer('productlabel_edit','name_of_nonce_field') )
        {
        $config = Factory::getConfig();
        require_once($config->path.'lib/uploadfile.class.php');
    
        $id = Request::getInt("id");
        $productLabel = Factory::getTable('productlabel');
        $post = Request::get("post");
        $lang = $config->cur_lang; //get_bloginfo('language');
        $post['name'] = $post["name_".$lang];
        do_action_ref_array('onBeforeSaveProductLabel', array(&$post));
        $upload = new UploadFile($_FILES['productlabel_image']);
        $upload->setAllowFile(array('jpeg','jpg','gif','png'));
        $upload->setDir($config->image_labels_path);
        $upload->setFileNameMd5(0);
        $upload->setFilterName(1);
        if ($upload->upload()){
            if ($post['old_image']){
                @unlink($config->image_labels_path."/".$post['old_image']);
            }
            $post['image'] = $upload->getName();
            @chmod($config->image_labels_path."/".$post['image'], 0777);
        }else{
            if ($upload->getError() != 4){
                addMessage(_WOP_SHOP_ERROR_UPLOADING_IMAGE);
                //saveToLog("error.log", "Label - Error upload image. code: ".$upload->getError());
            }
        }
        if (!$productLabel->bind($post)) {
            addMessage(_WOP_SHOP_ERROR_BIND);
            $this->setRedirect("admin.php?page=options&tab=productlabels");
            return 0;
        }
        if (!$productLabel->store()) {
            addMessage(_WOP_SHOP_ERROR_SAVE_DATABASE);
            $this->setRedirect("admin.php?page=options&tab=productlabels");
            return 0;
        }
        do_action_ref_array('onAfterSaveProductLabel', array(&$productLabel));
        $this->setRedirect("admin.php?page=options&tab=productlabels");
        }
        //$this->setRedirect('admin.php?page=options&tab=productlabels');
    }
    
    
    function delete(){
        $config = Factory::getConfig();
        $text = array();
        $productLabel = Factory::getTable('productlabel');
        $cid = Request::getVar("rows");
        do_action_ref_array( 'onBeforeRemoveProductLabel', array(&$cid) );
        foreach ($cid as $key => $value) {
            $productLabel->load($value);
            @unlink($config->image_labels_path."/".$productLabel->image);
            $productLabel->delete();			
            $text[] = _WOP_SHOP_ITEM_DELETED."<br>";			
        }
        do_action_ref_array( 'onAfterRemoveProductLabel', array(&$cid) );
        $this->setRedirect("admin.php?page=options&tab=productlabels", implode("</p><p>", $text));
                
        
        //$this->setRedirect('admin.php?page=options&tab=productlabels');
    }
    function deleteFoto(){
        $config = Factory::getConfig();
        $id = Request::getInt("id");
        $productLabel = Factory::getTable('productlabel');
        $productLabel->load($id);
        @unlink($config->image_labels_path."/".$productLabel->image);
        $productLabel->image = "";
        $productLabel->store();

       
    }
}