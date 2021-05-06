<?php
class LanguagesWshopAdminModel extends WshopAdminModel {
    public $string;
 
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix.'wshop_languages';

        parent::__construct();
    }

    function getAllLanguages($publish = 1) {
        global $wpdb;
        $where_add = $publish ? "where `publish`='1'": "";
        $query = "SELECT * FROM `".$wpdb->prefix."wshop_languages` ".$where_add." order by `ordering`";
		extract(ws_add_trigger(get_defined_vars(), "before"));
	$rows = $wpdb->get_results($query, OBJECT);
        $rowssort = array();

        foreach($rows as $k=>$v){
            $rows[$k]->lang = substr($v->language, 0, 2);
            if ($config->cur_lang == $v->language) $rowssort[] = $rows[$k];
        }
        foreach($rows as $k=>$v){
            if (isset($rowssort[0]) && $rowssort[0]->language==$v->language) continue;
            $rowssort[] = $v;            
        }
        unset($rows);
        return $rowssort;
    }
    function idLanguage() {
        global $wpdb;
        $query = "SELECT id FROM `".$wpdb->prefix."wshop_languages` WHERE  favorite = '1'";
	return $wpdb->get_var($query);
    }

    function getAllLanguagesCount($search = null, $publish=''){
        global $wpdb;

        $where = 'WHERE `publish` != "-1" ';
        if($publish != '') $where.= ' AND `publish` = '.$publish;
        if($search){
            $where.= " AND `name` LIKE '%".$search."%'";
        }
        $languages = $wpdb->get_var( "SELECT COUNT(*) FROM ".$this->table_name." ".$where);
        //$wpdb->show_errors();
        //$wpdb->print_error(); 
        return $languages;
    }

    function getCountLanguages($publish = ''){
        global $wpdb;

        $where = 'WHERE `publish` != "-1" ';

        if($publish != '') $where.= ' AND `publish` = '.$publish;

        $count = $wpdb->get_var( "SELECT COUNT(*) FROM ".$this->table_name." ".$where);

        //$wpdb->show_errors();
        //$wpdb->print_error(); 
        return $count;
    }

    function LanguagesActionPublish($action = '1', $langs = array()){
        global $wpdb;
        if(is_array($langs)){
            foreach($langs as $index=>$l){
                $favorite = $wpdb->get_var( "SELECT favorite FROM ".$this->table_name." WHERE id = ".esc_sql($l));
                if($favorite and $action != 1){
                    addMessage(_WOP_SHOP_ERROR_UNPUBLISH_LANGUAGE_FAVORITE, 'error'); 
                    return;
                }
                $favorite_copy = $wpdb->get_var( "SELECT favorite_copy FROM ".$this->table_name." WHERE id = ".esc_sql($l));
                if($favorite_copy  and $action != 1){
                    addMessage(_WOP_SHOP_ERROR_UNPUBLISH_LANGUAGE_FAVORITE, 'error'); 
                    return;
                }
                $wpdb->update( $this->table_name, array( 'publish' => esc_sql($action) ), array( 'id' => esc_sql($l) ), array( '%s', '%d' ), array( '%d' ) );
                addMessage(_WOP_SHOP_MESSAGE_SAVEOK);
                //$wpdb->show_errors();
                //$wpdb->print_error();
            }
            return;
        }
        addMessage(_WOP_SHOP_ERROR_BIND, 'error');
    }

    function LanguagesActionFavoriteCopy($lang = NULL){
        global $wpdb;

        if($lang > 0){
            $publish = $wpdb->get_var( "SELECT publish FROM ".$this->table_name." WHERE id = ".esc_sql($lang));
            if($publish!=1){
                addMessage(_WOP_SHOP_ERROR_SET_LANGUAGE_FAVORITE, 'error');
                return;
            }
            $wpdb->query("UPDATE `".$this->table_name."` SET `favorite_copy` = '0'");
            $wpdb->update( $this->table_name, array( 'favorite_copy' => 1 ), array( 'id' => esc_sql($lang), 'publish' => '1' ), array( '%s', '%d' ), array( '%d' ) );
            //$wpdb->show_errors();
            //$wpdb->print_error();
            addMessage(_WOP_SHOP_ACTION_LANGUAGE_SETFAVORITE);
            return;
        }
        addMessage(_WOP_SHOP_ERROR_BIND, 'error');
    }
    function LanguagesActionFavorite($lang = NULL){
        global $wpdb;
        if($lang > 0){
            $publish = $wpdb->get_var( "SELECT publish FROM ".$this->table_name." WHERE id = ".esc_sql($lang));
            if($publish!=1){
                addMessage(_WOP_SHOP_ERROR_SET_LANGUAGE_FAVORITE, 'error');
                return;
            }
            $wpdb->query("UPDATE `".$this->table_name."` SET `favorite` = '0'");
            $wpdb->update( $this->table_name, array( 'favorite' => 1 ), array( 'id' => esc_sql($lang), 'publish' => '1' ), array( '%s', '%d' ), array( '%d' ) );
            //$wpdb->show_errors();
            //$wpdb->print_error();
            addMessage(_WOP_SHOP_ACTION_LANGUAGE_SETFAVORITE);
            return;
        }
        addMessage(_WOP_SHOP_ERROR_BIND, 'error');
    }
    function getLanguage($lang_id){
        global $wpdb;
        return $wpdb->get_row( "SELECT * FROM ".$this->table_name." WHERE id = ".esc_sql($lang_id), OBJECT);
    }
    function _LoadTableFields(){
        $f=array();
        $fields = array();
        $f[] = array("name","varchar(255) NOT NULL");
        $fields["countries"] = $f;

        $f=array();
        $f[] = array("name","varchar(100) NOT NULL");
        $f[] = array("description","text NOT NULL");
        $fields["shipping_method"] = $f;

        $f=array();
        $f[] = array("name","varchar(100) NOT NULL");
        $f[] = array("description","text NOT NULL");
        $fields["payment_method"] = $f;

        $f=array();
        $f[] = array("name","varchar(100) NOT NULL");
        $fields["order_status"] = $f;

        $f=array();
        $f[] = array("name","varchar(255) NOT NULL");
        $fields["delivery_times"] = $f;

        $f=array();
        $f[] = array("name","varchar(255) NOT NULL");
        $fields["unit"] = $f;        

        $f=array();
        $f[] = array("name","varchar(255) NOT NULL");
        $f[] = array("description","text NOT NULL");
        $fields["attr"] = $f;

        $f=array();
        $f[] = array("name","varchar(255) NOT NULL");
        $fields["attr_values"] = $f;

        $f=array();
        $f[] = array("name","varchar(255) NOT NULL");
        $fields["attr_groups"] = $f;

        $f=array();
        $f[] = array("name","varchar(255) NOT NULL");
        $f[] = array("description","text NOT NULL");
        $fields["products_extra_fields"] = $f;

        $f=array();
        $f[] = array("name","varchar(255) NOT NULL");
        $fields["products_extra_field_values"] = $f;

        $f=array();
        $f[] = array("name","varchar(255) NOT NULL");
        $fields["products_extra_field_groups"] = $f;

        $f=array();
        $f[] = array("name","varchar(255) NOT NULL");
		$f[] = array("description","text NOT NULL");
        $fields["free_attr"] = $f;

        $f=array();
        $f[] = array("title","varchar(255) NOT NULL");
        $f[] = array("keyword","text NOT NULL");
        $f[] = array("description","text NOT NULL");
        $fields["config_seo"] = $f;

        $f=array();
        $f[] = array("text","text NOT NULL");
        $fields["config_statictext"] = $f;

        $f=array();
        $f[] = array("name","varchar(255) NOT NULL");
        $fields["product_labels"] = $f;

        $f=array();
        $f[] = array("name","varchar(255) NOT NULL");
        $f[] = array("alias","varchar(255) NOT NULL");
        $f[] = array("short_description","text NOT NULL");
        $f[] = array("description","text NOT NULL");
        $f[] = array("meta_title","varchar(255) NOT NULL");
        $f[] = array("meta_description","text NOT NULL");
        $f[] = array("meta_keyword","text NOT NULL");
        $fields["manufacturers"] = $f;

        $f=array();
        $f[] = array("name","varchar(255) NOT NULL");
        $f[] = array("alias","varchar(255) NOT NULL");
        $f[] = array("short_description","text NOT NULL");
        $f[] = array("description","text NOT NULL");
        $f[] = array("meta_title","varchar(255) NOT NULL");
        $f[] = array("meta_description","text NOT NULL");
        $f[] = array("meta_keyword","text NOT NULL");
        $fields["categories"] = $f;

        $f=array();
        $f[] = array("name","varchar(255) NOT NULL");
        $f[] = array("alias","varchar(255) NOT NULL");
        $f[] = array("short_description","text NOT NULL");
        $f[] = array("description","text NOT NULL");
        $f[] = array("meta_title","varchar(255) NOT NULL");
        $f[] = array("meta_description","text NOT NULL");
        $f[] = array("meta_keyword","text NOT NULL");
        $fields["products"] = $f;

        $f=array();
        $f[] = array("name","varchar(255) NOT NULL");
        $f[] = array("description","text NOT NULL");
        $fields["usergroups"] = $f;
        return $fields;
    }
}