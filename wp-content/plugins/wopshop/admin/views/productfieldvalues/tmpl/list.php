<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
displaySubmenuOptions('productfields');
$rows = $this->rows; $count = count ($rows); $i = 0; 
$saveOrder = $this->filter_order_Dir=="desc" && $this->filter_order=="ordering";
?>
<div class="wrap">
    <h2>
        <?php echo _WOP_SHOP_PRODUCT_EXTRA_FIELDS; ?>
        <a href="admin.php?page=options&tab=productfieldvalues&task=edit&field_id=<?php echo $this->field_id; ?>" class="add-new-h2"><?php echo _WOP_SHOP_NEW; ?></a>
    </h2>
    <?php echo $this->top_counters; ?>
    <form name="ExtraFieldsFilter" action="" method="POST">
        <table width="100%" style="padding-bottom:5px;">
          <tr>
            <td width="95%" align="right">
                <?php print $this->tmp_html_filter?>
            </td>
            <td>
                <input type="text" name="text_search" value="<?php echo htmlspecialchars($this->text_search);?>" />
            </td>
            <td>
                <input type="submit" class="button" value="<?php echo _WOP_SHOP_SEARCH;?>" />
            </td>
          </tr>
        </table>
    </form>
	<form id="adminForm" action = "admin.php?page=options&tab=productfieldvalues&field_id=<?php print $this->field_id?>" method = "post" name = "adminForm">
        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <?php echo $this->bulk;?>
            </div>
            <?php //echo $this->pagination;?>
            <br class="clear">
            
        </div>
        <table width="100%" class = "wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th id="cb" class="manage-column column-cb check-column" style="" scope="col">
                    <input id="cb-select-all-1" type="checkbox">
                </th>
                <?php if($this->filter_order == 'name') $class_name = 'sorted'; else $class_name = 'sortable';?>
                <th id="order_title" class="column-primary manage-column column-order_title <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col">
                    <a href="admin.php?page=options&tab=productfieldvalues&filter_order=name&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>&field_id=<?php print $this->field_id?>">
                        <span class="status_head tips"><?php echo _WOP_SHOP_TITLE; ?></span>
                        <span class="sorting-indicator"></span>
                    </a>
                </th>
				<?php if($this->filter_order == 'ordering') $class_name = 'sorted'; else $class_name = 'sortable';?>
				<th colspan="2" id="ordering" class="ordering center manage-column column-ordering <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col" width="100">
					<a href="admin.php?page=options&tab=productfieldvalues&filter_order=ordering&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>&field_id=<?php print $this->field_id?>">
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
                <th id="id" class="manage-column column-id <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col" align="center" width="50px">
                    <a href="admin.php?page=options&tab=productfieldvalues&filter_order=id&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>&field_id=<?php print $this->field_id?>">
                        <span class="status_head tips"><?php echo _WOP_SHOP_ID; ?></span>
                        <span class="sorting-indicator"></span>
                    </a>
                </th>
            </tr>
        </thead>
       <?php 
            foreach ($rows as $index=>$row) { ?>
                <tr class="<?php if($i%2) echo 'alt';?>">
                    <th class="check-column" scope="col">
                        <label class="screen-reader-text" for="cb-select-<?php echo $row->id; ?>"><?php echo _WOP_SHOP_ACTION_CHECK; ?></label>
                        <input id="user_<?php echo $row->id; ?>" type="checkbox" value="<?php echo $row->id; ?>" name="rows[]">
                    </th>
                    <td class="column-primary name-column" scope="col">
                        <strong><?php echo $row->name;?></strong>
                        <div class="row-actions">
                            <span class="edit">
                                <a class="" title="<?php echo _WOP_SHOP_EDIT; ?>" href="admin.php?page=options&tab=productfieldvalues&task=edit&field_id=<?php echo $this->field_id; ?>&id=<?php echo $row->id; ?>"><?php echo _WOP_SHOP_EDIT; ?></a>
                                |
                            </span>
                            <span class="trash">
                                <a class="submitdelete" title="<?php echo _WOP_SHOP_DELETE; ?>" href="admin.php?page=options&tab=productfieldvalues&task=delete&rows[]=<?php echo $row->id; ?>&field_id=<?php echo $this->field_id; ?>"><?php echo _WOP_SHOP_DELETE; ?></a>
                            </span>
                        </div>
                    </td>
					<td align="right" width="10">
					<?php
						 if ($index != 0 && $saveOrder) echo '<a class="btn btn-micro" href="admin.php?page=options&tab=productfieldvalues&task=order&id=' . $row->id . '&order=up&number=' . $row->ordering . '&field_id='.$this->field_id.'"><img alt="' . _WOP_SHOP_UP . '" src="'.WOPSHOP_PLUGIN_URL.'assets/images/uparrow.png"/></a>';
					?>
					</td>
					<td align="left" width="10">
					<?php
						 if ($index!=$count-1 && $saveOrder) echo '<a class="btn btn-micro" href="admin.php?page=options&tab=productfieldvalues&task=order&id=' . $row->id . '&order=down&number=' . $row->ordering . '&field_id='.$this->field_id.'"><img alt="' . _WOP_SHOP_DOWN . '" src="'.WOPSHOP_PLUGIN_URL.'assets/images/downarrow.png"/></a>';
					?>
					</td>
					<td align="center">
					 <input type="text" name="order[]" id="ord<?php echo $row->id;?>" size="3" value="<?php echo $row->ordering?>" <?php if (!$saveOrder) echo 'disabled'?> class="inputordering" style="text-align: center" />
					</td>					
                    <td align="center">
                        <?php print $row->id;?>
                    </td>
                </tr>
            <?php
                $i++;
            } ?>
        </table>
	<input type="hidden" value="options" name="page">
	<input type="hidden" value="productfieldvalues" name="tab">
	<input type = "hidden" name = "task" value = "<?php echo Request::getVar('task', 0)?>" />			
    </form>
    <br>        
    <a href="admin.php?page=options&tab=productfields" class="back button"><?php echo _WOP_SHOP_BACK; ?></a>
    <div id="ajax-response"></div>
    <br class="clear">
</div>