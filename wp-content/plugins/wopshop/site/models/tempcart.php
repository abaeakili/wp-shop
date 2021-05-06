<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class TempcartWshopModel extends WshopModel {
    
    public $savedays = 30;
    public $table_name;
    
    public function __construct(){
        global $wpdb;
        $this->table_name = $wpdb->prefix.'wshop_cart_temp';
        apply_filters('onConstructWshopTempCart', array(&$this));
    }
    
    function insertTempCart($cart) {
        global $wpdb;
        if ($cart->type_cart!='wishlist') return 0; //not save if type == cart

        //$patch = "/";
        //if (URI::base(true) != "") $patch = URI::base(true);
        //setcookie('wopshop_temp_cart', session_id() ,time() + 3600*24*$this->savedays, $patch);
        $wpdb->query( 
                $wpdb->prepare( 
                        "DELETE FROM $this->table_name
                         WHERE id_cookie = %s
                         AND type_cart = %s
                        ",
                        session_id(), $cart->type_cart 
                )
        );        
        
	if (!count($cart->products)) return 0;
        $wpdb->insert(
            $this->table_name,
            array(
                'id_cookie' => session_id(),
                'cart' => esc_sql(json_encode($cart->products)),
                'type_cart' => $cart->type_cart
            ),
            array(
                '%s',
                '%s',
                '%s'
            )
        );		
        return 1;
    }
        
    function getTempCart($id_cookie, $type_cart="cart") {
        global $wpdb;
        $cart = $wpdb->get_row("SELECT `cart` FROM $this->table_name WHERE `id_cookie` = '" . esc_sql($id_cookie) . "' AND `type_cart`='".$type_cart."'");
        if ($cart != ""){
            return (json_decode($cart, 1));
        } else {
            return array();
        }
    }
}