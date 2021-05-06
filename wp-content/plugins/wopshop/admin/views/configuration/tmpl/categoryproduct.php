<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$lists=$this->lists;
$config = $this->config;
displaySubmenuConfigs('catprod');
?>
<form action="admin.php?page=configuration&task=save" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<?php print $this->tmp_html_start?>
<?php wp_nonce_field('config','config_nonce_field'); ?>
<input type="hidden" name="layout" value="catprod">
<!--<input type="hidden" name="tab" value="6">-->

<div class="col100">
<fieldset class="adminform">
    <legend><?php echo _WOP_SHOP_LIST_PRODUCTS." / "._WOP_SHOP_PRODUCT ?></legend>
<table class="admintable">
<?php if ($config->tax){?>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_SHOW_TAX?>
    </td>
    <td>
        <input type="checkbox" name="show_tax_in_product" value="1" <?php if ($config->show_tax_in_product) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_SHOW_TAX_IN_CART?>
    </td>
    <td>
        <input type="checkbox" name="show_tax_product_in_cart" value="1" <?php if ($config->show_tax_product_in_cart) echo 'checked="checked"';?> />
    </td>
</tr>
<?php }?>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_SHOW_PLUS_SHIPPING?>
    </td>
    <td>
        <input type="checkbox" name="show_plus_shipping_in_product" value="1" <?php if ($config->show_plus_shipping_in_product) echo 'checked="checked"';?> />
    </td>
</tr>
<?php if ($config->stock){?>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_HIDE_PRODUCT_NOT_AVAIBLE_STOCK?>
    </td>
    <td>
        <input type="checkbox" id="hide_product_not_avaible_stock" name="hide_product_not_avaible_stock" value="1" <?php if ($config->hide_product_not_avaible_stock) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_HIDE_BUY_PRODUCT_NOT_AVAIBLE_STOCK?>
    </td>
    <td>
        <input type="checkbox" name="hide_buy_not_avaible_stock" value="1" <?php if ($config->hide_buy_not_avaible_stock) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_HIDE_HIDE_TEXT_PRODUCT_NOT_AVAILABLE?>
    </td>
    <td>
        <input type="checkbox" name="hide_text_product_not_available" value="1" <?php if ($config->hide_text_product_not_available) echo 'checked="checked"';?> />
    </td>
</tr>
<?php }?>
<?php if ($config->admin_show_delivery_time){?>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_SHOW_DELIVERY_TIME?>
    </td>
    <td>
        <input type="checkbox" name="show_delivery_time" value="1" <?php if ($config->show_delivery_time) echo 'checked="checked"';?> />
    </td>
</tr>
<?php }?>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_SHOW_DEFAULT_PRICE?>
    </td>
    <td>
        <input type="checkbox" name="product_list_show_price_default" value="1" <?php if ($config->product_list_show_price_default) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_HIDE_PRICE_NULL?>
    </td>
    <td>
        <input type="hidden" name="product_hide_price_null" value="0">
        <input type="checkbox" name="product_hide_price_null" value="1" <?php if ($config->product_hide_price_null) echo 'checked="checked"';?> />
    </td>
</tr>

<tr>
    <td class="key">
        <?php echo _WOP_SHOP_SHOW_PRICE?>
    </td>
    <td>
        <?php print $this->lists['displayprice'];?>
    </td>
</tr>

<tr>
    <td class="key">
        <?php echo _WOP_SHOP_DISPLAY_WEIGHT_AS?>
    </td>
    <td>
        <?php print $this->lists['units'];?>
    </td>
</tr>
	
</table>
</fieldset>
</div>
<div class="clr"></div>


<div class="col100">
<fieldset class="adminform">
    <legend><?php echo _WOP_SHOP_LIST_PRODUCTS;?></legend>
<table class="admintable" width="100%" >
<tr>
    <td class="key" style="width:220px;">
        <?php echo _WOP_SHOP_COUNT_PRODUCTS_PAGE;?>
    </td>
    <td>
        <input type="text" name="count_products_to_page" class="inputbox" id="count_products_to_page" value="<?php echo $config->count_products_to_page;?>" />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_COUNT_PRODUCTS_ROW;?>
    </td>
    <td>
        <input type="text" name="count_products_to_row" class="inputbox" id="count_products_to_row" value="<?php echo $config->count_products_to_row;?>" />
    </td>
</tr>

<tr>
    <td class="key">
        <?php echo _WOP_SHOP_CHANGE_COUNTS_PROD_ROWS_FOR_ALL_CATS?>
    </td>
    <td>
        <input type="checkbox" name="update_count_prod_rows_all_cats" value="1" />
    </td>
</tr>

<tr>
    <td class="key">
        <?php echo _WOP_SHOP_COUNT_CATEGORY_ROW;?>
    </td>
    <td>
        <input type="text" name="count_category_to_row" class="inputbox" id="count_category_to_row" value="<?php echo $config->count_category_to_row;?>" />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_COUNT_MANUFACTURER_ROW;?>
    </td>
    <td>
        <input type="text" name="count_manufacturer_to_row" class="inputbox" value="<?php echo $config->count_manufacturer_to_row;?>" />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_ORDERING_CATEGORY;?>
    </td>
    <td>
        <?php print $this->lists['category_sorting'];?>
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_MANUFACTURER_SORTING;?>
    </td>
    <td>
        <?php print $this->lists['manufacturer_sorting'];?>
    </td>
</tr>

<tr>
    <td class="key">
        <?php echo _WOP_SHOP_PRODUCT_SORTING;?>
    </td>
    <td>
        <?php print $this->lists['product_sorting'];?>
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_PRODUCT_SORTING_DIRECTION;?>
    </td>
    <td>
        <?php print $this->lists['product_sorting_direction'];?>
    </td>
</tr>

<tr>
    <td class="key">
        <?php echo _WOP_SHOP_SHOW_BAY_BUT_IN_CAT?>
    </td>
    <td>
        <input type="checkbox" name="show_buy_in_category" value="1" <?php if ($config->show_buy_in_category) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_ABILITY_TO_SORT_PRODUCTS?>
    </td>
    <td>
        <input type="checkbox" name="show_sort_product" value="1" <?php if ($config->show_sort_product) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_SHOW_SELECTBOX_COUNT_PRODUCTS_TO_PAGE?>
    </td>
    <td>
        <input type="checkbox" name="show_count_select_products" value="1" <?php if ($config->show_count_select_products) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_SHOW_FILTERS?>
    </td>
    <td>
        <input type="checkbox" name="show_product_list_filters" value="1" <?php if ($config->show_product_list_filters) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_SHOW_WEIGHT_PRODUCT?>
    </td>
    <td>
        <input type="checkbox" name="product_list_show_weight" value="1" <?php if ($config->product_list_show_weight) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_SHOW_MANUFACTURER?>
    </td>
    <td>
        <input type="checkbox" name="product_list_show_manufacturer" value="1" <?php if ($config->product_list_show_manufacturer) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_SHOW_EAN_PRODUCT?>
    </td>
    <td>
        <input type="checkbox" name="product_list_show_product_code" value="1" <?php if ($config->product_list_show_product_code) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_SHOW_MIN_PRICE?>
    </td>
    <td>
        <input type="checkbox" name="product_list_show_min_price" value="1" <?php if ($config->product_list_show_min_price) echo 'checked="checked"';?> />
    </td>
</tr>

<tr>
    <td class="key">
        <?php echo _WOP_SHOP_SHOW_PRICE_DESCRIPTION?>
    </td>
    <td>
        <input type="checkbox" name="product_list_show_price_description" value="1" <?php if ($config->product_list_show_price_description) echo 'checked="checked"';?> />
    </td>
</tr>

<?php if ($config->admin_show_vendors){?>
<tr>
    <td class="key">
      <?php echo _WOP_SHOP_SHOW_VENDOR;?>
    </td>
    <td>
      <input type="checkbox" name="product_list_show_vendor" value="1" <?php if ($config->product_list_show_vendor) echo 'checked="checked"';?> />
    </td>
</tr>
<?php }?>

<?php if ($config->admin_show_product_extra_field){?>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_SHOW_EXTRA_FIELDS?>
    </td>
    <td>
        <?php print $this->lists['product_list_display_extra_fields'];?>
    </td>
</tr>

<tr>
    <td class="key">
        <?php echo _WOP_SHOP_SHOW_EXTRA_FIELDS_FILTER?>
    </td>
    <td>
        <?php print $this->lists['filter_display_extra_fields'];?>
    </td>
</tr>

<tr>
    <td class="key">
        <?php echo _WOP_SHOP_SHOW_EXTRA_FIELDS_CART?>
    </td>
    <td>
        <?php print $this->lists['cart_display_extra_fields'];?>
    </td>
</tr>
<?php }?>
<?php if ($config->stock){?>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_QTY_IN_STOCK?>
    </td>
    <td>
        <input type="hidden" name="product_list_show_qty_stock" value="0" />
        <input type="checkbox" name="product_list_show_qty_stock" value="1" <?php if ($config->product_list_show_qty_stock) echo 'checked="checked"';?> />
    </td>
</tr>
<?php }?>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_SHORT_DESCR_MULTILINE?>
    </td>
    <td>
        <input type="hidden" name="display_short_descr_multiline" value="0" />
        <input type="checkbox" name="display_short_descr_multiline" value="1" <?php if ($config->display_short_descr_multiline) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td></td>
    <td><?php print _JSHP_SEOPAGE_tophitsproducts?></td>
    <td><?php print _JSHP_SEOPAGE_topratingproducts?></td>
    <td><?php print _JSHP_SEOPAGE_labelproducts?></td>
    <td><?php print _JSHP_SEOPAGE_bestsellerproducts?></td>
    <td><?php print _JSHP_SEOPAGE_randomproducts?></td>
    <td><?php print _JSHP_SEOPAGE_lastproducts?></td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_COUNT_PRODUCTS_PAGE?>
    </td>
    <td><input type="text" name="count_products_to_page_tophits" class="inputbox middle" value="<?php echo $config->count_products_to_page_tophits;?>" /></td>
    <td><input type="text" name="count_products_to_page_toprating" class="inputbox middle" value="<?php echo $config->count_products_to_page_toprating;?>" /></td>
    <td><input type="text" name="count_products_to_page_label" class="inputbox middle" value="<?php echo $config->count_products_to_page_label;?>" /></td>
    <td><input type="text" name="count_products_to_page_bestseller" class="inputbox middle" value="<?php echo $config->count_products_to_page_bestseller;?>" /></td>
    <td><input type="text" name="count_products_to_page_random" class="inputbox middle" value="<?php echo $config->count_products_to_page_random;?>" /></td>
    <td><input type="text" name="count_products_to_page_last" class="inputbox middle" value="<?php echo $config->count_products_to_page_last;?>" /></td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_COUNT_PRODUCTS_ROW?>
    </td>
    <td><input type="text" name="count_products_to_row_tophits" class="inputbox middle" value="<?php echo $config->count_products_to_row_tophits;?>" /></td>
    <td><input type="text" name="count_products_to_row_toprating" class="inputbox middle" value="<?php echo $config->count_products_to_row_toprating;?>" /></td>
    <td><input type="text" name="count_products_to_row_label" class="inputbox middle" value="<?php echo $config->count_products_to_row_label;?>" /></td>
    <td><input type="text" name="count_products_to_row_bestseller" class="inputbox middle" value="<?php echo $config->count_products_to_row_bestseller;?>" /></td>
    <td><input type="text" name="count_products_to_row_random" class="inputbox middle" value="<?php echo $config->count_products_to_row_random;?>" /></td>
    <td><input type="text" name="count_products_to_row_last" class="inputbox middle" value="<?php echo $config->count_products_to_row_last;?>" /></td>
</tr>
   
</table>
    
</fieldset>
</div>
<div class="clr"></div>

<div class="col100">
<fieldset class="adminform">
    <legend><?php echo _WOP_SHOP_PRODUCT;?></legend>
<table class="admintable" width="100%" >

<tr>
    <td class="key" style="width:220px;">
        <?php echo _WOP_SHOP_SHOW_DEMO_TYPE_AS_MEDIA?>
    </td>
    <td>
        <input type="checkbox" name="demo_type" value="1" <?php if ($config->demo_type) echo 'checked="checked"';?> />
    </td>
</tr>

<tr>
    <td class="key">
        <?php echo _WOP_SHOP_SHOW_MANUFACTURER_LOGO?>
    </td>
    <td>
        <input type="checkbox" name="product_show_manufacturer_logo" value="1" <?php if ($config->product_show_manufacturer_logo) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_SHOW_MANUFACTURER?>
    </td>
    <td>
        <input type="hidden" name="product_show_manufacturer" value="0" />
        <input type="checkbox" name="product_show_manufacturer" value="1" <?php if ($config->product_show_manufacturer) echo 'checked="checked"';?> />
    </td>
</tr>

<tr>
    <td class="key">
        <?php echo _WOP_SHOP_SHOW_WEIGHT_PRODUCT?>
    </td>
    <td>
        <input type="checkbox" name="product_show_weight" value="1" <?php if ($config->product_show_weight) echo 'checked="checked"';?> />
    </td>
</tr>

<tr>
    <td class="key">
        <?php echo _WOP_SHOP_PRODUCT_ATTRIBUT_FIRST_VALUE_EMPTY?>
    </td>
    <td>
        <input type="checkbox" name="product_attribut_first_value_empty" value="1" <?php if ($config->product_attribut_first_value_empty) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_PRODUCT_ATTRIBUT_RADIO_VALUE_DISPLAY_VERTICAL?>
    </td>
    <td>
        <input type="checkbox" name="radio_attr_value_vertical" value="1" <?php if ($config->radio_attr_value_vertical) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_PRODUCT_ATTRIBUT_ADD_PRICE_DISPLAY?>
    </td>
    <td>
        <input type="checkbox" name="attr_display_addprice" value="1" <?php if ($config->attr_display_addprice) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_PRODUCT_ATTRIBUT_SORTING." ("._WOP_SHOP_DEPENDENT.")"?>
    </td>
    <td>
        <?php print $this->lists['attribut_dep_sorting_in_product'];?>
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_PRODUCT_ATTRIBUT_SORTING." ("._WOP_SHOP_INDEPENDENT.")"?>
    </td>
    <td>
        <?php print $this->lists['attribut_nodep_sorting_in_product'];?>
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_HITS?>
    </td>
    <td>
        <input type="checkbox" name="show_hits" value="1" <?php if ($config->show_hits) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_SHOW_EAN_PRODUCT?>
    </td>
    <td>
        <input type="checkbox" name="show_product_code" value="1" <?php if ($config->show_product_code) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_USE_PLUGIN_CONTENT?>
    </td>
    <td>
        <input type="checkbox" name="use_plugin_content" value="1" <?php if ($config->use_plugin_content) echo 'checked="checked"';?> />
    </td>
</tr>

<tr>
    <td class="key">
      <?php echo _WOP_SHOP_ALLOW_REVIEW_PRODUCT;?>
    </td>
    <td>
      <input type="checkbox" name="allow_reviews_prod" value="1" <?php if ($config->allow_reviews_prod) echo 'checked="checked"';?> />
    </td>
</tr> 
<tr>
    <td class="key">
      <?php echo _WOP_SHOP_ALLOW_REVIEW_ONLY_REGISTERED;?>
    </td>
    <td>
      <input type="checkbox" name="allow_reviews_only_registered" value="1" <?php if ($config->allow_reviews_only_registered) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
      <?php echo _WOP_SHOP_DISPLAY_REVIEW_WITHOUT_CONFIRM;?>
    </td>
    <td>
        <input type="hidden" name="display_reviews_without_confirm" value="0">
        <input type="checkbox" name="display_reviews_without_confirm" value="1" <?php if ($config->display_reviews_without_confirm) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
      <?php echo _WOP_SHOP_SHOP_BUTTON_BACK;?>
    </td>
    <td>
      <input type="checkbox" name="product_show_button_back" value="1" <?php if ($config->product_show_button_back) echo 'checked="checked"';?> />
    </td>
</tr>
<?php if ($config->admin_show_vendors){?>
<tr>
    <td class="key">
      <?php echo _WOP_SHOP_SHOW_VENDOR;?>
    </td>
    <td>
      <input type="checkbox" name="product_show_vendor" value="1" <?php if ($config->product_show_vendor) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
      <?php echo _WOP_SHOP_SHOW_VENDOR_DETAIL;?>
    </td>
    <td>
      <input type="checkbox" name="product_show_vendor_detail" value="1" <?php if ($config->product_show_vendor_detail) echo 'checked="checked"';?> />
    </td>
</tr>
<?php }?>

<tr>
    <td class="key">
      <?php echo _WOP_SHOP_SHOW_BUTTON_PRINT;?>
    </td>
    <td>
      <input type="checkbox" name="display_button_print" value="1" <?php if ($config->display_button_print) echo 'checked="checked"';?> />
    </td>
</tr>
<?php if ($config->stock){?>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_QTY_IN_STOCK?>
    </td>
    <td>
        <input type="hidden" name="product_show_qty_stock" value="0" />
        <input type="checkbox" name="product_show_qty_stock" value="1" <?php if ($config->product_show_qty_stock) echo 'checked="checked"';?> />
    </td>
</tr>
<?php }?>
<tr>
    <td class="key">
      <?php echo _WOP_SHOP_REVIEW_MAX_MARK;?>
    </td>
    <td>
      <input type="text" name="max_mark" id="max_mark" value="<?php echo $config->max_mark?>" />
    </td>
</tr>
<tr>
   <td class="key">
     <?php echo _WOP_SHOP_PRODUCTS_RELATED_IN_ROW?>
   </td>
   <td>
     <input type="text" class="inputbox" name="product_count_related_in_row" value="<?php echo $config->product_count_related_in_row?>" />
   </td>
</tr>

<?php if ($config->admin_show_product_extra_field){?>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_HIDE_EXTRA_FIELDS?>
    </td>
    <td>
        <?php print $this->lists['product_hide_extra_fields'];?>
    </td>
</tr>
<?php }?>


</table>
    
</fieldset>
</div>
<div class="clr"></div>
<?php print $this->tmp_html_end?>
<p class="submit">
<input id="submit" class="button button-primary" type="submit" value="<?php echo _WOP_SHOP_ACTION_SAVE; ?>" name="submit">
</p>
<input type="hidden" value="6" name="tabs">
</form>