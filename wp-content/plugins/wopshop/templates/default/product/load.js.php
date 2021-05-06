<script type="text/javascript">
    <?php if ($this->product->product_quantity >0){?>
    var translate_not_available = "<?php print addslashes(_WOP_SHOP_PRODUCT_NOT_AVAILABLE_THIS_OPTION)?>";
    <?php }else{?>
    var translate_not_available = "<?php print addslashes(_WOP_SHOP_PRODUCT_NOT_AVAILABLE)?>";
    <?php }?>
    var translate_zoom_image = "<?php print addslashes(_WOP_SHOP_ZOOM_IMAGE)?>";
    var product_basic_price_volume = <?php print $this->product->weight_volume_units;?>;
    var product_basic_price_unit_qty = <?php print $this->product->product_basic_price_unit_qty;?>;
    var currency_code = "<?php print $this->config->currency_code;?>";
    var format_currency = "<?php print $this->config->format_currency[$this->config->currency_format];?>";
    var decimal_count = <?php print $this->config->decimal_count;?>;
    var decimal_symbol = "<?php print $this->config->decimal_symbol;?>";
    var thousand_separator = "<?php print $this->config->thousand_separator;?>";
    var attr_value = new Object();
    var attr_list = new Array();
    var attr_img = new Object();
    var liveurl = '<?php print WOPSHOP_PLUGIN_URL?>';
    var liveattrpath = '<?php print $this->config->image_attributes_live_path;?>';
    var liveproductimgpath = '<?php print $this->config->image_product_live_path;?>';
    var liveimgpath = '<?php print $this->config->live_path."assets/images";?>';
    var urlupdateprice = '<?php print $this->urlupdateprice;?>';
    <?php if($this->config->load_jquery_lightbox) : ?>
        function initWSlightBox(){
            jQuery("a.lightbox").lightBox({
                imageLoading: "<?php echo WOPSHOP_PLUGIN_URL;?>/assets/images/loading.gif",
                imageBtnClose: "<?php echo WOPSHOP_PLUGIN_URL;?>/assets/images/close.gif",
                imageBtnPrev: "<?php echo WOPSHOP_PLUGIN_URL;?>/assets/images/prev.gif",
                imageBtnNext: "<?php echo WOPSHOP_PLUGIN_URL;?>/assets/images/next.gif",
                imageBlank: "<?php echo WOPSHOP_PLUGIN_URL;?>/assets/images/blank.gif",
                txtImage: "<?php echo _WOP_SHOP_IMAGE;?>",
                txtOf: "<?php echo _WOP_SHOP_OF;?>"
            });
        }
		<?php wp_add_inline_script('functions.js', 'jQuery(document).ready(function(){initWSlightBox();});'); ?>
    <?php endif; ?>
    <?php if (count($this->attributes)){?>
        <?php $i=0;foreach($this->attributes as $attribut){?>
        attr_value["<?php print $attribut->attr_id?>"] = "<?php print $attribut->firstval?>";
        attr_list[<?php print $i++;?>] = "<?php print $attribut->attr_id?>";
        <?php } ?>
    <?php } ?>
    <?php foreach($this->all_attr_values as $attrval){ if ($attrval->image){?>attr_img[<?php print $attrval->value_id?>] = "<?php print $attrval->image?>";<?php } }?>
    <?php print $this->_tmp_product_ext_js;?>
</script>