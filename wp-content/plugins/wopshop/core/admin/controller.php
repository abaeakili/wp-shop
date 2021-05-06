<?php
class WshopAdminController {
    
    protected $model;
    protected $controller;
 
    public function __construct() {
        $this->model = $this->getNameModel();
    }
    
    public function getModel($name = null){
        $name = $name ? $name : $this->model;
        
        if (file_exists(WOPSHOP_PLUGIN_ADMIN_DIR ."/models/".strtolower($name).".php")){
            include_once(WOPSHOP_PLUGIN_ADMIN_DIR ."/models/".strtolower($name).".php");
            $modelname = $name."WshopAdminModel";
            if (class_exists($modelname)){
                $obj = new $modelname();
                return $obj;                   
            }         
        }
    }
    
    public function getView($name){
        if (file_exists(WOPSHOP_PLUGIN_ADMIN_DIR ."/views/".strtolower($name)."/view.php")){
            include_once(WOPSHOP_PLUGIN_ADMIN_DIR ."/views/".strtolower($name)."/view.php");
            $viewname = $name."WshopAdminView";
            if (class_exists($viewname)){
                $obj = new $viewname($name);
                return $obj;
            } else {
                wp_die('No View Class found');
            }            
        } else {
           wp_die('No View file found'); 
        }        
    }    
    
    public function setRedirect($url, $msg = null, $type = 'updated'){
        if ($msg !== null) {
            addMessage($msg, $type);
        }
        if (headers_sent()) {
            echo "<script>document.location.href='$url';</script>\n";
        } else {            
            header( 'HTTP/1.1 301 Moved Permanently' );
            header( 'Location: ' . $url );
        }
    }
    
    public function publish(){
        $cid = Request::getVar('cid', array(), 'default', 'array');
        $this->getModel()->publish($cid, 1);
		$this->setRedirect($this->getUrlListItems());
    }
    
    public function unpublish(){
        $cid = Request::getVar('cid', array(), 'default', 'array');
        $this->getModel()->publish($cid, 0);
		$this->setRedirect($this->getUrlListItems());
    }
    
    protected function getUrlListItems(){
        return "admin.php?page=".$this->getNameController();;
    }
    
    protected function getNameController(){
		if (empty($this->controller)){
			$r = null;
			preg_match('/(.*)WshopAdminController/i', get_class($this), $r);
			$this->controller = strtolower($r[1]);
		}
        
		return $this->controller;
	}
    
    protected function getNameModel(){
		if (empty($this->model)){
			$r = null;
			preg_match('/(.*)WshopAdminController/i', get_class($this), $r);
			$this->model = strtolower($r[1]);
		}
        
		return $this->model;
	}
}