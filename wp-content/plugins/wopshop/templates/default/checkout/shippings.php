<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div id="wshop_plugin">
    <?php print $this->checkout_navigator?>
    <?php print $this->small_cart?>

    <div class="wshop checkout_shipping_block">
        <form id = "shipping_form" name = "shipping_form" action = "<?php print $this->action ?>" method = "post" onsubmit = "return validateShippingMethods()" autocomplete="off" enctype="multipart/form-data">
            <?php print $this->_tmp_ext_html_shipping_start?>
            <div id = "table_shippings">
                <?php foreach($this->shipping_methods as $shipping){?>
                    <div class="name">
                        <input type = "radio" name = "sh_pr_method_id" id = "shipping_method_<?php print $shipping->sh_pr_method_id?>" value="<?php print $shipping->sh_pr_method_id ?>" <?php if ($shipping->sh_pr_method_id==$this->active_shipping){ ?>checked = "checked"<?php } ?> onclick="showShippingForm(<?php print $shipping->shipping_id?>)" />
                        <label for = "shipping_method_<?php print $shipping->sh_pr_method_id ?>"><?php
                        if ($shipping->image){
                            ?><span class="shipping_image"><img src="<?php print $shipping->image?>" alt="<?php print htmlspecialchars($shipping->name)?>" /></span><?php
                        }
                        ?><b><?php print $shipping->name?></b>
                        <span class="shipping_price">(<?php print formatprice($shipping->calculeprice); ?>)</span>
                        </label>
                        
                        <?php if ($this->config->show_list_price_shipping_weight && count($shipping->shipping_price)){ ?>
                            <table class="shipping_weight_to_price">
                                <?php foreach($shipping->shipping_price as $price){?>
                                    <tr>
                                        <td class="weight">
                                            <?php if ($price->shipping_weight_to!=0){?>
                                                <?php print formatweight($price->shipping_weight_from);?> - <?php print formatweight($price->shipping_weight_to);?>
                                            <?php }else{ ?>
                                                <?php print _WOP_SHOP_FROM." ".formatweight($price->shipping_weight_from);?>
                                            <?php } ?>
                                        </td>
                                        <td class="price">
                                            <?php print formatprice($price->shipping_price); ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                        <?php } ?>
                        
                        <div class="shipping_descr"><?php print $shipping->description?></div>
                        
                        <div id="shipping_form_<?php print $shipping->shipping_id?>" class="shipping_form <?php if ($shipping->sh_pr_method_id==$this->active_shipping) print 'shipping_form_active'?>"><?php print $shipping->form?></div>
                        
                        <?php if ($shipping->delivery){?>
                            <div class="shipping_delivery"><?php print _WOP_SHOP_DELIVERY_TIME.": ".$shipping->delivery?></div>
                        <?php }?>
                        
                        <?php if ($shipping->delivery_date_f){?>
                            <div class="shipping_delivery_date"><?php print _WOP_SHOP_DELIVERY_DATE.": ".$shipping->delivery_date_f?></div>
                        <?php }?>      
                    </div>
                <?php } ?>
            </div>

            <?php print $this->_tmp_ext_html_shipping_end?>
            <input type = "submit" class = "btn btn-primary button" value = "<?php print _WOP_SHOP_NEXT ?>" />
        </form>
    </div>
</div>