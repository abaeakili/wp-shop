<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class AddonsWshopAdminController extends WshopAdminController {
    public function __construct() {        
        parent::__construct();
    }
   
    public function display() {
        $addons = $this->getModel("addons");
        $rows = $addons->getList(1);
        
        $actions = array(
            'publish' => _WOP_SHOP_ACTION_PUBLISH,
            'unpublish' => _WOP_SHOP_ACTION_UNPUBLISH
        );
        $bulk = $addons->getBulkActions($actions);
        
        $view = $this->getView("addons");
        $view->setLayout("list");
        $view->assign('rows', $rows);
        $view->assign('back', base64_encode("admin.php?page=options&tab=addons"));
        $view->assign('bulk', $bulk);
        do_action_ref_array('onBeforeDisplayAddons', array(&$view));
        $view->display();
    }
    
    public function edit(){
        $id = Request::getInt("id");
        $row = Factory::getTable('addon');
        $row->load($id);

        $config_file_patch = WOPSHOP_PLUGIN_DIR."/site/addons/".$row->alias."/config.tmpl.php";

        $view = $this->getView("addons");
        $view->setLayout("edit");
        $view->assign('row', $row);
        $view->assign('params', $row->getParams());
        $view->assign('config_file_patch', $config_file_patch);
        $view->assign('config_file_exist', file_exists($config_file_patch));
        do_action_ref_array('onBeforeEditAddons', array(&$view));
        $view->display();
    }
    
    public function save(){        
        $post = Request::get('post');
        
        if (!empty($post) && is_array($post) && check_admin_referer('addon_edit', 'name_of_nonce_field')) {
            $this->getModel("addons")->save($post);
        }        
        
        $this->setRedirect('admin.php?page=options&tab=addons');
    }

    public function delete(){
        $id = Request::getInt("id");
        $this->getModel("addons")->delete($id);
        $this->setRedirect('admin.php?page=options&tab=addons');
    }
    
    protected function getUrlListItems(){
        return "admin.php?page=options&tab=addons";
    }
}