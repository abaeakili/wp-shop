<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$row = $this->row;
?>
<div class="form-wrap">
	<h3><?php echo  $row->id ? _WOP_SHOP_EDIT . ' / ' . $row->{Factory::getLang()->get('name')} :  _WOP_SHOP_NEW; ?></h3>
    <form action="admin.php?page=options&tab=productfields&task=save" method="post" name="adminForm" id="adminForm">
    <table width = "100%" class="admintable">
    <?php 
        foreach($this->languages as $lang){
        $name = "name_".$lang->language;
    ?>
        <tr>
            <td class="key" style="width:250px;">
                <?php echo _WOP_SHOP_TITLE; ?> <?php if ($this->multilang) print "(".$lang->lang.")";?>*
            </td>
            <td>
                <input type = "text" class = "inputbox" id = "<?php print $name?>" name = "<?php print $name?>" value = "<?php echo $row->$name;?>" />
            </td>
        </tr>
    <?php }?>
    <?php 
        foreach($this->languages as $lang){
        $description = "description_".$lang->language;
        ?>
        <tr>
            <td class="key" style="width:250px;">
                <?php echo _WOP_SHOP_DESCRIPTION; ?> <?php if ($this->multilang) print "(".$lang->lang.")";?>
            </td>
            <td>
                <input type = "text" class = "inputbox" id = "<?php print $description?>" name = "<?php print $description?>" value = "<?php echo $row->$description;?>" />
            </td>
        </tr>
        <?php }?>
        <tr>
         <td  class="key">
           <?php echo _WOP_SHOP_SHOW_FOR_CATEGORY;?>*
         </td>
         <td>
           <?php echo $this->lists['allcats'];?>
         </td>
        </tr>
        <tr id="tr_categorys" <?php if ($row->allcats=="1" and $row->id > 0) print "style='display:none;'";?>>
         <td  class="key">
           <?php echo _WOP_SHOP_CATEGORIES;?>*
         </td>
         <td>
           <?php echo $this->lists['categories'];?>
         </td>
        </tr>
        <tr>
         <td  class="key">
           <?php echo _WOP_SHOP_TYPE;?>*
         </td>
         <td>
           <?php echo $this->lists['type'];?>
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
    </table> 
    
    <div clas="submit">
        <p class="submit">
            <input class="button button-primary" type="submit" value="<?php echo _WOP_SHOP_ACTION_SAVE; ?>" name="submit">
            <a style="margin-left:50px;" href="admin.php?page=options&tab=productfields"><?php echo _WOP_SHOP_BACK; ?></a>
        </p> 
    </div>
    <input type="hidden" value="<?php echo $this->row->id; ?>" name="id">
    <?php wp_nonce_field('productfields_edit','name_of_nonce_field'); ?>
    </form>        
</div>