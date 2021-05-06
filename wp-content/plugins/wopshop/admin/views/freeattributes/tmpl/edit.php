<form method="POST" action="admin.php?page=options&tab=freeattributes&task=save" enctype="multipart/form-data">
<div class="wrap">
	<h3><?php echo $this->attribut->id ? _WOP_SHOP_EDIT_ATTRIBUT . ' / ' . $this->attribut->{Factory::getLang()->get('name')} :  _WOP_SHOP_NEW_ATTRIBUT; ?></h3>
    <table class="admintable" width = "100%" >
<?php 
    foreach($this->languages as $lang){
    $name="name_".$lang->language;
    ?>
     <tr>
       <td class="key">
         <?php echo _WOP_SHOP_TITLE; ?> <?php if ($this->multilang) print "(".$lang->lang.")";?>* 
       </td>
       <td>
         <input type="text" class="inputbox" name="<?php print $name?>" value="<?php echo $this->attribut->$name?>" />
       </td>
     </tr>
    <?php } ?>
    <?php 
    foreach($this->languages as $lang){
    $description="description_".$lang->language;
    ?>
     <tr>
       <td class="key">
         <?php echo _WOP_SHOP_DESCRIPTION; ?> <?php if ($this->multilang) print "(".$lang->lang.")";?>
       </td>
       <td>
         <input type="text" class="inputbox" name="<?php print $description?>" value="<?php echo $this->attribut->$description?>" />
       </td>
     </tr>
    <?php } ?>
    <tr>
       <td class="key">
         <?php echo _WOP_SHOP_REQUIRED;?>
       </td>
       <td>
         <input type="checkbox" name="required" value="1" <?php if ($this->attribut->required) print "checked";?> />
       </td>
    </tr>
    <?php if ($this->type){print $this->type;}?>
    </table>
</div>
<div clas="submit">
    <p class="submit">
        <input class="button button-primary" type="submit" value="<?php echo _WOP_SHOP_ACTION_SAVE; ?>" name="submit">
		<a class="button" href="admin.php?page=options&tab=freeattributes"><?php echo _WOP_SHOP_BACK; ?></a>
    </p> 
</div>
    <input type="hidden" value="<?php echo $this->attribut->id; ?>" name="id">
    <?php wp_nonce_field('freeattributes_edit','name_of_nonce_field'); ?>
</form>
