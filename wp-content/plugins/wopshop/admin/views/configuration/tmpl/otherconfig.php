<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$lists=$this->lists;
$config = $this->config;
displaySubmenuConfigs('otherconfig');
?>
<form action="admin.php?page=configuration&task=save" method="POST" name="adminForm" id="adminForm">
<?php print $this->tmp_html_start?>
<?php wp_nonce_field('config','config_nonce_field'); ?>
<input type="hidden" name="tabs" value="10">
<div class="col100">
    <fieldset class="adminform">
        <legend><?php echo _WOP_SHOP_OC;?></legend>
        <table class="admintable">
            <tr>
                <td class="key">
                    <?php echo _WOP_SHOP_EXTENDED_TAX_RULE_FOR?>
                </td>
                <td>
                    <?php print $lists['tax_rule_for'];?>
                </td>
            </tr>
            <tr>
                <td class="key">
                    <?php echo _WOP_SHOP_SAVE_ALIAS_AUTOMATICAL?>
                </td>
                <td>
                    <input type="hidden" name="create_alias_product_category_auto" value="0">
                    <input type="checkbox" name="create_alias_product_category_auto" value="1" <?php if ($config->create_alias_product_category_auto) echo 'checked="checked"';?> />
                </td>
            </tr>
            <?php foreach($this->other_config as $k){?>
            <tr>
                <td class="key">
                    <?php if (defined("_WOP_SHOP_OC_".$k)) print constant("_WOP_SHOP_OC_".$k); else print $k;?>
                </td>
                <td>
                <?php if (in_array($k, $this->other_config_checkbox)){?>
                    <input type="hidden" name="<?php print $k?>" value="0">
                    <input type="checkbox" name="<?php print $k?>" value="1" <?php if ($config->$k==1) print 'checked'?>>
                <?php }elseif (isset($this->other_config_select[$k])){?>
                    <?php 
                    $option = array();
                    foreach($this->other_config_select[$k] as $k2=>$v2){
                        $option_name = $v2;
                        if (defined("_WOP_SHOP_OC_".$k."_".$v2)){
                            $option_name = constant("_WOP_SHOP_OC_".$k."_".$v2);
                        }
                        $option[] = HTML::_('select.option', $k2, $option_name, 'id', 'name');
                    }
                    print HTML::_('select.genericlist', $option, $k, 'class = "inputbox"', 'id', 'name', $config->$k);
                    ?>
                <?php }else{?>
                            <input type="text" name="<?php print $k?>" value="<?php echo $config->$k?>">
                <?php }?>
                </td>
        </tr>
        <?php } ?>
        <?php /*foreach($this->other_config as $k){?>
            <tr>
                <td class="key">
                    <?php if (defined("_WOP_SHOP_OC_".$k)) print constant("_WOP_SHOP_OC_".$k); else print $k;?>
                </td>
                <td>
                    <?php if (in_array($k, $this->other_config_checkbox)){?>
                    <input type="hidden" name="<?php print $k?>" value="0">
                    <input type="checkbox" name="<?php print $k?>" value="1" <?php if ($config->$k==1) print 'checked'?>>
                    <?php }else{?>
                            <input type="text" name="<?php print $k?>" value="<?php echo $config->$k?>">
                    <?php }?>
                            <?php if (defined("_WOP_SHOP_OC_".$k."_INFO")) echo HTML::tooltip(constant("_WOP_SHOP_OC_".$k."_INFO")); ?>
                </td>
            </tr>
        <?php } */?>
        </table>
    </fieldset>
</div>
<div class="clr"></div>
<?php print $this->tmp_html_end?>
<p class="submit">
<input id="submit" class="button button-primary" type="submit" value="<?php echo _WOP_SHOP_ACTION_SAVE; ?>" name="submit">
</p>
</form>
