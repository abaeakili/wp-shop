<?php
class LanguagesWshopAdminController extends WshopAdminController {
    function __construct() {
        parent::__construct();
    }
   
    function display() {
        $languages = $this->getModel("languages");
        $rows = $languages->getAllLanguages(0);
        $config = Factory::getConfig();

        $actions = array(
            'publish' => _WOP_SHOP_ACTION_PUBLISH,
            'unpublish' => _WOP_SHOP_ACTION_UNPUBLISH,
        );
        $bulk = $languages->getBulkActions($actions);
        $view = $this->getView('languages');
        $view->setLayout('list');
        $view->assign('rows', $rows);
        $view->assign('bulk',$bulk);
        $view->assign('default_front', $config->frontend_lang);
        $view->assign('defaultLanguage', $config->defaultLanguage);
        do_action_ref_array('onBeforeDisplayLanguage', array(&$view));
        $view->display();
    }
    function publish(){
        global $wpdb;
        $config = Factory::getConfig();

        $cid = Request::getVar("rows");
        $defaultLang = $config->cur_lang;
        $lang_model = $this->getModel("languages");
        $fields = $lang_model->_LoadTableFields();

        foreach($cid as $lang_id){
            $language = $lang_model->getLanguage($lang_id);
            $lang = $language->language;
            foreach($fields as $key => $table){
                $table_name = $wpdb->prefix."wshop_".$key;

                $list_name_field = array();
                $query = 'SHOW FIELDS FROM `'.$table_name.'`';
                $fields = $wpdb->get_results($query, OBJECT);

                foreach($fields as $field){
                    $list_name_field[] = $field->Field;
                }

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
                    $query = "ALTER TABLE `".$table_name."` ".implode(", ",$sql_array_add_field);
                    $wpdb->query($query);

                    //copy information
                    if ($defaultLang!="" && count($sql_array_update_field)){
                        $query = "update `".$table_name."` set ".implode(", ",$sql_array_update_field);
                        $wpdb->query($query);
                    }
                }
            }

            $query = "SHOW FIELDS FROM ".$wpdb->prefix.'wshop_languages';
            $rrr = $wpdb->query($query);
        }
        $this->publishLanguage(1);
    }

    function unpublish(){
        $this->publishLanguage(0);
    }

    function publishLanguage($flag) {
        global $wpdb;
        $cid = Request::getVar("rows");
        $languages = $this->getModel("languages");
        $lang_id = $languages->idLanguage();
        foreach ($cid as $key => $value){
            if(!$flag and $value == $lang_id) continue;
            $wpdb->update(
                $wpdb->prefix.'wshop_languages',
                array('publish' => esc_sql($flag)),
                array('id' => $value)
            );
        }
        $this->setRedirect('admin.php?page=options&tab=languages');
    }
    function favorite_copy_save(){
        $lang_id = $_REQUEST['lang_id'];
        $model = $this->getModel('languages');
        $model->LanguagesActionFavoriteCopy($lang_id);
        $this->setRedirect('admin.php?page=options&tab=languages');
    }
    function favorite_save(){
        $lang_id = $_REQUEST['lang_id'];
        $model = $this->getModel('languages');
        $model->LanguagesActionFavorite($lang_id);
        $this->setRedirect('admin.php?page=options&tab=languages');
    }

    
}