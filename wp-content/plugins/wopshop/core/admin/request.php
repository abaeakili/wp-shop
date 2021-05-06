<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WshopAdminRequest {
    
    private $_folder = '';
    
    private $_controller = 'panel';
    
    private $_task = 'display';


    public function __construct(){
        if($_REQUEST['tab'] == '') unset($_REQUEST['tab']);
        if (isset($_REQUEST['tab'])) {
            $this->_controller = $_REQUEST['tab'];
            $this->_folder = $_REQUEST['page'].'/';
        } elseif (isset($_REQUEST['page'])) {
            $this->_controller = $_REQUEST['page'];
            $this->_folder = $_REQUEST['page'].'/';
        }        
        if (isset($_REQUEST['action2']) && $_REQUEST['action2'] !='-1') {
            $this->_task = $_REQUEST['action2'];
        } elseif (isset($_REQUEST['task'])) {
            $this->_task = $_REQUEST['task'];
        }
    }

    public function getController(){
        return $this->_controller;
    }
    
    public function getTask(){
        return $this->_task;
    }
    
    public function getFolder(){
        return $this->_folder;
    }
    
}    

