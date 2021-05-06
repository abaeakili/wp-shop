<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
displaySubmenuOptions('countries');
$saveOrder = $this->filter_order_Dir=="desc" && $this->filter_order=="ordering";
$count = count($this->rows);
?>
<div class="wrap">
    <h2>
        <?php echo _WOP_SHOP_LIST_COUNTRY; ?>
        <a href="admin.php?page=options&tab=countries&task=edit&row=0" class="add-new-h2"><?php echo _WOP_SHOP_NEW_COUNTRY; ?></a>
    </h2>
    <?php echo $this->top_counters; ?>
		<form id="listing" class="adminForm" action = "admin.php?page=options&tab=countries" method = "post" name = "adminForm">
        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <?php echo $this->bulk;?>
            </div>
            <?php print $this->tmp_html_filter?>
            <?php echo $this->pagination;?>
            <br class="clear">
        </div>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th id="cb" class="manage-column column-cb check-column" style="" scope="col">
                        <input id="cb-select-all-1" type="checkbox">
                    </th>
                    <?php if($this->filter_order == 'name') $class_name = 'sorted'; else $class_name = 'sortable';?>
                    <th id="order_title" class="column-primary manage-column column-order_title <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col" width="50%">
                        <a href="admin.php?page=options&tab=countries&filter_order=name&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_COUNTRY; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th class="manage-column" scope="col">
                        <?php echo _WOP_SHOP_CODE; ?>
                    </th>
                    <th class="manage-column" scope="col">
                        <?php echo _WOP_SHOP_CODE; ?> 2
                    </th>
                    <?php if($this->filter_order == 'ordering') $class_name = 'sorted'; else $class_name = 'sortable';?>
                    <th colspan="2" id="ordering" class="ordering center manage-column column-ordering <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col" width="100">
                        <a href="admin.php?page=options&tab=countries&filter_order=ordering&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_ORDERING; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>						
                    </th>
					
                    <th id="saveorder" class="save_ordering center manage-column" scope="col" width="100">
        <?php if ($saveOrder){?>
						<a class="saveorder" onclick="saveorder();" href="#"><img src="<?php echo WOPSHOP_PLUGIN_URL;?>assets/images/save.png"/></a>
        <?php }?>						
                    </th>					
                    <?php if($this->filter_order == 'country_publish') $class_publish = 'sorted'; else $class_publish = 'sortable';?>
                    <th class="manage-column column-order_status <?php echo $class_publish; ?> <?php echo $this->filter_order_Dir; ?>" scope="col" width="100">
                        <a href="admin.php?page=options&tab=countries&filter_order=country_publish&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_STATUS; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th width="40" class="center">
                        <?php echo _WOP_SHOP_ID; ?>
                    </th>   					
                </tr>
            </thead>
            <tbody id="the-list">
                <?php 
                    foreach($this->rows as $index=>$country){?>
                    <tr class="<?php if($index%2) echo 'alt';?>">
                        <th class="check-column" scope="col">
                            <label class="screen-reader-text" for="cb-select-<?php echo $country->country_id; ?>"><?php echo _WOP_SHOP_ACTION_CHECK; ?></label>
                            <input id="user_<?php echo $country->country_id; ?>" type="checkbox" value="<?php echo $country->country_id; ?>" name="rows[]">
                        </th>
                        <td class="column-primary name-column" scope="col">
                            <strong>
                            <?php echo $country->name; ?>
                            </strong>
                            <div class="row-actions">
                                <span class="edit">
                                <a class="" title="<?php echo _WOP_SHOP_EDIT; ?>" href="admin.php?page=options&tab=countries&task=edit&row=<?php echo $country->country_id; ?>"><?php echo _WOP_SHOP_EDIT; ?></a>
                                |
                                </span>
                                <span class="trash">
                                <a class="submitdelete" title="<?php echo _WOP_SHOP_DELETE; ?>" href="admin.php?page=options&tab=countries&task=delete&rows[]=<?php echo $country->country_id; ?>&action=-1"><?php echo _WOP_SHOP_DELETE; ?></a>
                                </span>
                            </div>
                        </td>
                        <td class="code-column " scope="col">
                            <?php echo $country->country_code; ?>
                        </td>
                        <td class="code2-column " scope="col">
                            <?php echo $country->country_code_2; ?>
                        </td>
						<td align="right" width="10">
						<?php
							 if ($index != 0 && $saveOrder) echo '<a class="btn btn-micro" href="admin.php?page=options&tab=countries&task=order&id=' . $country->country_id . '&order=up&number=' . $country->ordering . '"><img alt="' . _WOP_SHOP_UP . '" src="'.WOPSHOP_PLUGIN_URL.'assets/images/uparrow.png"/></a>';
						?>
						</td>
						<td align="left" width="10">
						<?php
							 if ($index!=$count-1 && $saveOrder) echo '<a class="btn btn-micro" href="admin.php?page=options&tab=countries&task=order&id=' . $country->country_id . '&order=down&number=' . $country->ordering . '"><img alt="' . _WOP_SHOP_DOWN . '" src="'.WOPSHOP_PLUGIN_URL.'assets/images/downarrow.png"/></a>';
						?>
						</td>
						<td align="center">
						 <input type="text" name="order[]" id="ord<?php echo $country->country_id;?>" size="3" value="<?php echo $country->ordering?>" <?php if (!$saveOrder) echo 'disabled'?> class="inputordering" style="text-align: center" />
						</td>						
						<td class="center">
							<?php echo $published=($country->country_publish) ? ('<a href = "admin.php?page=options&tab=countries&task=unpublish&rows[]='.$country->country_id.'"><img src="'.WOPSHOP_PLUGIN_URL.'assets/images/tick.png"/></a>') : ('<a href = "admin.php?page=options&tab=countries&task=publish&rows[]='.$country->country_id.'"><img src="'.WOPSHOP_PLUGIN_URL.'assets/images/publish_x.png"/></a>'); ?>
						</td>						
                        <td class="code-column " scope="col">
                            <?php print  $country->country_id;?>
                        </td>						
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <input type="hidden" value="options" name="page">
        <input type="hidden" value="countries" name="tab">
		<input type = "hidden" name = "task" value = "<?php echo Request::getVar('task', 0)?>" />
    </form>
    <div id="ajax-response"></div>
    <br class="clear">
</div>


