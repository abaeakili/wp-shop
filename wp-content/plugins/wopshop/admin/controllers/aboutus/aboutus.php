<?php
class AboutUsWshopAdminController extends WshopAdminController {
    public function __construct() {
        parent::__construct();
    }
    
    public function display() {
        $view = $this->getView('panel');
        $view->setLayout('info');
        $view->assign('version', get_option('wopshop_version'));
		do_action_ref_array('onBeforeDisplayAboutus', array(&$view));
        $view->display();
    }
}