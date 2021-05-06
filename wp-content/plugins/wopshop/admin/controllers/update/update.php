<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class UpdateWshopAdminController extends WshopAdminController {
    function __construct() {
        parent::__construct();
    }
    function demos(){
        $old_path['demo_products']  =   WOPSHOP_PLUGIN_DIR.'demos/files/demo_products';
        $old_path['img_attributes'] =   WOPSHOP_PLUGIN_DIR.'demos/files/img_attributes';
        $old_path['img_categories'] =   WOPSHOP_PLUGIN_DIR.'demos/files/img_categories';
        $old_path['img_products']   =   WOPSHOP_PLUGIN_DIR.'demos/files/img_products';

        $new_path['demo_products']  =   WOPSHOP_PLUGIN_DIR.'files/demo_products';
        $new_path['img_attributes'] =   WOPSHOP_PLUGIN_DIR.'files/img_attributes';
        $new_path['img_categories'] =   WOPSHOP_PLUGIN_DIR.'files/img_categories';
        $new_path['img_products']   =   WOPSHOP_PLUGIN_DIR.'files/img_products';

        foreach($old_path as $key=>$value){
            if(!is_dir($value)) mkdir($value);
            $dir = opendir($value);
            while($file = readdir($dir)){
                if(is_file($value."/".$file)){
                    copy($value."/".$file, $new_path[$key]."/".$file);
                }
            }
        }

        global $wpdb;

        $config = Factory::getConfig();
        $tag = $lang = $config->cur_lang;

        $lines = file(WOPSHOP_PLUGIN_DIR."demos/demo.sql");
        $fullline = implode(" ", $lines);
        $queryes = splitSql($fullline);
        foreach($queryes as $query){
            if (trim($query)!=''){
                $query = str_replace("#__", $wpdb->prefix, $query);
                $query1 = str_replace("en-GB", $tag, $query);
                $wpdb->query($query);
//                $query1 = str_replace("en-GB", $tag, $query);   
//                $wpdb->query($query1);
//                echo $query.'<br>'.$query1;
            }
        }
        $this->setRedirect('admin.php?page=products', _WOP_SHOP_STORE);
        //$lang_model = $this->getModel("languages");
        /*$fields = $lang_model->_LoadTableFields();
        foreach($fields as $table_name_end=>$table){
        }*/
        //echo "<pre>"; print_r($fields); echo "</pre>"; die();
    }
    
    public function display() {
        $view = $this->getView('update');
        $view->display();
    }
    
    public function update(){
        global $wp_filesystem;
        WP_Filesystem();
        
        $installtype = Request::getVar('installtype');
        $back = Request::getVar('back');

        if (!extension_loaded('zlib')){
            $this->setRedirect("admin.php?page=update", _WOP_SHOP_INSTALLER_MSG_INSTALL_WARNINSTALLZLIB, 'error');
            return false;
        }
        
        if ($installtype == 'package'){
            $userfile = $_FILES['install_package'];
            
            if (!function_exists('wp_handle_upload')) {
                require_once(ABSPATH . 'wp-admin/includes/file.php');
            }

            if (!(bool)ini_get('file_uploads')) {
                $this->setRedirect("admin.php?page=update", _WOP_SHOP_INSTALLER_MSG_INSTALL_WARNINSTALLFILE, 'error');
                return false;
            }
            
            if (!is_array($userfile)) {
                $this->setRedirect("admin.php?page=update", _WOP_SHOP_INSTALLER_NO_FILE_SELECTED, 'error');
                return false;
            }
            
            if ($userfile['error'] || $userfile['size'] < 1){
                $this->setRedirect("admin.php?page=update", _WOP_SHOP_INSTALLER_MSG_INSTALL_WARNINSTALLUPLOADERROR, 'error');
                return false;
            }
            
            $extractdir = WOPSHOP_PLUGIN_DIR . 'tmp/' . uniqid('install_') . '/';
            wp_mkdir_p($extractdir);
            $archivename = $extractdir . $userfile['name'];
            if (move_uploaded_file($userfile['tmp_name'], $archivename)) {
                unzip_file($archivename, $extractdir);
                unlink($archivename);
            } else {
                $wp_filesystem->rmdir($extractdir, true);
                $this->setRedirect("admin.php?page=update", _WOP_SHOP_INSTALLER_MSG_INSTALL_WARNINSTALLUPLOADERROR, 'error');
                return false;
            }

            saveToLog("install.log", "\nStart install. File:".$userfile['name']." IP:".$_SERVER['REMOTE_ADDR']." UID:".Factory::getUser()->user_id);
        } elseif ($installtype == 'url') {
            $url = Request::getVar('install_url');
            
            if (!$url) {
                $this->setRedirect("admin.php?page=update", _WOP_SHOP_INSTALLER_MSG_INSTALL_ENTER_A_URL, 'error');
                return false;
            }
            
            $extractdir = WOPSHOP_PLUGIN_DIR . 'tmp/' . uniqid('install_') . '/';
            wp_mkdir_p($extractdir);
            $archivename = $extractdir . 'archive.zip';
            $response = wp_safe_remote_get($url, array('timeout' => 300, 'stream' => true, 'filename' => $archivename));

            if (is_wp_error($response)) {
                $wp_filesystem->rmdir($extractdir, true);
                $this->setRedirect("admin.php?page=update", _WOP_SHOP_INSTALLER_MSG_INSTALL_INVALID_URL, 'error');
                return false;
            }

            if (wp_remote_retrieve_response_code($response) !== 200){
                $wp_filesystem->rmdir($extractdir, true);
                $this->setRedirect("admin.php?page=update", _WOP_SHOP_INSTALLER_MSG_INSTALL_INVALID_URL, 'error');
                return false;
            } 

            unzip_file($archivename, $extractdir);
            unlink($archivename);
            
            saveToLog("install.log", "\nStart install. URL:".$url." IP:".$_SERVER['REMOTE_ADDR']." UID:".Factory::getUser()->user_id);
        } else {
            $this->setRedirect("admin.php?page=update", _WOP_SHOP_INSTALLER_NO_FILE_SELECTED, 'error');
            return false;
        }

        if (!$this->copyFiles($extractdir)){
            saveToLog("install.log", 'Error copy files');
            $this->setRedirect("admin.php?page=update", _WOP_SHOP_INSTALLER_ERROR_COPY_FILES, 'error');
            return false;
        }
        
        if (file_exists($extractdir . "/update.php")) {
            include($extractdir . "/update.php");
        }

        do_action_ref_array('onAfterUpdateShop', array($extractdir));
        
        $wp_filesystem->rmdir($extractdir, true);
        
        $session = Factory::getSession();
        $checkedlanguage = array();
        $session->set("wshop_checked_language", $checkedlanguage); 
        
        if ($back == ''){
            $this->setRedirect('admin.php?page=update', _WOP_SHOP_COMPLETED);
        } else {
            $this->setRedirect($back, _WOP_SHOP_COMPLETED);
        }
    }
    
    private function copyFiles($startdir, $subdir = ""){
        global $wp_filesystem;
        $rootPath = get_home_path();
        
        if ($subdir != "" && !file_exists($rootPath.$subdir)){
            @mkdir($rootPath.$subdir, 0755);
        }
        
        $directoryList = $wp_filesystem->dirlist($startdir.$subdir);
        
        foreach ($directoryList as $fileName => $fileData){
            if ($fileData['type'] == 'f'){
                // copy files
                
                if ($subdir == "" && $fileName == "update.php"){
                    continue;
                }
                
                if ($subdir == ""){
                    $fileinfo = pathinfo($fileName);
                    if (strtolower($fileinfo['extension']) == 'xml'){
                        return 0;
                    }
                }
                
                if (@copy($startdir.$subdir."/".$fileName, $rootPath.$subdir."/".$fileName)){
                    saveToLog("install.log", "Copy file: ".$subdir."/".$fileName);
                } else {
                    addMessage("Copy file: ".$subdir."/".$fileName." ERROR");
                    saveToLog("install.log", "Copy file: ".$subdir."/".$fileName." ERROR");
                }
            } else {
                //copy directories
                $this->copyFiles($startdir, $subdir . "/" . $fileName);
            }
        }

        return 1;
    }
}