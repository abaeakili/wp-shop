<?php
/**
* @version      1.0.0 01.06.2016
* @author       MAXXmarketing GmbH
* @package      WOPshop
* @copyright    Copyright (C) 2010 http://www.wop-agentur.com. All rights reserved.
* @license      GNU/GPL
*/
?>
<div class="wrap">
    <h2><?php echo _WOP_SHOP_EDIT_TAX_EXT; ?></h2>
    <form method="POST" action="admin.php?page=options&tab=exttaxes&task=save&back_tax_id=<?php print $this->back_tax_id;?>" id="edittax">
        <?php print $this->tmp_html_start?>
        <div class="col100">
        <fieldset class="adminform">
        <table width="100%" class="admintable">
           <tr>
             <td class="key" style="width:250px;">
               <?php echo _WOP_SHOP_TITLE;?>*
             </td>
             <td>
               <?php print $this->lists['taxes'];?>
             </td>
           </tr>
           <tr>
            <td class="key">
                <?php echo _WOP_SHOP_COUNTRY."*<br/><br/><span style='font-weight:normal'>"._WOP_SHOP_MULTISELECT_INFO."</span>"; ?>
            </td>
            <td>
                <?php echo $this->lists['countries'];?>
            </td>
           </tr>
           <tr>
             <td  class="key">
               <?php echo _WOP_SHOP_TAX;?>*
             </td>
             <td>
               <input type="text" class="inputbox" name="tax" value="<?php echo $this->tax->tax;?>" /> %
             </td>
           </tr>
           <tr>
             <td class="key">
               <?php 
                if ($this->config->ext_tax_rule_for==1) 
                    echo _WOP_SHOP_USER_WITH_TAX_ID_TAX;
                else
                    echo _WOP_SHOP_FIRMA_TAX;
                ?>*
             </td>
             <td>
               <input type="text" class="inputbox" name="firma_tax" value="<?php echo $this->tax->firma_tax;?>" /> %
             </td>
           </tr>
         </table>
        </fieldset>
        </div>
        <div class="clr"></div>
        <input type="hidden" name="id" value="<?php echo $this->tax->id?>" />
        <?php print $this->tmp_html_end?>
        <?php wp_nonce_field('taxext_edit','name_of_nonce_field'); ?>
        <p class="submit">
            <input id="submit" class="button button-primary" type="submit" value="<?php echo _WOP_SHOP_ACTION_SAVE; ?>" name="submit">
            <a class="button" id="back" href="admin.php?page=options&tab=exttaxes&back_tax_id=<?php print $this->back_tax_id;?>"><?php echo _WOP_SHOP_BACK; ?></a>
        </p> 
    </form>
</div>