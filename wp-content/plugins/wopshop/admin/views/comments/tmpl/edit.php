<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
displaySubmenuOptions('reviews');
?>
<h3><?php echo $this->review->review_id ? _WOP_SHOP_EDIT_REVIEW :  _WOP_SHOP_NEW_REVIEW; ?></h3>
<div class="wop_shop_edit">
<form name="product" method="POST" action="admin.php?page=options&tab=reviews&task=save" enctype="multipart/form-data">
<?php print $this->tmp_html_start?>
     <div class="col100">
     <table class="admintable" >
       <?php if ($this->review->review_id){ ?>
       <tr>
         <td class="key" style="width:180px;">
           <?php echo _WOP_SHOP_NAME_PRODUCT; ?>
         </td>
         <td>
           <?php echo $this->review->name?>     
           <input type="hidden" name="product_id" value="<?php print $this->review->product_id;?>">
         </td>
       </tr>
       <?php }else { ?>
       <tr>
         <td class="key" style="width:180px;">
           <?php echo _WOP_SHOP_PRODUCT_ID;?>*
         </td>
         <td>
           <input type="text" name="product_id" value="">    
         </td>
       </tr>    
       <?php } ?>
       <tr>
         <td class="key" style="width:180px;">
           <?php echo _WOP_SHOP_USER; ?>*
         </td>
         <td>
           <input type="text" class="inputbox" size="50" name="user_name" value="<?php echo $this->review->user_name?>" />
         </td>
       </tr>
       <tr>
         <td class="key" style="width:180px;">
           <?php echo _WOP_SHOP_EMAIL; ?>*
         </td>
         <td>
           <input type="text" class="inputbox" size="50" name="user_email" value="<?php echo $this->review->user_email?>" />
         </td>
       </tr>       
              
       <tr>
         <td  class="key">
           <?php echo _WOP_SHOP_PRODUCT_REVIEW; ?>*
         </td>
         <td>
           <textarea name="review" cols="35"><?php echo $this->review->review ?></textarea>
         </td>
       </tr>
       <tr>
        <td class="key">
          <?php echo _WOP_SHOP_REVIEW_MARK; ?> 
        </td>
        <td>
            <?php print $this->mark?>
        </td>
       </tr>
     </table>
     </div>
     <div class="clr"></div>
     <input type="hidden" name="review_id" value="<?php echo $this->review->review_id?>">
<?php print $this->tmp_html_end?>
     <br>    
<div clas="submit">
    <p class="submit">
        <input class="button button-primary" type="submit" value="<?php echo _WOP_SHOP_ACTION_SAVE; ?>" name="submit">
        <a  class="button" href="admin.php?page=options&tab=reviews"><?php echo _WOP_SHOP_BACK; ?></a>
    </p> 
</div>
    <?php wp_nonce_field('review_edit','name_of_nonce_field'); ?>

</form>
</div>