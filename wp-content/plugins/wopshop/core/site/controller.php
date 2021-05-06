<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class WshopController extends Object {

    protected $default_view;
    protected $name;
    protected $redirect;

    function getModel($name){
        if (file_exists(WOPSHOP_PLUGIN_DIR ."/site/models/".strtolower($name).".php")){
            include_once(WOPSHOP_PLUGIN_DIR ."/site/models/".strtolower($name).".php");
            $modelname = $name."WshopModel";
            if (class_exists($modelname)){
                $obj = new $modelname();
                return $obj;
            }         
        }
    }
    
    function getView($name){
        if (file_exists(WOPSHOP_PLUGIN_DIR ."site/views/".strtolower($name)."/view.php")){
            include_once(WOPSHOP_PLUGIN_DIR ."site/views/".strtolower($name)."/view.php");
            $viewname = $name."WshopView";
            
            if (class_exists($viewname)){
                $obj = new $viewname($name);
                return $obj;
            } else {
                echo('No View Class found');
            }            
        } else {
           echo('No View file found'); 
        }        
    } 

    public function redirect() {
        if ($this->redirect) {
            $app = Factory::getApplication();
            $app->redirect($this->redirect, $this->message, $this->messageType);
        }

        return false;
    }

    public function addMetaTag($id, $content) {
        $app = Factory::getApplication();
        if (!$app->tags[$id] && $content){
            $app->tags[$id] = $content;
        }
        return true;
    }

    function setRedirect($url){
        if (headers_sent()) {
            echo "<script>document.location.href='$url';</script>\n";
        } else {            
            header( 'HTTP/1.1 301 Moved Permanently' );
            header( 'Location: ' . $url );
            die();
        }
    }     
}