<?php 
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
displaySubmenuOptions('freeattributes');

$rows = $this->rows;
$saveOrder = $this->filter_order_Dir=="desc" && $this->filter_order=="ordering";
$count = count ($rows);
?>
<div class="wrap">
    <h2>
        <?php echo _WOP_SHOP_LIST_ATTRIBUTES; ?>
        <a href="admin.php?page=options&tab=freeattributes&task=edit&row=0" class="add-new-h2"><?php echo _WOP_SHOP_NEW_ATTRIBUT; ?></a>
    </h2>
	<form id="listing" class="adminForm" action = "admin.php?page=options&tab=freeattributes" method = "post" name = "adminForm">
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
            <th width="80%" id="order_title" class="column-primary manage-column column-order_title <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col">
                <a href="admin.php?page=options&tab=freeattributes&filter_order=name&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                    <span class="status_head tips"><?php echo _WOP_SHOP_TITLE; ?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
            <th width="10%">
                <?php echo _WOP_SHOP_REQUIRED; ?>
            </th>
			<?php if($this->filter_order == 'ordering') $class_name = 'sorted'; else $class_name = 'sortable';?>
			<th colspan="2" id="ordering" class="ordering center manage-column column-ordering <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col" width="100">
				<a href="admin.php?page=options&tab=freeattributes&filter_order=ordering&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
					<span class="status_head tips"><?php echo _WOP_SHOP_ORDERING; ?></span>
					<span class="sorting-indicator"></span>
				</a>						
			</th>

			<th id="saveorder" class="save_ordering center manage-column" scope="col" width="100">
<?php if ($saveOrder){?>
				<a class="saveorder" onclick="saveorder();" href="#"><img src="<?php echo WOPSHOP_PLUGIN_URL;?>assets/images/save.png"/></a>
<?php }?>						
			</th>
			
            <?php if($this->filter_order == 'id') $class_name = 'sorted'; else $class_name = 'sortable';?>
            <th id="attr_id" class="manage-column column-attr_id <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col" width="30px">
                <a href="admin.php?page=options&tab=freeattributes&filter_order=id&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                    <span class="status_head tips"><?php echo _WOP_SHOP_ID; ?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>			
        </tr>
    </thead>
    <?php 
        foreach ($rows as $index=>$row){
     ?>
        <tr class="<?php if($index%2) echo 'alt';?>">
            <th class="check-column" scope="col">
                <label class="screen-reader-text" for="cb-select-<?php echo $data->id; ?>"><?php echo _WOP_SHOP_ACTION_CHECK; ?></label>
                <input id="attr_<?php echo $row->id; ?>" type="checkbox" value="<?php echo $row->id; ?>" name="rows[]">
            </th>
            <td class="column-primary name-column">
                <strong><?php echo $row->name;?></strong>
                <div class="row-actions">
                    <span class="edit">
                    <a class="" title="<?php echo _WOP_SHOP_EDIT; ?>" href = "admin.php?page=options&tab=freeattributes&task=edit&id=<?php echo $row->id; ?>"><?php echo _WOP_SHOP_EDIT; ?></a>
                    |
                    </span>
                    <span class="trash">
                        <a class="submitdelete" title="<?php echo _WOP_SHOP_DELETE; ?>" href="admin.php?page=options&tab=freeattributes&task=delete&rows[]=<?php echo $row->id; ?>&action=-1"><?php echo _WOP_SHOP_DELETE; ?></a>
                    </span>
                </div>
            </td>
            <td>
                <?php if ($row->required){?>
                    <img src="<?php echo WOPSHOP_PLUGIN_URL.'assets/images/icon-16-allow.png"'; ?>" >
                <?php }?>
            </td>
			<td align="right" width="10">
			<?php
				 if ($index != 0 && $saveOrder) echo '<a class="btn btn-micro" href="admin.php?page=options&tab=freeattributes&task=order&id=' . $row->id . '&order=up&number=' . $row->ordering . '"><img alt="' . _WOP_SHOP_UP . '" src="'.WOPSHOP_PLUGIN_URL.'assets/images/uparrow.png"/></a>';
			?>
			</td>
			<td align="left" width="10">
			<?php
				 if ($index!=$count-1 && $saveOrder) echo '<a class="btn btn-micro" href="admin.php?page=options&tab=freeattributes&task=order&id=' . $row->id . '&order=down&number=' . $row->ordering . '"><img alt="' . _WOP_SHOP_DOWN . '" src="'.WOPSHOP_PLUGIN_URL.'assets/images/downarrow.png"/></a>';
			?>
			</td>
			<td align="center">
			 <input type="text" name="order[]" id="ord<?php echo $row->id;?>" size="3" value="<?php echo $row->ordering?>" <?php if (!$saveOrder) echo 'disabled'?> class="inputordering" style="text-align: center" />
			</td>			
			<td align="center">
                <?php print $row->id;?>
            </td>
        </tr>
    <?php  }  ?>
    </table>
	<input type="hidden" value="options" name="page">
	<input type="hidden" value="freeattributes" name="tab">
	<input type = "hidden" name = "task" value = "<?php echo Request::getVar('task', 0)?>" />		
    </form>
<br class="clear">
</div>