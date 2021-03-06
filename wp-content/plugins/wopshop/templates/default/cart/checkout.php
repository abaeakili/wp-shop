<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="wshop">
<table class = "wshop cart cartcheckout">
    <tr>
        <th class="wshop_img_description_center" width = "20%">
            <?php print _WOP_SHOP_IMAGE?>
        </th>
        <th class="product_name">
            <?php print _WOP_SHOP_ITEM?>
        </th>    
        <th class="single_price" width = "15%">
            <?php print _WOP_SHOP_SINGLEPRICE ?>
        </th>
        <th class="quantity" width = "15%">
            <?php print _WOP_SHOP_NUMBER ?>
        </th>
        <th class="total_price" width = "15%">
            <?php print _WOP_SHOP_PRICE_TOTAL ?>
        </th>
    </tr>
    <?php
    $i=1;
    foreach($this->products as $key_id=>$prod){
    ?> 
    <tr class = "wshop_prod_cart <?php if ($i%2==0) print "even"; else print "odd"?>">
        <td class = "wshop_img_description_center">
            <div class="mobile-cart">
                <?php print _WOP_SHOP_IMAGE; ?>
            </div>
            <div class="data">
                <a href = "<?php print $prod['href']; ?>">
                    <img src = "<?php print $this->image_product_path ?>/<?php if ($prod['thumb_image']) print $prod['thumb_image']; else print $this->no_image; ?>" alt = "<?php print htmlspecialchars($prod['product_name']);?>" class = "wshop_img" />
                </a>
            </div>
        </td>
        <td class="product_name">
            <div class="mobile-cart">
                <?php print _WOP_SHOP_ITEM; ?>
            </div>
            <div class="data">
                <a href="<?php print $prod['href']?>">
                    <?php print $prod['product_name']?>
                </a>
                <?php if ($this->config->show_product_code_in_cart){?>
                    <span class="wshop_code_prod">(<?php print $prod['ean']?>)</span>
                <?php }?>
                <?php if ($prod['manufacturer']!=''){?>
                    <div class="manufacturer">
                        <?php print _WOP_SHOP_MANUFACTURER?>: 
                        <span><?php print $prod['manufacturer']?></span>
                    </div>
                <?php }?>
                <?php print sprintAtributeInCart($prod['attributes_value']);?>
                <?php print sprintFreeAtributeInCart($prod['free_attributes_value']);?>
                <?php print sprintFreeExtraFiledsInCart($prod['extra_fields']);?>
                <?php print $prod['_ext_attribute_html']?>
                <?php if ($this->config->show_delivery_time_step5 && $this->step==5 && $prod['delivery_times_id']){?>
                    <div class="deliverytime">
                        <?php print _WOP_SHOP_DELIVERY_TIME?>: 
                        <?php print $this->deliverytimes[$prod['delivery_times_id']]?>
                    </div>
                <?php }?>
            </div>
        </td>    
        <td class="single_price">
            <div class="mobile-cart">
                <?php print _WOP_SHOP_SINGLEPRICE; ?>
            </div>
            <div class="data">
                <?php print formatprice($prod['price'])?>
                <?php print $prod['_ext_price_html']?>
                <?php if ($this->config->show_tax_product_in_cart && $prod['tax']>0){?>
                    <span class="taxinfo"><?php print productTaxInfo($prod['tax']);?></span>
                <?php }?>
                <?php if ($this->config->cart_basic_price_show && $prod['basicprice']>0){?>
                    <div class="basic_price">
                        <?php print _WOP_SHOP_BASIC_PRICE?>: 
                        <span><?php print sprintBasicPrice($prod);?></span>
                    </div>
                <?php }?>
            </div>
        </td>
        <td class="quantity">
            <div class="mobile-cart">
                <?php print _WOP_SHOP_NUMBER; ?>
            </div>
            <div class="data">
                <?php print $prod['quantity']?><?php print $prod['_qty_unit'];?>
            </div>
        </td>
        <td class="total_price">
            <div class="mobile-cart">
                <?php print _WOP_SHOP_PRICE_TOTAL; ?>
            </div>
            <div class="data">
                <?php print formatprice($prod['price']*$prod['quantity']);?>
                <?php print $prod['_ext_price_total_html']?>
                <?php if ($this->config->show_tax_product_in_cart && $prod['tax']>0){?>
                    <span class="taxinfo"><?php print productTaxInfo($prod['tax']);?></span>
                <?php }?>
            </div>
        </td>
    </tr>
    <?php 
    $i++;
    }
    ?>
</table>
  
<?php if ($this->config->show_weight_order){?>  
    <div class="weightorder">
        <?php print _WOP_SHOP_WEIGHT_PRODUCTS?>: <span><?php print formatweight($this->weight);?></span>
    </div>
<?php }?>
  
<div class="cartdescr"><?php print $this->checkoutcartdescr;?></div>
  
<table class = "wshop wshop_subtotal">
    <?php if (!$this->hide_subtotal){?>
        <tr class="subtotal">    
            <td class = "name">
                <?php print _WOP_SHOP_SUBTOTAL ?>
            </td>
            <td class = "value">
                <?php print formatprice($this->summ);?><?php print $this->_tmp_ext_subtotal?>
            </td>
        </tr>
    <?php } ?>

    <?php print $this->_tmp_html_after_subtotal?>

    <?php if ($this->discount > 0){ ?>
        <tr class="discount">
            <td class = "name">
                <?php print _WOP_SHOP_RABATT_VALUE ?><?php print $this->_tmp_ext_discount_text?>
            </td>
            <td class = "value">
                <?php print formatprice(-$this->discount);?><?php print $this->_tmp_ext_discount?>
            </td>
        </tr>
    <?php } ?>

    <?php if (isset($this->summ_delivery)){?>
        <tr class="shipping">
            <td class = "name">
                <?php print _WOP_SHOP_SHIPPING_PRICE;?>
            </td>
            <td class = "value">
                <?php print formatprice($this->summ_delivery);?><?php print $this->_tmp_ext_shipping?>
            </td>
        </tr>
    <?php } ?>

    <?php if (isset($this->summ_package)){?>
        <tr class="package">
            <td class = "name">
                <?php print _WOP_SHOP_PACKAGE_PRICE;?>
            </td>
            <td class = "value">
                <?php print formatprice($this->summ_package);?><?php print $this->_tmp_ext_shipping_package?>
            </td>
        </tr>
    <?php } ?>

    <?php if ($this->summ_payment != 0){ ?>
        <tr class="payment">
            <td class = "name">
                <?php print $this->payment_name;?>
            </td>
            <td class = "value">
                <?php print formatprice($this->summ_payment);?><?php print $this->_tmp_ext_payment?>
            </td>
        </tr>
    <?php } ?>

    <?php if (!$this->config->hide_tax){ ?>
        <?php foreach($this->tax_list as $percent=>$value){?>
            <tr class="tax">
                <td class = "name">
                    <?php print displayTotalCartTaxName();?>
                    <?php if ($this->show_percent_tax) print formattax($percent)."%"?>
                </td>
                <td class = "value">
                    <?php print formatprice($value);?><?php print $this->_tmp_ext_tax[$percent]?>
                </td>
            </tr>
        <?php } ?>
    <?php } ?>

    <tr class="total">
        <td class = "name">
            <?php print $this->text_total; ?>
        </td>
        <td class = "value">
            <?php print formatprice($this->fullsumm)?><?php print $this->_tmp_ext_total?>
        </td>
    </tr>

    <?php print $this->_tmp_html_after_total?>

    <?php if ($this->free_discount > 0){?>  
        <tr class="free_discount">
            <td colspan="2" align="right">    
                <span class="free_discount">
                    <?php print _WOP_SHOP_FREE_DISCOUNT;?>:
                    <span><?php print formatprice($this->free_discount); ?></span>
                </span>
            </td>
        </tr>
    <?php }?>  
</table>

<?php print $this->_tmp_html_after_checkout_cart?>

</div>