<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
displaySubmenuOptions('orderstatus');
?>
<div class="wrap">
    <h2>
        <?php echo _WOP_SHOP_LIST_ORDER_STATUSS; ?>
        <a href="admin.php?page=options&tab=orderstatus&task=edit&row=0" class="add-new-h2"><?php echo _WOP_SHOP_NEW_ORDER_STATUS; ?></a>
    </h2>
    <form id="listing" method="GET" action="admin.php">
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
                        <a href="admin.php?page=options&tab=orderstatus&filter_order=name&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_NAME; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th class="manage-column" scope="col">
                        <?php echo _WOP_SHOP_CODE; ?>
                    </th>
                    <?php /*if($this->filter_order == 'publish') $class_publish = 'sorted'; else $class_publish = 'sortable';?>
                    <th class="manage-column column-order_status <?php echo $class_publish; ?> <?php echo $this->filter_order_Dir; ?>" scope="col">
                        <a href="admin.php?page=options&tab=orderstatus&filter_order=publish&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_STATUS; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>*/?>
                </tr>
            </thead>
            <tbody id="the-list">
                <?php if(count($this->rows) == 0){ ?>
                <tr class="no-items">
                <td class="colspanchange" colspan="3"><?php echo _WOP_SHOP_QUERY_RESULT_NULL; ?></td>
                </tr>
                <?php 
                }else{
                    foreach($this->rows as $index=>$data){?>
                    <tr class="<?php if($index%2) echo 'alt';?>">
                        <th class="check-column" scope="col">
                            <label class="screen-reader-text" for="cb-select-<?php echo $data->status_id; ?>"><?php echo _WOP_SHOP_ACTION_CHECK; ?></label>
                            <input id="user_<?php echo $data->status_id; ?>" type="checkbox" value="<?php echo $data->status_id; ?>" name="rows[]">
                        </th>
                        <td class="name-column" scope="col">
                            <strong>
                            <?php echo $data->name; ?>
                            </strong>
                            <div class="row-actions">
                                <span class="edit">
                                <a class="" title="<?php echo _WOP_SHOP_EDIT; ?>" href="admin.php?page=options&tab=orderstatus&task=edit&row=<?php echo $data->status_id; ?>"><?php echo _WOP_SHOP_EDIT; ?></a>
                                |
                                </span>
                                <span class="trash">
                                <a class="submitdelete" title="<?php echo _WOP_SHOP_DELETE; ?>" href="admin.php?page=options&tab=orderstatus&task=delete&rows[]=<?php echo $data->status_id; ?>&action=-1"><?php echo _WOP_SHOP_DELETE; ?></a>
                                </span>
                            </div>
                        </td>
                        <td class="code-column " scope="col">
                            <?php echo $data->status_code; ?>
                        </td>
                        <?php /*<td class="status-column">
                            <?php if($data->publish) echo _WOP_SHOP_ACTION_PUBLISHED; else echo _WOP_SHOP_ACTION_UNPUBLISHED; ?>
                        </td>*/?>
                    </tr>
                <?php }
                } ?>
            </tbody>
        </table>

        <input type="hidden" value="options" name="page">
        <input type="hidden" value="orderstatus" name="tab">
    </form>
    <div id="ajax-response"></div>
    <br class="clear">
</div>


