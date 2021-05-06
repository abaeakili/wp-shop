<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$rows = $this->rows;
$lists = $this->lists;
$config = $this->config;
?>
<div class="wrap">
    <h2>
        <?php echo _WOP_SHOP_MENU_ORDERS; ?>
        <a href="admin.php?page=orders&task=edit&row=0" class="add-new-h2"><?php echo _WOP_SHOP_NEW; ?></a>
    </h2>
    <form action="" method="POST" name="search">
        <?php print $this->tmp_html_filter?>
        <p class="search-box"><?php echo $this->text_search; ?></p>
        <p class="search-box"><?php echo $this->lists['notfinished']; ?></p>
        <p class="search-box" style="line-height: 30px;margin:0 20px;"><?php echo _WOP_SHOP_NOT_FINISHED; ?>:</p>
        <p class="search-box"><?php echo $this->lists['changestatus']; ?></p>
        <p class="search-box" style="line-height: 30px;margin:0 20px;"><?php echo _WOP_SHOP_ORDER_STATUS; ?>:</p>
        <p class="search-box"><?php echo $this->lists['year']; ?></p>
        <p class="search-box"><?php echo $this->lists['month']; ?></p>
        <p class="search-box"><?php echo $this->lists['day']; ?></p>
        <p class="search-box" style="line-height: 30px;margin-right:20px;"><?php echo _WOP_SHOP_SORT_DATE; ?>:</p>
    </form>    
    <form id="listing" action="" method="get">
        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <?php echo $this->bulk;?>
            </div>
            <?php 
                echo $this->pagination;
            ?>            
            <br class="clear">
        </div>
        <table class="wp-list-table widefat fixed posts">
            <thead>
                <tr>
                    <th id="cb" class="manage-column column-cb check-column wopshop-admin-list-check" scope="col">
                        <input id="cb-select-all-1" type="checkbox" />
                    </th>
                    <?php if($this->filter_order == 'order_number') $class_name = 'sorted'; else $class_name = 'sortable';?>
                    <th class="manage-column <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col" >
                        <a href="admin.php?page=orders&filter_order=order_number&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_NUMBER; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <?php if($this->filter_order == 'name') $class_name = 'sorted'; else $class_name = 'sortable';?>
                    <th class="manage-column <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col" >
                        <a href="admin.php?page=orders&filter_order=name&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_USER; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <?php if($this->filter_order == 'email') $class_name = 'sorted'; else $class_name = 'sortable';?>
                    <th class="manage-column <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col">
                        <a href="admin.php?page=orders&filter_order=email&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_EMAIL; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <?php if ($this->show_vendor){?>
                    <th>
                        <span class="status_head tips"><?php echo _WOP_SHOP_VENDOR; ?></span>
                    </th>
                    <?php } ?>
                    <th>
                        <span class="status_head tips"><?php echo _WOP_SHOP_ORDER_PRINT_VIEW; ?></span>
                    </th>
                    <?php if($this->filter_order == 'order_date') $class_name = 'sorted'; else $class_name = 'sortable';?>
                    <th class="manage-column <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col">
                        <a href="admin.php?page=orders&filter_order=order_date&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_DATE; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <?php if($this->filter_order == 'order_m_date') $class_name = 'sorted'; else $class_name = 'sortable';?>
                    <th class="manage-column <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col">
                        <a href="admin.php?page=orders&filter_order=order_m_date&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_ORDER_MODIFY_DATE; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <?php if (!$config->without_payment){?>
                    <th>
                        <span class="status_head tips"><?php echo _WOP_SHOP_PAYMENT; ?></span>
                    </th>
                    <?php }?>
                    <?php if (!$config->without_shipping){?>
                    <th>
                        <span class="status_head tips"><?php echo _WOP_SHOP_SHIPPINGS; ?></span>
                    </th>
                    <?php } ?>
                    <?php if($this->filter_order == 'order_status') $class_name = 'sorted'; else $class_name = 'sortable';?>
                    <th class="manage-column <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col">
                        <a href="admin.php?page=orders&filter_order=order_status&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_STATUS; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th>
                        <span class="status_head tips"><?php echo _WOP_SHOP_ORDER_UPDATE; ?></span>
                    </th>
                    <?php if($this->filter_order == 'order_total') $class_name = 'sorted'; else $class_name = 'sortable';?>
                    <th class="manage-column <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col">
                        <a href="admin.php?page=orders&filter_order=order_total&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_ORDER_TOTAL; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <?php if ($config->shop_mode==1){?>
                    <th>
                      <?php echo _WOP_SHOP_TRANSACTIONS?>
                    </th>
                    <?php }?>
                </tr>
            </thead>
            <tbody id="the-list">
                <?php if(count($rows) == 0){ ?>
                <tr class="no-items">
                    <td class="colspanchange" colspan="3"><?php echo _WOP_SHOP_QUERY_RESULT_NULL; ?></td>
                </tr>
                <?php 
                }else{
                    $i = 0; 
                    foreach($rows as $row){
                        $display_info_order = $row->display_info_order;
                ?>
                    <tr class="row<?php echo ($i  %2);?>" <?php if (!$row->order_created) print "style='font-style:italic; color: #b00;'"?>>
                        <td class="check-column wopshop-admin-list-check" scope="col">
                            <?php if ($row->blocked) : ?>
                                <img src="<?php echo WOPSHOP_PLUGIN_URL;?>assets/images/checked_out.png" />
                            <?php else : ?>
                                <input id="cid_<?php echo $row->order_id; ?>" type="checkbox" value="<?php echo $row->order_id; ?>" name="rows[]" />
                            <?php endif; ?>
                        </td>
                        <td class="name-column" scope="col">
                            <strong>
                            <a class="" title="<?php echo _WOP_SHOP_SHOW; ?>" href="admin.php?page=orders&task=show&order_id=<?php echo $row->order_id; ?>"><?php echo $row->order_number;?></a>
                            <?php if (!$row->order_created) print "("._WOP_SHOP_NOT_FINISHED.")";?>
                            </strong>
                            <div class="row-actions">
                                <span class="edit">
                                <a class="" title="<?php echo _WOP_SHOP_EDIT; ?>" href="admin.php?page=orders&task=edit&order_id=<?php echo $row->order_id; ?>"><?php echo _WOP_SHOP_EDIT; ?></a>
                                |
                                </span>
                                <span class="trash">
                                <a class="submitdelete" title="<?php echo _WOP_SHOP_DELETE; ?>" href="admin.php?page=orders&task=delete&rows[]=<?php echo $row->order_id; ?>"><?php echo _WOP_SHOP_DELETE; ?></a>
                                </span>
                            </div>                            
                        </td>
                        <td>        
                            <?php echo $row->name?>
                        </td>
                        <td><?php echo $row->email?></td>
                        <?php if ($this->show_vendor){?>
                        <td>
                           <?php print $row->vendor_name;?>
                        </td>
                        <?php }?>     
                        <td class = "center">
                           <?php if ($config->order_send_pdf_client || $config->order_send_pdf_admin){?>
                               <?php if ($display_info_order && $row->order_created && $row->pdf_file!=''){?>
                                   <a href = "javascript:void window.open('<?php echo $config->pdf_orders_live_path."/".$row->pdf_file?>', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=800,height=600,directories=no,location=no');">
                                       <img src="<?php echo WOPSHOP_PLUGIN_URL;?>assets/images/print.png">
                                   </a>
                               <?php }?>
                           <?php }else{?>
                               <a href = "javascript:void window.open('admin-ajax.php?page=orders&task=printOrder&action=printOrder&order_id=<?php echo $row->order_id?>', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=yes,resizable=yes,width=800,height=600,directories=no,location=no');">
                                   <img border="0" src="<?php echo WOPSHOP_PLUGIN_URL;?>assets/images/print.png" alt="printhtml" />
                               </a>
                           <?php }?>
                           <?php echo $row->_ext_order_info;?>
                        </td>
                        <td>
                          <?php echo $row->order_date;?>
                        </td>
                        <td>
                          <?php echo $row->order_m_date;?>
                        </td>
                        <?php if (!$config->without_payment){?>
                        <td>
                          <?php echo $row->payment_name?>
                        </td>
                        <?php }?>
                        <?php if (!$config->without_shipping){?>
                        <td>
                          <?php echo $row->shipping_name?>
                        </td>
                        <?php }?>
                        <td>
                           <?php if ($display_info_order && $row->order_created){
                               //echo getSelect( array('options'=>$lists['status_orders'], 'class'=>'inputbox', 'id'=>'select_status_id'.$row->order_id, 'name'=>'select_status_id['.$row->order_id.']', 'selected'=>$row->order_status, 'desc'=>'', 'extra'=>'' ));
                               echo HTML::_('select.genericlist', $lists['status_orders'], 'select_status_id['.$row->order_id.']', 'class="inputbox" id = "status_id_'.$row->order_id.'"', 'status_id', 'name', $row->order_status );
                           }else{
                               print $this->list_order_status[$row->order_status];
                           }
                           ?>
                        </td>
                        <td>
                        <?php if ($row->order_created && $display_info_order){?>
                           <input class = "inputbox" type = "checkbox" name = "order_check_id[<?php echo $row->order_id?>]" id = "order_check_id_<?php echo $row->order_id?>" />
                           <label for = "order_id_<?php echo $row->order_id?>"><?php echo _WOP_SHOP_NOTIFY_CUSTOMER?></label><br />
                           <input class = "button" type = "button" name = "" value = "<?php echo _WOP_SHOP_UPDATE_STATUS?>" onclick = "verifyStatus(<?php echo $row->order_status; ?>, <?php echo $row->order_id; ?>, '<?php echo _WOP_SHOP_CHANGE_ORDER_STATUS;?>', 0, '<?php echo $adv_string?>');" />
                        <?php }?>
                        <?php if ($display_info_order && !$row->order_created && !$row->blocked){
                            echo $row->order_id; ?>
                           <a href="admin.php?option=orders&task=finish&order_id=<?php print $row->order_id?>"><?php print _WOP_SHOP_FINISH_ORDER?></a>
                        <?php }?>
                        <?php print $row->_tmp_ext_info_update?>
                        </td>
                        <td>
                          <?php if ($display_info_order) echo formatprice( $row->order_total,$row->currency_code)?>
                        </td>
                        <?php if ($config->shop_mode==1){?>
                        <td align="center">
                          <a href='admin.php?option=orders&task=transactions&order_id=<?php print $row->order_id;?>'><img src="<?php echo WOPSHOP_PLUGIN_URL; ?>assets/images/icons/configurations.png"></a>
                        </td>
                        <?php }?>
                      </tr>
                      <?php
                      $i++;
                      }
                      ?>
                    <tr>
                        <?php 
                        $cols = 9;
                        if (!$config->without_payment) $cols++;
                        if (!$config->without_shipping) $cols++;
                        ?>
                        <td colspan="<?php print $cols+(int)$this->deltaColspan0?>" align="right"><b><?php print _WOP_SHOP_TOTAL?></b></td>
                        <td><b><?php print formatprice($this->total, getMainCurrencyCode())?></b></td>
                    </tr>
                <?php }
                ?>    
            </tbody>
        </table>
        
        <input type="hidden" value="orders" name="page" />
        <input type="hidden" name="filter_order" value="<?php echo $this->filter_order?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_order_Dir?>" />
    </form>
    <br class="clear">
</div>