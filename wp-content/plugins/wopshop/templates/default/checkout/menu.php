<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<table class = "wshop" id = "wshop_menu_order">
  <tr>
    <?php foreach($this->steps as $k=>$step){?>
      <td class = "wshop_order_step <?php print $this->cssclass[$k]?>">
        <?php print $step;?>
      </td>
    <?php }?>
  </tr>
</table>