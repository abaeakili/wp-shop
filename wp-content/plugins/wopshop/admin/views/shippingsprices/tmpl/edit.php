<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$row = $this->sh_method_price;
$lists = $this->lists;

?>
<form action = "admin.php?page=options&tab=shippingsprices&task=save" method = "post" name = "adminForm">
<div class="wrap">
    <div class="form-wrap">
		<h3><?php echo  $row->sh_pr_method_id ? _WOP_SHOP_EDIT_SHIPPING_PRICES :  _WOP_SHOP_NEW_SHIPPING_PRICES; ?></h3>
        <table class="admintable" width = "100%" >
            <tr>
                <td class="key">
                    <?php echo _WOP_SHOP_TITLE;?>*
                </td>
                <td>
                    <?php echo $lists['shipping_methods']?>
                </td>
            </tr>
            <tr>
                <td class="key">
                    <?php echo _WOP_SHOP_COUNTRY."*"."<br/><br/><span style='font-weight:normal'>"._WOP_SHOP_MULTISELECT_INFO."</span>"; ?>
                </td>
                <td>
                    <?php echo $lists['countries'];?>
                </td>
            </tr>
            <?php if ($this->config->admin_show_delivery_time) { ?>
            <tr>
                <td class="key">
                    <?php echo _WOP_SHOP_DELIVERY_TIME;?>
                </td>
                <td>
                    <?php echo $lists['deliverytimes'];?>
                </td>
            </tr>
            <?php }?>
            <tr>
                <td class="key">
                    <?php echo _WOP_SHOP_PRICE?>*
                </td>
                <td>
                    <input type = "text" class = "inputbox" name = "shipping_stand_price" value = "<?php echo $row->shipping_stand_price?>" />
                    <?php echo $this->currency->currency_code; ?>
                </td>
            </tr>
            <?php if ($this->config->tax){?>
            <tr>
                <td class="key">
                    <?php echo _WOP_SHOP_TAX?>*
                </td>
                <td>
                    <?php echo $lists['taxes']?>
                </td>
            </tr>
            <?php }?>
            <tr>
                <td class="key">
                    <?php echo _WOP_SHOP_PACKAGE_PRICE?>*
                </td>
                <td>
                    <input type = "text" class = "inputbox" name = "package_stand_price" value = "<?php echo $row->package_stand_price?>" />
                    <?php echo $this->currency->currency_code; ?>
                </td>
            </tr>
            <?php if ($this->config->tax){?>
            <tr>
                <td class="key">
                   <?php echo _WOP_SHOP_PACKAGE_TAX?>*
                </td>
                <td>
                    <?php echo $lists['package_taxes']?>
                </td>
            </tr>
            <?php }?>

        <?php foreach($this->extensions as $extension){
            $extension->exec->showShippingPriceForm($row->getParams(), $extension, $this);
            }
        ?>
        </table>
        <div class="clr"></div>
    </div>
</div>
<div clas="submit">
    <p class="submit">
        <input class="button button-primary" type="submit" value="<?php echo _WOP_SHOP_ACTION_SAVE; ?>" name="submit">
        <a class="button" href="admin.php?page=options&tab=shippingsprices"><?php echo _WOP_SHOP_BACK; ?></a>
    </p> 
</div>
    <input type = "hidden" name = "sh_pr_method_id" value = "<?php echo $row->sh_pr_method_id?>" />

    <?php wp_nonce_field('shippingsprices_edit','name_of_nonce_field'); ?>
</form>