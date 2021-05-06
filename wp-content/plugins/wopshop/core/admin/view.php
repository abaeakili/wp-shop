<?php
/**
* @version      1.0.0 01.06.2016
* @author       MAXXmarketing GmbH
* @package      WOPshop
* @copyright    Copyright (C) 2010 http://www.wop-agentur.com. All rights reserved.
* @license      GNU/GPL
*/

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class WshopAdminView {
    protected $controller;
    protected $templatePath;
    protected $layout = 'default';
    
    public function __construct($controller, $templatePath = null) {
        $this->controller = $controller;
        $this->templatePath = $templatePath;
    }
    
    public function setLayout($layout) {
        $this->layout = $layout;
    }
 
    public function assign($name, &$val) {
        $this->$name = $val;
    }    
    
    public function display() {
        $result = $this->loadTemplate();
        
        if ($result instanceof Exception) {
			return $result;
		}

		echo $result;
    }
    
    public function loadTemplate(){
        $template = $this->getTemplatePath() . $this->layout . ".php";
        
        if (file_exists($template)){
            // Start capturing output into a buffer
			ob_start();

			// Include the requested template filename in the local scope
			// (this will execute the view logic).
			include $template;

			// Done with the requested template; get the buffer and
			// clear it.
			$this->_output = ob_get_contents();
			ob_end_clean();

			return $this->_output;           
        } else {
            throw new Exception('File not found: '.$template, 500);
        }              
    }
    
    public function setTemplatePath($templatePath){
        $this->templatePath = $templatePath;
    }
    
    public function getTemplatePath(){
        if ($this->templatePath !== null){
            return trailingslashit($this->templatePath);
        }
        
        return WOPSHOP_PLUGIN_ADMIN_DIR . '/views/' . $this->controller . '/tmpl/';
    }
}