<?php 
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
displaySubmenuOptions('attributes');

$rows = $this->rows;
$count = count($rows);
$saveOrder = $this->filter_order_Dir=="desc" && $this->filter_order=="A.attr_ordering";
?>
<div class="wrap">
    <h2>
        <?php echo _WOP_SHOP_ATTRIBUTES; ?>
        <a href="admin.php?page=options&tab=attributes&task=edit&row=0" class="add-new-h2"><?php echo _WOP_SHOP_NEW_ATTRIBUT; ?></a>
        <a href="admin.php?page=options&tab=attributesgroups" class="add-new-h2"><?php echo _WOP_SHOP_NEW_GROUP; ?></a>
    </h2>

    <form id="listing" class="adminForm" action = "admin.php?page=options&tab=attributes" method = "post" name = "adminForm">
        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <?php echo $this->bulk;?>
            </div>
            <br class="clear">
        </div>

        <table class="wp-list-table widefat fixed posts striped">
            <thead>
                <tr>                
                    <th id="cb" class="manage-column column-cb check-column wopshop-admin-list-check" scope="col">
                        <input id="cb-select-all-1" type="checkbox">
                    </th>
                    <?php if($this->filter_order == 'name') $class_name = 'sorted'; else $class_name = 'sortable';?>
                    <th id="order_title" class="column-primary manage-column column-order_title <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col">
                        <a href="admin.php?page=options&tab=attributes&filter_order=name&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_TITLE; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th width="15%" class="manage-column">
                        <?php echo _WOP_SHOP_OPTIONS; ?>
                    </th>
                    <?php if($this->filter_order == 'A.independent') $class_independent = 'sorted'; else $class_independent = 'sortable';?>
                    <th class="manage-column column-independent <?php echo $class_independent; ?> <?php echo $this->filter_order_Dir; ?>" scope="col" width="15%">
                        <a href="admin.php?page=options&tab=attributes&filter_order=A.independent&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_DEPENDENT; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <?php if($this->filter_order == 'A.groupname') $class_name = 'sorted'; else $class_name = 'sortable';?>
                    <th id="groupname" class="manage-column column-groupname <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col" width="15%">
                        <a href="admin.php?page=options&tab=attributes&filter_order=groupname&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_GROUP; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
					
                    <?php if($this->filter_order == 'A.attr_ordering') $class_name = 'sorted'; else $class_name = 'sortable';?>
                    <th colspan="2" id="ordering" class="ordering center manage-column column-attr_ordering <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col" width="100">
                        <a href="admin.php?page=options&tab=attributes&filter_order=A.attr_ordering&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_ORDERING; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>						
                    </th>
					
                    <th id="saveorder" class="save_ordering center manage-column" scope="col" width="100">
        <?php if ($saveOrder){?>
						<a class="saveorder" onclick="saveorder();" href="#"><img src="<?php echo WOPSHOP_PLUGIN_URL;?>assets/images/save.png"/></a>
        <?php }?>						
                    </th>					
					
                    <?php if($this->filter_order == 'A.attr_id') $class_name = 'sorted'; else $class_name = 'sortable';?>
                    <th id="attr_id" class="manage-column column-attr_id <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col" style="width: 50px">
                        <a href="admin.php?page=options&tab=attributes&filter_order=A.attr_id&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_ID; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($count) == 0) : ?>
                    <tr class="no-items">
                        <td class="colspanchange" colspan="3"><?php echo _WOP_SHOP_QUERY_RESULT_NULL; ?></td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($rows as $index => $row) : ?>
                        <tr class="<?php if($index%2) echo 'alt';?>">
                            <td class="check-column wopshop-admin-list-check" scope="col">
                                <input id="attr_<?php echo $row->attr_id; ?>" type="checkbox" value="<?php echo $row->attr_id; ?>" name="rows[]" />
                            </td>
                            <td class="column-primary">
                                <?php if (!$row->count_values) {?><img src="<?php echo WOPSHOP_PLUGIN_URL; ?>assets/images/disabled.png" alt="" /><?php }?>
                                <strong><?php echo $row->name;?></strong>
                                <div class="row-actions">
                                    <span class="edit">
                                        <a class="" title="<?php echo _WOP_SHOP_EDIT; ?>" href = "admin.php?page=options&tab=attributes&task=edit&attr_id=<?php echo $row->attr_id; ?>"><?php echo _WOP_SHOP_EDIT; ?></a>
                                        |
                                    </span>
                                    <span class="trash">
                                        <a class="submitdelete" title="<?php echo _WOP_SHOP_DELETE; ?>" href="admin.php?page=options&tab=attributes&task=delete&rows[]=<?php echo $row->attr_id; ?>&action=-1"><?php echo _WOP_SHOP_DELETE; ?></a>
                                    </span>
                                </div>
                            </td>
                       <td>
                         <a href = "admin.php?page=options&tab=attributesvalues&attr_id=<?php echo $row->attr_id?>"><?php echo _WOP_SHOP_OPTIONS?></a>
                         <?php echo $row->values;?>
                       </td>
                       <td>
                        <?php if ($row->independent==0){
                            print _WOP_SHOP_YES;
                        }else{
                            print _WOP_SHOP_NO;
                        }?>
                       </td>
                       <td>
                        <?php print $row->groupname?>
                       </td>
					   
						<td align="right" width="10">
						<?php
							 if ($index != 0 && $saveOrder) echo '<a class="btn btn-micro" href="admin.php?page=options&tab=attributes&task=order&id=' . $row->attr_id . '&order=up&number=' . $row->attr_ordering . '"><img alt="' . _WOP_SHOP_UP . '" src="'.WOPSHOP_PLUGIN_URL.'assets/images/uparrow.png"/></a>';
						?>
						</td>
						<td align="left" width="10">
						<?php
							 if ($index!=$count-1 && $saveOrder) echo '<a class="btn btn-micro" href="admin.php?page=options&tab=attributes&task=order&id=' . $row->attr_id . '&order=down&number=' . $row->attr_ordering . '"><img alt="' . _WOP_SHOP_DOWN . '" src="'.WOPSHOP_PLUGIN_URL.'assets/images/downarrow.png"/></a>';
						?>
						</td>
						<td align="center">
						 <input type="text" name="order[]" id="ord<?php echo $row->attr_id;?>" size="5" value="<?php echo $row->attr_ordering?>" <?php if (!$saveOrder) echo 'disabled'?> class="inputordering" style="text-align: center" />
						</td>					   
					   
					   
                       <td align="center">
                        <?php print $row->attr_id;?>
                       </td>
                      </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
		<input type = "hidden" name = "task" value = "<?php echo Request::getVar('task', 0)?>" />
    </form>
<br class="clear">
</div>