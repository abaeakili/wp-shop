<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class ProductListSelectableWshopAdminController extends WshopAdminController {
    function __construct() {
        parent::__construct();
    }
    function display(){
        //checkAccessController("productlistselectable");
        //$app = Factory::getApplication();
        global $wpdb;
        $config = Factory::getConfig();
        $prodMdl = $this->getModel('Products');

        $context = "admin.product.";
        //$limit = getStateFromRequest($context.'limit', 'limit', 'list_limit');
        $limit = getStateFromRequest( $context.'per_page', 'per_page', 20);
        $limitstart = getStateFromRequest($context.'limitstart', 'limitstart', 0);

        $paged = getStateFromRequest($context.'paged', 'paged', 1);
        $per_page = getStateFromRequest('categories_per_page', 'per_page', 20);

        if (isset($_GET['category_id']) && $_GET['category_id'] === "0"){
            $app->setUserState($context.'category_id', 0);
            $app->setUserState($context.'manufacturer_id', 0);
            $app->setUserState($context.'label_id', 0);
            $app->setUserState($context.'publish', 0);
            $app->setUserState($context.'text_search', '');
        }

        $category_id = getStateFromRequest($context.'category_id', 'category_id', 0);
        $manufacturer_id = getStateFromRequest($context.'manufacturer_id', 'manufacturer_id', 0);
        $label_id = getStateFromRequest($context.'label_id', 'label_id', 0);
        $publish = getStateFromRequest($context.'publish', 'publish', 0);
        $text_search = getStateFromRequest($context.'text_search', 'text_search', '');
        $eName = Request::getVar('e_name');
        $jsfname = Request::getVar('jsfname');
        $eName = preg_replace('#[^A-Z0-9\-\_\[\]]#i', '', $eName);        
        if (!$jsfname) $jsfname = 'selectProductBehaviour';

        $filter = array("category_id" => $category_id,"manufacturer_id" => $manufacturer_id,"label_id" => $label_id,"publish" => $publish,"text_search" => $text_search);
        $total = $prodMdl->getCountAllProducts($filter);
        //$pagination = new Pagination($total, $limitstart, $limit);
        if(($paged-1) > ($total/$limit) ) $paged = 1;
        $limitstart = ($paged-1)*$limit;
        $pagination = $prodMdl->getPagination($total, $per_page);
        $search = $prodMdl->search($text_search);
        
        $rows = $prodMdl->getAllProducts($filter, $limitstart, $limit);

        $parentTop = new stdClass();
        $parentTop->category_id = 0;
        $parentTop->name = " - - - ";
        $categories_select = buildTreeCategory(0,1,0);

        array_unshift($categories_select, $parentTop);  

        $lists['treecategories'] = HTML::_('select.genericlist', $categories_select, 'category_id', 'style="width: 150px;" onchange="document.adminForm.submit();"', 'category_id', 'name', $category_id);

        $manuf1 = array();
        $manuf1[0] = new stdClass();
        $manuf1[0]->manufacturer_id = '0';
        $manuf1[0]->name = " - - - ";
        $manufs = $this->getModel('Manufacturers')->getList();

        $manufs = array_merge($manuf1, $manufs);
        $lists['manufacturers'] = HTML::_('select.genericlist', $manufs, 'manufacturer_id', 'style="style="width: 150px;" onchange="document.adminForm.submit();"', 'manufacturer_id', 'name', $manufacturer_id);

        if ($config->admin_show_product_labels) {
            $alllabels = $this->getModel('ProductLabels')->getList();
            $first = array();
            $first[] = HTML::_('select.option', '0'," - - - ", 'id','name');        
            $lists['labels'] = HTML::_('select.genericlist', array_merge($first, $alllabels), 'label_id', 'style="width: 80px;" onchange="document.adminForm.submit();"','id','name', $label_id);
        }
        $f_option = array();
        $f_option[] = HTML::_('select.option', 0, " - - - ", 'id', 'name');
        $f_option[] = HTML::_('select.option', 1, _WOP_SHOP_PUBLISH, 'id', 'name');
        $f_option[] = HTML::_('select.option', 2, _WOP_SHOP_UNPUBLISH, 'id', 'name');
        $lists['publish'] = HTML::_('select.genericlist', $f_option, 'publish', 'style="width: 100px;" onchange="document.adminForm.submit();"', 'id', 'name', $publish);

        $view = $this->getView('products');
        $view->setLayout("selectable");
        $view->assign('rows', $rows);
        $view->assign('lists', $lists);
        $view->assign('category_id', $category_id);
        $view->assign('manufacturer_id', $manufacturer_id);
        $view->assign('pagination', $pagination);
        $view->assign('text_search', $text_search);
        $view->assign('config', $config);        
        $view->assign('eName', $eName);		
        $view->assign('jsfname', $jsfname);
        do_action_ref_array('onBeforeDisplayProductListSelectable', array(&$view));
        $view->display();
    }

}
?>		