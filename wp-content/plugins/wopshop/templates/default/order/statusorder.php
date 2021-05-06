<?php 
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}?>
<?php print _WOP_SHOP_HI?> <?php print $this->order->f_name;?> <?php print $this->order->l_name;?>,
<?php printf(_WOP_SHOP_YOUR_ORDER_STATUS_CHANGE, $this->order->order_number);?>

<?php print _WOP_SHOP_NEW_STATUS_IS?>: <?php print $this->order_status?> 
<?php if ($this->order_detail){?>
<?php print _WOP_SHOP_ORDER_DETAILS?>: <?php print $this->order_detail?>
<?php }?> 
 
<?php if ($this->comment!=""){?>
<?php print _WOP_SHOP_COMMENT_YOUR_ORDER?>: <?php print $this->comment;?>

<?php }?>
<?php print $this->vendorinfo->company_name?> 
<?php print $this->vendorinfo->adress?> 
<?php print $this->vendorinfo->zip?> <?php print $this->vendorinfo->city?> 
<?php print $this->vendorinfo->country?> 
<?php print _WOP_SHOP_CONTACT_PHONE?>: <?php print $this->vendorinfo->phone?> 
<?php print _WOP_SHOP_CONTACT_FAX?>: <?php print $this->vendorinfo->fax?>