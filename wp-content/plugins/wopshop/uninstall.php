<?php
//if uninstall not called from WordPress exit
if (!defined('WP_UNINSTALL_PLUGIN')){ 
    exit();
}

delete_option('wopshop_version');

global $wpdb;
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_attr`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_attr`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_attr_values`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_attr_groups`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_categories`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_config`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_countries`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_coupons`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_currencies`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_manufacturers`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_order_history`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_order_item`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_order_status`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_orders`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_payment_method`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_products`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_products_attr`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_products_attr2`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_products_images`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_products_relations`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_products_to_categories`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_products_videos`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_shipping_method`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_shipping_ext_calc`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_shipping_method_price`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_shipping_method_price_countries`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_shipping_method_price_weight`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_products_reviews`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_taxes`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_users`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_cart_temp`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_products_prices`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_usergroups`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_languages`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_delivery_times`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_import_export`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_products_files`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_unit`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_product_labels`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_taxes_ext`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_config_display_prices`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_products_extra_fields`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_products_extra_field_values`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_products_extra_field_groups`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_addons`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_vendors`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_free_attr`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_products_free_attr`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_config_seo`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_config_statictext`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_products_option`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_payment_trx`', '1'));
$wpdb->query($wpdb->prepare('DROP TABLE `'.$wpdb->prefix.'wshop_payment_trx_data`', '1'));