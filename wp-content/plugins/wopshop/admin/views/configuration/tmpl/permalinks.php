<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
displaySubmenuConfigs('permalinks');
?>
<form action="admin.php?page=configuration&task=save" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
    <?php print $this->tmp_html_start?>
    <?php wp_nonce_field('config', 'config_nonce_field'); ?>
    <input type="hidden" value="11" name="tabs">
    <div class="wrap">
        <fieldset class="adminform">
            <legend><?php echo _WOP_SHOP_PERMALINKS; ?></legend>
            <table class="admintable wp-list-table widefat striped">
                <tr>
                    <td class="key">
                        <?php echo _WOP_SHOP_BASE_SHOP_PAGE; ?>
                    </td>
                    <td>
                        <?php echo $this->lists['shopBasePages']; ?>
                    </td>
                </tr>
            </table>
        </fieldset>
    </div>
    <div class="clear"></div>
    <?php print $this->tmp_html_end ?>
    <p class="submit">
        <input id="submit" class="button button-primary" type="submit" value="<?php echo _WOP_SHOP_ACTION_SAVE; ?>" name="submit">
    </p>
</form>