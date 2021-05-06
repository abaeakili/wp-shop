<?php 

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<?php print _WOP_SHOP_PRODUCT?>: <?php print $this->product_name;?><br/>
<?php print _WOP_SHOP_REVIEW_USER_NAME?>: <?php print $this->user_name;?><br/>
<?php print _WOP_SHOP_REVIEW_USER_EMAIL?>: <?php print $this->user_email;?><br/>
<?php print _WOP_SHOP_REVIEW_MARK_PRODUCT?>: <?php print $this->mark;?><br/>
<?php print _WOP_SHOP_COMMENT?>:<br/>
<?php print nl2br($this->review)?>

