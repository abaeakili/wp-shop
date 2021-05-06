<?php
/**
* @version      1.0.0 01.06.2016
* @author       MAXXmarketing GmbH
* @package      WOPshop
* @copyright    Copyright (C) 2010 http://www.wop-agentur.com. All rights reserved.
* @license      GNU/GPL
* @class        WshopAddon
*/

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

abstract class WshopAddon{
    
    protected $addon_params;
    protected $addon_alias;
	protected $addon_id;
    
    public function __construct(){
        if ($this->addon_alias == ''){
            throw new Exception('addon_alias empty');
        }
    }
    
    public function getAddonAlias(){
        return $this->addon_alias;
    }

    public function getAddonParams(){
        if (!$this->addon_params){
            $addon = Factory::getTable('addon');
            $addon->loadAlias($this->addon_alias);
            $this->addon_params = $addon->getParams();        
        }
        
        return $this->addon_params;
    }
    
    public function getAddonId(){
        if (!$this->addon_id){
            $addon = Factory::getTable('addon');
            $addon->loadAlias($this->addon_alias);
            $this->addon_id = $addon->id;        
        }
        
        return $this->addon_id;
    }
    
    public function getView($layout = ''){
        include_once WOPSHOP_PLUGIN_DIR ."site/views/addon/view.php";
        
        $templatePath = WOPSHOP_PLUGIN_DIR."/templates/addons/".$this->addon_alias.'/';
        $view = new AddonWshopView(null, $templatePath);

        if ($layout){
            $view->setLayout($layout);
        }   
        $view->set('addon_path_images', $this->getPathImages());
        return $view;
    }
    
    public function loadLanguage(){
        Factory::loadExtLanguageFile($this->addon_alias);
    }
    
    public function loadCss($extname = ''){
        wp_enqueue_style('wshop.'.$this->addon_alias.$extname.'.css', WOPSHOP_PLUGIN_URL.'assets/css/addons/'.$this->addon_alias.$extname.'.css');
    }
    
    public function loadJs($extname = '', $deps = array()){        
        wp_enqueue_script('wshop.'.$this->addon_alias.$extname.'.js', WOPSHOP_PLUGIN_URL.'assets/js/addons/'.$this->addon_alias.$extname.'.js', $deps);
    }
    
    public function getPathImages(){
        return URI::root().'wp-content/plugins/wopshop/assets/images/'.$this->addon_alias;
    }
}