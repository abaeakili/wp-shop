<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<div class="wrap">
    <form action="admin.php?page=options&tab=importexport&task=save" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
        <div class="buttons">
            <input type="submit" class="button-primary" value="<?php echo _WOP_SHOP_IMPORT." '".$name."'";?>">
            <a href="admin.php?page=options&tab=importexport" class="button-secondary"><?php echo _WOP_SHOP_BACK_TO.' "'._WOP_SHOP_PANEL_IMPORT_EXPORT.'"'; ?></a>
        </div>
        <br/>
        <input type="hidden" name="ie_id" value="<?php print $ie_id;?>" />

        <?php print _WOP_SHOP_FILE?> (*.csv):
        <input type="file" name="file">
    </form>
</div>