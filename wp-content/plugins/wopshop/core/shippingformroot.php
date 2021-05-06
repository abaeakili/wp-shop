<?php
abstract class ShippingFormRoot{
    
    var $_errormessage = "";
    
    abstract function showForm($shipping_id, $shippinginfo, $params);
    
    function check($params, $sh_method){
        return 1;
    }
    
    /**
    * Set message error check
    */
    function setErrorMessage($msg){
        $this->_errormessage = $msg;
    }
    
    /**
    * Get message error check
    */
    function getErrorMessage(){
        return $this->_errormessage;
    }
    
    /**
    * list display params name shipping saved to order
    */
    function getDisplayNameParams(){
        return array();
    }
    
    /**
    * exec before mail send
    */
    function prepareParamsDispayMail(&$order, &$sh_method){
    }

}