<?php                       
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div id="wshop_plugin">
    <?php print $this->checkout_navigator?>
    <?php print $this->small_cart?>

    <script type="text/javascript">
    var payment_type_check = {};
    <?php foreach($this->payment_methods as  $payment){?>
        payment_type_check['<?php print $payment->payment_class;?>'] = '<?php print $payment->existentcheckform;?>';
    <?php }?>
    </script>

    <div class="wshop checkout_payment_block">
        <form id = "payment_form" name = "payment_form" action = "<?php print $this->action ?>" method = "post" autocomplete="off" enctype="multipart/form-data">
            <?php print $this->_tmp_ext_html_payment_start?>
            <div id="table_payments">
                <?php 
                $payment_class = "";
                foreach($this->payment_methods as  $payment){
                    if ($this->active_payment==$payment->payment_id) $payment_class = $payment->payment_class;
                    ?>                    
                    <div class="name">
                        <input type = "radio" name = "payment_method" id = "payment_method_<?php print $payment->payment_id ?>" onclick = "showPaymentForm('<?php print $payment->payment_class ?>')" value = "<?php print $payment->payment_class ?>" <?php if ($this->active_payment==$payment->payment_id){?>checked<?php } ?> />
                        <label for = "payment_method_<?php print $payment->payment_id ?>"><?php
                            if ($payment->image){
                                ?><span class="payment_image"><img src="<?php print $payment->image?>" alt="<?php print htmlspecialchars($payment->name)?>" /></span><?php
                            }
                            ?><b><?php print $payment->name;?></b> 
                            <?php if ($payment->price_add_text!=''){?>
                                <span class="payment_price">(<?php print $payment->price_add_text?>)</span>
                            <?php }?>
                        </label>
                    </div>                    
                    <div class="paymform" id="tr_payment_<?php print $payment->payment_class ?>" <?php if ($this->active_payment != $payment->payment_id){?>style = "display:none"<?php } ?>>
                        <div class = "wshop_payment_method">
                            <?php print $payment->payment_description?>
                            <?php print $payment->form?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        
            <?php print $this->_tmp_ext_html_payment_end?>
            <input type = "button" id = "payment_submit" class = "btn btn-primary button" name = "payment_submit" value = "<?php print _WOP_SHOP_NEXT ?>" onclick="checkPaymentForm();" />
        </form>
    </div>
</div>
<?php if ($payment_class){ 
	wp_add_inline_script('functions.js', "showPaymentForm('".$payment_class."');");
}