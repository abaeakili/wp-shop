<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class CategoriesWshopAdminController extends WshopAdminController {
    public function __construct() {
        parent::__construct();
    }
   
    public function display() {
        $mainframe = Factory::getApplication();

        $_categories = $this->getModel("categories");
        $context = "admin.category.";

        $filter_order = $mainframe->getUserStateFromRequest($context.'filter_order', 'filter_order', "ordering");
        $filter_order_Dir = $mainframe->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc");
        $text_search = $mainframe->getUserStateFromRequest($context.'text_search', 's', '');
        $per_page = $mainframe->getUserStateFromRequest($context.'per_page', 'per_page', 20);
        $paged = $mainframe->getUserStateFromRequest($context.'paged', 'paged', '1');

        $filter = array("text_search" => $text_search);

        $categories = $_categories->getTreeAllCategories($filter, $filter_order, $filter_order_Dir);
        $total = count($categories);

        if(($paged-1) > ($total/$per_page) ) $paged = 1;
        $limitstart = ($paged-1)*$per_page;

        $pagination = $_categories->getPagination($total, $per_page);
        $search = $_categories->search($text_search);

        $countproducts = $_categories->getAllCatCountProducts();

        $actions = array(
            'delete' => _WOP_SHOP_DELETE,
            'publish' => _WOP_SHOP_ACTION_PUBLISH,
            'unpublish' => _WOP_SHOP_ACTION_UNPUBLISH,
        );
        $bulk = $_categories->getBulkActions($actions);

        $categories = array_slice($categories, $limitstart, $per_page);
        if($filter_order_Dir == 'asc') $filter_order_Dir = 'desc'; else $filter_order_Dir = 'asc';

        $view = $this->getView("categories");
        $view->setLayout("list");
        $view->assign('categories', $categories);
        $view->assign('countproducts', $countproducts);
        $view->assign('pagination', $pagination);
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);
        $view->assign('search', $search);
        $view->assign('bulk', $bulk);
        do_action_ref_array('onBeforeDisplayListCategoryView', array(&$view));
        $view->display();
    }

    function publish(){
        $this->publishCategory(1);
        $this->setRedirect('admin.php?page=categories');
    }
    
    function unpublish(){
        $this->publishCategory(0);
        $this->setRedirect('admin.php?page=categories');
    }
    function publishCategory($flag) {
        global $wpdb;
        $cid = Request::getVar("rows");
        do_action_ref_array( 'onBeforePublishCategory', array(&$cid, &$flag) );
        foreach ($cid as $key => $value) {
            $wpdb->update( $wpdb->prefix.'wshop_categories', array( 'category_publish' => esc_sql($flag) ), array( 'category_id' => esc_sql($value) ));
        }
        do_action_ref_array( 'onAfterPublishCategory', array(&$cid, &$flag) );
    }
    function delete(){
        $config = Factory::getConfig();
        global $wpdb;

        $text = array();
        $cid = Request::getVar("rows");
        $_categories = $this->getModel("categories");

        do_action_ref_array('onBeforeRemoveCategory', array(&$cid));
        $allCatCountProducts = $_categories->getAllCatCountProducts();

        foreach($cid as $key=>$value){
            $category = Factory::getTable("category");
            $category->load($value);
            $name_category = $category->getName();
            $childs = $category->getChildCategories();
            if ($allCatCountProducts[$value] || count($childs)){
                addMessage(sprintf(_WOP_SHOP_CATEGORY_NO_DELETED, $name_category), 'error');
                continue;
            }
            $_categories->deleteCategory($value);
            @unlink($config->image_category_path.'/'.$category->category_image);
            $text[]= sprintf(_WOP_SHOP_CATEGORY_DELETED, $name_category);
        }
        do_action_ref_array( 'onAfterRemoveCategory', array(&$cid) );
        $this->setRedirect('admin.php?page=categories', implode('</p><p>',$text));
    }    
    
    public function edit(){
        $config = Factory::getConfig();
        $cid = Request::getInt("category_id");

        $category = Factory::getTable("category");
        $category->load($cid);

        $_lang = $this->getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;

        $nofilter = array();
        //FilterOutput::objectHTMLSafe( $category, ENT_QUOTES, $nofilter);

        if ($cid) {
            $parentid = $category->category_parent_id;
            $rows = $this->_getAllCategoriesLevel($category->category_parent_id, $category->ordering);
        } else {
            $category->category_publish = 1;
            $parentid = Request::getInt("catid");
            $rows = $this->_getAllCategoriesLevel($parentid);
        }

        $lists['templates'] = getTemplates('category', $category->category_template);
        $lists['onelevel'] = $rows;
        $parentTop = new stdClass();
        $parentTop->category_id = 0;
        $parentTop->name = _WOP_SHOP_TOP_LEVEL;
        $categories = buildTreeCategory(0,1,0);
        array_unshift($categories, $parentTop);

        $lists['treecategories'] = HTML::_('select.genericlist', $categories,'category_parent_id','class="inputbox" size="1" onchange = "changeCategory()"','category_id','name', $parentid);
        $lists['parentid'] = $parentid;

        //$accessgroups = getAccessGroups();
        //$lists['access'] = JHTML::_('select.genericlist', $accessgroups, 'access','class = "inputbox" size = "1"','id','title', $category->access);

        $view=$this->getView("categories", 'html');
        $view->setLayout("edit");
        $view->assign('category', $category);
        $view->assign('lists', $lists);
        $view->assign('languages', $languages);
        $view->assign('multilang', $multilang);
        do_action_ref_array('onBeforeEditCategories', array(&$view));
        $view->display();
    }

    public function save(){
        $post = Request::get('post');
        
        if (is_array($post) && count($post) && check_admin_referer('category_edit', 'name_of_nonce_field')){
            $config = Factory::getConfig();
            require_once ($config->path.'lib/image.lib.php');
            require_once ($config->path.'lib/uploadfile.class.php');

            $_alias = $this->getModel("alias"); 
            global $wpdb;

            $_categories = $this->getModel("categories");
            $category = Factory::getTable("category");
            if (!$post["category_id"]){
                $post['category_add_date'] = getJsDate();
            }
            if (!isset($post['category_publish'])){
                $post['category_publish'] = 0;
            }

            $_lang = $this->getModel("languages");
            $languages = $_lang->getAllLanguages(1);
            if ($post['category_parent_id']==$post['category_id']) $post['category_parent_id'] = 0;
            do_action_ref_array('onBeforeSaveCategory', array(&$post));
            foreach($languages as $lang){
                $post['name_'.$lang->language] = trim($post['name_'.$lang->language]);
                if ($config->create_alias_product_category_auto && $post['alias_'.$lang->language]=="") $post['alias_'.$lang->language] = $post['name_'.$lang->language];
                $post['alias_'.$lang->language] = sanitize_title_with_dashes($post['alias_'.$lang->language]);
                if ($post['alias_'.$lang->language]!="" && !$_alias->checkExistAlias1Group($post['alias_'.$lang->language], $lang->language, $post['category_id'], 0)){
                    $post['alias_'.$lang->language] = "";
                    addMessage(_WOP_SHOP_ERROR_ALIAS_ALREADY_EXIST, 'error');
                }
                $post['description_'.$lang->language] = Request::getVar('description'.$lang->id, '', 'post',"string", 2);
                $post['short_description_'.$lang->language] = Request::getVar('short_description_'.$lang->language, '', 'post', "string", 2);
            }
            if (!$category->bind($post)) {
                addMessage(_WOP_SHOP_ERROR_BIND, 'error');
                $this->setRedirect("admin.php?page=categories");
                return 0;
            }
            $edit = $category->category_id;

            $upload_image = $_categories->uploadImage($post);
            if ($upload_image!=''){
                $category->category_image = $upload_image;
            }

            $this->_reorderCategory($category);

            if (!$category->store()) {
                addMessage(_WOP_SHOP_ERROR_SAVE_DATABASE, 'error');
                $this->setRedirect("admin.php?page=categories");
                return 0;
            }
            do_action_ref_array( 'onAfterSaveCategory', array(&$category) );
            $success = ($edit)?(_WOP_SHOP_CATEGORY_SUCC_UPDATE):(_WOP_SHOP_CATEGORY_SUCC_ADDED);
            $this->setRedirect('admin.php?page=categories', $success);
        } else {
            addMessage(_WOP_SHOP_ERROR_BIND, 'error');
        }
        
        $this->setRedirect('admin.php?page=categories');
    }

    function _getAllCategoriesLevel($parentId, $currentOrdering = 0){
        $config = Factory::getConfig();
        $_categories = $this->getModel("categories");
        $rows = $_categories->getSubCategories($parentId, "ordering");
        $first[] = HTML::_('select.option', '0',_JSHOP_ORDERING_FIRST,'ordering','name');
        $rows = array_merge($first,$rows);
        $currentOrdering = (!$currentOrdering) ? ($rows[count($rows) - 1]->ordering) : ($currentOrdering);
        return (HTML::_('select.genericlist', $rows,'ordering','class="inputbox" size="1"','ordering','name',$currentOrdering));		
    }

    function delete_foto(){
        $catid = Request::getInt("catid");
        $config = Factory::getConfig();
        $category = Factory::getTable("category");
        $category->load($catid);
        @unlink($config->image_category_path.'/'.$category->category_image);
        $category->category_image = "";
        $category->store();
        die();
    }
    
    public function sorting_cats_html(){
        $catid = Request::getInt("catid");
        echo $this->_getAllCategoriesLevel($catid);
        die();
    }
    function _reorderCategory(&$category) {
        global $wpdb;
        $query = "UPDATE `".$wpdb->prefix."wshop_categories` SET `ordering` = ordering + 1 WHERE `category_parent_id` = '" . $category->category_parent_id . "' AND `ordering` > '" . $category->ordering . "'";
        $wpdb->get_results($query);
        $category->ordering++;
    }
	
    function order(){
        $id = Request::getInt("id");
        $move = Request::getInt("move");
        $table = Factory::getTable('category');
        $table->load($id);
        $table->move($move, 'category_parent_id="'.$table->category_parent_id.'"');
        $this->setRedirect("admin.php?page=categories");
    }
	
	function move($delta, $where = ''){
		
	}
    
    function saveorder(){
		$cid = Request::getVar("rows");
        $order = Request::getVar( 'order', array(), 'post', 'array' );
        $category_parent_id = Request::getInt("category_parent_id");
        
        foreach ($cid as $k=>$id){
            $table = Factory::getTable('category');
            $table->load($id);
            if ($table->ordering!=$order[$k]){
                $table->ordering = $order[$k];
                $table->store();
            }        
        }
        
        $table = Factory::getTable('category');
        $table->ordering = null;
        $table->reorder('category_parent_id="'.$category_parent_id.'"');
                
        $this->setRedirect("admin.php?page=categories");
    }	
}