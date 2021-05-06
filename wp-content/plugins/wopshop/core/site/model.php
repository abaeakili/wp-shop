<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class WshopModel extends Object {
    public $lang;
 
    public function __construct() {
        $this->lang = Factory::getConfig()->getLang();
    }
}