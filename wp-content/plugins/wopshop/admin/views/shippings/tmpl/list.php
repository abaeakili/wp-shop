<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
displaySubmenuOptions('shippings');
$shippings = $this->rows;
$saveOrder = $this->filter_order_Dir=="desc" && $this->filter_order=="ordering";
$count = count($this->rows);
$i = 0;
?>
<div class="wrap">
    <h2>
        <?php echo _WOP_SHOP_SHIPPING_PRICES_LIST; ?>
        <a href="admin.php?page=options&tab=shippings&task=edit&shipping_id=0" class="add-new-h2"><?php echo _WOP_SHOP_NEW_SHIPPING; ?></a>
        <a href="admin.php?page=options&tab=shippingextprice" class="add-new-h2"><?php echo _WOP_SHOP_SHIPPING_EXT_PRICE_CALC; ?></a>
    </h2>
	<form id="listing" class="adminForm" action = "admin.php?page=options&tab=shippings" method = "post" name = "adminForm">
        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <?php echo $this->bulk;?>
            </div>
            <br class="clear">
        </div>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th id="cb" class="manage-column column-cb check-column" style="" scope="col">
                        <input id="cb-select-all-1" type="checkbox">
                    </th>
                    <?php if($this->filter_order == 'name') $class_name = 'sorted'; else $class_name = 'sortable';?>
                    <th id="order_title" class="column-primary manage-column column-order_title <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col">
                        <a href="admin.php?page=options&tab=shippings&filter_order=name&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_TITLE; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th id="order_id" class="manage-column column-order_id <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col" width="200px">
                        <span class="status_head tips"><?php echo _WOP_SHOP_SHIPPING_PRICES; ?></span>
                    </th>
                    <?php if($this->filter_order == 'ordering') $class_name = 'sorted'; else $class_name = 'sortable';?>
                    <th colspan="2" id="ordering" class="ordering center manage-column column-ordering <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col" width="100">
                        <a href="admin.php?page=options&tab=shippings&filter_order=ordering&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_ORDERING; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>						
                    </th>
					
                    <th id="saveorder" class="save_ordering center manage-column" scope="col" width="100">
        <?php if ($saveOrder){?>
						<a class="saveorder" onclick="saveorder();" href="#"><img src="<?php echo WOPSHOP_PLUGIN_URL;?>assets/images/save.png"/></a>
        <?php }?>						
                    </th>					
                    <?php if($this->filter_order == 'published') $class_publish = 'sorted'; else $class_publish = 'sortable';?>
                    <th class="manage-column column-order_status <?php echo $class_publish; ?> <?php echo $this->filter_order_Dir; ?>" scope="col" width="70">
                        <a href="admin.php?page=options&tab=shippings&filter_order=published&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_STATUS; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <?php if($this->filter_order == 'shipping_id') $class_name = 'sorted'; else $class_name = 'sortable';?>
                    <th class="manage-column column-order_id <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col" width="40px">
                        <a href="admin.php?page=options&tab=shippings&filter_order=shipping_id&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_ID; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                </tr>
            </thead>
            <tbody id="the-list">
                <?php 
                $count = count($shippings);
                foreach($shippings as $index=>$shipping){
                ?>
                    <tr class="<?php if($index%2) echo 'alt';?>">
                        <th class="check-column" scope="col">
                            <label class="screen-reader-text" for="cb-select-<?php echo $shipping->shipping_id; ?>"><?php echo _WOP_SHOP_ACTION_CHECK; ?></label>
                            <input id="user_<?php echo $shipping->shipping_id; ?>" type="checkbox" value="<?php echo $shipping->shipping_id; ?>" name="rows[]">
                        </th>
                        <td class="column-primary name-column" scope="col">
                            <strong>
                            <?php echo $shipping->name;?>
                            </strong>
                            <div class="row-actions">
                                <span class="edit">
                                <a class="" title="<?php echo _WOP_SHOP_EDIT; ?>" href="admin.php?page=options&tab=shippings&task=edit&shipping_id=<?php echo $shipping->shipping_id; ?>"><?php echo _WOP_SHOP_EDIT; ?></a>
                                </span>
                            </div>
                        </td>
                        <td class="code-column " scope="col">
                            <a href = "admin.php?page=options&tab=shippingsprices&shipping_id_back=<?php print $shipping->shipping_id;?>"><?php echo _WOP_SHOP_SHIPPING_PRICES." (".$shipping->count_shipping_price.")"?></a>
                        </td>
						<td align="right" width="10">
						<?php
							 if ($index != 0 && $saveOrder) echo '<a class="btn btn-micro" href="admin.php?page=options&tab=shippings&task=order&id=' . $shipping->shipping_id . '&order=up&number=' . $shipping->ordering . '"><img alt="' . _WOP_SHOP_UP . '" src="'.WOPSHOP_PLUGIN_URL.'assets/images/uparrow.png"/></a>';
						?>
						</td>
						<td align="left" width="10">
						<?php
							 if ($index!=$count-1 && $saveOrder) echo '<a class="btn btn-micro" href="admin.php?page=options&tab=shippings&task=order&id=' . $shipping->shipping_id . '&order=down&number=' . $shipping->ordering . '"><img alt="' . _WOP_SHOP_DOWN . '" src="'.WOPSHOP_PLUGIN_URL.'assets/images/downarrow.png"/></a>';
						?>
						</td>
						<td align="center">
						 <input type="text" name="order[]" id="ord<?php echo $shipping->shipping_id;?>" size="3" value="<?php echo $shipping->ordering?>" <?php if (!$saveOrder) echo 'disabled'?> class="inputordering" style="text-align: center" />
						</td>						
						<td class="center">
							<?php echo $published=($shipping->published) ? ('<a href = "admin.php?page=options&tab=shippings&task=unpublish&rows[]='.$shipping->shipping_id.'"><img src="'.WOPSHOP_PLUGIN_URL.'assets/images/tick.png"/></a>') : ('<a href = "admin.php?page=options&tab=shippings&task=publish&rows[]='.$shipping->shipping_id.'"><img src="'.WOPSHOP_PLUGIN_URL.'assets/images/publish_x.png"/></a>'); ?>
						</td>						
                        <td class="code-column " scope="col">
                            <?php print  $shipping->shipping_id;?>
                        </td>
                    </tr>
                <?php }?>
            </tbody>
        </table>
        <input type="hidden" value="options" name="page">
        <input type="hidden" value="shippings" name="tab">
		<input type = "hidden" name = "task" value = "<?php echo Request::getVar('task', 0)?>" />
    </form>
    <div id="ajax-response"></div>
    <br class="clear">
</div>