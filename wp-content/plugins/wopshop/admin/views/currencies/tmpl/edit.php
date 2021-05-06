<div class="wrap">
	<h3><?php echo  $this->currency->currency_id ? _WOP_SHOP_EDIT_CURRENCY . ' / ' . $this->currency->currency_name :  _WOP_SHOP_NEW_CURRENCY; ?></h3>
    <form method="POST" action="admin.php?page=options&tab=currencies&task=save" id="editcurrency" class="form-horizontal">
        <div class="shopping">
            <div class="form-group form-field form-required term-publish-wrap">
                <label class="control-label col-sm-2" for="currency_publish"><?php echo _WOP_SHOP_ACTION_PUBLISH; ?>:</label>
                <div class="col-sm-4">
                    <div class="checkbox">
                        <label>
                            <input id="currency_publish" type="checkbox" class="form-control" name="currency_publish" value="1" <?php if ($this->currency->currency_publish) echo 'checked="checked"'; ?> >
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2 form-field form-required term-code-wrap" for="currency_name"><?php echo _WOP_SHOP_NAME;?>*:</label>
                <div class="col-sm-4">
                    <input id="currency_name" type="text" class="form-control" size="40" value="<?php echo $this->currency->currency_name;?>" name="currency_name">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2 form-field form-required term-code-wrap" for="order_currencies"><?php echo _WOP_SHOP_ORDERING_CURRENCY;?>*:</label>
                <div class="col-sm-4">
                    <?php echo $this->lists['order_currencies'];?>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2 form-field form-required term-code-wrap" for="currency_code"><?php echo _WOP_SHOP_CODE;?>*:</label>
                <div class="col-sm-4">
                    <input id="currency_code" type="text" class="form-control" size="40" value="<?php echo $this->currency->currency_code;?>" name="currency_code">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2 form-field form-required term-code-wrap" for="currency_code_iso"><?php echo _WOP_SHOP_CODE." (ISO)";?>*:</label>
                <div class="col-sm-4">
                    <input id="currency_code_iso" type="text" class="form-control" size="3" value="<?php echo $this->currency->currency_code_iso;?>" name="currency_code_iso">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2 form-field term-code-wrap" for="currency_code_num"><?php echo _WOP_SHOP_CODE." ("._WOP_SHOP_NUMERIC.")";?>:</label>
                <div class="col-sm-4">
                    <input id="currency_code_num" type="text" class="form-control" size="40" value="<?php echo $this->currency->currency_code_num;?>" name="currency_code_num">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2 form-field form-required term-code-wrap" for="currency_value"><?php echo _WOP_SHOP_VALUE_CURRENCY;?>*:</label>
                <div class="col-sm-4">
                    <input id="currency_value" type="text" class="form-control" size="40" value="<?php echo $this->currency->currency_value ? $this->currency->currency_value : 1; ?>" name="currency_value">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-4">
                    <input id="submit" class="button-primary" type="submit" value="<?php echo _WOP_SHOP_ACTION_SAVE; ?>" name="submit">
                    <a class="button" id="back" href="admin.php?page=options&tab=currencies"><?php echo _WOP_SHOP_BACK; ?></a>
                </div>
            </div>
        </div>
        
        <input type="hidden" value="<?php echo $this->currency->currency_id; ?>" name="currency_id">
        <?php wp_nonce_field('coutry_edit','name_of_nonce_field'); ?>
    </form>
</div>