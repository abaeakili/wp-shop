<?php
class StatictextWshopAdminController extends WshopAdminController {
    function __construct() {
        parent::__construct();
    }
   
    function display() {
        $orderby = getStateFromRequest('statictext_orderby', 'orderby', 'id');
        $order = getStateFromRequest('statictext_order', 'order', 'asc');

        $config = Factory::getConfig();

        $model = $this->getModel('statictext');
        $actions = array();

        $statictext = $model->getAllStatictext($s, $publish, $orderby, $order, $start, $per_page);

        $view = $this->getView('statictext');
        $view->setLayout('list');
        $view->assign('statictext',$statictext);
        if($order == 'asc') $order = 'desc'; else $order = 'asc';
        $view->assign('orderby',$orderby);
        $view->assign('order',$order);
        $view->assign('config',$config);
		do_action_ref_array('onBeforeDisplayStatisticText', array(&$view));
        $view->display();
    }
    function edit(){
        //$id = $_REQUEST['row'];
        /*$id = Request::getInt("row");
        
        $model = $this->getModel('statictext');
        $statictext = $model->getDataStatictext($id);
        
        $listLanguages = $model->getListLanguages();
*/
        $config = Factory::getConfig();
        $id = Request::getInt("row");
        
        $statictext = Factory::getTable("statictext");
        $statictext->load($id);
        $_lang = $this->getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;

        $nofilter = array();
        //FilterOutput::objectHTMLSafe( $statictext, ENT_QUOTES, $nofilter);
        
        $view = $this->getView('statictext');
        $view->setLayout('edit');
        $view->assign('statictext',$statictext);
        $view->assign('languages', $languages);
        $view->assign('multilang', $multilang);
		do_action_ref_array('onBeforeDisplayStatisticTextEdit', array(&$view));
        $view->display();
    }
    function save(){
        if ( !empty($_POST) && check_admin_referer('statictext_edit','name_of_nonce_field') )
        {
            /*$statictext_id = $_POST['statictext_id'];
            $post = $_POST['statictext'];
                    
            $model = $this->getModel('statictext');
            
            if($statictext_id > 0){
                $model->StatictextUpdate($post, $statictext_id);
                addMessage(_WOP_SHOP_ACTION_STATICTEXT_UPDATE);
            }*/
            
            $config = Factory::getConfig();

            $id = Request::getInt("statictext_id");
            $post = Request::get("post");
			do_action_ref_array( 'onBeforeSaveConfigStaticPage', array(&$post) );
            $_lang = $this->getModel("languages");
            $languages = $_lang->getAllLanguages(1);

            foreach($languages as $lang){
                $post['text_'.$lang->language] = Request::getVar('text'.$lang->id,'','post',"string", 2);
            }
 
            $statictext = Factory::getTable("statictext");
            $statictext->load($id);
            $statictext->bind($post);        
            $result = $statictext->store($post);
			do_action_ref_array( 'onAfterSaveConfigStaticPage', array(&$statictext) );
            if($result) 
                $this->setRedirect('admin.php?page=configuration&tab=statictext', _WOP_SHOP_CONFIG_SUCCESS);
            else 
                $this->setRedirect('admin.php?page=configuration&tab=statictext', _WOP_SHOP_CONFIG_ERROR);
        }
    }

    function delete(){
        $id = Request::getInt("row");
        $statictext = Factory::getTable("statictext");
        $statictext->load($id);
        $statictext->delete();

        $this->setRedirect('admin.php?page=configuration&tab=statictext');
    }
}