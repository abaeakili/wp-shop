<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$row=$this->productLabel;
?>
<div class="wrap">
    <div class="form-wrap">
		<h3><?php echo  $row->id ? _WOP_SHOP_PRODUCT_LABEL_EDIT . ' / ' . $row->{Factory::getLang()->get('name')} :  _WOP_SHOP_PRODUCT_LABEL_NEW; ?></h3>
        <form method="POST" action="admin.php?page=options&tab=productlabels&task=save" id="editproductlabel" enctype="multipart/form-data">
            <div class="wrap shopping">
                <div id="icon-shopping" class="icon32 icon32-shopping-settings"><br></div>
                <div class="wrap">
                    <?php 
                    foreach($this->languages as $index=>$language){?>
                        <div class="form-field form-required term-name-wrap">
                            <label for="name_<?php echo $language->language; ?>"><?php echo _WOP_SHOP_TITLE; ?> <?php echo $language->name; ?></label>
                            <input id="name_<?php echo $language->language; ?>" type="text" size="40" value="<?php $n = 'name_'.$language->language; echo $row->$n; ?>" name="name_<?php echo $language->language; ?>">
                        </div>
                    <?php
                    }?>
                    <div class="form-field form-required term-code-wrap" id="images_container">
                        <?php if($row->image){?>
                        <div>
                            <div>
                                <img src="<?php echo $this->config->image_labels_live_path."/".$row->image; ?>">
                            </div>
                            <div class="link_delete_foto">
                                <a onclick="if (confirm('<?php echo _WOP_SHOP_DELETE_IMAGE; ?>')) deleteFotoProductlabel(<?php echo $row->id; ?>);return false;" href="#">
                                    <img src="<?php print WOPSHOP_PLUGIN_URL?>assets/images/publish_r.png">
                                    <?php echo _WOP_SHOP_DELETE_IMAGE; ?>
                                </a>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                
                    <div class="form-field form-required term-code-wrap">
                        <label for="productlabel_image"><?php echo _WOP_SHOP_IMAGE_SELECT; ?></label>
                        <input id="productlabel_image" type="file" name="productlabel_image">
                    </div>                    
                    
                    <p class="submit">
                        <input id="submit" class="button button-primary" type="submit" value="<?php echo _WOP_SHOP_ACTION_SAVE; ?>" name="submit">
                        <a class="button" id="back" href="admin.php?page=options&tab=productlabels"><?php echo _WOP_SHOP_BACK; ?></a>
                    </p> 
                </div>
            </div>
            <input type="hidden" value="<?php echo $row->id; ?>" name="id">
            <?php wp_nonce_field('productlabel_edit','name_of_nonce_field'); ?>
        </form>
    </div>
</div>