<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
displaySubmenuOptions('labels');
?>
<div class="wrap">
    <h2>
        <?php echo _WOP_SHOP_LIST_PRODUCT_LABELS; ?>
        <a href="admin.php?page=options&tab=productlabels&task=edit&row=0" class="add-new-h2"><?php echo _WOP_SHOP_PRODUCT_LABEL_NEW; ?></a>
    </h2>
    <?php /*<form id="listing" method="GET" action="admin.php">*/?>
    <form method="POST" action="admin.php?page=options&tab=productlabels">
        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <?php echo $this->bulk;?>
            </div>
            <?php //echo $this->pagination;?>
            <br class="clear">
            
        </div>
        <table class="wp-list-table widefat fixed posts">
            <thead>
                <tr>
                    <th id="cb" class="manage-column column-cb check-column" style="" scope="col" width="40px">
                        <input id="cb-select-all-1" type="checkbox">
                    </th>
                    <?php if($this->filter_order == 'name') $class_name = 'sorted'; else $class_name = 'sortable';?>
                    <th width="80%" id="order_title" class="manage-column column-order_title <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col">
                        <a href="admin.php?page=options&tab=productlabels&filter_order=name&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_TITLE; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th class="manage-column column-order_status" width="120px">
                        <span class="status_head tips"><?php echo _WOP_SHOP_IMAGE; ?></span>
                        <span class="sorting-indicator"></span>
                    </th>
                    <?php if($this->filter_order == 'id') $class_name = 'sorted'; else $class_name = 'sortable';?>
                    <th id="order_id" class="manage-column column-order_id <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col">
                        <a href="admin.php?page=options&tab=productlabels&filter_order=id&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
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
                            <input id="user_<?php echo $row->id; ?>" type="checkbox" value="<?php echo $row->id; ?>" name="rows[]">
                        </th>
                        <td class="name-column" scope="col">
                            <strong>
                            <?php echo $row->name; ?>
                            </strong>
                            <div class="row-actions">
                                <span class="edit">
                                <a class="" title="<?php echo _WOP_SHOP_EDIT; ?>" href="admin.php?page=options&tab=productlabels&task=edit&row=<?php echo $row->id; ?>"><?php echo _WOP_SHOP_EDIT; ?></a>
                                |
                                </span>
                                <span class="trash">
                                <a class="submitdelete" title="<?php echo _WOP_SHOP_DELETE; ?>" href="admin.php?page=options&tab=productlabels&task=delete&rows[]=<?php echo $row->id; ?>&action=-1"><?php echo _WOP_SHOP_DELETE; ?></a>
                                </span>
                            </div>
                        </td>
                        <th class="image-column">
                            <?php if($row->image){?>
                            <img src="<?php echo WOPSHOP_PLUGIN_URL.'files/img_labels/'.$row->image; ?>">
                            <?php } ?>
                        </th>
                        <th class="id-column">
                            <?php echo $row->id;?>
                        </th>
                    </tr>
                <?php }
                } ?>
            </tbody>
        </table>

        <input type="hidden" value="options" name="page">
        <input type="hidden" value="productlabels" name="tab">
    </form>
    <div id="ajax-response"></div>
    <br class="clear">
</div>


