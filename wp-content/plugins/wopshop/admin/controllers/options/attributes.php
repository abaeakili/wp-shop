<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class AttributesWshopAdminController extends WshopAdminController {
    public function __construct() {
        parent::__construct();
    }
   
    public function display() {
        $context = "admin.attributes.";
        $filter_order = getStateFromRequest($context.'filter_order', 'filter_order', 'A.attr_ordering');
        $filter_order_Dir = getStateFromRequest($context.'filter_order_dir', 'filter_order_Dir', 'asc');

    	$attributes = $this->getModel("attribut");
    	$attributesvalue = $this->getModel("attributvalue");
        $rows = $attributes->getAllAttributes(0, null, $filter_order, $filter_order_Dir);
        foreach ($rows as $key => $value){
            $rows[$key]->values = splitValuesArrayObject( $attributesvalue->getAllValues($rows[$key]->attr_id), 'name');
            $rows[$key]->count_values = count($attributesvalue->getAllValues($rows[$key]->attr_id));
        }

        if($filter_order_Dir == 'asc') $filter_order_Dir = 'desc'; else $filter_order_Dir = 'asc';
        $actions = array(
            'delete' => _WOP_SHOP_DELETE
        );
        $bulk = $attributes->getBulkActions($actions);
        
        $view = $this->getView("attributes");
        $view->setLayout("list");
        $view->assign('rows', $rows);
        $view->assign('bulk', $bulk);
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);
		do_action_ref_array('onBeforeDisplayAttributes', array(&$view));
        $view->display();
    }
    
    public function edit(){
        $attr_id = Request::getInt("attr_id");
        $attribut = Factory::getTable('attribut');
        $attribut->load($attr_id);

        if (!$attribut->independent) $attribut->independent = 0;
    
        $_lang = $this->getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
	
        $types[] = HTML::_('select.option', '1','Select','attr_type_id','attr_type');
        $types[] = HTML::_('select.option', '2','Radio','attr_type_id','attr_type');
        $type_attribut = HTML::_('select.genericlist', $types, 'attr_type','class = "inputbox" size = "1"','attr_type_id','attr_type',$attribut->attr_type);

        $dependent[] = HTML::_('select.option', '0',_WOP_SHOP_YES,'id','name');
        $dependent[] = HTML::_('select.option', '1',_WOP_SHOP_NO,'id','name');
        $dependent_attribut = HTML::_('select.radiolist', $dependent, 'independent', 'class = "inputbox" size = "1"', 'id', 'name', $attribut->independent, false, false, true);

        $all = array();
        $all[] = HTML::_('select.option', 1, _WOP_SHOP_ALL, 'id','value');
        $all[] = HTML::_('select.option', 0, _WOP_SHOP_SELECTED, 'id','value');
        if (!isset($attribut->allcats)) $attribut->allcats = 1;
        $lists['allcats'] = HTML::_('select.radiolist', $all, 'allcats','onclick="PFShowHideSelectCats()"','id','value', $attribut->allcats);
        
        $categories_selected = $attribut->getCategorys();
        $model_categories   = $this->getModel('categories');
        $categories = $model_categories->buildTreeCategory(0,1,0);
        $lists['categories'] = HTML::_('select.genericlist', $categories,'category_id[]','class="inputbox" size="10" multiple = "multiple"','category_id','name', $categories_selected);
        
        $mgroups = $this->getModel("attributesgroups");
        $groups = $mgroups->getList();
        $groups0 = array();
        $groups0[] = HTML::_('select.option', 0, "- - -", 'id', 'name');        
        $lists['group'] = HTML::_('select.genericlist', array_merge($groups0, $groups),'group','class="inputbox"','id','name', $attribut->group);
        
        //FilterOutput::objectHTMLSafe($attribut, ENT_QUOTES);
        $view=$this->getView("attributes");
        $view->setLayout("edit");
        $view->assign('attribut', $attribut);
        $view->assign('type_attribut', $type_attribut);
        $view->assign('dependent_attribut', $dependent_attribut);
        $view->assign('languages', $languages);
        $view->assign('multilang', $multilang);
        $view->assign('lists', $lists);
        do_action_ref_array('onBeforeEditAtribut', array(&$view, &$attribut));
        $view->display();
    }
    
    public function save(){
        if (!empty($_POST) && check_admin_referer('attributes_edit','name_of_nonce_field')) {
            global $wpdb;
            $attr_id = Request::getInt('attr_id');
            $attribut = Factory::getTable('attribut');
            $post = Request::get("post");
            $_lang = $this->getModel("languages");
            $languages = $_lang->getAllLanguages(1);
            foreach($languages as $lang){
                $post['description_'.$lang->language] = Request::getVar('description_'.$lang->language, '', 'post');
            }
			do_action_ref_array('onBeforeSaveAttribut', array(&$post));
            if (!$attr_id){
                $query = "SELECT MAX(attr_ordering) AS attr_ordering FROM `".$wpdb->prefix."wshop_attr`";
                $row = $wpdb->get_row($query, OBJECT);
                $post['attr_ordering'] = $row->attr_ordering + 1;
            }

            if (!$attribut->bind($post)) {
                addMessage(_WOP_SHOP_ERROR_BIND, 'error');
                $this->setRedirect("admin.php?page=options&tab=attributes");
                return 0;
            }
        
            if (isset($post['category_id'])) 
                $categorys = $post['category_id'];
            else
                $categorys = '';

            if (!is_array($categorys)) $categorys = array();

            $attribut->setCategorys($categorys);

            if (!$attribut->store()) {
                addMessage(_WOP_SHOP_ERROR_SAVE_DATABASE, 'error');
                $this->setRedirect("admin.php?page=options&tab=attributes");
                return 0;
            }
        
            if (!$attr_id){
                $query="ALTER TABLE `".$wpdb->prefix."wshop_products_attr` ADD `attr_".$attribut->attr_id."` INT( 11 ) NOT NULL";
                $wpdb->query($query);
                $attr_id = $attribut->attr_id;
            }
			do_action_ref_array('onAfterSaveAttribut', array(&$attribut));
            $this->setRedirect("admin.php?page=options&tab=attributes");
        }

        $this->setRedirect('admin.php?page=options&tab=attributes');
    }

    public function delete(){
        $cid = Request::getVar("rows");
        $config = Factory::getConfig();
        global $wpdb;
        do_action_ref_array( 'onBeforeRemoveAttribut', array(&$cid) );
        $text = '';
	
        foreach ($cid as $key => $value) {
            $value = intval($value);
            $query = "DELETE FROM `".$wpdb->prefix."wshop_attr` WHERE `attr_id` = '".esc_sql($value)."'";
            $wpdb->query($query);
            
            $query="ALTER TABLE `".$wpdb->prefix."wshop_products_attr` DROP `attr_".$value."`";
            $wpdb->query($query);
            
            $query = "select * from `".$wpdb->prefix."wshop_attr_values` where `attr_id` = '".esc_sql($value)."' ";
            $attr_values = $wpdb->get_results($query);
            foreach ($attr_values as $attr_val){
                @unlink($config->image_attributes_path."/".$attr_val->image);
            }
            $query = "delete from `".$wpdb->prefix."wshop_attr_values` where `attr_id` = '".esc_sql($value)."' ";
            $wpdb->query($query);
            
            $text = _WOP_SHOP_ATTRIBUT_DELETED;
        }
		do_action_ref_array( 'onAfterRemoveAttribut', array(&$cid) );
        $this->setRedirect("admin.php?page=options&tab=attributes", $text);
    }
	
	function order() {
		$order = Request::getVar("order");
		$cid = Request::getInt("id");
		$number = Request::getInt("number");
		global $wpdb;
		switch ($order) {
			case 'up':
				$query = "SELECT a.attr_id, a.attr_ordering
					   FROM `".$wpdb->prefix."wshop_attr` AS a
					   WHERE a.attr_ordering < '" . $number . "'
					   ORDER BY a.attr_ordering DESC
					   LIMIT 1";
				break;
			case 'down':
				$query = "SELECT a.attr_id, a.attr_ordering
					   FROM `".$wpdb->prefix."wshop_attr` AS a
					   WHERE a.attr_ordering > '" . $number . "'
					   ORDER BY a.attr_ordering ASC
					   LIMIT 1";
		}
		$row = $wpdb->get_row($query);
		$query1 = "UPDATE `".$wpdb->prefix."wshop_attr` AS a
					 SET a.attr_ordering = '" . $row->attr_ordering . "'
					 WHERE a.attr_id = '" . $cid . "'";
		$query2 = "UPDATE `".$wpdb->prefix."wshop_attr` AS a
					 SET a.attr_ordering = '" . $number . "'
					 WHERE a.attr_id = '" . $row->attr_id . "'";
		$wpdb->query($query1);
		$wpdb->query($query2);
		
		$this->setRedirect("admin.php?page=options&tab=attributes");
	}
    
    function saveorder(){
		$cid = Request::getVar("rows");
        $order = Request::getVar('order', array(), 'post', 'array' );                
        foreach($cid as $k=>$id){
            $table = Factory::getTable('attribut');
            $table->load($id);
            if ($table->attr_ordering!=$order[$k]){
                $table->attr_ordering = $order[$k];
                $table->store();
            }
        }                
        $this->setRedirect("admin.php?page=options&tab=attributes");
    }	
}