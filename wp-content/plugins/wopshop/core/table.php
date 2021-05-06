<?php

abstract class WshopTable extends Object {

    protected $_tbl = '';
    protected $_tbl_key = '';
    protected $_tbl_keys = array();
    protected $_autoincrement = true;
    protected $_db;

    public function __construct($table, $key) {
        global $wpdb;
		$this->_tbl = $table;
        
		if (is_string($key)) {
            $key = array($key);
        } elseif (is_object($key)) {
            $key = (array) $key;
        }

        $this->_tbl_keys = $key;
        $this->_db = $wpdb;

		if (count($key) == 1) {
            $this->_autoincrement = true;
        } else {
            $this->_autoincrement = false;
        }

        $this->_tbl_key = $this->getKeyName();
		$fields = $this->getFields();
		if ($fields) {
            foreach ($fields as $name => $v) {
                if (!property_exists($this, $name)) {
                    $this->$name = null;
                }
            }
        }

//        $this->_tbl     = $table;
//        $this->_tbl_key = $key;
//        if ($fields = $this->getFields()) {
//            foreach ($fields as $name => $v) {
//                if (!property_exists($this, $name)) {
//                    $this->$name = null;
//                }
//            }
//        }
    }

	public function getFields() {
        static $cache = null;

        if ($cache === null) {
            // Lookup the fields for this table only once.
            $name = $this->_tbl;
            $fields = $this->getTableColumns($name, false);

            if (empty($fields)) {
                throw new UnexpectedValueException(sprintf('No columns found for %s table', $name));
            }

            $cache = $fields;
        }

        return $cache;
    }

    public function getTableColumns($table, $typeOnly = true) {
        global $wpdb;

        $result = array();

        $fields = $wpdb->get_results('SHOW FULL COLUMNS FROM `' . esc_sql($table) . '`');
        if ($typeOnly) {
            foreach ($fields as $field) {
                $result[$field->Field] = preg_replace("/[(0-9)]/", '', $field->Type);
            }
        } else {
            foreach ($fields as $field) {
                $result[$field->Field] = $field;
            }
        }

        return $result;
    }

    public static function getInstance($type, $prefix = 'WshopTable'){
		// Sanitize and prepare the table class name.
		$type = preg_replace('/[^A-Z0-9_\.-]/i', '', $type);
		$tableClass = ucfirst($type) . $prefix;

		// Only try to load the class if it doesn't already exist.
		if (!class_exists($tableClass)) {
            if (file_exists(WOPSHOP_PLUGIN_DIR ."/tables/".strtolower($type).".php")) {
                require_once WOPSHOP_PLUGIN_DIR ."/tables/".strtolower($type).".php";
            }
            
			if (!class_exists($tableClass)){
				// If we were unable to find the class file in the Table include paths, raise a warning and return false.
				throw new Exception(sprintf('%s class not found', $tableClass));

				return false;
			}
		}

		// Instantiate a new table class and return it.
		return new $tableClass();
	}

    protected function _getAssetName() {
        $k = $this->_tbl_key;

        return $this->_tbl . '.' . (int)$this->$k;
    }

    protected function _getAssetTitle() {
        return $this->_getAssetName();
    }

    public function getTableName() {
        return $this->_tbl;
    }

	public function getKeyName($multiple = false) {
        if (count($this->_tbl_keys)) {
            if ($multiple) {
                return $this->_tbl_keys;
            } else {
                return $this->_tbl_keys[0];
            }
        }

        return '';
    }

    public function getDbo() {
        return $this->_db;
    }

    public function reset() {
        // Get the default values for the class from the table.
        foreach ($this->getFields() as $k => $v) {
            // If the property is not the primary key or private, reset it.
            if ($k != $this->_tbl_key && (strpos($k, '_') !== 0)) {
                $this->$k = $v->Default;
            }
        }
    }

	public function bind($src, $ignore = array()) {
        // JSON encode any fields required
        if (!empty($this->_jsonEncode)) {
            foreach ($this->_jsonEncode as $field) {
                if (isset($src[$field]) && is_array($src[$field])) {
                    $src[$field] = json_encode($src[$field]);
                }
            }
        }

        // If the source value is not an array or object return false.
        if (!is_object($src) && !is_array($src)) {
            throw new InvalidArgumentException(sprintf('%s::bind(*%s*)', get_class($this), gettype($src)));
        }

        // If the source value is an object, get its accessible properties.
        if (is_object($src)) {
            $src = get_object_vars($src);
        }

        // If the ignore value is a string, explode it over spaces.
        if (!is_array($ignore)) {
            $ignore = explode(' ', $ignore);
        }

        // Bind the source value, excluding the ignored fields.
        foreach ($this->getProperties() as $k => $v) {
            // Only process fields not in the ignore array.
            if (!in_array($k, $ignore)) {
                if (isset($src[$k])) {
                    $this->$k = $src[$k];
                }
            }
        }

        return true;
    }

    public function load($keys = null, $reset = true) {
        global $wpdb;
        if (empty($keys)) {
            $empty = true;
            $keys = array();

            // If empty, use the value of the current key
            foreach ($this->_tbl_keys as $key) {
                $empty = $empty && empty($this->$key);
                $keys[$key] = $this->$key;
            }

            // If empty primary key there's is no need to load anything
            if ($empty) {
                return true;
            }
        } elseif (!is_array($keys)) {
            // Load by primary key.
            $keyCount = count($this->_tbl_keys);

            if ($keyCount) {
                if ($keyCount > 1) {
                    throw new InvalidArgumentException('Table has multiple primary keys specified, only one primary key value provided.');
                }

                $keys = array($this->getKeyName() => $keys);
            } else {
                throw new RuntimeException('No table keys defined.');
            }
        }

        if ($reset) {
            $this->reset();
        }

        // Initialise the query.
        $query = 'SELECT * FROM `' . $this->_tbl . '` WHERE ';
        $fields = array_keys($this->getProperties());

        foreach ($keys as $field => $value) {
            // Check that $field is in the table.
            if (!in_array($field, $fields)) {
                throw new UnexpectedValueException(sprintf('Missing field in database: %s &#160; %s.', get_class($this), $field));
            }
            $query .= $field . ' = ' . $value;
            // Add the search tuple to the query.
            //$query->where($this->_db->quoteName($field) . ' = ' . $this->_db->quote($value));
        }
        $row = $wpdb->get_row($query, ARRAY_A);
        //$row = get_object_vars($row);
//		$this->_db->setQuery($query);
//
//		$row = $this->_db->loadAssoc();
        // Check that we have a result.
        if (empty($row)) {
            $result = false;
        } else {
            // Bind the object with the row and return.
            $result = $this->bind($row);
        }
        
        return $result;
    }

    public function appendPrimaryKeys($query, $pk = null) {
        if (is_null($pk)) {
            foreach ($this->_tbl_keys as $k) {
                $query->where($this->_db->quoteName($k) . ' = ' . $this->_db->quote($this->$k));
            }
        } else {
            if (is_string($pk)) {
                $pk = array($this->_tbl_key => $pk);
            }

            $pk = (object) $pk;

            foreach ($this->_tbl_keys as $k) {
                $query->where($this->_db->quoteName($k) . ' = ' . $this->_db->quote($pk->$k));
            }
        }
    }

    public function hasPrimaryKey() {
        if ($this->_autoincrement) {
            $empty = true;

            foreach ($this->_tbl_keys as $key) {
                $empty = $empty && empty($this->$key);
            }
        } else {
            $query = "SELECT COUNT(*) FROM $this->_tbl";
            $count = $this->_db->get_var($query);

            if ($count == 1) {
                $empty = false;
            } else {
                $empty = true;
            }
        }

        return !$empty;
    }
	        
    public function store($updateNulls = false) {
		$key = $this->_tbl_keys[0];

		// If a primary key exists update the object, otherwise insert it.
		if ($this->hasPrimaryKey()) {
			$result = $this->updateObject($updateNulls);
		} else {
			$result = $this->insertObject($key);
		}

		return $result;
    }
    
	public function insertObject($key = null) {
        $insert = array();

        // Iterate over the object variables to build the query fields and values.
        foreach (get_object_vars($this) as $k => $v) {
            // Only process non-null scalars.
            if (is_array($v) || is_object($v) || $v === null) {
                continue;
            }

            // Ignore any internal fields.
            if ($k[0] == '_') {
                continue;
            }

            // Prepare and sanitize the fields and values for the database query.
            //$insert[$k] = esc_html(wp_unslash($v));
            $insert[$k] = stripslashes($v);
        }

        if (!$this->_db->insert($this->_tbl, $insert)) {
            return false;
        }

        $this->$key = $this->_db->insert_id;
        return true;
    }

    public function updateObject($nulls = false){
        $fields = array();
        $where = array();

        if (is_string($this->_tbl_keys)) {
            $key = array($this->_tbl_keys);
        }

        if (is_object($this->_tbl_keys)) {
            $key = (array) $this->_tbl_keys;
        }

        // Iterate over the object variables to build the query fields/value pairs.
        foreach (get_object_vars($this) as $k => $v) {
            // Only process scalars that are not internal fields.
            if (is_array($v) || is_object($v) || $k[0] == '_') {
                continue;
            }

            // Set the primary key to the WHERE clause instead of a field to update.
            if (in_array($k, $this->_tbl_keys)) {
                $where[$k] = $this->_db->_real_escape($v);
                //$where[] = '`'.$k.'`' . '=' . '\'' . $this->_db->_real_escape($v) . '\'';
                continue;
            }

            // Prepare and sanitize the fields and values for the database query.
            if ($v === null) {
                // If the value is null and we want to update nulls then set it.
                if ($nulls) {
                    $val = 'NULL';
                }
                // If the value is null and we do not want to update nulls then ignore this field.
                else {
                    continue;
                }
            }
            // The field is not null so we prep it for update.
            else {
                //$val = esc_sql($v);
                //$val = $this->_db->_real_escape($v);
                $val = stripslashes($v);
            }

            // Add the field to be updated.
            $fields[$k] = $val;
            //$fields[$k] =  esc_html(wp_unslash($val));
        }

        // We don't have any fields to update.
        if (empty($fields)){
            return true;
        }

        // Set the query and execute the update.
        if ($this->_db->update($this->_tbl, $fields, $where) !== false){
            return true;
        } else {
            return false;
        }
    }    
    

    public function save($src, $orderingFilter = '', $ignore = '') {
        // Attempt to bind the source to the instance.
        if (!$this->bind($src, $ignore)) {
            return false;
        }

        // Attempt to store the properties to the database table.
        if (!$this->store()) {
            return false;
        }

        // Set the error to empty and return true.
        $this->setError('');

        return true;
    }

    public function delete($pk = null) {
        // Initialise variables.
        $k  = $this->_tbl_key;
        $pk = (is_null($pk)) ? $this->$k : $pk;

        // If no primary key is given, return false.
        if ($pk === null) {
            $e = new Exception('No Primary key found');
            $this->setError($e);

            return false;
        }

        // Delete the row by primary key.
        $where[$this->_tbl_key] = esc_sql($pk);
        //$query = 'DELETE FROM '.$this->_tbl.' WHERE '.$this->_tbl_key . ' = ' . esc_sql($pk);
        return $this->_db->delete($this->_tbl, $where);
    }


	public function hit($pk = null) {
        global $wpdb;
        if (!property_exists($this, 'hits')) {
            return true;
        }

        if (is_null($pk)) {
            $pk = array();

            foreach ($this->_tbl_keys AS $key) {
                $pk[$key] = $this->$key;
            }
        } elseif (!is_array($pk)) {
            $pk = array($this->_tbl_key => $pk);
        }

        foreach ($this->_tbl_keys AS $key) {
            $pk[$key] = is_null($pk[$key]) ? $this->$key : $pk[$key];

            if ($pk[$key] === null) {
                throw new UnexpectedValueException('Null primary key not allowed.');
            }
        }

        $k = $this->_tbl_keys[0];
        $query = "UPDATE $this->_tbl SET `hits` = (`hits` + 1) WHERE `$k` = '$pk[$k]'";
        $wpdb->query($query);
        $this->hits++;

        return true;
    }

    public function getNextOrder($where = '') {
        // If there is no ordering field set an error and return false.
        if (!property_exists($this, 'ordering')) {
            $e = _WOP_SHOP_ERROR_SAVE_DATABASE;
            $this->setError($e);
            return false;
        }

        // Get the largest ordering value for a given where clause
        global $wpdb;
        $query = 'SELECT MAX(ordering) FROM '.$this->_tbl;
        if ($where) {
            $query.= ' WHERE '.$where;
        }
//        if (!$this->_db->get_var($query)) {
//            $wpdb->show_errors();
//            $e = $wpdb->print_error();
//            addMessage($e, 'error');
//            return '1';
//        }
        $max = (int)$this->_db->get_var($query);
        // Return the largest ordering value + 1.
        return ($max + 1);
    }
    
    public function getBuildQueryListProductDefaultResult($adfields=array()){
        $config = Factory::getConfig();
		if (count($adfields)>0) $adquery = ",".implode(', ',$adfields); else $adquery = '';
        return "prod.product_id, pr_cat.category_id, prod.`name_".$config->cur_lang."` as name, prod.`short_description_".$config->cur_lang."` as short_description, prod.product_ean, prod.image, prod.product_price, prod.currency_id, prod.product_tax_id as tax_id, prod.product_old_price, prod.product_weight, prod.average_rating, prod.reviews_count, prod.hits, prod.weight_volume_units, prod.basic_price_unit_id, prod.label_id, prod.product_manufacturer_id, prod.min_price, prod.product_quantity, prod.different_prices".$adquery;
    }
    
    public function getBuildQueryListProduct($type, $restype, &$filters, &$adv_query, &$adv_from, &$adv_result){
        $config = Factory::getConfig();
//        $user = Factory::getUser();
        $originaladvres = $adv_result;
        
//        $groups = implode(',', $user->getAuthorisedViewLevels());
//        if ($type=="category"){
//            $adv_query .=' AND prod.access IN ('.$groups.')';
//        }else{
//            $adv_query .=' AND prod.access IN ('.$groups.') AND cat.access IN ('.$groups.')';
//        }
        
        if ($config->show_delivery_time){            
            $adv_result .= ", prod.delivery_times_id";
        }        
        if ($config->admin_show_product_extra_field){
            $adv_result .= getQueryListProductsExtraFields();
        }        
        if ($config->product_list_show_vendor){
            $adv_result .= ", prod.vendor_id";
        }        
        if ($config->hide_product_not_avaible_stock){
            $adv_query .= " AND prod.product_quantity > 0";
        }
        if (isset($filters['categorys']) && $type!="category" && is_array($filters['categorys']) && count($filters['categorys'])){
            $adv_query .= " AND cat.category_id in (".implode(",",$filters['categorys']).")";
        }
        if (isset($filters['manufacturers']) && $type!="manufacturer" && is_array($filters['manufacturers']) && count($filters['manufacturers'])){
            $adv_query .= " AND prod.product_manufacturer_id in (".implode(",",$filters['manufacturers']).")";
        }        
        if (isset($filters['labels']) && is_array($filters['labels']) && count($filters['labels'])){
            $adv_query .= " AND prod.label_id in (".implode(",",$filters['labels']).")";
        }
        if (isset($filters['vendors']) && $type!="vendor" && is_array($filters['vendors']) && count($filters['vendors'])){
            $adv_query .= " AND prod.vendor_id in (".implode(",",$filters['vendors']).")";
        }
        if (isset($filters['freeatributes']) && $type=="products"){
            $adv_from .= "LEFT JOIN `".$this->_db->prefix."wshop_products_free_attr` AS freeattr ON prod.product_id = freeattr.product_id ";
            $adv_query .= " AND freeattr.attr_id = ".$filters['freeatributes']; 
        }
        if (isset($filters['extra_fields']) && is_array($filters['extra_fields'])){
            foreach($filters['extra_fields'] as $f_id=>$vals){
                if (is_array($vals) && count($vals)){
                    $tmp = array();
                    foreach($vals as $val_id){
                        $tmp[] = " find_in_set('".$val_id."', prod.`extra_field_".$f_id."`) ";
                    }
                    $mchfilterlogic = 'OR';
                    if ($config->mchfilterlogic_and[$f_id]) $mchfilterlogic = 'AND';
                    $_tmp_adv_query = implode(' '.$mchfilterlogic.' ', $tmp);
                    $adv_query .= " AND (".$_tmp_adv_query.")";
                }elseif(is_string($vals) && $vals!=""){
                    $adv_query .= " AND prod.`extra_field_".$f_id."`='".esc_sql($vals)."'";
                }
            }
        }
        
        $this->getBuildQueryListProductFilterPrice($filters, $adv_query, $adv_from);
        
        if ($config->product_list_show_qty_stock){
            $adv_result .= ", prod.unlimited";
        }
        
        if ($restype=="count"){
            $adv_result = $originaladvres;
        }    
    }
    
    public function getBuildQueryListProductFilterPrice($filters, &$adv_query, &$adv_from){
        if (isset($filters['price_from'])){
            $price_from = getCorrectedPriceForQueryFilter($filters['price_from']);
        }else{
            $price_from = 0;
        }
        if (isset($filters['price_to'])){
            $price_to = getCorrectedPriceForQueryFilter($filters['price_to']);
        }else{
            $price_to = 0;
        }        
        if (!$price_from && !$price_to) return 0;
        
        $config = Factory::getConfig();
        $userShop = Factory::getUserShop();
        $multyCurrency = count(Factory::getAllCurrency());
        if ($userShop->percent_discount){
            $price_part = 1-$userShop->percent_discount/100;
        }else{
            $price_part = 1;
        }
        
        $adv_query2 = "";
        $adv_from2 = "";
        
        if ($multyCurrency > 1){
            $adv_from2 .= " LEFT JOIN `".$this->_db->prefix."wshop_currencies` AS cr USING (currency_id) ";
            if ($price_to){
                if ($config->product_list_show_min_price){
                    $adv_query2 .= " AND (( prod.product_price*$price_part / cr.currency_value )<=".$price_to." OR ( prod.min_price*$price_part / cr.currency_value)<=" . $price_to." )";
                }else{
                    $adv_query2 .= " AND ( prod.product_price*$price_part / cr.currency_value ) <= ".$price_to;
                }
            } 
            
            if ($price_from){
                if ($config->product_list_show_min_price){
                    $adv_query2 .= " AND (( prod.product_price*$price_part / cr.currency_value ) >= ".$price_from." OR ( prod.min_price*$price_part / cr.currency_value ) >= " . $price_from." )";
                }else{
                    $adv_query2 .= " AND ( prod.product_price*$price_part / cr.currency_value ) >= ".$price_from;
                }
            }
        }else{
            if ($price_to){
                if ($config->product_list_show_min_price){
                    $adv_query2 .= " AND (prod.product_price*$price_part <=".$price_to." OR prod.min_price*$price_part <=" . $price_to." )";
                }else{
                    $adv_query2 .= " AND prod.product_price*$price_part <= ".$price_to;
                }
            }
            if ($price_from){
                if ($config->product_list_show_min_price){
                    $adv_query2 .= " AND (prod.product_price*$price_part >= ".$price_from." OR prod.min_price*$price_part >= " . $price_from." )";
                }else{
                    $adv_query2 .= " AND prod.product_price*$price_part >= ".$price_from;
                }
            }
        }
        
        do_action_ref_array('onBuildQueryListProductFilterPrice', array($filters, &$adv_query, &$adv_from, &$adv_query2, &$adv_from2));
        
        $adv_query .= $adv_query2;
        $adv_from .= $adv_from2;
    }
    
    public function getBuildQueryOrderListProduct($order, $orderby, &$adv_from){
        $order_query = "";
        if (!$order) return $order_query;
        $order_original = $order;
        $config = Factory::getConfig();
        $multyCurrency = count(Factory::getAllCurrency());
        if ($multyCurrency>1 && $order=="prod.product_price"){
            if (strpos($adv_from,"wshop_currencies")===false){
                $adv_from .= " LEFT JOIN `".$this->_db->prefix."wshop_currencies` AS cr USING (currency_id) ";
            }
            if ($config->product_list_show_min_price){
                $order = "prod.min_price/cr.currency_value";
            }else{
                $order = "prod.product_price/cr.currency_value";
            }
        }
        if ($order=="prod.product_price" && $config->product_list_show_min_price){
            $order = "prod.min_price";
        }
        $order_query = " ORDER BY ".$order;
        if ($orderby){
            $order_query .= " ".$orderby;
        }
        
        do_action_ref_array('onBuildQueryOrderListProduct', array($order, $orderby, &$adv_from, &$order_query, $order_original));
        
        return $order_query;
    }
    
    public function getBuildQueryListProductSimpleList($type, $array_categories, &$filters, &$adv_query, &$adv_from, &$adv_result){
        $config = Factory::getConfig();
        $user = Factory::getUser();
                
        if (is_array($array_categories) && count($array_categories)){
            $adv_query .= " AND pr_cat.category_id IN (".implode(",", $array_categories).")";
        }        
//        $groups = implode(',', $user->getAuthorisedViewLevels());
//        $adv_query .=' AND prod.access IN ('.$groups.') AND cat.access IN ('.$groups.')';
        
        if ($config->hide_product_not_avaible_stock){
            $adv_query .= " AND prod.product_quantity > 0";
        }
        if ($config->show_delivery_time){
            $adv_result .= ", prod.delivery_times_id";
        }
        if ($config->admin_show_product_extra_field){
            $adv_result .= getQueryListProductsExtraFields();
        }
        if ($config->product_list_show_vendor){
            $adv_result .= ", prod.vendor_id";
        }
        if ($config->product_list_show_qty_stock){
            $adv_result .= ", prod.unlimited";
        }

        if (isset($filters['categorys']) && is_array($filters['categorys']) && count($filters['categorys'])){
            $adv_query .= " AND cat.category_id in (".implode(",",$filters['categorys']).")";
        }
        if (isset($filters['manufacturers']) && is_array($filters['manufacturers']) && count($filters['manufacturers'])){
            $adv_query .= " AND prod.product_manufacturer_id in (".implode(",",$filters['manufacturers']).")";
        }        
        if (isset($filters['labels']) && is_array($filters['labels']) && count($filters['labels'])){
            $adv_query .= " AND prod.label_id in (".implode(",",$filters['labels']).")";
        }
        if (isset($filters['vendors']) && is_array($filters['vendors']) && count($filters['vendors'])){
            $adv_query .= " AND prod.vendor_id in (".implode(",",$filters['vendors']).")";
        }        
        if (isset($filters['extra_fields']) && is_array($filters['extra_fields'])){
            foreach($filters['extra_fields'] as $f_id=>$vals){
                if (is_array($vals) && count($vals)){
                    $tmp = array();
                    foreach($vals as $val_id){
                        $tmp[] = " find_in_set('".$val_id."', prod.`extra_field_".$f_id."`) ";
                    }
                    $mchfilterlogic = 'OR';
                    if ($config->mchfilterlogic_and[$f_id]) $mchfilterlogic = 'AND';
                    $_tmp_adv_query = implode(' '.$mchfilterlogic.' ', $tmp);
                    $adv_query .= " AND (".$_tmp_adv_query.")";
                }elseif(is_string($vals) && $vals!=""){
                    $adv_query .= " AND prod.`extra_field_".$f_id."`='". esc_sql($vals)."'";
                }
            }
        }
        
        $this->getBuildQueryListProductFilterPrice($filters, $adv_query, $adv_from);
    }
	
	public function move($delta, $where = '')
	{
		// If the change is none, do nothing.
		if (empty($delta))
		{
			return true;
		}
		$_where = '';
		$order = '';
		$row   = null;
		$pk = $this->_tbl_key;
		$query = 'SELECT '.$this->_tbl_key . ', ordering FROM '.$this->_tbl;


		// If the movement delta is negative move the row up.
		if ($delta < 0)
		{
			$_where = ' WHERE ordering < ' . (int) $this->ordering;
			$order = ' ORDER BY ordering DESC';
		}
		// If the movement delta is positive move the row down.
		elseif ($delta > 0)
		{
			$_where = ' WHERE ordering > ' . (int) $this->ordering;
			$order = ' ORDER BY ordering ASC';
		}

		// Add the custom WHERE clause if set.
		if ($where)
		{
			$_where .= ' AND '.$where;
		}

		// Select the first row with the criteria.
		$query .= $_where.$order.' LIMIT 0, 1';

		$row = $this->_db->get_row($query);

		// If a row is found, move the item.
		if (!empty($row))
		{
			// Update the ordering field for this instance to the row's ordering value.
			$query = 'UPDATE '.$this->_tbl.' SET ordering = '.(int) $row->ordering . ' WHERE '.$pk.'='.$this->$pk;
			$this->_db->query($query);

			// Update the ordering field for the row to this instance's ordering value.		
			$query = 'UPDATE '.$this->_tbl.' SET ordering = '.(int) $this->ordering . ' WHERE '.$pk.'='.$row->$pk;
			$this->_db->query($query);
		}
		else
		{
			// Update the ordering field for this instance.
			$query = 'UPDATE '.$this->_tbl.' SET ordering = '.(int) $row->ordering . ' WHERE '.$pk.'='.$this->$pk;
			$this->_db->query($query);
		}

		return true;
	}
	
	public function reorder($where = '')
	{
		$_where = ' WHERE ordering >= 0';
		$order = '';
		// Get the primary keys and ordering values for the selection.
		
		$pk = $this->_tbl_key;
		$query = 'SELECT '.$this->_tbl_key . ', ordering FROM '.$this->_tbl;

		// Setup the extra where and ordering clause data.
		if ($where)
		{
			$_where .= ' AND '.$where;
		}
		$query .= $_where.' ORDER BY ordering';


		$rows = $this->_db->get_results($query);

		// Compact the ordering values.
		foreach ($rows as $i => $row)
		{
			// Make sure the ordering is a positive integer.
			if ($row->ordering >= 0)
			{
				// Only update rows that are necessary.
				if ($row->ordering != $i + 1)
				{
					// Update the row ordering field.
					
					$query = 'UPDATE '.$this->_tbl.' SET ordering = '. ($i + 1).' WHERE '.$pk.'='.$row->$pk;
					$this->_db->query($query);
				}
			}
		}

		return true;
	}	
}