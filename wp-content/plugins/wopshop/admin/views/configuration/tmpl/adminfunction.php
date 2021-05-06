<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$lists=$this->lists;
$config = $this->config;
displaySubmenuConfigs('adminfunction');
?>
<div class="wrap">
<form action="admin.php?page=configuration&task=save" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<?php print $this->tmp_html_start?>
<?php wp_nonce_field('config','config_nonce_field'); ?>
<input type="hidden" value="8" name="tabs">
<div class="col100">
<fieldset class="adminform">
    <legend><?php echo _WOP_SHOP_GENERAL;?></legend>
<table class="admintable">
<tr>
    <td class="key">
      <?php echo _WOP_SHOP_ENABLE_WISHLIST;?>
    </td>
    <td>
      <input type="checkbox" name="enable_wishlist" class="inputbox" id="enable_f_wishlist" value="1" <?php if ($config->enable_wishlist) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
      <?php echo _WOP_SHOP_USE_RABATT_CODE;?>
    </td>
    <td>
      <input type="checkbox" name="use_rabatt_code" id="use_rabatt_code" value="1" <?php if ($config->use_rabatt_code) echo 'checked="checked"';?> />
    </td>
</tr> 
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_PURCHASE_WITHOUT_REGISTERING?>
    </td>
    <td>
        <?php print $this->lists['shop_register_type'];?>
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_USER_AS_CATALOG?>
    </td>
    <td>
        <input type="checkbox" name="user_as_catalog" value="1" <?php if ($config->user_as_catalog) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_PANEL_LANGUAGES?>
    </td>
    <td>
        <input type="checkbox" name="admin_show_languages" value="1" <?php if ($config->admin_show_languages) echo 'checked="checked"';?> />
    </td>
</tr>

<tr>
    <td class="key">
        <?php echo _WOP_SHOP_SHIPPINGS?>
    </td>
    <td>
        <input type="checkbox" name="without_shipping" value="1" <?php if (!$config->without_shipping) echo 'checked="checked"';?> />
    </td>
</tr>

<tr>
    <td class="key">
        <?php echo _WOP_SHOP_PAYMENTS?>
    </td>
    <td>
        <input type="checkbox" name="without_payment" value="1" <?php if (!$config->without_payment) echo 'checked="checked"';?> />
    </td>
</tr>

<tr>
    <td class="key">
        <?php echo _WOP_SHOP_USE_DIFFERENT_TEMPLATES_CATEGORIES_PRODUCTS?>
    </td>
    <td>
        <input type="checkbox" name="use_different_templates_cat_prod" value="1" <?php if ($config->use_different_templates_cat_prod) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_USE_VENDORS?>
    </td>
    <td>
        <input type="checkbox" name="admin_show_vendors" value="1" <?php if ($config->admin_show_vendors) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_UNIT_MEASURE?>
    </td>
    <td>
        <input type="hidden" name="admin_show_units" value="0">
        <input type="checkbox" name="admin_show_units" value="1" <?php if ($config->admin_show_units) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_USE_ATTRIBUTE_EXTEND_PARAMS?>
    </td>
    <td>
        <input type="checkbox" name="use_extend_attribute_data" value="1" <?php if ($config->use_extend_attribute_data) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_TAX?>
    </td>
    <td>
        <input type="hidden" name="tax" value="0"/>
        <input type="checkbox" name="tax" value="1" <?php if ($config->tax) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_STOCK?>
    </td>
    <td>
        <input type="hidden" name="stock" value="0"/>
        <input type="checkbox" name="stock" value="1" <?php if ($config->stock) echo 'checked = "checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_SHOP_MODE?>
    </td>
    <td>
        <?php print $this->lists['shop_mode'];?>
    </td>
</tr>
</table>
</fieldset>
</div>
<div class="clear"></div>

<div class="col100">
<fieldset class="adminform">
    <legend><?php echo _WOP_SHOP_PRODUCTS ?></legend>
<table class="admintable">
    <tr>
        <td class="key">
            <?php echo _WOP_SHOP_ATTRIBUTES?>
        </td>
        <td>
            <input type="checkbox" name="admin_show_attributes" value="1" <?php if ($config->admin_show_attributes) echo 'checked="checked"';?> />
        </td>
    </tr>
    <tr>
        <td class="key">
            <?php echo _WOP_SHOP_FREE_ATTRIBUTES?>
        </td>
        <td>
            <input type="checkbox" name="admin_show_freeattributes" value="1" <?php if ($config->admin_show_freeattributes) echo 'checked="checked"';?> />
        </td>
    </tr>
    <tr>
        <td class="key">
            <?php echo _WOP_SHOP_DELIVERY_TIME?>
        </td>
        <td>
            <input type="checkbox" name="admin_show_delivery_time" value="1" <?php if ($config->admin_show_delivery_time) echo 'checked="checked"';?> />
        </td>
    </tr>
    <tr>
        <td class="key">
            <?php echo _WOP_SHOP_PRODUCT_VIDEOS?>
        </td>
        <td>
            <input type="checkbox" name="admin_show_product_video" value="1" <?php if ($config->admin_show_product_video) echo 'checked="checked"';?> />
        </td>
    </tr>

    <tr>
        <td class="key">
            <?php echo _WOP_SHOP_PRODUCT_RELATED?>
        </td>
        <td>
            <input type="checkbox" name="admin_show_product_related" value="1" <?php if ($config->admin_show_product_related) echo 'checked="checked"';?> />
        </td>
    </tr>
    <tr>
        <td class="key">
            <?php echo _WOP_SHOP_FILES?>
        </td>
        <td>
            <input type="checkbox" name="admin_show_product_files" value="1" <?php if ($config->admin_show_product_files) echo 'checked="checked"';?> />
        </td>
    </tr>

    <tr>
        <td class="key">
            <?php echo _WOP_SHOP_LABEL;?>
        </td>
        <td>
            <input type="checkbox" name="admin_show_product_labels" value="1" <?php if ($config->admin_show_product_labels) echo 'checked="checked"';?> />
        </td>
    </tr>

    <tr>
        <td class="key">
            <?php echo _WOP_SHOP_PRODUCT_BUY_PRICE?>
        </td>
        <td>
            <input type="checkbox" name="admin_show_product_bay_price" value="1" <?php if ($config->admin_show_product_bay_price) echo 'checked="checked"';?> />
        </td>
    </tr>

    <tr>
        <td class="key">
            <?php echo _WOP_SHOP_BASIC_PRICE?>
        </td>
        <td>
            <input type="checkbox" name="admin_show_product_basic_price" value="1" <?php if ($config->admin_show_product_basic_price) echo 'checked="checked"';?> />
        </td>
    </tr>

    <tr>
        <td class="key">
            <?php echo _WOP_SHOP_EXTRA_FIELDS?>
        </td>
        <td>
            <input type="checkbox" name="admin_show_product_extra_field" value="1" <?php if ($config->admin_show_product_extra_field) echo 'checked="checked"';?> />
        </td>
    </tr>
<!--    <tr>
    <td class="key">
        <?php echo _WOP_SHOP_PRODUCT_WEIGHT?>
    </td>
    <td>
        <input type="checkbox" name="admin_show_weight" value="1" <?php if ($config->admin_show_weight) echo 'checked="checked"';?> />
    </td>
</tr>-->

</table>

</fieldset>
</div>
<div class="clear"></div>
<?php print $this->tmp_html_end?>
<p class="submit">
<input id="submit" class="button button-primary" type="submit" value="<?php echo _WOP_SHOP_ACTION_SAVE; ?>" name="submit">
</p> 

<input type="hidden" value="8" name="tabs">
</form>
</div>