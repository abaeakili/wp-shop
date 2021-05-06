<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="wrap">
    <div class="form-wrap">
		<h3><?php echo  $this->edit ? _WOP_SHOP_EDIT_TAX . ' / ' . $this->tax->tax_name :  _WOP_SHOP_NEW_TAX; ?></h3>
        <form method="POST" action="admin.php?page=options&tab=taxes&task=save" id="edittax">
            <table width = "100%" class="admintable">
               <tr>
                 <td class="key" style="width:250px;">
                   <?php echo _WOP_SHOP_TITLE;?>*
                 </td>
                 <td>
                   <input type = "text" class = "inputbox" id = "tax_name" name = "tax_name" value = "<?php echo $this->tax->tax_name;?>" />
                 </td>
               </tr>
               <tr>
                 <td  class="key">
                   <?php echo _WOP_SHOP_VALUE;?>*
                 </td>
                 <td>
                   <input type = "text" class = "inputbox" id = "tax_value" name = "tax_value" value = "<?php echo $this->tax->tax_value;?>" /> %
                   <?php echo HTML::tooltip(_WOP_SHOP_VALUE_TAX_INFO);?>
                 </td>
               </tr>
             </table>
            <input type="hidden" value="<?php echo $this->tax->tax_id; ?>" name="tax_id">
            <?php wp_nonce_field('tax_edit','name_of_nonce_field'); ?>
            
            <p class="submit">
                <input id="submit" class="button button-primary" type="submit" value="<?php echo _WOP_SHOP_ACTION_SAVE; ?>" name="submit">
                <a class="button" id="back" href="admin.php?page=options&tab=taxes"><?php echo _WOP_SHOP_BACK; ?></a>
            </p> 
        </form>
    </div>
</div>