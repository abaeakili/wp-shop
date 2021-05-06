<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
displaySubmenuOptions('productfields');
$rows      = $this->rows;
$count     = count( $rows );
$i         = 0;
$lists     = $this->lists;
$saveOrder = $this->filter_order_Dir == "desc" && $this->filter_order == "F.ordering";
?>
<div class="wrap">
    <h2>
        <?php echo _WOP_SHOP_PRODUCT_EXTRA_FIELDS; ?>
        <a href="admin.php?page=options&tab=productfields&task=edit&row=0" class="add-new-h2"><?php echo _WOP_SHOP_NEW; ?></a>
        <a href="admin.php?page=options&tab=productfieldgroups" class="add-new-h2"><?php echo _WOP_SHOP_NEW_GROUP; ?></a>
    </h2>
    <form id="listing" method="POST" action="admin.php?page=options&tab=productfields" name ="ExtraFieldsFilter" >	
        <table width="100%" style="padding-bottom:5px;">
            <tr>
                <td width="50%" align="right">
                    <?php print $this->tmp_html_filter; ?>
                </td>
                <td align="right">
                    <?php echo _WOP_SHOP_GROUP . ": " . $lists['group']; ?>&nbsp;&nbsp;&nbsp;
                </td>
                <td>
                    <?php echo $this->search; ?>
                </td>
            </tr>
        </table>
    </form>
    <form id="adminForm" action = "admin.php?page=options&tab=productfields" method = "post" name = "adminForm"  class="adminForm">
        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <?php echo $this->bulk; ?>
            </div>
        </div>
        <table width="100%" class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th id="cb" class="manage-column column-cb check-column" style="" scope="col">
                        <input id="cb-select-all-1" type="checkbox">
                    </th>
                    <?php if( $this->filter_order == 'name' ) $class_name='sorted'; else $class_name='sortable'; ?>
                    <th id="order_title" class="column-primary manage-column column-order_title <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col">
                        <a href="admin.php?page=options&tab=productfields&filter_order=name&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_TITLE; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <?php if( $this->filter_order == 'F.type' ) $class_name='sorted'; else $class_name='sortable'; ?>
                    <th id="order_type" class="manage-column column-order_type <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col">
                        <a href="admin.php?page=options&tab=productfields&filter_order=F.type&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_TYPE; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th align="left">
                        <?php echo _WOP_SHOP_OPTIONS; ?>
                    </th>
                    <th align="left">
                        <?php echo _WOP_SHOP_CATEGORIES; ?>
                    </th>
                    <?php if( $this->filter_order == 'groupname' ) $class_name='sorted'; else $class_name='sortable'; ?>
                    <th id="order_type" class="manage-column column-order_type <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col">
                        <a href="admin.php?page=options&tab=productfields&filter_order=groupname&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_GROUP; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>				
                    <?php if( $this->filter_order == 'F.ordering' ) $class_name='sorted'; else $class_name='sortable';?>
                    <th colspan="2" id="ordering" class="ordering center manage-column column-ordering <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col" width="100">
                        <a href="admin.php?page=options&tab=productfields&filter_order=F.ordering&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_ORDERING; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th id="saveorder" class="save_ordering center manage-column" scope="col" width="100">
                    <?php if( $saveOrder ) { ?>
                        <a class="saveorder" onclick="saveorder();" href="#"><img src="<?php echo WOPSHOP_PLUGIN_URL; ?>assets/images/save.png"/></a>
                    <?php } ?>						
                    </th>
                    <?php if( $this->filter_order == 'id' ) $class_name='sorted'; else $class_name='sortable'; ?>
                    <th id="order_id" class="manage-column column-order_id <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col" align="center" width="50px">
                        <a href="admin.php?page=options&tab=productfields&filter_order=id&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_ID; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                </tr>
            </thead>
            <?php foreach( $rows as $index => $row ) { ?>
            <tr class="<?php if( $index % 2 ) echo 'alt'; ?>">
                <th class="check-column" scope="col">
                  <label class="screen-reader-text" for="cb-select-<?php echo $row->id; ?>"><?php echo _WOP_SHOP_ACTION_CHECK; ?></label>
                  <input id="user_<?php echo $row->id; ?>" type="checkbox" value="<?php echo $row->id; ?>" name="rows[]">
                </th>
                <td class="column-primary name-column" scope="col">
                    <?php if( !$row->count_option && $row->type == 0 ) { ?><img src="<?php echo WOPSHOP_PLUGIN_URL; ?>assets/images/icon-16-denyinactive.png" alt="" /><?php } ?>
                    <strong><?php echo $row->name; ?></strong>
                    <div class="row-actions">
                        <span class="edit">
                            <a class="" title="<?php echo _WOP_SHOP_EDIT; ?>" href="admin.php?page=options&tab=productfields&task=edit&id=<?php echo $row->id; ?>"><?php echo _WOP_SHOP_EDIT; ?></a>
                            |
                        </span>
                        <span class="trash">
                            <a class="submitdelete" title="<?php echo _WOP_SHOP_DELETE; ?>" href="admin.php?page=options&tab=productfields&task=delete&rows[]=<?php echo $row->id; ?>&action=-1"><?php echo _WOP_SHOP_DELETE; ?></a>
                        </span>
                    </div>
                </td>
                <td>
                    <?php print $this->types[$row->type]; ?>
                </td>
                <td>
                    <?php if( $row->type == 0 ) { ?>
                        <a href="admin.php?page=options&tab=productfieldvalues&field_id=<?php echo $row->id; ?>"><?php echo _WOP_SHOP_OPTIONS; ?></a>
                    (<?php if( is_array( $this->vals[$row->id] ) ) echo implode( ", ", $this->vals[$row->id]); ?>)
                    <?php } else { ?>
                        -
                    <?php } ?>
                </td>
                <td>
                    <?php print $row->printcat; ?>        
                </td>
                <td>
                    <?php print $row->groupname; ?>
                </td>
                <td align="right" width="10">
                    <?php
                        if( $index != 0 && $saveOrder ) echo '<a class="btn btn-micro" href="admin.php?page=options&tab=productfields&task=order&id=' . $row->id . '&order=up&number=' . $row->ordering . '"><img alt="' . _WOP_SHOP_UP . '" src="'.WOPSHOP_PLUGIN_URL . 'assets/images/uparrow.png"/></a>';
                    ?>
                </td>
                <td align="left" width="10">
                    <?php
                        if( $index != $count - 1 && $saveOrder ) echo '<a class="btn btn-micro" href="admin.php?page=options&tab=productfields&task=order&id=' . $row->id . '&order=down&number=' . $row->ordering . '"><img alt="' . _WOP_SHOP_DOWN . '" src="' . WOPSHOP_PLUGIN_URL . 'assets/images/downarrow.png"/></a>';
                    ?>
                </td>
                <td align="center">
                    <input type="text" name="order[]" id="ord<?php echo $row->id; ?>" size="3" value="<?php echo $row->ordering; ?>" <?php if( !$saveOrder ) echo 'disabled'; ?> class="inputordering" style="text-align: center" />
                </td>		   
                <td align="center">
                    <?php print $row->id; ?>
                </td>
            </tr>
        <?php
         $i++;
         }
        ?>
        </table>
	<input type="hidden" value="options" name="page">
	<input type="hidden" value="productfields" name="tab">
	<input type="hidden" name="task" value="<?php echo Request::getVar( 'task', 0 ); ?>" />	
    </form>
    <div id="ajax-response"></div>
    <br class="clear">
</div>