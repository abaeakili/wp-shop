<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!class_exists("WshopAdmin")) {
    class WshopAdmin 
    {
        public $route;
        /**
         * Constructor
         */
        public function __construct() {
            add_action('admin_notices', array ($this, 'my_admin_notice'));
        }                      

        public function my_admin_notice () {
                $lists = array();
                $message = '';

                // Get the message queue
                $messages = Factory::getApplication()->getMessageQueue();

                // Build the sorted message list
                if (is_array($messages) && !empty($messages))
                {
                        foreach ($messages as $msg)
                        {
                                if (isset($msg['type']) && isset($msg['message']))
                                {
                                        $lists[$msg['type']][] = $msg['message'];
                                }
                        }
                    //Set messages to html     
                    foreach ($lists as $key => $value) {
                        $message .= '<div class="'.$key.'">';
                        foreach ($value as $v) {
                            $message .= '<p>'.$v.'</p>';
                        }
                        $message .= '</div>';
                    }                    
                }

                echo $message;              
        }    

        public function actions() {
            WshopAdminRouter::route();
        }
    }

    new WshopAdmin();
}