<?php

class PanelWshopAdminController extends WshopAdminController {

    function __construct() {
        parent::__construct();
    }
    
    function display() {
        $view = $this->getView('panel');
		do_action_ref_array('onBeforeDisplayHomePanel', array(&$view));
        $view->display();
    }    

}