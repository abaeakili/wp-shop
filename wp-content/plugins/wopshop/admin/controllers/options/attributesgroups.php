<?php
class AttributesGroupsWshopAdminController extends WshopAdminController {
    function __construct() {
        parent::__construct();
    }
   
    function display($cachable = false, $urlparams = false){        
        $config = Factory::getConfig();
        
        $model = $this->getModel("attributesgroups");
        $rows = $model->getList();
        
        $view = $this->getView("attributesgroups");
        $view->setLayout("list");
        $view->assign('rows', $rows);
		do_action_ref_array('onBeforeDisplayAttributesGroups', array(&$view));
        $view->display();
    }
    
    function edit(){
        global $wpdb;
        $id = (int)$_REQUEST['id'];
        
        $query = "SELECT * FROM `".$wpdb->prefix."wshop_attr_groups` WHERE `id` = '".esc_sql($id)."'";
        $row = $wpdb->get_row($query);
        $_lang = $this->getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
        $view = $this->getView("attributesgroups");
        $view->setLayout("edit");
        $view->assign('row', $row);
        $view->assign('languages', $languages);
        $view->assign('multilang', $multilang);
		do_action_ref_array('onBeforeEditAttributesGroups', array(&$view));
        $view->display();
    }

    function save(){
        $id = (int)$_REQUEST['id'];
        
        global $wpdb;
        $query = "SELECT MAX(ordering) FROM `".$wpdb->prefix."wshop_attr_groups`";
        $ordering = $wpdb->get_var($query);

        $post = $_POST;
		do_action_ref_array('onBeforeSaveAttributesGroups', array(&$post));
        unset($post['id']); unset($post['_wp_http_referer']); unset($post['name_of_nonce_field']); unset($post['submit']);

        /*if (!$id){
            $row->ordering = null;
            $row->ordering = $row->getNextOrder();
        }*/ 
        if($id){
            $wpdb->update( $wpdb->prefix.'wshop_attr_groups', $post, array( 'id' => esc_sql($id) ));
        }else{
            $post['ordering'] = $ordering + 1;
            $wpdb->insert( $wpdb->prefix.'wshop_attr_groups', $post);
        }
        do_action_ref_array('onAfterSaveAttributesGroups', array(&$row) );
        $this->setRedirect("admin.php?page=options&tab=attributesgroups");
    }

    function delete(){
        $cid = $_REQUEST['rows'];

        global $wpdb;
        
        $text = array();
        foreach ($cid as $key => $value) {            
            $r = $wpdb->delete($wpdb->prefix."wshop_attr_groups", array( 'id' => $value ) );
            if ($r){
                $text[] = _WOP_SHOP_ITEM_DELETED;
            }    
        }
		do_action_ref_array('onAfterRemoveAttributesGroups', array(&$cid));
        $this->setRedirect("admin.php?page=options&tab=attributesgroups", implode("</li><li>", $text));
    }
	
	function order() {
		$order = Request::getVar("order");
		$cid = Request::getInt("id");
		$number = Request::getInt("number");
		global $wpdb;
		switch ($order) {
			case 'up':
				$query = "SELECT a.id, a.ordering
					   FROM `".$wpdb->prefix."wshop_attr_groups` AS a
					   WHERE a.ordering < '" . $number . "'
					   ORDER BY a.ordering DESC
					   LIMIT 1";
				break;
			case 'down':
				$query = "SELECT a.id, a.ordering
					   FROM `".$wpdb->prefix."wshop_attr_groups` AS a
					   WHERE a.ordering > '" . $number . "'
					   ORDER BY a.ordering ASC
					   LIMIT 1";
		}
		$row = $wpdb->get_row($query);
		$query1 = "UPDATE `".$wpdb->prefix."wshop_attr_groups` AS a
					 SET a.ordering = '" . $row->ordering . "'
					 WHERE a.id = '" . $cid . "'";
		$query2 = "UPDATE `".$wpdb->prefix."wshop_attr_groups` AS a
					 SET a.ordering = '" . $number . "'
					 WHERE a.id = '" . $row->id . "'";
		$wpdb->query($query1);
		$wpdb->query($query2);
		
		$this->setRedirect("admin.php?page=options&tab=attributesgroups");
	}
    
    function saveorder(){
		$cid = Request::getVar("rows");
        $order = Request::getVar('order', array(), 'post', 'array' );                
        foreach($cid as $k=>$id){
            $table = Factory::getTable('attributesgroup');
            $table->load($id);
            if ($table->ordering!=$order[$k]){
                $table->ordering = $order[$k];
                $table->store();
            }
        }                
        $this->setRedirect("admin.php?page=options&tab=attributesgroups");
    }	
    
   
}