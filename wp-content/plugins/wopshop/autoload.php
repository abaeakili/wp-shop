<?php

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * WopshopAutoloader Class.
 *
 * @class WopshopAutoloader
 */
class WshopAutoloader
{
    /**
     * Auto loading the core classes and helpers
     * 
     * @param string $className
     */
    public static function load($className) {
        if (class_exists($className)) {
            return true;
        }
        
        if (strpos($className, 'WshopAdmin') !== false){
            $fileName = str_replace('WshopAdmin', '', $className);
            self::loadFromAdmin($className, $fileName);
        } else if (strpos($className, 'Wshop') !== false){
            $fileName = str_replace('Wshop', '', $className);
            self::loadFromCore($className, $fileName);
            self::loadFromSite($className, $fileName);
            self::loadFromLib($className, $fileName);
        } else {
            self::loadFromCore($className);
            self::loadFromSite($className);
            self::loadFromLib($className);
        }
    }
    
    
    private static function loadFromAdmin($className, $fileName){
        if (!class_exists($className) && file_exists(dirname(__FILE__) . "/core/admin/" . strtolower($fileName) . ".php")) {
            require_once(dirname(__FILE__) . "/core/admin/" . strtolower($fileName) . ".php");
        }
    }
    
    private static function loadFromCore($className, $fileName = null){
        $fileName = $fileName ? $fileName : $className;
        if (!class_exists($className) && file_exists(dirname(__FILE__) . "/core/" . strtolower($fileName) . ".php")) {
            require_once(dirname(__FILE__) . "/core/" . strtolower($fileName) . ".php");
        }
    }
    
    private static function loadFromSite($className, $fileName = null){
        $fileName = $fileName ? $fileName : $className;
        if (!class_exists($className) && file_exists(dirname(__FILE__) . "/core/site/" . strtolower($fileName) . ".php")) {
            require_once(dirname(__FILE__) . "/core/site/" . strtolower($fileName) . ".php");
        }
    }
    
    private static function loadFromLib($className, $fileName = null){
        $fileName = $fileName ? $fileName : $className;
        if (!class_exists($className) && file_exists(dirname(__FILE__) . "/lib/" . strtolower($fileName) . ".php")) {
            require_once(dirname(__FILE__) . "/lib/" . strtolower($fileName) . ".php");
        }
    }
}

spl_autoload_register(array('WshopAutoloader', 'load'));