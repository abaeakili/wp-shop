<?php
class ProductFieldsWshopAdminController extends WshopAdminController {
    function __construct() {
        parent::__construct();
    }

    function display(){
        global $wpdb;
        $config = Factory::getConfig();

        $context = "admin.productfields.";
        $filter_order = getStateFromRequest($context.'filter_order', 'filter_order', "F.ordering");
        $filter_order_Dir = getStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc");
        $group = getStateFromRequest($context.'group', 'group', 0);
        $text_search = getStateFromRequest($context.'text_search', 's', '');

        $filter = array("group"=>$group, "text_search"=>$text_search);

        $_categories = $this->getModel("categories");
        $search = $_categories->search($text_search);
        $listCats = $_categories->getAllList(1);

        $_productfields = $this->getModel("productFields");
        $rows = $_productfields->getList(0, $filter_order, $filter_order_Dir, $filter);
        foreach($rows as $k=>$v){
            if ($v->allcats){
                $rows[$k]->printcat = _WOP_SHOP_ALL;
            }else{
                $catsnames = array();
                $_cats = json_decode($v->cats, 1);
                foreach($_cats as $cat_id){
                    $catsnames[] = $listCats[$cat_id];
                    $rows[$k]->printcat = implode(", ", $catsnames);
                }
            }
        }

        $_productfieldvalues = $this->getModel("productFieldValues");
        $vals = $_productfieldvalues->getAllList(2);

        foreach($rows as $k=>$v){
            if (isset($vals[$v->id])){
                if (is_array($vals[$v->id])){
                    $rows[$k]->count_option = count($vals[$v->id]);
                }else{
                    $rows[$k]->count_option = 0;
                }
            }else{
                $rows[$k]->count_option = 0;
            }    
        }
        $lists = array();
        $_productfieldgroups = $this->getModel("productFieldGroups");
        $groups = $_productfieldgroups->getList();
        $groups0 = array();
        $groups0[] = HTML::_('select.option', 0, "- - -", 'id', 'name');        
        $lists['group'] = HTML::_('select.genericlist', array_merge($groups0, $groups),'group','onchange="document.ExtraFieldsFilter.submit();"','id','name', $group);

        $types = array(_WOP_SHOP_LIST, _WOP_SHOP_TEXT);
        $actions = array(
            'delete' => _WOP_SHOP_DELETE
        );
        $bulk = $_categories->getBulkActions($actions);

        if($filter_order_Dir == 'asc') $filter_order_Dir = 'desc'; else $filter_order_Dir = 'asc';

        $view = $this->getView("productfields");
        $view->setLayout("list");
        $view->assign('lists', $lists);
        $view->assign('rows', $rows);
        $view->assign('vals', $vals);
        $view->assign('types', $types);
        $view->assign('bulk', $bulk);
        $view->assign('search', $search);
        $view->assign('text_search', $text_search);
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);
        do_action_ref_array('onBeforeDisplayProductField', array(&$view));
        $view->display();
    }
    function edit(){
        $id = Request::getInt("id");
        $productfield = Factory::getTable('productfield');
        $productfield->load($id);
        
        $_lang = $this->getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
        
        $all = array();
        $all[] = HTML::_('select.option', 1, _WOP_SHOP_ALL, 'id','value');
        $all[] = HTML::_('select.option', 0, _WOP_SHOP_SELECTED, 'id','value');
        if (!isset($productfield->allcats)) $productfield->allcats = 1;
        $lists['allcats'] = HTML::_('select.radiolist', $all, 'allcats','onclick="PFShowHideSelectCats()"','id','value', $productfield->allcats);
        
        $categories_selected = $productfield->getCategorys();
        
        $model_categories   = $this->getModel('categories');
        $categories = $model_categories->buildTreeCategory(0,1,0);

        $lists['categories'] = HTML::_('select.genericlist', $categories,'category_id[]','class="inputbox" size="10" multiple = "multiple"','category_id','name', $categories_selected);
        
        $type = array();
        $type[] = HTML::_('select.option', 0, _WOP_SHOP_LIST, 'id', 'value');
        $type[] = HTML::_('select.option', -1, _WOP_SHOP_MULTI_LIST, 'id', 'value');
        $type[] = HTML::_('select.option', 1, _WOP_SHOP_TEXT, 'id', 'value');
        if (!isset($productfield->type)) $productfield->type = 0;
        if ($productfield->multilist) $productfield->type = -1;
        $lists['type'] = HTML::_('select.radiolist', $type, 'type','','id','value', $productfield->type);

        $_productfieldgroups = $this->getModel("productFieldGroups");
        $groups = $_productfieldgroups->getList();
        $groups0 = array();
        $groups0[] = HTML::_('select.option', 0, "- - -", 'id', 'name');        
        $lists['group'] = HTML::_('select.genericlist', array_merge($groups0, $groups),'group','class="inputbox"','id','name', $productfield->group);

        $view = $this->getView("productfields", 'html');
        $view->setLayout("edit");
        //FilterOutput::objectHTMLSafe($productfield, ENT_QUOTES);
        $view->assign('row', $productfield);
        $view->assign('lists', $lists);
        $view->assign('languages', $languages);
        $view->assign('multilang', $multilang);
        do_action_ref_array('onBeforeEditProductFields', array(&$view));
        $view->display();
        
    }
    function save(){
        if ( !empty($_POST) && check_admin_referer('productfields_edit','name_of_nonce_field') )
        {
            $config = Factory::getConfig();
            global $wpdb;

            $id = Request::getInt("id");
            $productfield = Factory::getTable('productfield');
            $post = Request::get("post");
            if ($post['type']==-1){
                $post['type'] = 0;
                $post['multilist'] = 1;
            }else{
                $post['multilist'] = 0;
            }
            do_action_ref_array( 'onBeforeSaveProductField', array(&$post) );
            if (!$productfield->bind($post)) {
                addMessage(_WOPSHOP_ERROR_BIND);
                $this->setRedirect("admin.php?page=options&tab=productfields");
                return 0;
            }

            $categorys = $post['category_id'];
            if (!is_array($categorys)) $categorys = array();

            $productfield->setCategorys($categorys);

            if (!$id){
                $productfield->ordering = null;
                $productfield->ordering = $productfield->getNextOrder();            
            }

            if (!$productfield->store()) {
                addMessage(_WOP_SHOP_ERROR_SAVE_DATABASE);
                $this->setRedirect("admin.php?page=options&tab=productfields");
                return 0; 
            }

            if (!$id){
                $query = "ALTER TABLE `".$wpdb->prefix."wshop_products` ADD `extra_field_".$productfield->id."` ".$config->new_extra_field_type." NOT NULL";
                $wpdb->get_results($query);
            }
			do_action_ref_array( 'onAfterSaveProductField', array(&$productfield) );
            $this->setRedirect('admin.php?page=options&tab=productfields');
        }
    }
    function delete(){
        $cid = Request::getVar("rows");
        global $wpdb;
        $text = array();
        do_action_ref_array( 'onBeforeRemoveProductField', array(&$cid) );
        foreach ($cid as $key => $value) {
            if ($wpdb->delete( $wpdb->prefix."wshop_products_extra_fields", array( 'id' => esc_sql($value) ))){
                $text[] = _WOP_SHOP_ITEM_DELETED;
            }
            $wpdb->delete( $wpdb->prefix."wshop_products_extra_field_values", array( 'field_id' => esc_sql($value) ) );
            $query = "ALTER TABLE `".$wpdb->prefix."wshop_products` DROP `extra_field_".$value."`";
            $wpdb->get_results($query, output_type);
        }
        do_action_ref_array( 'onAfterRemoveProductField', array(&$cid) );
        $this->setRedirect("admin.php?page=options&tab=productfields", implode("</li><li>",$text));
        
    }
	
    function order(){
		$order = Request::getVar("order");
		$cid = Request::getInt("id");
		$number = Request::getInt("number");
		global $wpdb;
		switch ($order) {
			case 'up':
				$query = "SELECT a.id, a.ordering
					   FROM `".$wpdb->prefix."wshop_products_extra_fields` AS a
					   WHERE a.ordering < '" . $number . "'
					   ORDER BY a.ordering DESC
					   LIMIT 1";
				break;
			case 'down':
				$query = "SELECT a.id, a.ordering
					   FROM `".$wpdb->prefix."wshop_products_extra_fields` AS a
					   WHERE a.ordering > '" . $number . "'
					   ORDER BY a.ordering ASC
					   LIMIT 1";
		}
		$row = $wpdb->get_row($query);
		$query1 = "UPDATE `".$wpdb->prefix."wshop_products_extra_fields` AS a
					 SET a.ordering = '" . $row->ordering . "'
					 WHERE a.id = '" . $cid . "'";
		$query2 = "UPDATE `".$wpdb->prefix."wshop_products_extra_fields` AS a
					 SET a.ordering = '" . $number . "'
					 WHERE a.id = '" . $row->id . "'";
		$wpdb->query($query1);
		$wpdb->query($query2);
		
		$this->setRedirect("admin.php?page=options&tab=productfields");		
    }
    
    function saveorder(){
		$cid = Request::getVar("rows");
        $order = Request::getVar('order', array(), 'post', 'array' );                
        foreach($cid as $k=>$id){
            $table = Factory::getTable('ProductField');
            $table->load($id);
            if ($table->ordering!=$order[$k]){
                $table->ordering = $order[$k];
                $table->store();
            }
        }                
        $this->setRedirect("admin.php?page=options&tab=productfields");		
    }	
}