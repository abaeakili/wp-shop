<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="wshop">
<h1><?php print _WOP_SHOP_SEARCH_RESULT?> <?php if ($this->search) print '"'.$this->search.'"';?></h1>

<?php echo _WOP_SHOP_NO_SEARCH_RESULTS;?>
</div>