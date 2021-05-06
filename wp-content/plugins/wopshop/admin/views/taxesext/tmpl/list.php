<?php 
/**
* @version      1.0.0 01.06.2016
* @author       MAXXmarketing GmbH
* @package      WOPshop
* @copyright    Copyright (C) 2010 http://www.wop-agentur.com. All rights reserved.
* @license      GNU/GPL
*/

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
$i=0;

displaySubmenuOptions("taxes");?>
<div class="wrap">
    <h2>
        <?php echo _WOP_SHOP_LIST_TAXES; ?>
        <a href="admin.php?page=options&tab=exttaxes&task=edit&row=0&back_tax_id=1" class="add-new-h2"><?php echo _WOP_SHOP_NEW_TAX; ?></a>
        <a href="admin.php?page=options&tab=taxes" class="add-new-h2"><?php echo _WOP_SHOP_LIST_TAXES; ?></a>
    </h2>
    <?php echo $this->top_counters; ?>
    <form action="admin.php?page=options&tab=exttaxes" method="POST">
        <?php echo $this->search; ?>
    </form>
</div>
<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<form method="GET" id="listing">
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
            <th align = "left">
                <?php echo _WOP_SHOP_TITLE; ?>
            </th>
            <th>
                <?php echo _WOP_SHOP_COUNTRY; ?>
            </th>
            <?php if($this->filter_order == 'ET.tax') $class_name = 'sorted'; else $class_name = 'sortable';?>
            <th id="order_exttaxes_tax" class="manage-column column-order_exttaxes_tax <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col">
                <a href="admin.php?page=options&tab=exttaxes&back_tax_id=<?php echo $this->back_tax_id; ?>&filter_order=ET.tax&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>&paged=1">
                    <span class="status_head tips"><?php echo _WOP_SHOP_TAX; ?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
            <?php if($this->filter_order == 'ET.firma_tax') $class_name = 'sorted'; else $class_name = 'sortable';?>
            <th  id="order_exttaxes_firm" class="manage-column column-order_exttaxes_firm <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col">
                <?php
                    if ($this->config->ext_tax_rule_for==1){ ?>
                        <a href="admin.php?page=options&tab=exttaxes&back_tax_id=<?php echo $this->back_tax_id; ?>&filter_order=ET.firma_tax&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>&paged=1">
                            <span class="status_head tips"><?php echo _WOP_SHOP_USER_WITH_TAX_ID_TAX; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                <?php }
                    else {
                ?>
                    <a href="admin.php?page=options&tab=exttaxes&back_tax_id=<?php echo $this->back_tax_id; ?>&filter_order=ET.firma_tax&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>&paged=1">
                        <span class="status_head tips"><?php echo _WOP_SHOP_FIRMA_TAX; ?></span>
                        <span class="sorting-indicator"></span>
                    </a>
                <?php } ?>
            </th>
            <th width = "80">
                <?php echo _WOP_SHOP_EDIT; ?>
            </th>
            <?php if($this->filter_order == 'id') $class_name = 'sorted'; else $class_name = 'sortable';?>
            <th width = "50" id="order_exttaxes_id" class="manage-column column-order_exttaxes_id <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col">
                <a href="admin.php?page=options&tab=exttaxes&back_tax_id=<?php echo $this->back_tax_id; ?>&filter_order=id&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>&paged=1">
                    <span class="status_head tips"><?php echo _WOP_SHOP_ID; ?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
        </tr>
    </thead>
    <tbody id="the-list">
    <?php foreach($this->rows as $row) { ?>
        <tr class="row<?php echo $i % 2;?>">
            <th class="check-column" scope="col">
                <input id="exttax_<?php echo $row->id; ?>" type="checkbox" value="<?php echo $row->id; ?>" name="rows[]">
            </th>
            <td>
              <?php echo $row->tax_name;?>
            </td>
            <td>
             <?php echo $row->countries;?>
            </td>
            <td>
             <?php echo $row->tax;?> %
            </td>
            <td>
             <?php echo $row->firma_tax;?> %
            </td>
            <td class="center">
                <a href='admin.php?page=options&tab=exttaxes&task=edit&row=<?php print $row->id?>&back_tax_id=<?php print $this->back_tax_id;?>'><img src='<?php echo WOPSHOP_PLUGIN_URL; ?>assets/images/icon-16-edit.png'></a>
            </td>
            <td class="center">
                 <?php print $row->id;?>
            </td>
        </tr>
    <?php
    $i++;
    }
    ?>
    </tbody>
</table>

<input type="hidden" name="filter_order" value="<?php echo $this->filter_order?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_order_Dir?>" />
<input type="hidden" value="options" name="page">
<input type="hidden" value="exttaxes" name="tab">
<input type="hidden" value="<?php print $this->back_tax_id;?>" name="back_tax_id">

<?php print $this->tmp_html_end?>
</form>
</div>