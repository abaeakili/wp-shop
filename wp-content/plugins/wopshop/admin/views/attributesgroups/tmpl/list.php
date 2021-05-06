<?php 
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
displaySubmenuOptions('attributes');

$rows = $this->rows;
$count = count ($rows);
?>
<div class="wrap">
    <h2>
        <?php echo _WOP_SHOP_ATTRIBUTES_GROUPS; ?>
        <a href="admin.php?page=options&tab=attributesgroups&task=edit&row=0" class="add-new-h2"><?php echo _WOP_SHOP_NEW; ?></a>
    </h2>

    <form id="listing" class="adminForm" action = "admin.php?page=options&tab=attributesgroups" method = "post" name = "adminForm">
    <table class="wp-list-table widefat fixed posts">
    <thead>
        <tr>
            <th id="cb" class="manage-column column-cb check-column" style="" scope="col">
                <input id="cb-select-all-1" type="checkbox">
            </th>
            <th>
                <?php echo _WOP_SHOP_TITLE; ?>
            </th>
                    <th colspan="2" id="ordering" scope="col" width="10%">
                        <a href="admin.php?page=options&tab=attributesgroups&filter_order=A.attr_ordering&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_ORDERING; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>						
                    </th>
					
                    <th id="saveorder" class="save_ordering center manage-column" scope="col" width="8%">
						<a class="saveorder" onclick="saveorder();" href="#"><img src="<?php echo WOPSHOP_PLUGIN_URL;?>assets/images/save.png"/></a>					
                    </th>
            <th width="60px" align="center">
               <?php echo _WOP_SHOP_ID; ?>
            </th>
        </tr>
    </thead>
    <?php 
    if($count == 0){ ?>
        <tr class="no-items">
            <td class="colspanchange" colspan="6"><?php echo _WOP_SHOP_QUERY_RESULT_NULL; ?></td>
        </tr>
    <?php 
    }else{
        foreach ($rows as $index=>$row){
     ?>
        <tr class="<?php if($index%2) echo 'alt';?>">
            <th class="check-column" scope="col">
                <label class="screen-reader-text" for="cb-select-<?php echo $data->status_id; ?>"><?php echo _WOP_SHOP_ACTION_CHECK; ?></label>
                <input id="attr_<?php echo $row->id; ?>" type="checkbox" value="<?php echo $row->id; ?>" name="rows[]">
            </th>
       <td>
        <strong><?php echo $row->name;?></strong>
        <div class="row-actions">
            <span class="edit">
                <a class="" title="<?php echo _WOP_SHOP_EDIT; ?>" href = "admin.php?page=options&tab=attributesgroups&task=edit&id=<?php echo $row->id; ?>"><?php echo _WOP_SHOP_EDIT; ?></a>
                |
            </span>
            <span class="trash">
                <a class="submitdelete" title="<?php echo _WOP_SHOP_DELETE; ?>" href="admin.php?page=options&tab=attributesgroups&task=delete&rows[]=<?php echo $row->id; ?>"><?php echo _WOP_SHOP_DELETE; ?></a>
            </span>
        </div>
       </td>
						<td align="right" width="10">
						<?php
							 if ($index != 0) echo '<a class="btn btn-micro" href="admin.php?page=options&tab=attributesgroups&task=order&id=' . $row->id . '&order=up&number=' . $row->ordering . '"><img alt="' . _WOP_SHOP_UP . '" src="'.WOPSHOP_PLUGIN_URL.'assets/images/uparrow.png"/></a>';
						?>
						</td>
						<td align="left" width="10">
						<?php
							 if ($index!=$count-1) echo '<a class="btn btn-micro" href="admin.php?page=options&tab=attributesgroups&task=order&id=' . $row->id . '&order=down&number=' . $row->ordering . '"><img alt="' . _WOP_SHOP_DOWN . '" src="'.WOPSHOP_PLUGIN_URL.'assets/images/downarrow.png"/></a>';
						?>
						</td>
						<td align="center">
						 <input type="text" name="order[]" id="ord<?php echo $row->id;?>" size="5" value="<?php echo $row->ordering?>" class="inputordering" style="text-align: center" />
						</td>
       <td align="center">
        <?php print $row->id;?>
       </td>
      </tr>
    <?php
        }
    }
    ?>
    </table>
		<input type = "hidden" name = "task" value = "<?php echo Request::getVar('task', 0)?>" />
    </form>
<br class="clear">
</div>
<div clas="submit">
    <a class="button" href="admin.php?page=options&tab=attributes"><?php echo _WOP_SHOP_BACK; ?></a>
</div>