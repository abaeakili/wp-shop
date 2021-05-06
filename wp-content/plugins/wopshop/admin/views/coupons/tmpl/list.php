<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
displaySubmenuOptions('coupons');
$rows=$this->rows;
//$pageNav=$this->pageNav;
$i=0;
?>
<div class="wrap">
    <h2>
        <?php echo _WOP_SHOP_LIST_COUPONS; ?>
        <a href="admin.php?page=options&tab=coupons&task=edit&row=0" class="add-new-h2"><?php echo _WOP_SHOP_NEW_COUPON; ?></a>
    </h2>
    <?php echo $this->top_counters; ?>
    <form action="admin.php?page=options&tab=coupons" method="POST">
        <?php print $this->tmp_html_filter?>
        <?php echo $this->search; ?>
    </form>
    <form id="listing" class="adminForm" method="GET" action="admin.php">
        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <?php echo $this->bulk;?>
            </div>
            <?php echo $this->pagination;?>
            <br class="clear">
        </div>
        <table class="wp-list-table widefat fixed posts">
            <thead>
                <tr>
                    <th id="cb" class="manage-column column-cb check-column" style="" scope="col">
                        <input id="cb-select-all-1" type="checkbox">
                    </th>
                    <?php if($this->filter_order == 'coupon_code') $class_name = 'sorted'; else $class_name = 'sortable';?>
                    <th id="order_code" class="manage-column column-order_code <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col">
                        <a href="admin.php?page=options&tab=coupons&filter_order=coupon_code&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>&paged=1">
                            <span class="status_head tips"><?php echo _WOP_SHOP_CODE; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th width = "80" align = "left">
                        <?php echo _WOP_SHOP_VALUE;?>
                    </th>
                      <th width = "80">
                          <?php echo _WOP_SHOP_START_DATE_COUPON ?>
                      </th>
                      <th width = "80">
                          <?php echo _WOP_SHOP_EXPIRE_DATE_COUPON ?>
                      </th>
                      <th width = "80">
                          <?php echo _WOP_SHOP_FINISHED_AFTER_USED ?>
                      </th>
                      <th width = "80">
                          <?php echo _WOP_SHOP_FOR_USER ?>
                      </th>
                      <th width = "80">
                          <?php echo _WOP_SHOP_COUPON_USED ?>
                      </th>
                          <?php echo $this->tmp_extra_column_headers?>
                      <th width = "50">
                          <?php echo _WOP_SHOP_PUBLISH;?>
                      </th>
                      <th width = "50">
                          <?php echo _WOP_SHOP_EDIT;?>
                      </th>
                      <th width = "50" id="order_coupon_id" class="manage-column column-order_coupon_id <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col">
                        <a href="admin.php?page=options&tab=coupons&filter_order=coupon_id&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>&paged=1">
                            <span class="status_head tips"><?php echo _WOP_SHOP_ID; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                </tr>
            </thead>
            <tbody id="the-list">
                <?php
                    foreach($rows as $row){
                        $finished=0; $date=date('Y-m-d');
                        if ($row->used) $finished=1;
                        if ($row->coupon_expire_date < $date && $row->coupon_expire_date!='0000-00-00' ) $finished=1;
                    ?>
                      <tr class="row<?php echo $i % 2;?>" <?php if ($finished) print "style='font-style:italic; color: #999;'"?>>
                       <?php /*<td>
                         <?php echo $pageNav->getRowOffset($i);?>
                       </td>*/?>
                       <th class="check-column" scope="col">
                            <input id="user_<?php echo $row->coupon_id; ?>" type="checkbox" value="<?php echo $row->coupon_id; ?>" name="rows[]">
                       </th>
                       <td class="name-column" scope="col">
                            <strong>
                            <?php echo $row->coupon_code;?>
                            </strong>
                            <div class="row-actions">
                                <span class="edit">
                                <a class="" title="<?php echo _WOP_SHOP_EDIT; ?>" href="admin.php?page=options&tab=coupons&task=edit&row=<?php echo $row->coupon_id; ?>"><?php echo _WOP_SHOP_EDIT; ?></a>
                                |
                                </span>
                                <span class="trash">
                                <a class="submitdelete" title="<?php echo _WOP_SHOP_DELETE; ?>" href="admin.php?page=options&tab=coupons&task=delete&rows[]=<?php echo $row->coupon_id; ?>&action=-1"><?php echo _WOP_SHOP_DELETE; ?></a>
                                </span>
                            </div>
                        </td>
                       <td class="code-column " scope="col">
                         <?php echo $row->coupon_value; ?>
                         <?php if ($row->coupon_type==0) print "%"; else print $this->currency;?>
                       </td>
                       <td class="code-column " scope="col">
                        <?php if ($row->coupon_start_date!='0000-00-00') print /*formatdate*/($row->coupon_start_date);?>
                       </td>
                       <td class="code2-column " scope="col">
                        <?php if ($row->coupon_expire_date!='0000-00-00')  print /*formatdate*/($row->coupon_expire_date);?>
                       </td>
                       <td align="center">
                        <?php if ($row->finished_after_used) print _WOP_SHOP_YES; else print _WOP_SHOP_NO?>
                       </td>
                       <td align="center">
                        <?php if ($row->for_user_id) print $row->f_name." ".$row->l_name; else print _WOP_SHOP_ALL;?>
                       </td>
                       <td align="center">
                        <?php if ($row->used) print _WOP_SHOP_YES; else print _WOP_SHOP_NO?>
                       </td>
                       <?php echo $row->tmp_extra_column_cells?>
                       <td align="center">
                         <?php echo $published=($row->coupon_publish) ? ('<a href="admin.php?page=options&tab=coupons&task=unpublish&rows[]='.$row->coupon_id.'"><img src="'.WOPSHOP_PLUGIN_URL.'assets/images/tick.png" title="'._WOP_SHOP_PUBLISH.'" ></a>') : ('<a href="admin.php?page=options&tab=coupons&task=publish&rows[]='.$row->coupon_id.'"><img title="'._WOP_SHOP_UNPUBLISH.'" src="'.WOPSHOP_PLUGIN_URL.'assets/images/publish_x.png"></a>'); ?>
                       </td>
                       <td align="center">
                            <a href='admin.php?page=options&tab=coupons&task=edit&row=<?php print $row->coupon_id?>'><img src='<?php echo WOPSHOP_PLUGIN_URL; ?>assets/images/icon-16-edit.png'></a>
                       </td>
                       <td align="center">
                         <?php echo $row->coupon_id ?>
                       </td>
                      </tr>
                    <?php
                    $i++;
                    }
                    ?>
            </tbody>
        </table>

        <input type="hidden" value="options" name="page">
        <input type="hidden" value="coupons" name="tab">
    </form>
    <div id="ajax-response"></div>
    <br class="clear">
</div>





