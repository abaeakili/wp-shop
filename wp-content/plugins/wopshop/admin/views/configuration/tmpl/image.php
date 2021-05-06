<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$lists=$this->lists;
$config = $this->config;
displaySubmenuConfigs('image');
?>
<form action="admin.php?page=configuration&task=save" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<?php print $this->tmp_html_start?>
<?php wp_nonce_field('config','config_nonce_field'); ?>
<input type="hidden" name="layout" value="image">
<input type="hidden" value="3" name="tabs">
<div class="col100">
<fieldset class="adminform">
    <legend><?php echo _WOP_SHOP_IMAGE_VIDEO_PARAMETERS ?></legend>
<table class="admintable">
  <tr>
    <td class="key" style="width:200px;">
      <?php echo _WOP_SHOP_IMAGE_CATEGORY_WIDTH?>
    </td>
    <td>
      <input type="text" name="image_category_width" id="image_category_width" value ="<?php echo $config->image_category_width?>" />
    </td>
    <td>
    </td>
  </tr>
  <tr>
    <td class="key">
      <?php echo _WOP_SHOP_IMAGE_CATEGORY_HEIGHT?>
    </td>
    <td>
      <input type="text" name="image_category_height" id="image_category_height" value ="<?php echo $config->image_category_height?>" />
    </td>
    <td>
    </td>
  </tr>
  <tr><td></td></tr>
  <tr>
    <td class="key">
      <?php echo _WOP_SHOP_IMAGE_PRODUCT_THUMB_WIDTH?>
    </td>
    <td>
      <input type="text" name="image_product_width" id="image_product_width" value ="<?php echo $config->image_product_width?>" />
    </td>
    <td>
    </td>
  </tr>
  <tr>
    <td class="key">
      <?php echo _WOP_SHOP_IMAGE_PRODUCT_THUMB_HEIGHT?>
    </td>
    <td>
      <input type="text" name="image_product_height" id="image_product_height" value ="<?php echo $config->image_product_height?>" />
    </td>
  </tr>
  <tr><td></td></tr>
  <tr>
    <td class="key">
      <?php echo _WOP_SHOP_IMAGE_PRODUCT_FULL_WIDTH;?>
    </td>
    <td>
      <input type="text" name="image_product_full_width" id="image_product_full_width" value ="<?php echo $config->image_product_full_width?>" />
    </td>
  </tr>
  <tr>
    <td class="key">
      <?php echo _WOP_SHOP_IMAGE_PRODUCT_FULL_HEIGHT; ?>
    </td>
    <td>
      <input type="text" name="image_product_full_height" id="image_product_full_height" value ="<?php echo $config->image_product_full_height?>" />
    </td>
    <td>
    </td>
  </tr>
  <tr><td></td></tr>
  <tr>
    <td class="key">
      <?php echo _WOP_SHOP_IMAGE_PRODUCT_ORIGINAL_WIDTH;?>
    </td>
    <td>
      <input type="text" name="image_product_original_width" value="<?php echo $config->image_product_original_width?>" />
    </td>
  </tr>
  <tr>
    <td class="key">
      <?php echo _WOP_SHOP_IMAGE_PRODUCT_ORIGINAL_HEIGHT; ?>
    </td>
    <td>
      <input type="text" name="image_product_original_height" value="<?php echo $config->image_product_original_height?>" />
    </td>
    <td>
    </td>
  </tr>
  <tr><td></td></tr>
  <tr>
    <td class="key">
      <?php echo _WOP_SHOP_VIDEO_PRODUCT_WIDTH;?>
    </td>
    <td>
      <input type="text" name="video_product_width" value ="<?php echo $config->video_product_width?>" />      
    </td>
  </tr>
  <tr>
    <td class="key">
      <?php echo _WOP_SHOP_VIDEO_PRODUCT_HEIGHT; ?>
    </td>
    <td>
      <input type="text" name="video_product_height" value ="<?php echo $config->video_product_height?>" />
    </td>
    <td>
    </td>
  </tr>
  <tr>
    <td class="key">
      <?php echo _WOP_SHOP_IMAGE_RESIZE_TYPE; ?>
    </td>
    <td>
      <?php print $this->select_resize_type;?>
    </td>
    <td>
    </td>
  </tr>
<tr>
    <td class="key">
        <?php echo _WOP_SHOP_OC_image_quality?>
    </td>
    <td>
        <input type="text" name="image_quality" value ="<?php echo $config->image_quality?>" />
    </td>
</tr>

<tr>
    <td class="key">
        <?php echo _WOP_SHOP_OC_image_fill_color?>
    </td>
    <td>
        <input type="text" name="image_fill_color" value ="<?php echo $config->image_fill_color?>" />
    </td>
</tr>

</table>
</fieldset>
</div>
<div class="clr"></div>
<?php print $this->tmp_html_end?>
<p class="submit">
    <input id="submit" class="button button-primary" type="submit" value="<?php echo _WOP_SHOP_ACTION_SAVE; ?>" name="submit">
</p> 
</form>