<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<script type="text/javascript">

jQuery(document).ready(function() {
    jQuery('.datepicker').datepicker({
        dateFormat : 'yy-mm-dd'
    });
});

</script>
<div class="wrap">
	<h3><?php echo  $this->coupon->coupon_id ? _WOP_SHOP_EDIT_COUPON  :  _WOP_SHOP_NEW_COUPON; ?></h3>
    <form method="POST" action="admin.php?page=options&tab=coupons&task=save" id="editcoupon">
        <table class="admintable">
            <tr>
                <td class="key"><label for="publish"><?php echo _WOP_SHOP_ACTION_PUBLISH; ?></label></td>
                <td><input id="publish" type="checkbox" value="1" name="coupon_publish" <?php if($this->coupon->coupon_publish > 0) echo 'checked="checked"'; ?> ></td>
            </tr>
            <tr>
                <td class="key"><label for="coupon_code"><?php echo _WOP_SHOP_CODE; ?></label></td>
                <td><input id="coupon_code" type="text" size="40" value="<?php echo $this->coupon->coupon_code; ?>" name="coupon_code"></td>
            </tr>
            <tr>
                <td class="key"><label for="type"><?php echo _WOP_SHOP_TYPE_COUPON; ?></label></td>
                <td>
                    <?php
                        $check_coupon_type0 = '';
                        $check_coupon_type1 = '';
                        if($this->coupon->coupon_type) $check_coupon_type1 = 'checked="checked"'; else $check_coupon_type0 = 'checked="checked"';
                    ?>
                    <div class="controls">
                        <label id="coupon_type0-lbl" class="radio-inline" for="coupon_type0">
                            <input id="coupon_type0" type="radio" onchange="changeCouponType('0')" value="0" name="coupon_type" <?php echo $check_coupon_type0; ?> />
                            <?php echo _WOP_SHOP_COUPON_PERCENT; ?>
                        </label>
                        <label id="coupon_type1-lbl" class="radio-inline" for="coupon_type1">
                            <input id="coupon_type1" type="radio" onchange="changeCouponType('1')" value="1" name="coupon_type" <?php echo $check_coupon_type1; ?> />
                            <?php echo _WOP_SHOP_COUPON_ABS_VALUE; ?>
                        </label>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="key"><label for="value"><?php echo _WOP_SHOP_COUPON_ABS_VALUE; ?></label></td>
                <td>
                    <input id="value" type="text" size="40" value="<?php echo $this->coupon->coupon_value; ?>" name="coupon_value">
                    <?php
                    $hidd_percent = '';
                    $hidd_value = '';
                    if($this->coupon->coupon_type) $hidd_percent = 'display:none;'; else $hidd_value = 'display:none;';
                    ?>
                    <span id="ctype_percent" style="<?php echo $hidd_percent; ?>">%</span>
                    <span id="ctype_value" style="<?php echo $hidd_value; ?>"><?php echo $this->main_curr?></span>
                </td>
            </tr>
            <tr>
                <td class="key"><label for="start_date"><?php echo _WOP_SHOP_START_DATE_COUPON; ?></label></td>
                <td><input id="start_date" class="datepicker" type="text" size="40" value="<?php echo $this->coupon->coupon_start_date;?>" name="coupon_start_date"></td>
            </tr>
            <tr>
                <td class="key"><label for="end_date"><?php echo _WOP_SHOP_EXPIRE_DATE_COUPON; ?></label></td>
                <td><input id="end_date" class="datepicker" type="text" size="40" value="<?php echo $this->coupon->coupon_expire_date;?>" name="coupon_expire_date"></td>
            </tr>
            <tr>
                <td class="key"><label for="user_id"><?php echo _WOP_SHOP_FOR_USER_ID; ?></label></td>
                <td><input id="user_id" type="text" size="40" value="<?php echo $this->coupon->for_user_id;?>" name="for_user_id"></td>
            </tr>
            <tr>
                <td class="key"><label for="after"><?php echo _WOP_SHOP_FINISHED_AFTER_USED; ?></label></td>
                <td>
                    <?php 
                    $check_coupon_after = '';
                    if($this->coupon->finished_after_used) $check_coupon_after = 'checked="checked"';
                    ?>
                    <input id="after" type="checkbox" value="1" name="finished_after_used" <?php echo $check_coupon_after; ?> ></td>
            </tr>
   <?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>			
        </table>

        <div class="wrap shopping">
            <div id="icon-shopping" class="icon32 icon32-shopping-settings"><br></div>
                <p class="submit">
                    <input id="submit" class="button button-primary" type="submit" value="<?php echo _WOP_SHOP_ACTION_SAVE; ?>" name="submit">
                    <a class="button" id="back" href="admin.php?page=options&tab=coupons"><?php echo _WOP_SHOP_BACK; ?></a>
                </p> 
        </div>


        <input type="hidden" value="<?php echo $this->coupon->coupon_id; ?>" name="coupon_id">
        <?php wp_nonce_field('coupon_edit','name_of_nonce_field'); ?>
    </form>
</div>