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

class WshopView extends Object {

    protected $layout = 'default';
    protected $controller;
    protected $templatePath = null;
    
    public function __construct($controller, $templatePath = null) {
        $this->controller = $controller;
        $this->templatePath = $templatePath;
    }
    
    public function setLayout($layout){
        $this->layout = $layout;
    }
    
    public function assign(){
        $arg0 = @func_get_arg(0);
        $arg1 = @func_get_arg(1);

        // Assign by object
        if (is_object($arg0)){
            // Assign public properties
            foreach (get_object_vars($arg0) as $key => $val) {
                if (substr($key, 0, 1) != '_') {
                    $this->$key = $val;
                }
            }
            
            return true;
        }

        // Assign by associative array
        if (is_array($arg0)){
            foreach ($arg0 as $key => $val) {
                if (substr($key, 0, 1) != '_') {
                    $this->$key = $val;
                }
            }
            
            return true;
        }

        // Assign by string name and mixed value.

        // We use array_key_exists() instead of isset() because isset()
        // fails if the value is set to null.
        if (is_string($arg0) && substr($arg0, 0, 1) != '_' && func_num_args() > 1){
            $this->$arg0 = $arg1;
            return true;
        }

        // $arg0 was not object, array, or string.
        return false;
    }    
    
    public function display(){        
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
        
        $config = Factory::getConfig();
        return WOPSHOP_PLUGIN_DIR ."templates/".$config->template."/".$this->controller."/";
    }
}