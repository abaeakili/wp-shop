<?php
class ExtTaxesWshopAdminController extends WshopAdminController{
    function __construct() {
        parent::__construct();
    }

    function display(){
        $сonfig = Factory::getConfig();
        $back_tax_id = Request::getInt("back_tax_id");
        global $wpdb;
        $mainframe = Factory::getApplication();
        $context = "wopshop.list.admin.exttaxes";
        $filter_order = $mainframe->getUserStateFromRequest($context.'filter_order', 'filter_order', "ET.id", 'cmd');
        $filter_order_Dir = $mainframe->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        
        $taxes = $this->getModel("taxes");
        $rows = $taxes->getExtTaxes($back_tax_id, $filter_order, $filter_order_Dir);

        $countries = $this->getModel("countries");
        $list = $countries->getAllCountries(0);
        $countries_name = array();
        foreach($list as $v){
            $countries_name[$v->country_id] = $v->name;
        }

        foreach($rows as $k=>$v){
            if($v->zones != '')
            $list = json_decode($v->zones, 1);

            if(is_array($list))
            foreach($list as $k2=>$v2){
                $list[$k2] = $countries_name[$v2];
            }
            if (count($list) > 10){
                $tmp = array_slice($list, 0, 10);
                $rows[$k]->countries = implode(", ", $tmp)."...";
            }else{
                if(is_array($list))
                $rows[$k]->countries = implode(", ", $list);
                else 
                $rows[$k]->countries = '';
            }
        }
        $actions = array(
            'delete' => _WOP_SHOP_DELETE
        );
        $bulk = $countries->getBulkActions($actions);
        if($filter_order_Dir == 'asc') $filter_order_Dir = 'desc'; else $filter_order_Dir = 'asc';
        $view = $this->getView("taxesext");
        $view->setLayout("list");
        $view->assign('rows', $rows);
        $view->assign('back_tax_id', $back_tax_id);
        $view->assign('config', $сonfig);
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);
        $view->assign('bulk', $bulk);
        do_action_ref_array('onBeforedisplayExtTax', array(&$view)); 
        $view->display();
    }

    function edit(){
        $сonfig = Factory::getConfig();
        $back_tax_id = Request::getInt("back_tax_id");
        $id = Request::getInt("row");

        $tax = Factory::getTable('taxext');
        $tax->load($id);

        if (!$tax->tax_id && $back_tax_id){
            $tax->tax_id = $back_tax_id;
        }

        $list_c = $tax->getZones();
        $zone_countries = array();
        foreach($list_c as $v){
            $obj = new stdClass();
            $obj->country_id = $v;
            $zone_countries[] = $obj;
        }

        $taxes = $this->getModel("taxes");
        $all_taxes = $taxes->getAllTaxes();
        $list_tax = array();
        foreach ($all_taxes as $_tax) {
            $list_tax[] = HTML::_('select.option', $_tax->tax_id,$_tax->tax_name, 'tax_id', 'tax_name');
        }
        $lists['taxes'] = HTML::_('select.genericlist', $list_tax, 'tax_id', '', 'tax_id', 'tax_name', $tax->tax_id);

        $countries = $this->getModel("countries");
        $lists['countries'] = HTML::_('select.genericlist', $countries->getAllCountries(0), 'countries_id[]', 'size = "10", multiple = "multiple"', 'country_id', 'name', $zone_countries);        

        $view = $this->getView("taxesext", 'html');
        $view->setLayout("edit");
        //JFilterOutput::objectHTMLSafe($tax, ENT_QUOTES);
        $view->assign('tax', $tax);
        $view->assign('back_tax_id', $back_tax_id);
        $view->assign('lists', $lists);
        $view->assign('config', $сonfig);
        //$view->assign('etemplatevar', '');
        do_action_ref_array('onBeforeEditExtTax', array(&$view));
        $view->display();
    }

    function save(){
        $back_tax_id = Request::getInt("back_tax_id");
        $id = Request::getInt("id");
        $tax = Factory::getTable('taxExt');
        $post = Request::get("post"); 
        $post['tax'] = saveAsPrice($post['tax']);
        $post['firma_tax'] = saveAsPrice($post['firma_tax']);
        do_action_ref_array( 'onBeforeSaveExtTax', array(&$post) );
        
        if (!$tax->bind($post)) {
            addMessage(_WOP_SHOP_ERROR_BIND);
            $this->setRedirect("admin.php?page=options&tab=exttaxes&back_tax_id=".$back_tax_id);
            return 0;
        }
        $tax->setZones($post['countries_id']);

        if (!$tax->store()){
            addMessage(_WOP_SHOP_ERROR_SAVE_DATABASE);
            $this->setRedirect("admin.php?page=options&tab=exttaxes&back_tax_id=".$back_tax_id);
            return 0; 
        }

        updateCountExtTaxRule();

        do_action_ref_array( 'onAfterSaveExtTax', array(&$tax) );

            $this->setRedirect("admin.php?page=options&tab=exttaxes&back_tax_id=".$back_tax_id);
    }

    function delete(){
        $back_tax_id = Request::getInt("back_tax_id");
        $cid = Request::getVar("rows");
        global $wpdb;
        $text = array();

        do_action_ref_array( 'onBeforeRemoveExtTax', array(&$cid) );

        foreach ($cid as $key => $value) {
            $wpdb->delete($wpdb->prefix."wshop_taxes_ext", array( 'id' => esc_sql($value)) );
            $text[] = _WOP_SHOP_ITEM_DELETED;
        }
        
        updateCountExtTaxRule();
        
        do_action_ref_array( 'onAfterRemoveExtTax', array(&$cid) );
        
        $this->setRedirect("admin.php?page=options&tab=exttaxes&back_tax_id=".$back_tax_id, implode("</li><li>",$text));
    }
    
    function back(){
        $this->setRedirect("admin.php?page=options&tab=exttaxes");
    }
    
}