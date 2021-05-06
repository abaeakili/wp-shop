<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$row = $this->row;
?>
<div class="form-wrap">
	<h3><?php echo  $row->field_id ? _WOP_SHOP_EDIT . ' / ' . $row->{Factory::getLang()->get('name')} :  _WOP_SHOP_NEW; ?></h3>
    <form action="admin.php?page=options&tab=productfieldvalues&task=save" method="post" name="adminForm" id="adminForm">
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
    </table> 
    
    <div clas="submit">
        <p class="submit">
            <input class="button button-primary" type="submit" value="<?php echo _WOP_SHOP_ACTION_SAVE; ?>" name="submit">
            <a style="margin-left:50px;" href="admin.php?page=options&tab=productfieldvalues&field_id=<?php echo $this->field_id; ?>"><?php echo _WOP_SHOP_BACK; ?></a>
        </p> 
    </div>
    <input type = "hidden" name = "field_id" value = "<?php print $this->field_id?>" />
        <input type = "hidden" name = "id" value = "<?php echo $row->id?>" />
    <?php wp_nonce_field('productfieldvalues_edit','name_of_nonce_field'); ?>
    </form>        
</div>