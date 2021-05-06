<?php
/**
* @version      1.0.0 01.06.2016
* @author       MAXXmarketing GmbH
* @package      WOPshop
* @copyright    Copyright (C) 2010 http://www.wop-agentur.com. All rights reserved.
* @license      GNU/GPL
*/

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

$row = $template->sh_method_price;
?>
<tr><td>&nbsp;</td></tr>
<tr>
  <td class="key" style = "vertical-align:top">
    <b><?php echo _WOP_SHOP_PRICE_DEPENCED_WEIGHT;?></b>
  </td>
  <td>
    <table class="adminlist widefat" id="table_shipping_weight_price">
    <thead>
       <tr>
         <th>
           <?php echo _WOP_SHOP_MINIMAL_WEIGHT;?> (<?php print sprintUnitWeight();?>)
         </th>
         <th>
           <?php echo _WOP_SHOP_MAXIMAL_WEIGHT;?> (<?php print sprintUnitWeight();?>)
         </th>
         <th>
           <?php echo _WOP_SHOP_PRICE;?> (<?php echo $template->currency->currency_code; ?>)
         </th>
         <th>
           <?php echo _WOP_SHOP_PACKAGE_PRICE;?> (<?php echo $template->currency->currency_code; ?>)
         </th>         
         <th>
           <?php echo _WOP_SHOP_DELETE;?>
         </th>
       </tr>                   
       </thead>
       <?php
       $key = 0;
       foreach ($row->prices as $key=>$value){?>
       <tr id='shipping_weight_price_row_<?php print $key?>'>
         <td>
           <input type = "text" class = "inputbox" name = "shipping_weight_from[]" value = "<?php echo $value->shipping_weight_from;?>" />
         </td>
         <td>
           <input type = "text" class = "inputbox" name = "shipping_weight_to[]" value = "<?php echo $value->shipping_weight_to;?>" />
         </td>
         <td>
           <input type = "text" class = "inputbox" name = "shipping_price[]" value = "<?php echo $value->shipping_price;?>" />
         </td>
         <td>
           <input type = "text" class = "inputbox" name = "shipping_package_price[]" value = "<?php echo $value->shipping_package_price;?>" />
         </td>         
         <td style="text-align:center">
            <a class="btn btn-micro" href="#" onclick="delete_shipping_weight_price_row(<?php print $key?>);return false;">
                <i class="glyphicon wshop-icon glyphicon-remove-circle"></i>
            </a>
         </td>
       </tr>
       <?php }?>    
    </table>
    <table class="adminlist widefat"> 
    <tr>
        <td style="padding-top:5px;" align="right">
            <input type="button" class="btn" value="<?php echo _WOP_SHOP_ADD_VALUE?>" onclick = "addFieldShPrice();">
        </td>
    </tr>
    </table>
    <script type="text/javascript"> 
        <?php print "var shipping_weight_price_num = $key;";?>
    </script>
</td>
</tr>