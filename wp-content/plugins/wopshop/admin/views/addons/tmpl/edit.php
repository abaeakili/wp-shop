<form method="post" action="admin.php?page=options&tab=addons&task=save" enctype="multipart/form-data" name="adminForm">
    <div class="wrap">
        <h2><?php echo _WOP_SHOP_EDIT_ADDON; ?>: <?php echo $this->row->name; ?></h2>
        <hr />
        <?php echo $this->tmp_html_start; ?>
        
        <?php if ($this->config_file_exist) : ?>
            <?php include $this->config_file_patch; ?>
        <?php endif; ?>
        
        <div clas="submit">
            <p class="submit">
                <input class="button button-primary" type="submit" value="<?php echo _WOP_SHOP_ACTION_SAVE; ?>" name="submit">
                <a class="button" href="admin.php?page=options&tab=addons"><?php echo _WOP_SHOP_BACK; ?></a>
            </p> 
        </div>
        <input type="hidden" value="<?php echo $this->row->id; ?>" name="id">
        <?php wp_nonce_field('addon_edit', 'name_of_nonce_field'); ?>
        
        <?php echo $this->tmp_html_end; ?>
    </div>
</form>