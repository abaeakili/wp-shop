<form method="POST" action="admin.php?page=configuration&tab=statictext&task=save" enctype="multipart/form-data">
<div class="wrap">
    <h2><?php echo _WOP_SHOP_EDIT_STATICTEXT; echo " "; print $this->statictext->alias; ?></h2>
    <?php if (!$this->statictext->id){?>
    <ul>
       <li class="key">
         <?php echo _WOP_SHOP_ALIAS; ?>
       </li>
       <li>
         <input type="text" class="inputbox" name="alias" size="40" value="<?php echo $this->statictext->alias; ?>" />
       </li>
    </ul>
    <?php } ?>
    <div class="tabs">
        <ul class="tab-links">
            <?php 
            foreach($this->languages as $index=>$language){?>
                <li><a <?php if($index=0) echo ' class="active" '; ?> href="<?php echo '#tab'.$language->language; ?>"><?php echo _WOP_SHOP_DESCRIPTION.' '; echo $language->name; ?></a></li>
            <?php } ?>
        </ul>
        <div class="tab-content">
        <?php foreach($this->languages as $index=>$language){?>
        <div class="tab <?php if($index=0) echo ' active '; ?>" id="tab<?php echo $language->language; ?>" style="<?php if($index != 0) echo ' display:none; '; ?>">
            <div class="form-field form-required term-code-wrap">
                <?php 
                $val_description = 'text_'.$language->language;
				$args = array('media_buttons' => 1,'textarea_name' => "text".$language->id,'textarea_rows' => 20,'tabindex'      => null,'tinymce'       => 1);
				wp_editor( $this->statictext->$val_description, "text".$language->text, $args );
                ?>
            </div>
        </div>
    <?php } ?>
        </div>
    </div>
<div clas="submit">
    <p class="submit">
        <input class="button button-primary" type="submit" value="<?php echo _WOP_SHOP_ACTION_SAVE; ?>" name="submit">
        <a class="button" href="admin.php?page=configuration&tab=statictext"><?php echo _WOP_SHOP_BACK; ?></a>
    </p> 
</div>
    <input type="hidden" value="<?php echo $this->statictext->id; ?>" name="statictext_id">
    <?php wp_nonce_field('statictext_edit','name_of_nonce_field'); ?>
</form>
