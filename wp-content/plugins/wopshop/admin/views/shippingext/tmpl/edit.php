<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$row=$this->row;
?>
<form action = "admin.php?page=options&tab=shippingextprice&task=save" method = "post" name = "adminForm">
<div class="wrap">
    <div class="form-wrap">
		
		<h3><?php echo _WOP_SHOP_SHIPPING_EXT_PRICE_CALC; ?></h3>		
		<?php print $this->tmp_html_start?>		
        <fieldset class="adminform">
            <table class="admintable" width = "100%" >
   	<tr>
     	<td class="key" width="30%">
       		<?php echo _WOP_SHOP_PUBLISH;?>
     	</td>
     	<td>
            <input type="hidden" name="published" value="0" />
       		<input type="checkbox" name="published" value="1" <?php if ($row->published) echo 'checked="checked"'?> />
     	</td>
   	</tr>    
   	<tr>
     	<td class="key">
       		<?php echo _WOP_SHOP_TITLE;?>*
     	</td>
     	<td>
       		<input type="text" class="inputbox" name="name" value="<?php echo $row->name?>" />
     	</td>
   	</tr>
    <tr>
         <td class="key">
               <?php echo _WOP_SHOP_DESCRIPTION;?>
         </td>
         <td>
            <textarea name="description" cols="40" rows="5"><?php echo $row->description?></textarea>               
         </td>
       </tr>
    <tr>
         <td class="key">
            <?php echo _WOP_SHOP_SHIPPINGS;?>
         </td>
         <td>
            <?php foreach($this->list_shippings as $shipping){?>
                <div style="padding:5px 0px;">
                    <input type="hidden" name="shipping[<?php print $shipping->shipping_id?>]" value="0">
                    <input type="checkbox" name="shipping[<?php print $shipping->shipping_id?>]" value="1" <?php if ($this->shippings_conects[$shipping->shipping_id]!=="0") print "checked"?>>                    
                    <?php print $shipping->name;?>
                </div>
            <?php }?>
         </td>
    </tr>
    <?php        
        $row->exec->showConfigForm($row->getParams(), $row, $this);
    ?>
   <?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>
        </table>
        </fieldset>
    </div>
    <div class="clr"></div>
</div>
<input type = "hidden" name = "id" value = "<?php echo $row->id?>" />

<div clas="submit">
    <p class="submit">
        <input class="button button-primary" type="submit" value="<?php echo _WOP_SHOP_ACTION_SAVE; ?>" name="submit">
        <a class="button" href="admin.php?page=options&tab=shippingextprice"><?php echo _WOP_SHOP_BACK; ?></a>
    </p> 
</div>
<?php print $this->tmp_html_end?>
</form>
