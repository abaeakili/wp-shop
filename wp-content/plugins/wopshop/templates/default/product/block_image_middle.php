<?php print $this->_tmp_product_html_body_image?>
<?php if(!count($this->images)){?>
    <img id="main_image" src="<?php print $this->image_product_path?>/<?php print $this->noimage?>" alt="<?php print htmlspecialchars($this->product->name)?>" />
<?php }?>
<?php foreach($this->images as $k=>$image){?>
    <a class="lightbox" id="main_image_full_<?php print $image->image_id?>" href="<?php print $this->image_product_path?>/<?php print $image->image_full;?>" <?php if ($k!=0){?>style="display:none"<?php }?> title="<?php print htmlspecialchars($image->_title)?>">
        <img id = "main_image_<?php print $image->image_id?>" src = "<?php print $this->image_product_path?>/<?php print $image->image_name;?>" alt="<?php print htmlspecialchars($image->_title)?>" title="<?php print htmlspecialchars($image->_title)?>" />
        <div class="text_zoom">
            <img src="<?php print $this->path_to_image?>search.png" alt="zoom" /> <?php print _WOP_SHOP_ZOOM_IMAGE?>
        </div>
    </a>
<?php }?>