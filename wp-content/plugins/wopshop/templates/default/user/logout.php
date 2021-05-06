<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
};
?>
<div class="wshop" id="wshop_plugin">
    <h1><?php print _WOP_SHOP_LOGOUT ?></h1>
    <?php print $this->checkout_navigator?>
    
    <input type="button" class="btn button" value="<?php print _WOP_SHOP_LOGOUT ?>" onclick="location.href='<?php print SEFLink("controller=user&task=logout"); ?>'" />
</div>