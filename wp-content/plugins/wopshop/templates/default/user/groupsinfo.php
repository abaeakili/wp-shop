<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="wshop" id="wshop_plugin">
    <h1><?php print _WOP_SHOP_USER_GROUPS_INFO?></h1>
    
    <?php echo $this->_tmpl_start?>
    <table class="groups_list">
    <tr>
        <th class="title"><?php print _WOP_SHOP_TITLE?></th> 
        <th class="discount"><?php print _WOP_SHOP_DISCOUNT?></th> 
    </tr>
    <?php foreach($this->rows as $row) : ?>
        <tr>
            <td class="title"><?php print $row->name?></td> 
            <td class="discount"><?php print floatval($row->usergroup_discount)?>%</td>
        </tr>
    <?php endforeach; ?>
    </table>
    
    <?php echo $this->_tmpl_end?>
</div>