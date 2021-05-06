<?php 
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}?>
<form method="POST" action="admin.php?page=options&tab=attributesgroups&task=save" enctype="multipart/form-data">
<div class="wrap">
	<h3><?php echo $this->row->id ? _WOP_SHOP_EDIT . ' / ' . $this->row->{Factory::getLang()->get('name')} :  _WOP_SHOP_NEW; ?></h3>
    <h2><?php echo _WOP_SHOP_EDIT; ?></h2>
    <table width="100%" class="admintable">
    <?php 
    foreach($this->languages as $lang){
        $field = "name_".$lang->language;
    ?>
       <tr>
         <td class="key" style="width:250px;">
            <?php echo _WOP_SHOP_TITLE; ?> <?php if ($this->multilang) print "(".$lang->lang.")";?>*
         </td>
         <td>
            <input type="text" class="inputbox" id="<?php print $field?>" name="<?php print $field?>" value="<?php echo $this->row->$field;?>" />
         </td>
       </tr>
    <?php }?>
    </table>
</div>
<div clas="submit">
    <p class="submit">
        <input class="button button-primary" type="submit" value="<?php echo _WOP_SHOP_ACTION_SAVE; ?>" name="submit">
        <a class="button" href="admin.php?page=options&tab=attributesgroups"><?php echo _WOP_SHOP_BACK; ?></a>
    </p> 
</div>
    <input type="hidden" value="<?php echo $this->row->id; ?>" name="id">
    <?php wp_nonce_field('attributesgroups_edit','name_of_nonce_field'); ?>
</form>
