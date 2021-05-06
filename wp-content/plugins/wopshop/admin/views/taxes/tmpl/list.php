<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
displaySubmenuOptions('taxes');
$taxes = $this->rows;
$i = 0;
?>
<div class="wrap">
    <h2>
        <?php echo _WOP_SHOP_LIST_UNITS_MEASURE; ?>
        <a href="admin.php?page=options&tab=taxes&task=edit" class="add-new-h2"><?php echo _WOP_SHOP_NEW_TAX; ?></a>
    </h2>
    <form id="listing" method="GET">
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
            <?php if($this->filter_order == 'tax_name') $class_name = 'sorted'; else $class_name = 'sortable';?>
            <th id="order_tax_name" class="manage-column column-order_tax_name <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col">
                <a href="admin.php?page=options&tab=taxes&filter_order=tax_name&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                    <span class="status_head tips"><?php echo _WOP_SHOP_TITLE; ?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
            <th width="150">
                <?php echo _WOP_SHOP_EXTENDED_RULE_TAX; ?>
            </th>
            <th width = "100px">
                <?php echo _WOP_SHOP_EDIT; ?>
            </th>
            <?php if($this->filter_order == 'tax_id') $class_name = 'sorted'; else $class_name = 'sortable';?>
            <th id="order_tax_id" class="manage-column column-order_tax_id <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col" width="50px">
                <a href="admin.php?page=options&tab=taxes&filter_order=tax_id&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                    <span class="status_head tips"><?php echo _WOP_SHOP_ID; ?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
        </tr>
    </thead>  
    <?php if(count($taxes) == 0){ ?>
        <tr class="no-items">
            <td class="colspanchange" colspan="3"><?php echo _WOP_SHOP_QUERY_RESULT_NULL; ?></td>
        </tr>
    <?php 
          }else{
        foreach($taxes as $tax){
      ?>
        <tr class = "row<?php echo $i % 2;?>">
            <th class="check-column" scope="col">
                <label class="screen-reader-text" for="cb-select-<?php echo $tax->tax_id; ?>"><?php echo _WOP_SHOP_ACTION_CHECK; ?></label>
                <input id="user_<?php echo $tax->tax_id; ?>" type="checkbox" value="<?php echo $tax->tax_id; ?>" name="rows[]">
            </th>
            <td>
                <strong>
                    <?php echo $tax->tax_name; ?> (<?php echo $tax->tax_value;?> %)
                </strong>
                <div class="row-actions">
                    <span class="edit">
                        <a class="" title="<?php echo _WOP_SHOP_EDIT; ?>" href="admin.php?page=options&tab=taxes&task=edit&tax_id=<?php echo $tax->tax_id; ?>"><?php echo _WOP_SHOP_EDIT; ?></a>
                        |
                    </span>
                    <span class="trash">
                        <a class="submitdelete" title="<?php echo _WOP_SHOP_DELETE; ?>" href="admin.php?page=options&tab=taxes&task=delete&rows[]=<?php echo $tax->tax_id; ?>"><?php echo _WOP_SHOP_DELETE; ?></a>
                    </span>
                </div>
            </td>
            <td>
                <a href="admin.php?page=options&tab=exttaxes&back_tax_id=<?php echo $tax->tax_id; ?>">
                    <?php echo _WOP_SHOP_EXTENDED_RULE_TAX; ?>
                </a>
           </td>
            <td align="center">
                <?php print "<a href='admin.php?page=options&tab=taxes&task=edit&tax_id=".$tax->tax_id."'><img src='".WOPSHOP_PLUGIN_URL."assets/images/icon-16-edit.png'></a>"; ?>
            </td>
            <td align="center">
                <?php print $tax->tax_id;?>
            </td>
        </tr>
      <?php
        }
          }   
    ?>
    </table>
        <input type="hidden" value="options" name="page">
        <input type="hidden" value="taxes" name="tab">

    </form>
</div>
