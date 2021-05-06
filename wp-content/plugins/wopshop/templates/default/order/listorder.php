<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="wshop myorders_list" id="wshop_plugin">

    <h1><?php print _WOP_SHOP_MY_ORDERS ?></h1>
    
    <?php print $this->_tmp_html_before_user_order_list;?>
    
    <?php if (count($this->orders)) {?>
        <?php foreach ($this->orders as $order){?>
            <div class="myorders_block_info">
            
                <div class="order_number">
                    <b><?php print _WOP_SHOP_ORDER_NUMBER ?>:</b>
                    <span><?php print $order->order_number?></span>
                </div>
                <div class="order_status">
                    <b><?php print _WOP_SHOP_ORDER_STATUS ?>:</b>
                    <span><?php print $order->status_name?></span>
                </div>
                
                <div class="table_order_list">
                    <div class="row-fluid">
                        <div class="span6 users">
                            <div>
                                <b><?php print _WOP_SHOP_ORDER_DATE ?>:</b>
                                <span><?php print formatdate($order->order_date, 0) ?></span>
                            </div>
                            <div>
                                <b><?php print _WOP_SHOP_EMAIL_BILL_TO ?>:</b>
                                <span><?php print $order->f_name ?> <?php print $order->l_name ?></span>
                            </div>
                            <div>
                                <b><?php print _WOP_SHOP_EMAIL_SHIP_TO ?>:</b>
                                <span><?php print $order->d_f_name ?> <?php print $order->d_l_name ?></span>
                            </div>
                            <?php print $order->_tmp_ext_user_info;?>
                        </div>
                        <div class="span3 products">
                            <div>
                                <b><?php print _WOP_SHOP_PRODUCTS ?>:</b> 
                                <span><?php print $order->count_products ?></span>
                            </div>
                            <div>
                                <b></b> 
                                <span><?php print formatprice($order->order_total, $order->currency_code)?></span>
                                <?php print $order->_ext_price_html?>
                            </div>
                            <?php print $order->_tmp_ext_prod_info;?>
                        </div>
                        <div class="span3 buttons">
                            <a class="btn" href = "<?php print $order->order_href ?>"><?php print _WOP_SHOP_DETAILS?></a> 
                            <?php print $order->_tmp_ext_but_info;?>
                        </div>
                    </div>
                    <?php print $order->_tmp_ext_row_end;?>
                </div>
            </div>
        <?php } ?>
        
        <div class="myorders_total">
            <span class="name"><?php print _WOP_SHOP_TOTAL?>:</span>
            <span class="price"><?php print formatprice($this->total, getMainCurrencyCode())?></span>
        </div>
        
    <?php }else{ ?>
        <div class="myorders_no_orders">
            <?php print _WOP_SHOP_NO_ORDERS ?>
        </div>
    <?php } ?>
    
    <?php print $this->_tmp_html_after_user_order_list;?>
</div>