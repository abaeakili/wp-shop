<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div id="product_related" class="tab-pane">
    <div class="col100">
        <fieldset class="adminform">
            <legend><?php echo _WOP_SHOP_PRODUCT_RELATED ?></legend>
            <div id="list_related">
                <?php
                foreach($this->related_products as $row_related){
                    if (!$row_related->image) $row_related->image = $config->noimage;
                ?>
                    <div class="block_related" id="related_product_<?php print $row_related->product_id;?>">
                        <div class="block_related_inner">
                            <div class="name"><?php echo $row_related->name;?> (ID:&nbsp;<?php print $row_related->product_id?>)</div>
                            <div class="image">
                                <a href="admin.php?page=products&task=edit&product_id=<?php print $row_related->product_id;?>"><img src="<?php print getPatchProductImage($row_related->image, 'thumb', 1)?>" width="90" border="0" /></a>
                            </div>
                            <div style="padding-top:5px;"><input type="button" value="<?php print _WOP_SHOP_DELETE;?>" onclick="delete_related(<?php print $row_related->product_id;?>)"></div>
                            <input type="hidden" name="related_products[]" value="<?php print $row_related->product_id;?>"/>
                            </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </fieldset>
    </div>
    <div class="clr"></div>
   <br/>
   <div class="col100">
    <fieldset class="adminform">
        <legend><?php echo _WOP_SHOP_SEARCH ?></legend>
       <div>
            <input type="text" size="35" id="related_search" value="" />
            &nbsp;
            <input type="button" class="button" value="<?php echo _WOP_SHOP_SEARCH;?>" onclick="releted_product_search(0, '<?php echo $row->product_id?>');" />
        </div>
        <br/>
        <div id="list_for_select_related"></div>
    </fieldset>
    </div>
    <div class="clr"></div>
</div>