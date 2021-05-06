<?php
class UserGroupsWshopAdminController extends WshopAdminController {
    function __construct() {
        parent::__construct();
    }
   
    function display() {
        $context = "admin.usergroups.";
        $filter_order = getStateFromRequest($context.'filter_order', 'filter_order', "usergroup_id");
        $filter_order_Dir = getStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc");

        $usergroups = $this->getModel("usergroups");
        $rows = $usergroups->getAllUsergroups($filter_order, $filter_order_Dir);

        $actions = array(
            'delete' => _WOP_SHOP_DELETE
        );
        $bulk = $usergroups->getBulkActions($actions);

        if($filter_order_Dir == 'asc') $filter_order_Dir = 'desc'; else $filter_order_Dir = 'asc';
        $view=$this->getView("usergroups");
        $view->setLayout("list");
        $view->assign("rows", $rows);
        $view->assign("bulk", $bulk);
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);
        do_action_ref_array('onBeforeDisplayUserGroups', array(&$view));
        $view->display();
    }
    function edit(){
        $usergroup_id = Request::getInt("row");
        $usergroup = Factory::getTable('usergroup');
        $usergroup->load($usergroup_id);
        $edit = ($usergroup_id) ? 1 : 0;
        //FilterOutput::objectHTMLSafe( $usergroup, ENT_QUOTES, "usergroup_description");
        $view=$this->getView("usergroups");
        $view->setLayout("edit");
        $view->assign("usergroup", $usergroup);
        $view->assign('edit', $edit);
        do_action_ref_array('onBeforeEditUserGroups', array(&$view));
        $view->display();
    }
    function save(){
        if ( !empty($_POST) && check_admin_referer('usergroups_edit','name_of_nonce_field') )
        {
        $usergroup_id = Request::getInt("usergroup_id");
        $usergroup = Factory::getTable('usergroup');
        $usergroups = $this->getModel("usergroups");        
        $post = Request::get("post");
        $post['usergroup_description'] = Request::getVar('usergroup_description','');
        do_action_ref_array( 'onBeforeSaveUserGroup', array(&$post) );
        if (!$usergroup->bind($post)) {
            addMessage(_WOP_SHOP_ERROR_BIND22);
            $this->setRedirect("admin.php?page=options&tab=usergroups");
        }
        if ($usergroup->usergroup_is_default){
            $default_usergroup_id = $usergroups->resetDefaultUsergroup();
        }
        if (!$usergroup->store()) {
            addMessage(_WOP_SHOP_ERROR_SAVE_DATABASE222);
            $usergroups->setDefaultUsergroup($default_usergroup_id);
            $this->setRedirect("admin.php?page=options&tab=usergroups");
        }
        do_action_ref_array( 'onAfterSaveUserGroup', array(&$usergroup) );
        $this->setRedirect("admin.php?page=options&tab=usergroups");
        }else $this->setRedirect('admin.php?page=options&tab=usergroups', _WOP_SHOP_ERROR_BIND);
    }
    
    /*function unpublish(){
        $rows = $_REQUEST['rows'];
        $model = $this->getModel('usergroups');
        $result = $model->UsergroupsActionPublish('0', $rows);
        if($result == 'error') addMessage(_WOP_SHOP_ERROR_UNPUBLISH_USERGROUP_FAVORITE,'error');
        if($result == 'success') addMessage(_WOP_SHOP_ACTION_USERGROUP_UNPUBLISHED);
        $this->setRedirect('admin.php?page=options&tab=usergroups');
    }
    function publish(){
        $rows = $_REQUEST['rows'];
        $model = $this->getModel('usergroups');
        $result = $model->UsergroupsActionPublish('1', $rows);
        if($result == 'success') addMessage(_WOP_SHOP_ACTION_USERGROUP_PUBLISHED);
        $this->setRedirect('admin.php?page=options&tab=usergroups');
    }
    function setDefault(){
        $rows = $_REQUEST['rows'];
        $model = $this->getModel('usergroups');
        $result = $model->UsergroupsActionSetDefault($rows);

        $this->setRedirect('admin.php?page=options&tab=usergroups');
    }*/
    function delete(){
        $cid = Request::getVar("rows");
        global $wpdb;

        do_action_ref_array( 'onBeforeRemoveUserGroup', array(&$cid) );
        $text = "";
        foreach ($cid as $key=>$value){
            $query = "SELECT `usergroup_name` FROM `".$wpdb->prefix."wshop_usergroups` WHERE `usergroup_id` = '".esc_sql($value)."'";
            $wpdb->get_var($query);
            if ($wpdb->delete( $wpdb->prefix.'wshop_usergroups', array( 'usergroup_id' => esc_sql($value) ))){
                $text .= sprintf(_WOP_SHOP_USERGROUP_DELETED, $usergroup_name)."<br>";
            }
        }
        do_action_ref_array( 'onAfterRemoveUserGroup', array(&$cid) );
        $this->setRedirect("admin.php?page=options&tab=usergroups", $text);
    }
}