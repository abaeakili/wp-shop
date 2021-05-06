<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$row=$this->units; 
$edit=$this->edit; 
?>
<div class="wrap">
    <div class="form-wrap">
        <h3><?php echo ($this->edit) ? (_WOP_SHOP_UNITS_MEASURE_EDIT.' / '.$this->units->{Factory::getLang()->get('name')}) : (_WOP_SHOP_UNITS_MEASURE_NEW); ?></h3>
        <form method="POST" action="admin.php?page=options&tab=units&task=save" id="edittax">
			<?php print $this->tmp_html_start?>
            <table width = "100%" class="admintable">
			<?php
			   foreach($this->languages as $lang){
			   $field="name_".$lang->language;
			   ?>
			   <tr>
				 <td class="key">
				   <?php echo _WOP_SHOP_TITLE;?> <?php if ($this->multilang) print "(".$lang->lang.")";?>*
				 </td>
				 <td>
				   <input type="text" class="inputbox" name="<?php print $field;?>" value="<?php echo $row->$field;?>" />
				 </td>
			   </tr>
			   <?php }?>				
				<tr>
				  <td class="key">
					<?php echo _WOP_SHOP_BASIC_QTY;?>*
				  </td>
				  <td>
					<input type="text" class="inputbox" name="qty" value="<?php echo $row->qty;?>" />
				  </td>
				</tr>
				<?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>				
				
             </table>
            <input type="hidden" value="<?php echo $row->id; ?>" name="id">
            <?php wp_nonce_field('unit_edit','name_of_nonce_field'); ?>
            
            <p class="submit">
                <input id="submit" class="button button-primary" type="submit" value="<?php echo _WOP_SHOP_ACTION_SAVE; ?>" name="submit">
                <a class="button" id="back" href="admin.php?page=options&tab=units"><?php echo _WOP_SHOP_BACK; ?></a>
            </p> 
        </form>
    </div>
</div>