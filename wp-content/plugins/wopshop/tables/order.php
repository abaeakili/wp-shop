<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class OrderWshopTable extends WshopTable {

    function __construct(){
        global $wpdb;
        parent::__construct($wpdb->prefix.'wshop_orders', 'order_id');         
    }

    function getAllItems(){
        if (!isset($this->items)){
            $config = Factory::getConfig();
            $query = "SELECT OI.* FROM `".$this->_db->prefix."wshop_order_item` as OI WHERE OI.order_id = '".esc_sql($this->order_id)."'";
            $this->items = $this->_db->get_results($query);
            foreach($this->items as $k=>$v){
                $this->items[$k]->_qty_unit = '';
                $this->items[$k]->delivery_time = '';
            }
            if ($config->display_delivery_time_for_product_in_order_mail){
                $deliverytimes = Factory::getAllDeliveryTime();
                foreach($this->items as $k=>$v){
                    if (isset($deliverytimes[$v->delivery_times_id])) {
                        $this->items[$k]->delivery_time = $deliverytimes[$v->delivery_times_id];
                    }
                }
            } 
        }
    return $this->items;
    }
    
    function getWeightItems(){
        $items = $this->getAllItems();
        $weight = 0;
        foreach($items as $row){
            $weight += $row->product_quantity * $row->weight;
        }
        do_action_ref_array('onGetWeightOrderProducts', array(&$this, &$weight));
    return $weight;
    }

    function getHistory() {
        $config = Factory::getConfig();
        $query = "SELECT history.*, status.*, status.`name_".$config->cur_lang."` as status_name  FROM `".$this->_db->prefix."wshop_order_history` AS history
                  INNER JOIN `".$this->_db->prefix."wshop_order_status` AS status ON history.order_status_id = status.status_id
                  WHERE history.order_id = '" . esc_sql($this->order_id) . "'
                  ORDER BY history.status_date_added";
        return $this->_db->get_results($query);
    }

    function getStatusTime(){
        $query = "SELECT max(status_date_added) FROM `".$this->_db->prefix."wshop_order_history` WHERE order_id = '".  esc_sql($this->order_id)."'";
        $res = $this->_db->get_var($query);
    return strtotime($res);
    }

    function getStatus() {
        $config = Factory::getConfig();
        $query = "SELECT `name_".$config->cur_lang."` as name FROM `".$this->_db->prefix."wshop_order_status` WHERE status_id = '" . esc_sql($this->order_status) . "'";
        return $this->_db->get_var($query);
    }

    function copyDeliveryData(){
        $this->d_title = $this->title;
        $this->d_f_name = $this->f_name;
        $this->d_l_name = $this->l_name;
		$this->d_m_name = $this->m_name;
        $this->d_firma_name = $this->firma_name;
        $this->d_home = $this->home;
        $this->d_apartment = $this->apartment;
        $this->d_street = $this->street;
        $this->d_street_nr = $this->street_nr;
        $this->d_zip = $this->zip;
        $this->d_city = $this->city;
        $this->d_state = $this->state;
        $this->d_email = $this->email;
		$this->d_birthday = $this->birthday;
        $this->d_country = $this->country;
        $this->d_phone = $this->phone;
        $this->d_mobil_phone = $this->mobil_phone;
        $this->d_fax = $this->fax;
        $this->d_ext_field_1 = $this->ext_field_1;
        $this->d_ext_field_2 = $this->ext_field_2;
        $this->d_ext_field_3 = $this->ext_field_3;
		do_action_ref_array('onAfterCopyDeliveryData', array(&$this));
    }

    function getOrdersForUser($id_user) {
        $config = Factory::getConfig(); 
        $lang = $config->cur_lang;
        $query = "SELECT orders.*, order_status.`name_".$lang."` as status_name, COUNT(order_item.order_id) AS count_products
                  FROM `$this->_tbl` AS orders
                  INNER JOIN `".$this->_db->prefix."wshop_order_status` AS order_status ON orders.order_status = order_status.status_id
                  INNER JOIN `".$this->_db->prefix."wshop_order_item` AS order_item ON order_item.order_id = orders.order_id
                  WHERE orders.user_id = '".esc_sql($id_user)."' and orders.order_created='1'
                  GROUP BY order_item.order_id 
                  ORDER BY orders.order_date DESC";
        return $this->_db->get_results($query);
    }

    /**
    * Next order id    
    */
    function getLastOrderId() {
        $query = "SELECT MAX(orders.order_id) AS max_order_id FROM `$this->_tbl` AS orders";
        return $this->_db->get_var() + 1;
    }

    function formatOrderNumber($num){
	$config = Factory::getConfig();
        $number = outputDigit($num, $config->ordernumberlength);
        do_action_ref_array('onAfterFormatOrderNumber', array(&$number, &$num));
        return $number;
    }

    /**
    * save name pdf from order
    */
    function insertPDF(){
        $query = "UPDATE `$this->_tbl` SET pdf_file = '".  esc_sql($this->pdf_file)."' WHERE order_id='".  esc_sql($this->order_id)."'";
        $this->_db->query($query);
    }
    
	function setInvoiceDate(){
        if (datenull($this->invoice_date)){
            $this->invoice_date = getJsDate();
            $query = "UPDATE `$this->_tbl` SET invoice_date='".esc_sql($this->invoice_date)."' WHERE order_id = '".esc_sql($this->order_id)."'";
            $this->_db->query($query);
        }
    }
	
	function getFilesStatDownloads($fileinfo = 0){
        if ($this->file_stat_downloads == "") return array();
        $rows = json_decode($this->file_stat_downloads, 1);
        if ($fileinfo && count($rows)){
            $files_id = array_keys($rows);
            $query = "SELECT * FROM `".$this->_db->prefix."wshop_products_files` where id in (".implode(',',$files_id).")";
            $_list = $this->_db->get_results($query);
            $list = array();
            foreach($_list as $k=>$v){
                if (is_array($rows[$v->id])){
                    $v->count_download = $rows[$v->id]['download'];
                    $v->time = $rows[$v->id]['time'];
                }else{
                    $v->count_download = $rows[$v->id];
                }
                $list[$v->id] = $v;
            }
            return $list;
        }else{
            foreach($rows as $k=>$v){
                if (!is_array($v)){
                    $rows[$k] = array('download'=>$v, 'time'=>'');
                }
            }
            return $rows;
        }
    }
    
    function setFilesStatDownloads($array){
        $this->file_stat_downloads = json_encode($array);
    }
    
    function getTaxExt(){
        if ($this->order_tax_ext == "") return array();
        return json_decode($this->order_tax_ext, 1);
    }
    
    function setTaxExt($array){
        $this->order_tax_ext = json_encode($array);
    }
    
    function setShippingTaxExt($array){
        $this->shipping_tax_ext = json_encode($array);
    }
    
    function getShippingTaxExt(){
        if ($this->shipping_tax_ext == "") return array();
        return json_decode($this->shipping_tax_ext, 1);
    }
    
    function setPackageTaxExt($array){
        $this->package_tax_ext = json_encode($array);
    }
    
    function getPackageTaxExt(){
        if ($this->shipping_tax_ext == "") return array();
        return json_decode($this->package_tax_ext, 1);
    }

    function setPaymentTaxExt($array){
        $this->payment_tax_ext = json_encode($array);
    }
    
    function getPaymentTaxExt(){
        if ($this->payment_tax_ext == "") return array();
        return json_decode($this->payment_tax_ext, 1);
    }
    
    function getPaymentParamsData(){
        if ($this->payment_params_data == "") return array();
        return json_decode($this->payment_params_data, 1);
    }
    
    function setPaymentParamsData($array){
        $this->payment_params_data = json_encode($array);
    }
    
    function getLang(){
        $lang = $this->lang;
        if ($lang == "") {
            $lang = "en-GB";
        }
        
        return $lang;
    }
	
	function getListFieldCopyUserToOrder(){        
        $list = array('user_id','f_name','l_name','m_name','firma_name','client_type','firma_code','tax_number','email','birthday','home','apartment','street','street_nr','zip','city','state','country','phone','mobil_phone','fax','title','ext_field_1','ext_field_2','ext_field_3','d_f_name','d_l_name','d_m_name','d_firma_name','d_email','d_birthday','d_home','d_apartment','d_street','d_street_nr','d_zip','d_city','d_state','d_country','d_phone','d_mobil_phone','d_title','d_fax','d_ext_field_1','d_ext_field_2','d_ext_field_3');
        do_action_ref_array('onBeforeGetListFieldCopyUserToOrder', array(&$list));
        return $list;
    }
    
    public function saveOrderItem($items) {
        foreach($items as $key => $value){
            $order_item = Factory::getTable('orderitem');
            $order_item->order_id = $this->order_id;
            $order_item->product_id = $value['product_id'];
            $order_item->product_ean = $value['ean'];
            $order_item->product_name = $value['product_name'];
            $order_item->product_quantity = $value['quantity'];
            $order_item->product_item_price = $value['price'];
            $order_item->product_tax = $value['tax'];
            $order_item->product_attributes = $attributes_value = '';
            $order_item->product_freeattributes = $free_attributes_value = '';
            $order_item->attributes = $value['attributes'];
            $order_item->files = $value['files'];
            $order_item->freeattributes = $value['freeattributes'];
            $order_item->weight = $value['weight'];
            $order_item->thumb_image = $value['thumb_image'];
            $order_item->delivery_times_id = $value['delivery_times_id'];
            $order_item->vendor_id = $value['vendor_id'];
            $order_item->manufacturer = $value['manufacturer'];
			$order_item->basicprice = $value['basicprice'];
            $order_item->basicpriceunit = $value['basicpriceunit'];
            $order_item->params = $value['params'];
            
            if (isset($value['attributes_value'])){
                foreach ($value['attributes_value'] as $attr){
                    $attributes_value .= $attr->attr.': '.$attr->value."\n";
                }
            }
            $order_item->product_attributes = $attributes_value;
            
            if (isset($value['free_attributes_value'])){
                foreach ($value['free_attributes_value'] as $attr){
                    $free_attributes_value .= $attr->attr.': '.$attr->value."\n";
                }
            }
            $order_item->product_freeattributes = $free_attributes_value;
            
            if (isset($value['extra_fields'])){
                $order_item->extra_fields = '';
                foreach($value['extra_fields'] as $extra_field){
                    $order_item->extra_fields .= $extra_field['name'].': '.$extra_field['value']."\n";
                }
            }
            
            do_action_ref_array('onBeforeSaveOrderItem', array(&$order_item, &$value));
            
            $order_item->store();
        }
        
        return 1;
    }
    
    /**
    * get or return product in Stock
    * @param $change ("-" - get, "+" - return) 
    */
    function changeProductQTYinStock($change = "-"){
        $query = "SELECT OI.*, P.unlimited FROM `".$this->_db->prefix."wshop_order_item` as OI left join `".$this->_db->prefix."wshop_products` as P on P.product_id=OI.product_id
                  WHERE order_id = '".esc_sql($this->order_id)."'";
        $items = $this->_db->get_results($query);
		do_action_ref_array('onBeforechangeProductQTYinStock', array(&$items, &$this, &$change));

        foreach($items as $item){
            
            if ($item->unlimited) continue;
            
            if ($item->attributes!=""){
                $attributes = json_decode($item->attributes, 1);
            }else{
                $attributes = array();
            }            
            if (!is_array($attributes)) $attributes = array();
            
            $allattribs = Factory::getAllAttributes(1);

            $dependent_attr = array();
            foreach($attributes as $k=>$v){
                if ($allattribs[$k]->independent==0){
                    $dependent_attr[$k] = $v;
                }
            }

            if (count($dependent_attr)){
                $where="";
                foreach($dependent_attr as $k=>$v){
                    $where.=" and `attr_$k`='".intval($v)."'";
                }
                $query = "update `".$this->_db->prefix."wshop_products_attr` set `count`=`count`  ".$change." ".$item->product_quantity." where product_id='".intval($item->product_id)."' ".$where;
                $this->_db->query($query);

                $query="select sum(count) as qty from `".$this->_db->prefix."wshop_products_attr` where product_id='".intval($item->product_id)."' and `count`>0 ";
                $qty = $this->_db->get_var($query);
                
                $query = "UPDATE `".$this->_db->prefix."wshop_products` SET product_quantity = '".$qty."' WHERE product_id = '".intval($item->product_id)."'";
                $this->_db->query($query);
            }else{
                $query = "UPDATE `".$this->_db->prefix."wshop_products` SET product_quantity = product_quantity ".$change." ".$item->product_quantity." WHERE product_id = '".intval($item->product_id)."'";
                $this->_db->query($query);
            }
            do_action_ref_array('onAfterchangeProductQTYinStock', array(&$item, &$change, &$this));
        }
        
        if ($change=='-'){
            $product_stock_removed = 1;
        }else{
            $product_stock_removed = 0;
        }
        $query = "update $this->_tbl set product_stock_removed=".$product_stock_removed." WHERE order_id = '".esc_sql($this->order_id)."'";
        $this->_db->query($query);
		do_action_ref_array('onAfterchangeProductQTYinStockPSR', array(&$items, &$this, &$change, &$product_stock_removed));
    }
    
    /**    
    * get list vendors for order
    */
    function getVendors(){
        $query = "SELECT distinct V.* FROM `".$this->_db->prefix."wshop_order_item` as OI
                  left join `".$this->_db->prefix."wshop_vendors` as V on V.id = OI.vendor_id
                  WHERE order_id = '".esc_sql($this->order_id)."'";
        return $this->_db->get_results($query);
    }
    
    function getVendorItems($vendor_id){
        $items = $this->getAllItems();
        foreach($items as $k=>$v){
            if ($v->vendor_id!=$vendor_id){
                unset($items[$k]);
            }
        }
    return $items;
    }
    
    function getVendorInfo(){
        $config = Factory::getConfig();
        $vendor_id = $this->vendor_id;
        if ($vendor_id==-1) $vendor_id = 0;
        if ($config->vendor_order_message_type<2) $vendor_id = 0;
        $vendor = Factory::getTable('vendor');
        $vendor->loadFull($vendor_id);
        $vendor->country_id = $vendor->country;
        $country = Factory::getTable('country');
        $country->load($vendor->country_id);
        $field_country_name = "name".$config->cur_lang;
        $vendor->country = $country->$field_country_name;
    return $vendor;
    }
    
    function getVendorIdForItems(){
        $items = $this->getAllItems();
		$vendors = array();
        foreach($items as $v){
            $vendors[] = $v->vendor_id;
        }
        $vendors = array_unique($vendors);
        if (count($vendors)==0){
            return 0;
        }elseif (count($vendors)>1){
            return -1;
        }else{
            return $vendors[0];
        }
    }
    
    function getReturnPolicy(){
        $items = $this->getAllItems();
        $products = array();
        foreach($items as $v){
            $products[] = $v->product_id;
        }
        $products = array_unique($products);
        $statictext = Factory::getTable("statictext");
        $rows = $statictext->getReturnPolicyForProducts($products);
        do_action_ref_array('onAfterOrderGetReturnPolicy', array(&$this, &$rows));
        return $rows;
    }
    
    function saveTransactionData($rescode, $status_id, $data){
        $row = Factory::getTable("paymenttrx");
        $row->order_id = $this->order_id;
        $row->rescode = $rescode;
        $row->status_id = $status_id;
        $row->transaction = $this->transaction;
        $row->date = getJsDate();
        $row->store();
        if (is_array($data)){
            foreach($data as $k=>$v){
                $rowdata = Factory::getTable("PaymentTrxData");
                $rowdata->id = 0;
                $rowdata->trx_id = $row->id;
                $rowdata->order_id = $this->order_id;
                $rowdata->key = $k;
                $rowdata->value = $v;
                $rowdata->store();
            }
        }
    }
    
    function getListTransactions(){
        $query = "SELECT * FROM `".$this->_db->prefix."wshop_payment_trx` WHERE order_id = '".esc_sql($this->order_id)."' order by id desc";
        $rows = $this->_db->get_results($query);
        foreach($rows as $k=>$v){
            $rows[$k]->data = $this->getTransactionData($v->id);
        }
    
        return $rows;
    }
    
    function getTransactionData($trx_id){
        $query = "SELECT * FROM `".$this->_db->prefix."wshop_payment_trx_data` WHERE trx_id = '".esc_sql($trx_id)."' order by id";       
        return $this->_db->get_results($query);
    }
    
    function setShippingParamsData($array){
        $this->shipping_params_data = json_encode($array);
    }
    
    function getShippingParamsData(){
        if ($this->shipping_params_data == "") return array();
        return json_decode($this->shipping_params_data, 1);
    }
    
    public function getPayment(){
        $pm_method = Factory::getTable('paymentMethod');
        $pm_method->load($this->payment_method_id);
        return $pm_method;
	}
	
	public function getShipping(){
        $sh = Factory::getTable('shippingMethod');
        $sh->load($this->shipping_method_id);
		return $sh;
	}
    
    public function getPaymentName(){
        return $this->getPayment()->getName();
    }
    
    public function getShippingName(){
        return $this->getShipping()->getName();
    }
}