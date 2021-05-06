<?php
/**
* @version      1.0.0 01.06.2016
* @author       MAXXmarketing GmbH
* @package      WOPshop
* @copyright    Copyright (C) 2010 http://www.wop-agentur.com. All rights reserved.
* @license      GNU/GPL
* @class        WshopMultiLang
*/

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class WshopMultiLang {
    
    private $table = null;
    private $lang = null;
    private $tableFields = array();
    
    public function __construct(){
        $this->loadTableFields();        
    }
    
    function setTable($table){
        $this->table = $table;
    }
    
    function setLang($lang){
        $this->lang = $lang;
    }
    
    function get($field){
        return $field."_".$this->lang;
    }
    
    function getListFields(){
        $array = array();
        if ($this->table){
            $array = $this->tableFields[$this->table];    
        }
        return $array;
    }
    
    /**
    * get build guery multi language fields
    * @return strin query ml fiels
    */
    function getBuildQuery(){
        $query = array();
        $fields = $this->getListFields();
        foreach($fields as $field){
            $query[] = " `".$this->get($field[0])."` as ".$field[0];
        }
    
		return implode(", ",$query);
    }
    
    public function addNewFieldLandInTables($lang, $defaultLang = ""){
		global $wpdb;
        $finish = 1;
        
        foreach ($this->tableFields as $table_name_end => $table){
            $table_name = $wpdb->prefix."wshop_".$table_name_end;
            
            $list_name_field = array();
            $query = 'SHOW FIELDS FROM `'.$table_name.'`';
            $fields = $wpdb->get_results($query);

            foreach($fields as $field){
                $list_name_field[] = $field->Field;
            }

            //filter existent field
            foreach($table as $k=>$field){
                if (in_array($field[0]."_".$lang, $list_name_field)){
                    unset($table[$k]);
                }
            }

            $sql_array_add_field = array();
            foreach($table as $field){
                $name = $field[0]."_".$lang;
                $sql_array_add_field[] = "ADD `".$name."` ".$field[1];
            }
            
            $sql_array_update_field = array();
            foreach($table as $field){
                $name = $field[0]."_".$lang;
                $name2 = $field[0]."_".$defaultLang;
                if (in_array($name2, $list_name_field)){
                    $sql_array_update_field[] = " `".$name."` = `".$name2."`";
                }
            }
            
            if (count($sql_array_add_field)){                
                $query = "ALTER TABLE `".$table_name."` ".implode(", ", $sql_array_add_field);
                if ($wpdb->query($query) === false){
                    addMessage("Error install new language:<br>".$wpdb->print_error(), 'error');
                    $finish = 0;
                }
                               
                //copy information
                if ($defaultLang != "" && count($sql_array_update_field)){
                    $query = "UPDATE `".$table_name."` SET ".implode(", ", $sql_array_update_field);
                    if ($wpdb->query($query) === false){
                        addMessage("Error copy new language:<br>".$wpdb->print_error(), 'error');
                        $finish = 0;
                    }
                }
            }
        }
        
        return $finish;
    }
    
    /**
    * Static list Table and Fields
    */
    private function loadTableFields(){
        $f=array();
        $f[] = array("name","varchar(255) NOT NULL");
        $this->tableFields["countries"] = $f;
        
        $f=array();
        $f[] = array("name","varchar(100) NOT NULL");
        $f[] = array("description","text NOT NULL");
        $this->tableFields["shipping_method"] = $f;
        
        $f=array();
        $f[] = array("name","varchar(100) NOT NULL");
        $f[] = array("description","text NOT NULL");
        $this->tableFields["payment_method"] = $f;
        
        $f=array();
        $f[] = array("name","varchar(100) NOT NULL");
        $this->tableFields["order_status"] = $f;
        
        $f=array();
        $f[] = array("name","varchar(255) NOT NULL");
        $this->tableFields["delivery_times"] = $f;
        
        $f=array();
        $f[] = array("name","varchar(255) NOT NULL");
        $this->tableFields["unit"] = $f;        
        
        $f=array();
        $f[] = array("name","varchar(255) NOT NULL");
		$f[] = array("description","text NOT NULL");
        $this->tableFields["attr"] = $f;
        
        $f=array();
        $f[] = array("name","varchar(255) NOT NULL");
        $this->tableFields["attr_values"] = $f;
        
        $f=array();
        $f[] = array("name","varchar(255) NOT NULL");
        $this->tableFields["attr_groups"] = $f;
        
        $f=array();
        $f[] = array("name","varchar(255) NOT NULL");
		$f[] = array("description","text NOT NULL");
        $this->tableFields["products_extra_fields"] = $f;
        
        $f=array();
        $f[] = array("name","varchar(255) NOT NULL");
        $this->tableFields["products_extra_field_values"] = $f;
        
        $f=array();
        $f[] = array("name","varchar(255) NOT NULL");
        $this->tableFields["products_extra_field_groups"] = $f;
        
        $f=array();
        $f[] = array("name","varchar(255) NOT NULL");
		$f[] = array("description","text NOT NULL");
        $this->tableFields["free_attr"] = $f;
        
        $f=array();
        $f[] = array("title","varchar(255) NOT NULL");
        $f[] = array("keyword","text NOT NULL");
        $f[] = array("description","text NOT NULL");
        $this->tableFields["config_seo"] = $f;
        
        $f=array();
        $f[] = array("text","text NOT NULL");
        $this->tableFields["config_statictext"] = $f;
        
        $f=array();
        $f[] = array("name","varchar(255) NOT NULL");
		$this->tableFields["product_labels"] = $f;
		
		$f=array();
		$f[] = array("name","varchar(255) NOT NULL");
        $f[] = array("alias","varchar(255) NOT NULL");
        $f[] = array("short_description","text NOT NULL");
        $f[] = array("description","text NOT NULL");
        $f[] = array("meta_title","varchar(255) NOT NULL");
        $f[] = array("meta_description","text NOT NULL");
        $f[] = array("meta_keyword","text NOT NULL");
        $this->tableFields["manufacturers"] = $f;
        
        $f=array();
        $f[] = array("name","varchar(255) NOT NULL");
        $f[] = array("alias","varchar(255) NOT NULL");
        $f[] = array("short_description","text NOT NULL");
        $f[] = array("description","text NOT NULL");
        $f[] = array("meta_title","varchar(255) NOT NULL");
        $f[] = array("meta_description","text NOT NULL");
        $f[] = array("meta_keyword","text NOT NULL");
        $this->tableFields["categories"] = $f;
        
        $f=array();
        $f[] = array("name","varchar(255) NOT NULL");
        $f[] = array("alias","varchar(255) NOT NULL");
        $f[] = array("short_description","text NOT NULL");
        $f[] = array("description","text NOT NULL");
        $f[] = array("meta_title","varchar(255) NOT NULL");
        $f[] = array("meta_description","text NOT NULL");
        $f[] = array("meta_keyword","text NOT NULL");
        $this->tableFields["products"] = $f;
        
        $f=array();
        $f[] = array("name","varchar(255) NOT NULL");
        $f[] = array("description","text NOT NULL");
        $this->tableFields["usergroups"] = $f;

        do_action_ref_array('onLoadMultiLangTableField', array(&$this));
    }
}