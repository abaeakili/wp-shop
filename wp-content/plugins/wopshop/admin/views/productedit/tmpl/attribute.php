<?php
if ( ! defined( 'ABSPATH' ) ) {
 exit; // Exit if accessed directly
}
?>
<div id="tabAttributes" class="tab">
<?php if ( (count($lists['all_independent_attributes'])+count($lists['all_attributes']))>0 ){?>
    <script type="text/javascript">
        var lang_error_attribute = "<?php print _WOP_SHOP_ERROR_ADD_ATTRIBUTE; ?>";
        var lang_attribute_exist = "<?php print _WOP_SHOP_ATTRIBUTE_EXIST; ?>";
        var folder_image_attrib = "<?php print $config->image_attributes_live_path?>";
        var use_basic_price = "<?php print $config->admin_show_product_basic_price?>";
        var use_bay_price = "<?php print $config->admin_show_product_bay_price?>";
        var use_stock = "<?php print intval($config->stock)?>";
        var attrib_images = new Object();
        <?php foreach($lists['attribs_values'] as $k=>$v){?>
        attrib_images[<?php print $v->value_id?>] = "<?php print $v->image?>";
        <?php }?>
    </script>
<?php }?>
<?php if (count($lists['all_attributes'])){ ?>
    <script type="text/javascript">
        var attrib_ids = new Array();
        var attrib_exist = new Object();
        <?php $i=0; foreach($lists['all_attributes'] as $key=>$value){ ?>
            attrib_ids[<?php print $i++;?>] = "<?php echo $value->attr_id ?>";            
       <?php } ?>
       
       <?php
       $attr_tmp_row_num = 0;
       if (count($lists['attribs'])){
           
           foreach($lists['attribs'] as $k=>$v){
               $attr_tmp_row_num++;
               print "attrib_exist[".$attr_tmp_row_num."]={};\n";
               foreach($lists['all_attributes'] as $key=>$value){
                    $tmp_field = "attr_".$value->attr_id;
                    $tmp_val = $v->$tmp_field;
                    print "attrib_exist[".$attr_tmp_row_num."][".$value->attr_id."]='".$tmp_val."';\n";
               }
           
           }
       }
       print "var attr_tmp_row_num = $attr_tmp_row_num;\n";
       ?>       
       </script>
       <table class = "adminlist" id="list_attr_value">
       <thead>
       <tr>
       <?php foreach($lists['all_attributes'] as $key=>$value){ ?>
            <th width="120"><?php echo $value->name?></th>
       <?php } ?>
            <th width="120"><?php print _WOP_SHOP_PRICE; ?></th>
			<?php print $this->dep_attr_td_header?>
            <?php if ($config->stock){?>
            <th width="120"><?php print _WOP_SHOP_QUANTITY_PRODUCT ?></th>
            <?php }?>
            <th width="120"><?php print _WOP_SHOP_EAN_PRODUCT ?></th>
            <th width="120"><?php print _WOP_SHOP_PRODUCT_WEIGHT?> (<?php print sprintUnitWeight()?>)</th>
            <?php if ($config->admin_show_product_basic_price){?>
                <th width="120"><?php print _WOP_SHOP_WEIGHT_VOLUME_UNITS ?></th>
            <?php }?>
            <th width="120"><?php print _WOP_SHOP_OLD_PRICE; ?></th>
            <?php if ($config->admin_show_product_bay_price){?>
            <th width="120"><?php print _WOP_SHOP_PRODUCT_BUY_PRICE; ?></th>
            <?php }?>
            <th></th>
            <th width="60"><input type='checkbox' id='ch_attr_delete_all' onclick="selectAllListAttr(this.checked)"></th>
       </tr>
       </thead>
       <?php       
       if (count($lists['attribs'])){
           $attr_tmp_row_num = 0;
           foreach($lists['attribs'] as $k=>$v){
               $attr_tmp_row_num++;
               print "<tr id='attr_row_".$attr_tmp_row_num."'>";
               foreach($lists['all_attributes'] as $key=>$value){
                    $tmp_field = "attr_".$value->attr_id;
                    $tmp_val = $v->$tmp_field;
                    $tmp_val_val = $lists['attribs_values'][$tmp_val]->name;
                    $image_ = "";
                    if ($lists['attribs_values'][$tmp_val]->image!=''){
                        $image_ = "<img src='".$config->image_attributes_live_path."/".$lists['attribs_values'][$tmp_val]->image."' align='left' hspace='5' width='16' height='16' style='margin-right:5px;' class='img_attrib'>";
                    }
                    print "<td><input type='hidden' name='attrib_id[".$value->attr_id."][]' value='".$tmp_val."'>".$image_.$tmp_val_val."</td>";
               }			   
               print "<td><input type='text' name='attrib_price[]' value='".floatval($v->price)."'></td>";
			   print $this->dep_attr_td_row[$k];
               if ($config->stock){
               print "<td><input type='text' name='attr_count[]' value='".$v->count."'></td>";
               }
               print "<td><input type='text' name='attr_ean[]' value='".$v->ean."'></td>";
               print "<td><input type='text' name='attr_weight[]' value='".$v->weight."'></td>";
               if ($config->admin_show_product_basic_price){
                print "<td><input type='text' name='attr_weight_volume_units[]' value='".$v->weight_volume_units."'></td>";
               }
               print "<td><input type='text' name='attrib_old_price[]' value='".$v->old_price."'></td>";
               if ($config->admin_show_product_bay_price){
                  print "<td><input type='text' name='attrib_buy_price[]' value='".floatval($v->buy_price)."'></td>";
               }
               print "<td>";
               if ($config->use_extend_attribute_data){
                   print "<a class='btn btn-mini button' target='_blank' href='admin.php?page=products&task=edit&product_attr_id=".$v->product_attr_id."' onclick='editAttributeExtendParams(".$v->product_attr_id.");return false;'>"._WOP_SHOP_ATTRIBUTE_EXTEND_PARAMS."</a>";
               }
               print "</td>";		   
               print "<td><input type='hidden' name='product_attr_id[]' value='".$v->product_attr_id."'><input type='checkbox' class='ch_attr_delete' value='".$attr_tmp_row_num."'></td>";
               print "</tr>";
           }           
       }
       print "<tr id='attr_row_end'>";
       foreach($lists['all_attributes'] as $key=>$value){
           print "<td></td>";
       }
       if ($config->stock){
       print "<td></td>";
       }
       print "<td></td><td></td><td></td>";
	   print $this->dep_attr_td_row_empty;
       if ($config->admin_show_product_basic_price) print "<td></td>";
       print "<td></td>";
       if ($config->admin_show_product_bay_price) print "<td></td>";              
       print "<td></td>";
       print "<td><input type='button' value='"._WOP_SHOP_DELETE."' onclick='deleteListAttr()'></td>";
       print "</tr>";
       ?>
       </table>
       <br/>
       <div class="col width-55">
        <fieldset class="adminform" style="margin-left:0px;">
        <legend><?php echo _WOP_SHOP_ADD_ATTRIBUT?></legend>
            <table class="admintable">
            <?php foreach($lists['all_attributes'] as $key=>$value){ ?>
            <tr>
                <td class="key"><?php echo $value->name?></td>
                <td><?php echo $value->values_select;?></td>
            </tr>    
            <?php } ?>
            <tr>
                <td class="key"><?php print _WOP_SHOP_PRICE;?>*</td>
                <td><input type="text" id="attr_price" value="<?php echo $row->product_price?>" style="width:100px;"></td>
            </tr>
			<?php print $this->dep_attr_td_footer;?>
            <?php if ($config->stock){?>
            <tr>
                <td class="key"><?php print _WOP_SHOP_QUANTITY_PRODUCT?>*</td>
                <td><input type="text" id="attr_count"  style="width:100px;" value="1"></td> 
            </tr>
            <?php }?>
            <tr>
                <td class="key"><?php print _WOP_SHOP_EAN_PRODUCT?></td>
                <td><input type="text" id="attr_ean" style="width:100px;" value="<?php echo $row->product_ean?>"></td>
            </tr>
            <tr>
                <td class="key"><?php print _WOP_SHOP_PRODUCT_WEIGHT?></td>
                <td><input type="text" id="attr_weight" style="width:100px;" value="<?php echo $row->product_weight?>"> <?php print sprintUnitWeight();?></td>
            </tr>
            <?php if ($config->admin_show_product_basic_price){?>
            <tr>
                <td class="key"><?php print _WOP_SHOP_WEIGHT_VOLUME_UNITS?></td>
                <td><input type="text" id="attr_weight_volume_units" style="width:100px;" value="<?php echo $row->weight_volume_units?>"></td>
            </tr>
            <?php }?>
            <tr>
                <td class="key"><?php print _WOP_SHOP_OLD_PRICE;?></td>
                <td><input type="text" id="attr_old_price" value="<?php echo $row->product_old_price?>" style="width:100px;"></td>
            </tr>
            <?php if ($config->admin_show_product_bay_price){?>
            <tr>
                <td class="key"><?php print _WOP_SHOP_PRODUCT_BUY_PRICE;?></td>
                <td><input type="text" id="attr_buy_price" value="<?php echo $row->product_buy_price?>" style="width:100px;"> </td>
            </tr>
            <?php }?>
            <tr>
                <td></td>
                <td>
                <div style="width:100px;text-align:right;">                
		<?php print $lists['dep_attr_button_add']?>
                </div>
                </td>
            </tr>            
            </table>
        </fieldset>    
       </div>
       <div class="clr"></div>
       <br/>
   <?php
   }
   
   if (count($lists['all_independent_attributes'])){
   ?>
   <?php foreach($lists['all_independent_attributes'] as $ind_attr){?>
        <div style="padding-top:20px;">
        <table class = "adminlist" id="list_attr_value_ind_<?php print $ind_attr->attr_id?>">
        <thead>
        <tr>
            <th width="150"><?php print $ind_attr->name?></th>
            <th width="120"><?php print _WOP_SHOP_PRICE_MODIFICATION?></th>
            <th width="120"><?php print _WOP_SHOP_PRICE; ?></th>
            <?php print $this->ind_attr_td_header?>
            <th><?php print _WOP_SHOP_DELETE?></th>
        </tr>
        </thead>        
        <?php 
        //print_r($lists['attribs_values']); die();
        if (is_array($lists['ind_attribs_gr'][$ind_attr->attr_id])){
            //echo "<pre>";            print_r($lists['ind_attribs_gr']);            echo "</pre>";
        foreach($lists['ind_attribs_gr'][$ind_attr->attr_id] as $ind_attr_val){ //print_r($ind_attr_val);
        ?>
        <tr id='attr_ind_row_<?php print $ind_attr_val->attr_id?>_<?php print $ind_attr_val->attr_value_id?>'>
            <td>
            <?php if ($lists['attribs_values'][$ind_attr_val->attr_value_id]->image!=''){?>
                <img src='<?php print $config->image_attributes_live_path."/".$lists['attribs_values'][$ind_attr_val->attr_value_id]->image?>' align='left' hspace='5' width='16' height='16' style='margin-right:5px;' class='img_attrib'>
            <?php }?>
            <input type='hidden' id='attr_ind_<?php print $ind_attr_val->attr_id?>_<?php print $ind_attr_val->attr_value_id?>' name='attrib_ind_id[]' value='<?php print $ind_attr_val->attr_id?>'>
            <input type='hidden' name="attrib_ind_value_id[]" value='<?php print $ind_attr_val->attr_value_id?>'>
            <?php /*echo '-'.$ind_attr->attr_id; echo '--'.$ind_attr->attr_id; echo '---'.$ind_attr_val->attr_value_id.'---'; */print $lists['attribs_values'][$ind_attr_val->attr_value_id]->name; ?>
            </td>
            <td><input type='text' name='attrib_ind_price_mod[]' value='<?php print $ind_attr_val->price_mod?>'></td>
            <td><input type='text' name='attrib_ind_price[]' value='<?php print floatval($ind_attr_val->addprice)?>'></td>
            <?php print $this->ind_attr_td_row[$ind_attr_val->attr_value_id]?>
            <td><a href='#' onclick="jQuery('#attr_ind_row_<?php print $ind_attr_val->attr_id?>_<?php print $ind_attr_val->attr_value_id?>').remove();return false;"><img src="<?php print WOPSHOP_PLUGIN_URL?>assets/images/publish_r.png"></a></td>
        </tr>
        <?php }
        }
        ?>
        </table>
        </div>
        
        <div style="padding-top:5px;">
        <table cellpadding="4">
            <tr>
                <td width="150"><?php print $ind_attr->values_select;?></td>
                <td width="120"><?php print $ind_attr->price_modification_select;?></td>
                <td width="120"><input type="text" id="attr_ind_price_tmp_<?php print $ind_attr->attr_id?>" value="0"></td>
                <?php print $this->ind_attr_td_footer[$ind_attr->attr_id]?>
                <td><?php print $ind_attr->submit_button;?></td>
            </tr>
        </table>
        </div>
    <?php }?>
   <br/><br/>
   <?php
   }   
   ?>
   

   <a href="admin.php?page=options&tab=attributes" target="_blank"><img src="<?php echo WOPSHOP_PLUGIN_URL; ?>assets/images/shop_attributes_s.png" border='0' align="left" style="margin-right:5px"><?php print _WOP_SHOP_LIST_ATTRIBUTES;?></a>
   </div>
