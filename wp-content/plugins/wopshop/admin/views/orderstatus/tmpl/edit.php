<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$row=$this->order_status;
?>
<div class="wrap">
    <div class="form-wrap">
		<h3><?php echo  $row->status_id ? _WOP_SHOP_EDIT_ORDER_STATUS . ' / ' . $row->{Factory::getLang()->get('name')} :  _WOP_SHOP_NEW_ORDER_STATUS; ?></h3>
        <form method="POST" action="admin.php?page=options&tab=orderstatus&task=save" id="edit">
            <div class="wrap shopping">
                <div id="icon-shopping" class="icon32 icon32-shopping-settings"><br></div>
                <div class="wrap">
                    <?php 
                    foreach($this->languages as $index=>$language){echo $this->orderstatus;?>
                        <div class="form-field form-required term-name-wrap">
                            <label for="name_<?php echo $language->language; ?>"><?php echo _WOP_SHOP_TITLE; ?> <?php echo $language->name; ?></label>
                            <input id="name_<?php echo $language->language; ?>" type="text" size="40" value="<?php $n = 'name_'.$language->language; echo $row->$n; ?>" name="name_<?php echo $language->language; ?>">
                        </div>
                    <?php
                    }?>
                    <div class="form-field form-required term-code-wrap">
                        <label for="code"><?php echo _WOP_SHOP_CODE; ?></label>
                        <input id="code" type="text" size="40" value="<?php echo $row->status_code;?>" name="status_code">
                    </div>
                    <p class="submit">
                        <input id="submit" class="button button-primary" type="submit" value="<?php echo _WOP_SHOP_ACTION_SAVE; ?>" name="submit">
                        <a class="button" id="back" href="admin.php?page=options&tab=orderstatus"><?php echo _WOP_SHOP_BACK; ?></a>
                    </p> 
                </div>
            </div>
            <input type="hidden" value="<?php echo $row->status_id; ?>" name="status_id">
            <?php wp_nonce_field('status_edit','name_of_nonce_field'); ?>
        </form>
    </div>
</div>