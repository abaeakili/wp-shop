<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class ShippingMethodWshopTable extends WshopTable{

    function __construct(){
        global $wpdb;
        parent::__construct($wpdb->prefix.'wshop_shipping_method', 'shipping_id');        
    }
    function setParams($params){
        $this->params = json_encode($params);
    }
    function getParams(){
        if ($this->params==""){
            return array();
        }else{
            return json_decode($this->params, 1);
        }
    }
    function getPayments(){
        extract(ws_add_trigger(get_defined_vars()));
        if ($this->payments==""){
            return array();
        }else{
            return explode(",", $this->payments);
        }
    }
    function setPayments($payments){
        $payments = (array)$payments;
        foreach($payments as $v){
            if ($v==0){
                $payments = array();
                break;
            }
        }
        extract(ws_add_trigger(get_defined_vars()));
        $this->payments = implode(",", $payments);
    }
    function loadFromAlias($alias){
        $query = "SELECT shipping_id FROM `$this->_tbl` WHERE `alias`='".  esc_sql($alias)."'";
        extract(ws_add_trigger(get_defined_vars(), "query"));
        $id = $this->_db->get_row($query);
        return $this->load($id);
    }
    
    function getAllShippingMethods($publish = 1) {
        $config = Factory::getConfig();
        $query_where = ($publish)?("WHERE published = '1'"):("");
        $query = "SELECT shipping_id, `name_".$config->cur_lang."` as name, `description_".$config->cur_lang."` as description, published, ordering
                  FROM `$this->_tbl` 
                  $query_where 
                  ORDER BY ordering";
		extract(ws_add_trigger(get_defined_vars(), "query"));
        return $this->_db->get_results($query);
    }

    function getAllShippingMethodsCountry($country_id, $payment_id, $publish = 1){
	$config = Factory::getConfig();
        $query_where = ($publish) ? ("AND sh_method.published = '1'") : ("");
		if ($payment_id && $config->step_4_3==0){
			$query_where.= " AND (sh_method.payments='' OR FIND_IN_SET(".$payment_id.", sh_method.payments) ) ";
		}
        $query = "SELECT *, sh_method.`name_".$config->cur_lang."` as name, `description_".$config->cur_lang."` as description FROM `$this->_tbl` AS sh_method
                  INNER JOIN `".$this->_db->prefix."wshop_shipping_method_price` AS sh_pr_method ON sh_method.shipping_id = sh_pr_method.shipping_method_id
                  INNER JOIN `".$this->_db->prefix."wshop_shipping_method_price_countries` AS sh_pr_method_country ON sh_pr_method_country.sh_pr_method_id = sh_pr_method.sh_pr_method_id
                  INNER JOIN `".$this->_db->prefix."wshop_countries` AS countries  ON sh_pr_method_country.country_id = countries.country_id
                  WHERE countries.country_id = '".  esc_sql($country_id)."' $query_where
                  ORDER BY sh_method.ordering";
		extract(ws_add_trigger(get_defined_vars(), "query"));
        return $this->_db->get_results($query);
    }
    
    function getShippingPriceId($shipping_id, $country_id, $publish = 1){
        $query_where = ($publish) ? ("AND sh_method.published = '1'") : ("");
        $query = "SELECT sh_pr_method.sh_pr_method_id FROM `$this->_tbl` AS sh_method
                  INNER JOIN `".$this->db->prefix."wshop_shipping_method_price` AS sh_pr_method ON sh_method.shipping_id = sh_pr_method.shipping_method_id
                  INNER JOIN `".$this->db->prefix."wshop_shipping_method_price_countries` AS sh_pr_method_country ON sh_pr_method_country.sh_pr_method_id = sh_pr_method.sh_pr_method_id
                  INNER JOIN `".$this->db->prefix."wshop_countries` AS countries  ON sh_pr_method_country.country_id = countries.country_id
                  WHERE countries.country_id = '".  esc_sql($country_id)."' and sh_method.shipping_id=".intval($shipping_id)."  $query_where";
        extract(ws_add_trigger(get_defined_vars(), "query"));
        return (int)$this->_db->get_var($query);
    }
    
    
    

	
    

    
    function getShippingForm($alias = null){
        if (is_null($alias)){
            $alias = $this->alias;
        }
        $config = Factory::getConfig();
        $script = str_replace(array('.','/'),'', $alias);
        $patch = $config->path.'shippingform/'.$script."/".$script.'.php';
        if ($script!='' && file_exists($patch)){
            include_once($patch);
            $data = new $script();
        }else{
            $data = null;
        }
        return $data;
    }
    
    function loadShippingForm($shipping_id, $shippinginfo, $params){
        $shippingForm = $this->getShippingForm($shippinginfo->alias);
        if ($shippingForm){
            ob_start();
            $shippingForm->showForm($shipping_id, $shippinginfo, $params);
            $html = ob_get_contents();
            ob_get_clean();
        }
        return $html;
    }
}