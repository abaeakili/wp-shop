<?php
/**
 * Plugin Name: WOPshop
 * Plugin URI: http://www.wop-agentur.com/wopshop/
 * Description: WOPshop - shop plugin
 * Version: 1.4.1
 * Author: MAXXmarketing GmbH
 * Author URI: http://www.wop-agentur.com
 *
 * Open Source License, GNU GPL
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

define ('WSHOP_PLUGIN_BASE_DIR', WP_PLUGIN_DIR . '/' . basename(dirname(__FILE__)), true);

if (!class_exists('Wopshop')) {
    
    /**
     * Main WOPshop Class
     *
     * @class Wopshop
     */
    final class Wopshop {
        
        /**
         * The single instance of the class
         *
         * @var Wopshop
         */
        protected static $_instance = null;
        
        /**
         * Wopshop version.
         *
         * @var string
         */
        public $version = '1.4.1';
        
        /**
         * Application object
         *
         * @var Application
         */
        protected $app = null;
        
        /**
         * Minimum Wordpress version.
         *
         * @var string
         */
        protected $minimumWordpressVersion = '3.6';

        public static function instance() {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            
            return self::$_instance;
        }

        /**
         * WOPshop Constructor.
         */
        public function __construct() {
            register_activation_hook(__FILE__, array($this, 'activate'));
            register_deactivation_hook(__FILE__, array($this, 'deactivate'));

            $this->loadCoreFiles();
            $this->define_constants();
            
            // Register shop routes
            WshopRouter::getInstance()->registerRewriteRules();
            
			$this->loadAddons();
            add_action('init', array($this, 'initialise'));
        }

        public function initialise() {
            $this->app = Factory::getApplication();
            $this->app->initialise();
            $ajax = Request::getInt('ajax', 0);
            
            Factory::initLanguageFile();
            Factory::getConfig();

            if ($this->app->isAdmin()) {
				if (!$ajax){
					$wshopInstaller = new WshopInstaller();
					$wshopInstaller->installNewLanguages();
				}
                
                require_once WOPSHOP_PLUGIN_DIR . 'admin/wshopadmin.php';
                add_action('admin_menu', array($this, 'wopshop_admin_menu'));
                add_action('admin_init', array($this, 'wopshop_admin_init'));
                add_action('admin_head-nav-menus.php', array(&$this, 'add_menu_meta_boxes'));
                add_action('save_post', array('WshopRouter', 'regenerateRewriteRulesForPages'));
                
                //add link to settings page
                $plugin = plugin_basename(__FILE__);
                add_filter("plugin_action_links_".$plugin, array($this, 'plugin_add_settings_link'));
            } else {
                WshopRouter::getInstance()->generatePage();
            }
        }

        /**
         * Define WOPshop Constants.
         */
        private function define_constants() {
            $this->define('WOP_WP_CNT', content_url());
            
            $admin_url = rtrim(admin_url(), '/');
            $this->define('WOP_URL_ADMIN', $admin_url);    
            $this->define('WOPSHOP_PLUGIN_URL', plugin_dir_url(__FILE__));
            $this->define('WOPSHOP_PLUGIN_DIR', plugin_dir_path(__FILE__));
            $this->define('WOPSHOP_PLUGIN_INCLUDE_DIR', plugin_dir_path(__FILE__).'includes');
            $this->define('WOPSHOP_PLUGIN_ADMIN_DIR', plugin_dir_path(__FILE__).'admin');
            $this->define('WOP_WP_CNT', content_url());
        }

        /**
         * Define constant if not already set.
         *
         * @param  string $name
         * @param  string|bool $value
         */
        private function define($name, $value) {
            if (!defined($name)) {
                define($name, $value);
            }
        }
    
        public function wopshop_admin_init() {
            Factory::loadJQuery();
            Factory::loadJsBootstrap();
            Factory::loadDatepicker();
            
            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_script('jquery-ui-button');
            wp_enqueue_script('jquery-ui-tabs');
            wp_enqueue_script('jquery-ui-dialog');
            
            wp_enqueue_style('admin.css', WOPSHOP_PLUGIN_URL.'assets/css/admin.css');
            wp_enqueue_script('admin.js', WOPSHOP_PLUGIN_URL.'assets/js/system/admin.js');
			wp_enqueue_style('jquery.ui.css', WOPSHOP_PLUGIN_URL.'assets/css/jquery.ui.css');
        }

        public function plugin_add_settings_link($links) {
            $settings_link = '<a href="admin.php?page=configuration">' . __('Settings') . '</a>';
            array_unshift($links, $settings_link);
            
            return $links;
        }

        public function wopshop_admin_menu() {
            add_menu_page('wopshop', 'Wopshop', 'edit_posts', 'panel', array('WshopAdmin', 'actions'), WOPSHOP_PLUGIN_URL . 'assets/images/icons/wopshop.png', 56);

            add_submenu_page('panel', 'Wopshop', '<img src="'.WOPSHOP_PLUGIN_URL.'assets/images/icons/control_panel.png" class="wopshop_menu_icons"> '._WOP_SHOP_MENU_PANEL, 'edit_posts', 'panel', array('WshopAdmin', 'actions'));
            add_submenu_page('panel', 'Wopshop', '<img src="'.WOPSHOP_PLUGIN_URL.'assets/images/icons/categories.png" class="wopshop_menu_icons"> '._WOP_SHOP_MENU_CATEGORIES, 'edit_posts', 'categories', array('WshopAdmin', 'actions'));
            add_submenu_page('panel', 'Wopshop', '<img src="'.WOPSHOP_PLUGIN_URL.'assets/images/icons/products.png" class="wopshop_menu_icons"> '._WOP_SHOP_MENU_PRODUCTS, 'edit_posts', 'products', array('WshopAdmin', 'actions'));
            add_submenu_page('panel', 'Wopshop', '<img src="'.WOPSHOP_PLUGIN_URL.'assets/images/icons/orders.png" class="wopshop_menu_icons"> '._WOP_SHOP_MENU_ORDERS, 'edit_posts', 'orders', array('WshopAdmin', 'actions'));
            add_submenu_page('panel', 'Wopshop', '<img src="'.WOPSHOP_PLUGIN_URL.'assets/images/icons/clients.png" class="wopshop_menu_icons"> '._WOP_SHOP_MENU_CLIENTS, 'edit_posts', 'clients', array('WshopAdmin', 'actions'));
            add_submenu_page('panel', 'Wopshop', '<img src="'.WOPSHOP_PLUGIN_URL.'assets/images/icons/options.png" class="wopshop_menu_icons"> '._WOP_SHOP_MENU_OTHER, 'edit_posts', 'options', array('WshopAdmin', 'actions'));
            add_submenu_page('panel', 'Wopshop', '<img src="'.WOPSHOP_PLUGIN_URL.'assets/images/icons/configurations.png" class="wopshop_menu_icons"> '._WOP_SHOP_MENU_CONFIG, 'manage_options', 'configuration', array('WshopAdmin', 'actions'));
            add_submenu_page('panel', 'Wopshop', '<img src="'.WOPSHOP_PLUGIN_URL.'assets/images/icons/install.png" class="wopshop_menu_icons"> '._WOP_SHOP_INSTALL_AND_UPDATE, 'manage_options', 'update', array('WshopAdmin', 'actions'));
            add_submenu_page('panel', 'Wopshop', '<img src="'.WOPSHOP_PLUGIN_URL.'assets/images/icons/about_us.png" class="wopshop_menu_icons"> '._WOP_SHOP_MENU_INFO, 'edit_posts', 'aboutus', array('WshopAdmin', 'actions'));
        }

        public function add_menu_meta_boxes() {
            add_meta_box('add-wopshop', __('Wopshop','wshop'), array(&$this, 'meta_box_display'), 'nav-menus', 'side', 'default');
			wp_enqueue_script('admin_menu.js', WOPSHOP_PLUGIN_URL.'assets/js/system/admin_menu.js');
        }
        
        public function meta_box_display($post, $data) {
            include(WSHOP_PLUGIN_BASE_DIR . '/lib/form.meta_boxes.php');
        }
        
        public function activate(){
            if (version_compare($GLOBALS['wp_version'], $this->minimumWordpressVersion, '<')) {
                echo '<strong>'.sprintf('WOPshop %s requires WordPress %s or higher.', $this->version, $this->minimumWordpressVersion).'</strong> '.sprintf('Please <a href="%1$s">upgrade WordPress</a> to a current version', 'https://codex.wordpress.org/Upgrading_WordPress');
                exit();
            } else {                    
                if (get_option('wopshop_version') === false){
                    $wshopInstaller = new WshopInstaller();
                    $wshopInstaller->install($this->version);					
                }
                
				flush_rewrite_rules();
            }		
        }
        
        public function deactivate(){
            WshopRouter::getInstance()->unregisterRewriteRules();
            flush_rewrite_rules();
        }
        
        private function loadCoreFiles(){
            require_once dirname(__FILE__) . "/autoload.php";
            require_once dirname(__FILE__) . "/functions.php";
        }
        
        private function loadAddons(){
            global $wpdb;
            require_once WSHOP_PLUGIN_BASE_DIR ."/core/addon.php";
            
            $query = "SELECT `id`, `alias` FROM `".$wpdb->prefix."wshop_addons` WHERE `publish` = 1";
            $addons = $wpdb->get_results($query);
            
            foreach ($addons as $addon){
                $addonPath = WSHOP_PLUGIN_BASE_DIR ."/site/addons/".$addon->alias."/$addon->alias.php";
                
                if (file_exists($addonPath)){
                    require_once $addonPath;
                    $addonClass = ucfirst(str_replace('_', '', $addon->alias)).'WshopAddon';
                    
                    if (class_exists($addonClass) && method_exists($addonClass, 'loadActions')){
                        $addonObj = new $addonClass();
                        $addonObj->loadActions();
                    }                    
                }
            }
        }
    }
}

$wshop = new Wopshop();