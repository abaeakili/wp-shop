<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$row = $this->usergroup;
?>
<div class="wrap">
    <div class="form-wrap">
		<h3><?php echo  $row->usergroup_id ? _WOP_SHOP_EDIT_USERGROUP . ' / ' . $row->{Factory::getLang()->get('name')} :  _WOP_SHOP_NEW_USERGROUP; ?></h3>
        <form method="POST" action="admin.php?page=options&tab=usergroups&task=save" id="edit">
            <div class="wrap shopping">
                <div id="icon-shopping" class="icon32 icon32-shopping-settings"><br></div>
                <div class="wrap">
                    <div class="form-field form-required term-publish-wrap">
                        <label for="publish"><?php echo _WOP_SHOP_DEFAULT; ?></label>
                        <input id="publish" type="checkbox" value="1" name="usergroup_is_default" <?php if($row->usergroup_is_default > 0) echo 'checked="checked"'; ?> >
                    </div>
                    <div class="form-field form-required term-name-wrap">
                        <label for="name"><?php echo _WOP_SHOP_SORT_ALPH; ?></label>
                        <input id="name" type="text" size="40" value="<?php echo $row->usergroup_name; ?>" name="usergroup_name">
                    </div>
                    <div class="form-field form-required term-description-wrap">
                        <label for="description"><?php echo _WOP_SHOP_DESCRIPTION; ?></label>
                        <textarea id="description" name="usergroup_description"><?php echo $row->usergroup_description; ?></textarea>
                    </div>
                    <div class="form-field form-required term-discount-wrap">
                        <label for="discount"><?php echo _WOP_SHOP_DISCOUNT; ?> (%)</label>
                        <input id="discount" type="text" size="40" value="<?php echo $row->usergroup_discount;?>" name="usergroup_discount">
                     </div>
                    <p class="submit">
                        <input id="submit" class="button button-primary" type="submit" value="<?php echo _WOP_SHOP_ACTION_SAVE; ?>" name="submit">
                        <a class="button" id="back" href="admin.php?page=options&tab=usergroups"><?php echo _WOP_SHOP_BACK; ?></a>
                    </p> 
                </div>
            </div>
            <input type="hidden" value="<?php echo $row->usergroup_id; ?>" name="usergroup_id">
            <?php wp_nonce_field('usergroups_edit','name_of_nonce_field'); ?>
        </form>
    </div>
</div>