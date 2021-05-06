<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class IeController extends Object{
    
    function execute( $task ){
        $this->$task();
    }
    
    function save(){
    }
    
    function loadLanguageFile(){
        $config = Factory::getConfig();
        $alias = $this->get('alias'); 
        if(file_exists(dirname(__FILE__).'/'.$alias.'/lang/'.$config->cur_lang.'.php')) {
            require_once (dirname(__FILE__).'/'.$alias.'/lang/'.$config->cur_lang.'.php');
        } else {
            require_once (dirname(__FILE__).'/'.$alias.'/lang/en-GB.php');
        }
    }
}