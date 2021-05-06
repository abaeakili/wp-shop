<?php 
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}?>
<form method="POST" action="admin.php?page=options&tab=attributesvalues&task=save" enctype="multipart/form-data">
<div class="wrap">
	<h3><?php echo $this->attributValue->value_id ? _WOP_SHOP_EDIT . ' / ' . $this->attributValue->{Factory::getLang()->get('name')} :  _WOP_SHOP_NEW; ?></h3>
    <table class="admintable" width = "100%" >
        <?php 
        foreach($this->languages as $lang){
        $field = "name_".$lang->language;
        ?>
         <tr>
           <td class="key">
             <?php echo _WOP_SHOP_NAME_ATTRIBUT_VALUE;?> <?php if ($this->multilang) print "(".$lang->lang.")";?>* 
           </td>
           <td>
             <input type = "text" class = "inputbox" name = "<?php print $field?>" value = "<?php echo $this->attributValue->$field?>" />
           </td>
         </tr>
      <?php } ?>
      <tr>
        <td class="key"><?php print _WOP_SHOP_IMAGE_ATTRIBUT_VALUE?></td>
        <td>
        <?php if ($this->attributValue->image) {?>
        <div id="image_attrib_value">
            <div><img src = "<?php echo $this->config->image_attributes_live_path."/".$this->attributValue->image?>" alt = ""/></div>
            <div style="padding-bottom:5px;" class="link_delete_foto">
                <a href="#" onclick="if (confirm('<?php print _WOP_SHOP_DELETE_IMAGE;?>')) deleteFotoAttribValue('<?php echo $this->attributValue->value_id?>');return false;">
                    <img src="<?php echo WOPSHOP_PLUGIN_URL; ?>assets/images/publish_r.png"> <?php print _WOP_SHOP_DELETE_IMAGE;?>
                </a>
            </div>
        </div>
        <?php }?>
        <div style="clear:both"></div>
        <input type = "file" name = "image" />
        </td>
      </tr>
    </table>
</div>
<div clas="submit">
    <p class="submit">
        <input class="button button-primary" type="submit" value="<?php echo _WOP_SHOP_ACTION_SAVE; ?>" name="submit">
        <a class="button" href="admin.php?page=options&tab=attributesvalues&attr_id=<?php echo $this->attr_id; ?>"><?php echo _WOP_SHOP_BACK; ?></a>
    </p> 
</div>
    
    <input type="hidden" name="old_image" value="<?php print $this->attributValue->image;?>" />
    <input type="hidden" name="value_id" value="<?php echo $this->attributValue->value_id;?>" />
    <input type="hidden" name="attr_id" value="<?php echo $this->attr_id;?>" />
    <?php wp_nonce_field('attributesvalues_edit','name_of_nonce_field'); ?>
</form>
