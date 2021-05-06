<form method="POST" action="admin.php?page=options&tab=attributes&task=save" enctype="multipart/form-data">
    <div class="wrap">
		<h3><?php echo $this->attribut->attr_id ? _WOP_SHOP_EDIT_ATTRIBUT . ' / ' . $this->attribut->{Factory::getLang()->get('name')} :  _WOP_SHOP_NEW_ATTRIBUT; ?></h3>
        <table class="admintable" width = "100%" >
            <?php 
            foreach($this->languages as $lang){
                $name = "name_".$lang->language;
            ?>
            <tr>
               <td class="key">
                 <?php echo _WOP_SHOP_TITLE; ?> <?php if ($this->multilang) print "(".$lang->lang.")";?>* 
               </td>
               <td>
                 <input type = "text" class = "inputbox" name = "<?php print $name?>" value = "<?php echo $this->attribut->$name?>" />
               </td>
            </tr>
            <?php } ?>
            <?php 
            foreach($this->languages as $lang){
            $description = "description_".$lang->language;
            ?>
            <tr>
               <td class="key">
                 <?php echo _WOP_SHOP_DESCRIPTION; ?> <?php if ($this->multilang) print "(".$lang->lang.")";?>
               </td>
               <td>
                 <input type = "text" class = "inputbox" name = "<?php print $description?>" value = "<?php echo $this->attribut->$description?>" />
               </td>
            </tr>
            <?php } ?>

            <tr>
                <td class="key">
                    <?php echo _WOP_SHOP_TYPE_ATTRIBUT;?>*
                </td>
                <td>
                    <?php echo $this->type_attribut;?>
                    <?php echo HTML::tooltip(_WOP_SHOP_INFO_TYPE_ATTRIBUT);?>
                </td>
            </tr>

            <tr>
                <td class="key">
                    <?php echo _WOP_SHOP_DEPENDENT;?>*
                </td>
                <td>
                    <?php echo $this->dependent_attribut;?>
                    <?php echo HTML::tooltip(_WOP_SHOP_INFO_DEPENDENT_ATTRIBUT);?>
                </td>
            </tr>
            <tr>
             <td  class="key">
               <?php echo _WOP_SHOP_GROUP;?>
             </td>
             <td>
               <?php echo $this->lists['group'];?>
             </td>
            </tr>
            <tr>
             <td  class="key">
               <?php echo _WOP_SHOP_SHOW_FOR_CATEGORY;?>*
             </td>
             <td>
               <?php echo $this->lists['allcats'];?>
             </td>
           </tr>
           <tr id="tr_categorys" <?php if ($this->attribut->allcats=="1") print "style='display:none;'";?>>
             <td  class="key">
               <?php echo _WOP_SHOP_CATEGORIES;?>*
             </td>
             <td>
               <?php echo $this->lists['categories'];?>
             </td>
           </tr>
            <?php if ($this->type){print $this->type;}?>
        </table>
    </div>
    
    <div clas="submit">
        <p class="submit">
            <input class="button button-primary" type="submit" value="<?php echo _WOP_SHOP_ACTION_SAVE; ?>" name="submit">
            <a class="button" href="admin.php?page=options&tab=attributes"><?php echo _WOP_SHOP_BACK; ?></a>
        </p> 
    </div>
    <input type="hidden" value="<?php echo $this->attribut->attr_id; ?>" name="attr_id">
    <?php wp_nonce_field('attributes_edit', 'name_of_nonce_field'); ?>
</form>