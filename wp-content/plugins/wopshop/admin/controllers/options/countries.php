<?php
class CountriesWshopAdminController extends WshopAdminController {
    function __construct() {
        parent::__construct();
    }
    function display() {
        $context = "admin.countries.";
        $publish = getStateFromRequest( $context.'publish', '');
        $filter_order = getStateFromRequest($context.'filter_order', 'filter_order', 'ordering');
        $filter_order_Dir = getStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', 'asc');

        $paged = getStateFromRequest($context.'paged', 'paged', 1);
        $limit = getStateFromRequest($context.'per_page', 'per_page', 20);
        $model = $this->getModel('countries');
        $total = $model->getCountAllCountries();

        $actions = array(
            'delete' => _WOP_SHOP_DELETE,
            'publish' => _WOP_SHOP_ACTION_PUBLISH,
            'unpublish' => _WOP_SHOP_ACTION_UNPUBLISH,
        );

        $bulk = $model->getBulkActions($actions);
        if ($publish == 0) {
            $total = $model->getCountAllCountries();
        } else {
            $total = $model->getCountPublishCountries($publish % 2);
        }

        if(($paged-1) > ($total/$limit) ) $paged = 1;
        $limitstart = ($paged-1)*$limit;
        $pagination = $model->getPagination($total, $limit);
        //$search = $model->search($s);
        $rows = $model->getAllCountries($publish, $limitstart, $limit, 0, $filter_order, $filter_order_Dir);

        $f_option = array();
        $f_option[] = HTML::_('select.option', 0, _WOP_SHOP_ALL, 'id', 'name');
        $f_option[] = HTML::_('select.option', 1, _WOP_SHOP_PUBLISH, 'id', 'name');
        $f_option[] = HTML::_('select.option', 2, _WOP_SHOP_UNPUBLISH, 'id', 'name');

        $filter = HTML::_('select.genericlist', $f_option, 'publish', 'onchange="document.adminForm.submit();"', 'id', 'name', $publish);

        if($filter_order_Dir == 'asc') $filter_order_Dir = 'desc'; else $filter_order_Dir = 'asc';
        $view=$this->getView("countries");
        $view->setLayout("list");		
        $view->assign('rows', $rows); 
        $view->assign('pagination', $pagination);       
        $view->assign('filter', $filter);
        $view->assign('bulk', $bulk);
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);
		do_action_ref_array('onBeforeDisplayCountries', array(&$view));
        $view->display();
    }
    function edit(){
        $country_id = Request::getInt("row");
        $countries = $this->getModel("countries");
        $country = Factory::getTable('country');
        $country->load($country_id);

        $first[] = HTML::_('select.option', '0',_WOP_SHOP_ORDERING_FIRST,'ordering','name');
        $rows = array_merge($first, $countries->getAllCountries(0));

        $lists['order_countries'] = HTML::_('select.genericlist', $rows,'ordering','class="inputbox" size="1"','ordering','name', $country->ordering);

        $_lang = $this->getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;

        $edit = ($country_id)?($edit = 1):($edit = 0);

        //FilterOutput::objectHTMLSafe( $country, ENT_QUOTES);

        $view=$this->getView("countries");
        $view->setLayout("edit");
        $view->assign('country', $country);
        $view->assign('lists', $lists);
        $view->assign('edit', $edit);
        $view->assign('languages', $languages);
        $view->assign('multilang', $multilang);
		do_action_ref_array('onBeforeEditCountries', array(&$view));
        $view->display();
    }
    function save(){
        if ( !empty($_POST) && check_admin_referer('coutry_edit','name_of_nonce_field') )
        {
            $country_id = Request::getInt("country_id");
            $post = Request::get('post');
			do_action_ref_array( 'onBeforeSaveCountry', array(&$post) );
            $country = Factory::getTable('country');

            if (!$country->bind($post)){
                addMessage(_WOP_SHOP_ERROR_BIND, 'error');
                $this->setRedirect("admin.php?page=options&tab=countries");
                return 0;
            }
            if (!$country->country_publish){
                $country->country_publish = 0;
            }
            $this->_reorderCountry($country);
            if (!$country->store()) {
                addMessage(_WOP_SHOP_ERROR_SAVE_DATABASE, 'error');
                $this->setRedirect("admin.php?page=options&tab=countries");
                return 0;
            }
        }
        else addMessage(_WOP_SHOP_ERROR_BIND, 'error');
		do_action_ref_array( 'onAfterSaveCountry', array(&$country) );
        $this->setRedirect('admin.php?page=options&tab=countries',_WOP_SHOP_SAVE);
    }
    function publish(){
        $this->publishCountry(1);
    }
    function unpublish(){
        $this->publishCountry(0);
    }
    function publishCountry($flag) {
        $cid = Request::getVar("rows");
        global $wpdb;
        foreach ($cid as $key => $value) {
            $wpdb->update( 
                $wpdb->prefix.'wshop_countries',
                array('country_publish' => esc_sql($flag)),
                array('country_id' => $value)
            );
        }
		do_action_ref_array( 'onAfterPublishCountry', array(&$cid, &$flag) );
        $this->setRedirect('admin.php?page=options&tab=countries', _WOP_SHOP_SAVE);
    }
    function _reorderCountry(&$country) {
        global $wpdb;
	$query = "UPDATE `".$wpdb->prefix."wshop_countries` SET `ordering` = ordering + 1 WHERE `ordering` > '".$country->ordering."'";
	$wpdb->query($query);
    }
    function delete(){
        global $wpdb;
        $query = '';
        $text = '';
        $cid = Request::getVar("rows");
		do_action_ref_array( 'onBeforeRemoveCountry', array(&$cid) );
        if(!is_array($cid)){
            $this->setRedirect("admin.php?page=options&tab=countries");
            return;
        }
        foreach ($cid as $key => $value) {
            if ($wpdb->delete( $wpdb->prefix.'wshop_countries', array('country_id' => $value)))
                $text .= _WOP_SHOP_COUNTRY_DELETED."<br>";
            else
                $text .= _WOP_SHOP_COUNTRY_ERROR_DELETED."<br>";	
        }
		do_action_ref_array( 'onAfterRemoveCountry', array(&$cid) );
        $this->setRedirect("admin.php?page=options&tab=countries", $text);
    }
	
    function order(){
		$order = Request::getVar("order");
		$cid = Request::getInt("id");
		$number = Request::getInt("number");
		global $wpdb;
		switch ($order) {
			case 'up':
				$query = "SELECT a.country_id, a.ordering
					   FROM `".$wpdb->prefix."wshop_countries` AS a
					   WHERE a.ordering < '" . $number . "'
					   ORDER BY a.ordering DESC
					   LIMIT 1";
				break;
			case 'down':
				$query = "SELECT a.country_id, a.ordering
					   FROM `".$wpdb->prefix."wshop_countries` AS a
					   WHERE a.ordering > '" . $number . "'
					   ORDER BY a.ordering ASC
					   LIMIT 1";
		}
		$row = $wpdb->get_row($query);
		$query1 = "UPDATE `".$wpdb->prefix."wshop_countries` AS a
					 SET a.ordering = '" . $row->ordering . "'
					 WHERE a.country_id = '" . $cid . "'";
		$query2 = "UPDATE `".$wpdb->prefix."wshop_countries` AS a
					 SET a.ordering = '" . $number . "'
					 WHERE a.country_id = '" . $row->country_id . "'";
		$wpdb->query($query1);
		$wpdb->query($query2);
		
		$this->setRedirect("admin.php?page=options&tab=countries");		
    }
    
    function saveorder(){
		$cid = Request::getVar("rows");
        $order = Request::getVar('order', array(), 'post', 'array' );                
        foreach($cid as $k=>$id){
            $table = Factory::getTable('country');
            $table->load($id);
            if ($table->ordering!=$order[$k]){
                $table->ordering = $order[$k];
                $table->store();
            }
        }                
        $this->setRedirect("admin.php?page=options&tab=countries");		
    }	
}