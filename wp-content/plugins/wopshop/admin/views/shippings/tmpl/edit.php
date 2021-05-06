<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$row = $this->shipping; 
$edit = $this->edit; 
?>
<form action = "admin.php?page=options&tab=shippings&task=save" method = "post" name = "adminForm">
<div class="wrap">
    <div class="form-wrap">
		<h3><?php echo  $row->shipping_id ? _WOP_SHOP_EDIT_SHIPPING . ' / ' . $row->{Factory::getLang()->get('name')} :  _WOP_SHOP_NEW_SHIPPING; ?></h3>
        <fieldset class="adminform">
            <table class="admintable" width = "100%" >
                <tr>
                <td class="key" width = "30%">
                        <?php echo _WOP_SHOP_PUBLISH;?>
                </td>
                <td>
                        <input type = "checkbox" name = "published" value = "1" <?php if ($row->published) echo 'checked = "checked"'?> />
                </td>
                </tr>
            <?php 
            foreach($this->languages as $lang){
            $field = "name_".$lang->language;
            ?>
                <tr>
                <td class="key">
                        <?php echo _WOP_SHOP_TITLE;?> <?php if ($this->multilang) print "(".$lang->lang.")";?>*
                </td>
                <td>
                        <input type = "text" class = "inputbox" id = "<?php print $field?>" name = "<?php print $field?>" value = "<?php echo $row->$field;?>" />
                </td>
                </tr>
            <?php }?>
            <tr>
             <td class="key">
               <?php echo _WOP_SHOP_ALIAS;?>
             </td>
             <td>
               <input type="text" class="inputbox" name="alias" value="<?php echo $row->alias?>" <?php if ($this->config->shop_mode==0 && $row->shipping_id){?>readonly <?php }?> />
             </td>
            </tr>
            <tr>
                <td class="key">
                    <?php echo _WOP_SHOP_PAYMENTS;?>
                </td>
                <td>
                   <?php print $this->lists['payments']?>
                </td>
            </tr>
            <tr>
                <td class="key">
                    <?php echo _WOP_SHOP_IMAGE_URL;?>
                </td>
                <td>
                    <input type="text" class="inputbox" name="image" value="<?php echo $row->image;?>" />
                </td>
            </tr>
            <?php 
            foreach($this->languages as $lang){
            $field = "description_".$lang->language;
            ?>
                <tr>
                <td class="key">
                        <?php echo _WOP_SHOP_DESCRIPTION; ?> <?php if ($this->multilang) print "(".$lang->lang.")";?>
                </td>
                <td>
                <?php $args = array('media_buttons' => 1,
                                    'textarea_name' => "description".$lang->id,
                                    'textarea_rows' => 20,
                                    'tabindex'      => null,
                                    'tinymce'       => 1,
                              );
                              wp_editor( $row->$field, "description".$lang->id, $args );
                        ?>
                </td>
                </tr>
            <?php }?>
        </table>
        </fieldset>
    </div>
    <div class="clr"></div>
</div>
<input type = "hidden" name = "edit" value = "<?php echo $edit;?>" />
<?php if ($edit) {?>
  <input type = "hidden" name = "shipping_id" value = "<?php echo $row->shipping_id?>" />
<?php }?>
<div clas="submit">
    <p class="submit">
        <input class="button button-primary" type="submit" value="<?php echo _WOP_SHOP_ACTION_SAVE; ?>" name="submit">
        <a class="button" href="admin.php?page=options&tab=shippings"><?php echo _WOP_SHOP_BACK; ?></a>
    </p> 
</div>
    <?php wp_nonce_field('shippings_edit','name_of_nonce_field'); ?>
</form>
