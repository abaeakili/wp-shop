<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!class_exists("WshopSite")) {
    class WshopSite {

        public function __construct() {
            $config = Factory::getConfig();
            Factory::loadJQuery();
            Factory::loadCssFiles();
            Factory::loadJsFiles();

            $controllerPath = Request::getVar('controller', 'products');
            $task = Request::getVar('task', 'display');
            $_controller = ucfirst($controllerPath) . 'WshopController';

            $user = wp_get_current_user();
            $session = Factory::getSession();
            $wshop_update_all_price = $session->get('wshop_update_all_price');
            $wshop_prev_user_id = $session->get('wshop_prev_user_id');
            if ($wshop_update_all_price || ($wshop_prev_user_id != $user->ID)){
                updateAllprices();
                $session->set('wshop_update_all_price', 0);
            }
            $session->set("wshop_prev_user_id", $user->ID);
            do_action_ref_array('onAfterLoadShopParams', array());
            
            if (file_exists(WOPSHOP_PLUGIN_DIR . '/site/controllers/' .  $controllerPath . '.php')){
                require_once WOPSHOP_PLUGIN_DIR . '/site/controllers/' . $controllerPath . '.php';
                
                if (class_exists($_controller) && (int)method_exists($_controller, $task)) {
                    $controller = new $_controller();
                    call_user_func(array($controller, $task));
                } else {
                    global $wp_query;
                    $wp_query->set_404();
                    status_header(404);
                    get_template_part(404); exit();
                }
            } else {
                global $wp_query;
                $wp_query->set_404();
                status_header(404);
                get_template_part(404); exit();
            }
			
            if ($controllerPath != 'content' && !compareX64(replaceWWW(getHttpHost()), $config->licensekod)){
                print $config->copyrightText;
            }
        }
    }
      
    new WshopSite();
}