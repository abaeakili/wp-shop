<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
include_once(WOPSHOP_PLUGIN_DIR."shippings/shippingext.php");

class ShippingExtWshopTable extends WshopTable{

    function __construct(){
        global $wpdb;
        parent::__construct($wpdb->prefix.'wshop_shipping_ext_calc', 'id');         
    }
    
    function loadFromAlias($alias){
        $query = "SELECT id FROM `$this->_tbl` WHERE `alias`='".  esc_sql($alias)."'";
        extract(ws_add_trigger(get_defined_vars(), "query"));
        $id = $this->_db->get_var($query);
        return $this->load($id);
    }
    
    function load($id = null, $reset = true){
        $return = parent::load($id, $reset);
        $config = Factory::getConfig();
        $path = $config->path."shippings";
        $extname = $this->alias;
        $filepatch = $path."/".$extname."/".$extname.".php";
        if (file_exists($filepatch)){
            include_once($filepatch);
            $this->exec = new $extname();
        }else{
            addMessage("Load ShippingExt ".$extname." error.", 'error');
        }
        
        return $return;
    }
    
    function getList($active = 0){
        $adv_query = "";
        if ($active==1){
            $adv_query = "where `published`='1'";
        }
        $query = "select * from `$this->_tbl` ".$adv_query." order by `ordering`";
        return $this->_db->get_results($query);
    }
    
    function setShippingMethod($data){
        $this->shipping_method = json_encode($data);
    }
    
    function getShippingMethod(){
        if ($this->shipping_method=="") return array();
        return json_decode($this->shipping_method, 1);
    }
    
    function setParams($data){
        $this->params = json_encode($data);
    }
    
    function getParams(){        
        if ($this->params=="") return array();
        return json_decode($this->params, 1);
    }
}