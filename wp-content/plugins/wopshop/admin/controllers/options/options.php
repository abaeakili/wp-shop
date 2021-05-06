<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class OptionsWshopAdminController extends WshopAdminController {

    public function __construct() {
        parent::__construct();
    }
    
    public function display() {
        $view = $this->getView('options');
        $menu = getItemsOptionPanelMenu();
        $view->items = $menu;
        $view->display();
    }
}