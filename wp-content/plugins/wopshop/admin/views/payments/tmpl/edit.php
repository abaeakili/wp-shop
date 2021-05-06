<?php
$row=$this->payment;
$params=$this->params;
$lists=$this->lists;
?>
<div class="wrap">
    <h3><?php echo  $row->payment_id ? _WOP_SHOP_EDIT_PAYMENT . ' / ' . $row->{Factory::getLang()->get('name')} :  _WOP_SHOP_NEW_PAYMENT; ?></h3>
	<form action="admin.php?page=options&tab=payments&task=save" method="post" name="adminForm" id="adminForm">
    <?php print $this->tmp_html_start?>
        <div class="tabs">
            <ul class="tab-links">
                <li class="active"><a href="#tab1"><?php echo _WOP_SHOP_PAYMENT_GENERAL?></a></li>
                <li><a href="#tab2"><?php echo _WOP_SHOP_PAYMENT_CONFIG?></a></li>
            </ul>
            <div class="tab-content">
                <div id="tab1" class="tab active">                   
                    <table class="admintable" width="100%" >
                        <tr>
                            <td class="key" width="30%">
                                <?php echo _WOP_SHOP_PUBLISH?>
                            </td>
                            <td>
                                <input type="checkbox" name="payment_publish" value="1" <?php if ($row->payment_publish) echo 'checked="checked"'?> />
                            </td>
                        </tr>
                        <tr>
                            <td class="key">
                                <?php echo _WOP_SHOP_CODE?>
                            </td>
                            <td>
                                <input type="text" class="inputbox" id="payment_code" name="payment_code" value="<?php echo $row->payment_code;?>" />
                            </td>
                        </tr>
                    <?php foreach($this->languages as $lang){ $field="name_".$lang->language; ?>
                        <tr>
                          <td class="key">
                                <?php echo _WOP_SHOP_TITLE; ?> <?php if ($this->multilang) print "(".$lang->lang.")";?>*
                          </td>
                          <td>
                            <input type="text" class="inputbox" id="<?php print $field?>" name="<?php print $field?>" value="<?php echo $row->$field;?>" />
                          </td>
                        </tr>
                    <?php }?>
                        <tr>
                          <td class="key">
                            <?php echo _WOP_SHOP_ALIAS;?>*
                          </td>
                          <td>
                            <input type="text" class="inputbox" name="payment_class" value="<?php echo $row->payment_class;?>" />
                          </td>
                        </tr>
                        <tr>
                          <td class="key">
                            <?php echo _WOP_SHOP_SCRIPT_NAME?>
                          </td>
                          <td>       
                                <input type="text" class="inputbox" name="scriptname" value="<?php echo $row->scriptname;?>" <?php if ($this->config->shop_mode==0 && $row->payment_id){?>readonly <?php }?> />
                          </td>
                        </tr>
                    <?php if ($this->config->tax){?>
                        <tr>
                            <td class="key">
                                <?php echo _WOP_SHOP_SELECT_TAX;?>*
                            </td>
                            <td>
                                <?php echo $lists['tax'];?>
                            </td>
                        </tr>
                    <?php }?>
                        <tr>
                            <td class="key">
                                <?php echo _WOP_SHOP_PRICE;?>
                            </td>
                            <td>
                                <input type="text" class="inputbox" name="price" value="<?php echo $row->price;?>" />
                                <?php echo $lists['price_type'];?>
                            </td>
                        </tr>
                        <tr>
                            <td class="key">
                                <?php echo _WOP_SHOP_IMAGE_URL;?>
                            </td>
                            <td>
                                <input type="text" class="inputbox" name="image" value="<?php echo $row->image;?>" />
                            </td>
                        </tr>
                        <tr>
                            <td class="key">
                                <?php echo _WOP_SHOP_TYPE_PAYMENT;?>
                            </td>
                            <td>
                                <?php echo $lists['type_payment'];?>
                            </td>
                        </tr>
                    <?php
                    foreach($this->languages as $lang){
                    $field="description_".$lang->language;
                    ?>
                        <tr>
                            <td class="key">
                                <?php echo _WOP_SHOP_DESCRIPTION; ?> <?php if ($this->multilang) print "(".$lang->lang.")";?>
                            </td>
                            <td>
                                <?php
                                  $args = array('media_buttons' => 1, 'textarea_name' => "description".$lang->id, 'textarea_rows' => 20, 'tabindex' => null, 'tinymce' => 1,);
                                  wp_editor( $row->$field, "description".$lang->id, $args );
                                ?>
                            </td>
                        </tr>
                    <?php }?>
                        <tr>
                            <td class="key">
                                <?php echo _WOP_SHOP_SHOW_DESCR_IN_EMAIL;?>
                            </td>
                            <td>
                                <input type="checkbox" name="show_descr_in_email" value="1" <?php if ($row->show_descr_in_email) echo 'checked="checked"'?> />
                            </td>
                        </tr>
                        <tr>
                            <td class="key">
                                <?php echo _WOP_SHOP_SHOW_DEFAULT_BANK_IN_BILL;?>
                            </td>
                            <td>
                                <input type="hidden" name="show_bank_in_order" value="0">
                                <input type="checkbox" name="show_bank_in_order" value="1" <?php if ($row->show_bank_in_order) echo 'checked="checked"'?> />
                            </td>
                        </tr>
                        <tr>
                            <td class="key">
                                <?php echo _WOP_SHOP_DESCRIPTION_IN_BILL;?>
                            </td>
                            <td>
                                <textarea name="order_description" rows="6" cols="30"><?php print $row->order_description?></textarea>
                            </td>
                        </tr>
						<?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>
                    </table> 
                </div>
                <div id="tab2" class="tab">
                <?php
                if ($lists['html']!=""){
                    echo $lists['html'];
                }
                ?>
                </div>
           </div>
        </div>
    <input type="hidden" name="payment_id" value="<?php echo $row->payment_id?>" />
    <?php print $this->tmp_html_end?>
    <?php wp_nonce_field('payment_edit','wop_shop'); ?>
    <p class="submit">
    <input id="submit" class="button button-primary" type="submit" value="<?php echo _WOP_SHOP_ACTION_SAVE; ?>" name="submit">

    <a style="margin-left:50px;" id="back" href="admin.php?page=options&tab=payments"><?php echo _WOP_SHOP_BACK; ?></a>
    </p> 
    </form>
</div>