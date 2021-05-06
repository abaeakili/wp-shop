<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
displaySubmenuOptions('deliverytimes');
?>
<div class="wrap">
    <h2>
        <?php echo _WOP_SHOP_DELIVERY_TIME; ?>
        <a href="admin.php?page=options&tab=deliverytimes&task=edit&row=0" class="add-new-h2"><?php echo _WOP_SHOP_DELIVERY_TIME_NEW; ?></a>
    </h2>
    <form id="listing" class="adminForm" method="GET" action="admin.php">
        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <?php echo $this->bulk;?>
            </div>
            <br class="clear">
        </div>
        <table class="wp-list-table widefat fixed posts">
            <thead>
                <tr>
                    <th id="cb" class="manage-column column-cb check-column" style="" scope="col">
                        <input id="cb-select-all-1" type="checkbox">
                    </th>
                    <?php if($this->filter_order == 'name') $class_name = 'sorted'; else $class_name = 'sortable';?>
                    <th id="order_title" class="manage-column column-order_title <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col" width="90%">
                        <a href="admin.php?page=options&tab=deliverytimes&filter_order=name&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_DELIVERY_TIME; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <?php if($this->filter_order == 'id') $class_name = 'sorted'; else $class_name = 'sortable';?>
                    <th id="order_id" class="manage-column column-order_id <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col">
                        <a href="admin.php?page=options&tab=deliverytimes&filter_order=id&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_ID; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                </tr>
            </thead>
            <tbody id="the-list">
                <?php if(count($this->rows) == 0){ ?>
                <tr class="no-items">
                <td class="colspanchange" colspan="3"><?php echo _WOP_SHOP_QUERY_RESULT_NULL; ?></td>
                </tr>
                <?php 
                }else{
                    foreach($this->rows as $index=>$row){?>
                    <tr class="<?php if($index%2) echo 'alt';?>">
                        <th class="check-column" scope="col">
                            <label class="screen-reader-text" for="cb-select-<?php echo $row->id; ?>"><?php echo _WOP_SHOP_ACTION_CHECK; ?></label>
                            <input id="user_<?php echo $row->id; ?>" type="checkbox" value="<?php echo $row->id; ?>" name="rows[]">
                        </th>
                        <td class="name-column" scope="col">
                            <strong>
                            <?php echo $row->name; ?>
                            </strong>
                            <div class="row-actions">
                                <span class="edit">
                                <a class="" title="<?php echo _WOP_SHOP_EDIT; ?>" href="admin.php?page=options&tab=deliverytimes&task=edit&row=<?php echo $row->id; ?>"><?php echo _WOP_SHOP_EDIT; ?></a>
                                
                                </span>
                                <?php/*
                                <span class="trash">
                                <a class="submitdelete" title="<?php echo _WOP_SHOP_DELETE; ?>" href="admin.php?page=options&tab=deliverytimes&task=delete&rows[]=<?php echo $row->id; ?>&action=-1"><?php echo _WOP_SHOP_DELETE; ?></a>
                                </span>*/?>
                            </div>
                        </td>
                        <td class="code-column " scope="col">
                            <?php echo $row->id; ?>
                        </td>
                    </tr>
                <?php }
                } ?>
            </tbody>
        </table>

        <input type="hidden" value="options" name="page">
        <input type="hidden" value="deliverytimes" name="tab">
    </form>
    <div id="ajax-response"></div>
    <br class="clear">
</div>


