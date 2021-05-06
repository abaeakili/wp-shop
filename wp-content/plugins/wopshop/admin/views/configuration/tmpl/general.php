<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$lists=$this->lists;
$config = $this->config;

displaySubmenuConfigs('general');
?>
<form action="admin.php?page=configuration&task=save" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<?php print $this->tmp_html_start?>
<?php wp_nonce_field('config','config_nonce_field'); ?>
<!--<input type="hidden" name="layout" value="general">-->
<input type="hidden" value="1" name="tabs">
<div class="col100">
<fieldset class="adminform">
    <legend><?php echo _WOP_SHOP_GENERAL_PARAMETERS ?></legend>
<table class="admintable">
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_EMAIL_ADMIN;?>
    </td>
    <td>
        <input type="text" name="contact_email" class="inputbox" value="<?php echo $config->contact_email;?>" />
        <?php echo HTML::tooltip(_WOP_SHOP_EMAIL_ADMIN_INFO);?>
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_DEFAULT_LANGUAGE;?>
    </td>
    <td>
        <?php echo $lists['languages']; ?>
        <?php echo HTML::tooltip(_WOP_SHOP_INFO_DEFAULT_LANGUAGE);?>
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_TEMPLATE;?>
    </td>
    <td>
        <?php echo $lists['template'];?>
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_DISPLAY_PRICE_ADMIN;?>
    </td>
    <td>
        <?php echo $lists['display_price_admin']; ?>        
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_DISPLAY_PRICE_FRONT;?>
    </td>
    <td>
        <?php echo $lists['display_price_front']; ?> 
        <!--<a href="admin.php?page=configuration&tab=configdisplayprice"><?php print _WOP_SHOP_EXTENDED_CONFIG;?></a>-->        
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_USE_SSL?>
    </td>
    <td>
        <input type="checkbox" name="use_ssl"  value="1" <?php if ($config->use_ssl) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_SAVE_INFO_TO_LOG?>
    </td>
    <td>
        <input type="checkbox" name="savelog" id="savelog" value="1" <?php if ($config->savelog) echo 'checked="checked"';?> onclick="if (!jQuery('#savelog').attr('checked')) jQuery('#savelogpaymentdata').attr('checked',false);" />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_SAVE_PAYMENTINFO_TO_LOG?>
    </td>
    <td>
        <input type="checkbox" name="savelogpaymentdata" id="savelogpaymentdata" value="1" <?php if ($config->savelogpaymentdata) echo 'checked="checked"';?> onclick="if (!jQuery('#savelog').attr('checked')) this.checked=false;" />
        <?php echo HTML::tooltip(_WOP_SHOP_SAVE_PAYMENTINFO_TO_LOG_INFO);?>
    </td>
</tr>
<tr>
     <td class="key">
       <?php echo _WOP_SHOP_STORE_DATE_FORMAT;?>
     </td>
     <td>
       <input size="50" type="text" class="inputbox" name="store_date_format" value="<?php echo $config->store_date_format?>" />
     </td>
</tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_LICENSEKEY?>
    </td>
    <td>
        <input type="text" name="licensekod" class="inputbox" size="50" value="<?php print $config->licensekod;?>" />
    </td>
</tr>
</table>
</fieldset>
</div>
<div class="clear"></div>
<?php print $this->tmp_html_end?>
<p class="submit">
<input id="submit" class="button button-primary" type="submit" value="<?php echo _WOP_SHOP_ACTION_SAVE; ?>" name="submit">
</p>
</form>