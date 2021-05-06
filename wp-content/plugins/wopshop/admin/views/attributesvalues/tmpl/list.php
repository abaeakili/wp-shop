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
        <?php echo _WOP_SHOP_LIST_ATTRIBUT_VALUES; ?>
        <a href="admin.php?page=options&tab=attributesvalues&task=edit&attr_id=<?php echo $this->attr_id; ?>" class="add-new-h2"><?php echo _WOP_SHOP_NEW; ?></a>
    </h2>

    <form id="listing" class="adminForm" action = "admin.php?page=options&tab=attributesvalues&attr_id=<?php echo $this->attr_id; ?>" method = "post" name = "adminForm">
        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <?php echo $this->bulk;?>
            </div>
            <br class="clear">
        </div>
        <table class="wp-list-table widefat fixed posts">
            <thead>
                <tr>
                    <th id="cb" class="manage-column column-cb check-column wopshop-admin-list-check" scope="col">
                        <input id="cb-select-all-1" type="checkbox">
                    </th> 
                    <?php if($this->filter_order == 'name') $class_name = 'sorted'; else $class_name = 'sortable';?>
                    <th id="order_title" class="manage-column column-order_title <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col" >
                        <a href="admin.php?page=options&tab=attributesvalues&filter_order=name&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>&attr_id=<?php echo $this->attr_id; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_NAME_ATTRIBUT_VALUE; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th width="100px" >
                        <?php echo _WOP_SHOP_IMAGE_ATTRIBUT_VALUE; ?>
                    </th>
                    <th width="100px" align="center">
                       <?php echo _WOP_SHOP_ORDERING; ?>
                    </th>
                    <?php if($this->filter_order == 'value_id') $class_name = 'sorted'; else $class_name = 'sortable';?>
                    <th id="order_id" class="manage-column column-order_title <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col" width="60px" align="center">
                        <a href="admin.php?page=options&tab=attributesvalues&filter_order=value_id&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>&attr_id=<?php echo $this->attr_id; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_ID; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>            
                </tr>
            </thead>
            <tbody>
                <?php if($count == 0) :  ?>
                    <tr class="no-items">
                        <td class="colspanchange" colspan="3"><?php echo _WOP_SHOP_QUERY_RESULT_NULL; ?></td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($rows as $index => $row) : ?>
                        <tr class="<?php if($index%2) echo 'alt';?>">
                            <td class="check-column wopshop-admin-list-check" scope="col">
                                <input id="attr_<?php echo $row->attr_id; ?>" type="checkbox" value="<?php echo $row->value_id; ?>" name="rows[]" />
                            </td>
                            <td>
                             <strong><?php echo $row->name;?></strong>
                             <div class="row-actions">
                                 <span class="edit">
                                     <a class="" title="<?php echo _WOP_SHOP_EDIT; ?>" href = "admin.php?page=options&tab=attributesvalues&task=edit&value_id=<?php echo $row->value_id; ?>&attr_id=<?php echo $this->attr_id?>"><?php echo _WOP_SHOP_EDIT; ?></a>
                                     |
                                 </span>
                                 <span class="trash">
                                     <a class="submitdelete" title="<?php echo _WOP_SHOP_DELETE; ?>" href="admin.php?page=options&tab=attributesvalues&task=delete&rows[]=<?php echo $row->value_id; ?>&attr_id=<?php echo $this->attr_id?>"><?php echo _WOP_SHOP_DELETE; ?></a>
                                 </span>
                             </div>
                            </td>
                            <td align="center">
                                 <?php if ($row->image) {?>
                                     <img src = "<?php echo $this->config->image_attributes_live_path."/".$row->image?>"  alt = "" width="20" height="20" />
                                 <?php }?>
                            </td>
                            <td align="center">
                                <?php print $row->value_ordering;?>
                            </td>
                            <td align="center">
                             <?php print $row->value_id;?>
                            </td>
                       </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </form>
    <br class="clear">
</div>
<div clas="submit">
    <a class="button" href="admin.php?page=options&tab=attributes"><?php echo _WOP_SHOP_BACK; ?></a>
</div>