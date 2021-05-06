<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$rows=$this->rows;
$lists=$this->lists;
$text_search=$this->text_search;
$category_id=$this->category_id;
$manufacturer_id=$this->manufacturer_id;
$count=count ($rows);
$i=0;
$saveOrder=$this->filter_order_Dir=="desc" && $this->filter_order=="ordering";
?>
<div class="wrap">
    <h2>
        <?php echo _WOP_SHOP_MENU_PRODUCTS; ?>
        <a href="admin.php?page=products&task=edit&row=0" class="add-new-h2"><?php echo _WOP_SHOP_NEW_PRODUCT; ?></a>
    </h2>
    <hr />
    <div class="wopshop_admin_products_tab">
	<div class='wopshop_admin_producs_filters_block'>
    <form action="" method="POST" name="search">
        <?php print $this->tmp_html_filter?>
        <?php echo $this->search; ?>
        <p class="search-box wopshop_admin_products_filters_elements"><?php echo $this->lists['treecategories']; ?></p>
        <p class="search-box wopshop_admin_products_filters_elements"><?php echo $this->lists['manufacturers']; ?></p>
        <p class="search-box wopshop_admin_products_filters_elements"><?php echo $this->lists['labels']; ?></p>
        <p class="search-box wopshop_admin_products_filters_elements"><?php echo $this->lists['publish']; ?></p>
    </form>    
	</div>
	<form id="listing" class="adminForm" action = "admin.php?page=products" method = "post" name = "adminForm">
        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <?php echo $this->bulk;?>
            </div>
            
            <br class="clear">
            
        </div>
        <table class="wp-list-table widefat posts wopshop_admin_products_list_lines">
            <thead>
                <tr>
                    <th class="manage-column column-cb check-column wopshop-admin-list-check" width="50">
                        <input id="cb-select-all-1" type="checkbox" />
                    </th>
                    <?php if($this->filter_order == 'name_image') $class_image= 'sorted'; else $class_image= 'sortable';?>
                    <th class="manage-column column-order_image <?php echo $class_image; ?> <?php echo $this->filter_order_Dir; ?> tableProductTitleCell wopshop_admin_products_img_cell_title" scope="col" width="110">
                        <span style='display:block'>
                            <a href="admin.php?page=products&filter_order=name_image&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                                <span class="status_head tips"><?php echo _WOP_SHOP_IMAGE; ?></span>
                                <span class="sorting-indicator"></span>
                            </a>
                        </span>
                    </th>
                    <?php if($this->filter_order == 'name') $class_name = 'sorted'; else $class_name = 'sortable';?>
                    <th id="order_title" class="manage-column column-order_title <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?> tableProductTitleCell" scope="col">
                        <a href="admin.php?page=products&filter_order=name&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_TITLE; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th class='tableProductTitleCell' width="80">
                        <span class="status_head tips"><?php echo _WOP_SHOP_STATUS; ?></span>
                    </th>
                    <?php if (!$this->category_id){?>
                        <?php if($this->filter_order == 'category') $class_category = 'sorted'; else $class_category = 'sortable';?>
                        <th class="manage-column column-order_category <?php echo $class_category; ?> <?php echo $this->filter_order_Dir; ?> tableProductTitleCell" scope="col" width="150">
                            <a href="admin.php?page=products&filter_order=category&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                                <span class="status_head tips"><?php echo _WOP_SHOP_CATEGORY; ?></span>
                                <span class="sorting-indicator"></span>
                            </a>
                        </th>
                    <?php } ?>
                    <?php if($this->filter_order == 'manufacturer') $class_manufacturer = 'sorted'; else $class_manufacturer = 'sortable';?>
                    <th class="manage-column column-order_manufacturer <?php echo $class_manufacturer; ?> <?php echo $this->filter_order_Dir; ?> tableProductTitleCell" scope="col" width="150">
                        <a href="admin.php?page=products&filter_order=manufacturer&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_MANUFACTURER; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <?php if($this->filter_order == 'ean') $class_ean = 'sorted'; else $class_ean = 'sortable';?>
                    <th class="manage-column column-order_ean <?php echo $class_ean; ?> <?php echo $this->filter_order_Dir; ?> tableProductTitleCell" scope="col" width="130">
                        <a href="admin.php?page=products&filter_order=ean&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_EAN_PRODUCT; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <?php if($this->filter_order == 'qty') $class_qty = 'sorted'; else $class_qty = 'sortable';?>
                    <th class="manage-column column-order_qty <?php echo $class_qty; ?> <?php echo $this->filter_order_Dir; ?> tableProductTitleCell" scope="col" width="100">
                        <a href="admin.php?page=products&filter_order=qty&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_QUANTITY_PRODUCT; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <?php if($this->filter_order == 'price') $class_price = 'sorted'; else $class_price = 'sortable';?>
                    <th class="manage-column column-order_price <?php echo $class_price; ?> <?php echo $this->filter_order_Dir; ?> tableProductTitleCell" scope="col" width="120">
                        <a href="admin.php?page=products&filter_order=price&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_PRODUCT_PRICE; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <?php if($this->filter_order == 'hits') $class_hits = 'sorted'; else $class_hits = 'sortable';?>
                    <th class="manage-column column-order_hits <?php echo $class_hits; ?> <?php echo $this->filter_order_Dir; ?> tableProductTitleCell" scope="col" width="80">
                        <a href="admin.php?page=products&filter_order=hits&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_HITS; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <?php if($this->filter_order == 'date') $class_date = 'sorted'; else $class_date = 'sortable';?>
                    <th class="manage-column column-order_date <?php echo $class_date; ?> <?php echo $this->filter_order_Dir; ?> tableProductTitleCell" scope="col" width="120">
                        <a href="admin.php?page=products&filter_order=date&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_DATE; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
					
					
					<?php if ($category_id) {?>
						<?php if($this->filter_order == 'ordering') $class_name = 'sorted'; else $class_name = 'sortable';?>
						<th colspan="2" id="ordering" class="ordering center manage-column column-ordering <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col" width="100">
							<a href="admin.php?page=products&filter_order=ordering&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
								<span class="status_head tips"><?php echo _WOP_SHOP_ORDERING; ?></span>
								<span class="sorting-indicator"></span>
							</a>						
						</th>

						<th id="saveorder" class="save_ordering center manage-column" scope="col" width="100">
				<?php if ($saveOrder){?>
							<a class="saveorder" onclick="saveorder();" href="#"><img src="<?php echo WOPSHOP_PLUGIN_URL;?>assets/images/save.png"/></a>
				<?php }?>						
						</th>
					<?php }?>
                    <?php if($this->filter_order == 'product_id') $class_date = 'sorted'; else $class_date = 'sortable';?>
                    <th class="manage-column column-order_date <?php echo $class_date; ?> <?php echo $this->filter_order_Dir; ?> tableProductTitleCell" scope="col" width="50">
                        <a href="admin.php?page=products&filter_order=product_id&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_ID; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                </tr>
            </thead>
            <tbody id="the-list">
                <?php if(count($rows) == 0){ ?>
                <tr class="no-items">
                    <td class="colspanchange" colspan="3"><?php echo _WOP_SHOP_QUERY_RESULT_NULL; ?></td>
                </tr>
                <?php
                }else{
                foreach($rows as $k=>$row) {
                    if (!$row->image) $row->image = 'noimage.gif';
                ?>
                <tr class="<?php if($k%2) echo 'alt';?> wopshop_admin_products_list_line" >
                    <td class="check-column wopshop-admin-list-check">
                        <input id="cid_<?php echo $row->product_id; ?>" type="checkbox" value="<?php echo $row->product_id; ?>" name="rows[]">
                    </td>
                    <td class='tableProductCell wopshop_admin_products_img_cell'>
                        <?php if ($row->label_id){?>
                            <div class="product_label">
                            <?php if ($row->_label_image){?>
                                <img src="<?php print $row->_label_image?>" width="25" alt="" />
                            <?php }else{?>
                                <span class="label_name"><?php print $row->_label_name;?></span>
                            <?php }?>
                            </div>
                        <?php }?>
                        <?php if ($row->image){?>
                            <a href="admin.php?page=products&task=edit&product_id=<?php echo $row->product_id; ?>">
                                <img src="<?php print $this->config->image_product_live_path."/".$row->image?>" width="90" border="0" />
                            </a>
                        <?php }?>
                    </td>
                    <td class="name-column tableProductCell" scope="col">
                        <strong>
                            <?php echo $row->name;?>
                        </strong>
                        <div class="row-actions">
                            <span class="edit">
                                <a title="<?php echo _WOP_SHOP_EDIT; ?>" href="admin.php?page=products&task=edit&product_id=<?php echo $row->product_id; ?>"><?php echo _WOP_SHOP_EDIT; ?></a>
                                |
                            </span>
                            <span class="trash">
                                <a class="submitdelete" title="<?php echo _WOP_SHOP_DELETE; ?>" href="admin.php?page=products&task=delete&rows[]=<?php echo $row->product_id; ?>&action=-1"><?php echo _WOP_SHOP_DELETE; ?></a>
                                |
                            </span>
                            <span class="duplicate">
                                <a title="<?php echo _WOP_SHOP_DUPLICATE; ?>" href="admin.php?page=products&task=copy&rows[]=<?php echo $row->product_id; ?>"><?php echo _WOP_SHOP_DUPLICATE; ?></a>
                            </span>
                        </div>
                    </td>
                    <td class='tableProductCell'>
                        <?php if($row->product_publish) echo '<img src="'.WOPSHOP_PLUGIN_URL.'assets/images/tick.png">'; else echo '<img src="'.WOPSHOP_PLUGIN_URL.'assets/images/publish_x.png">'; ?>
                    </td>
                    <?php if (!$this->category_id){?>
                    <td class="name-column tableProductCell" scope="col">
                        <?php echo $row->namescats;?>
                    </td>
                    <?php } ?>
                    <td class='tableProductCell'>
                        <?php echo $row->man_name;?>
                    </td>
                    <td class='tableProductCell'>
                        <?php echo $row->ean;?>
                    </td>
                    <td class='tableProductCell'>
                        <?php if ($row->unlimited){
                            print _WOP_SHOP_UNLIMITED;
                        }else{
                            echo $row->qty;
                        }
                        ?>
                    </td>
                    <td class='tableProductCell'>
                        <?php echo formatprice($row->product_price); ?>
                    </td>
                    <td class="name-column tableProductCell" scope="col">
                        <?php echo $row->hits;?>
                    </td>
                    <td class="name-column tableProductCell" scope="col">
                        <?php echo $row->product_date_added;?>
                    </td>					
					<?php if ($category_id) {?>   
					 <td align="right" width="10">
					 <?php
						  if ($k != 0 && $saveOrder) echo '<a class="btn btn-micro" href="admin.php?page=products&task=order&product_id='.$row->product_id.'&category_id='.$category_id.'&order=up&number='.$row->product_ordering.'"><img alt="' . _WOP_SHOP_UP . '" src="'.WOPSHOP_PLUGIN_URL.'assets/images/uparrow.png"/></a>';
					 ?>
					 </td>      
					 <td align="left" width="10">
					 <?php
						  if ($k!=$count-1 && $saveOrder) echo '<a class="btn btn-micro" href="admin.php?page=products&task=order&product_id='.$row->product_id.'&category_id='.$category_id.'&order=down&number='.$row->product_ordering.'"><img alt="' . _WOP_SHOP_DOWN . '" src="'.WOPSHOP_PLUGIN_URL.'assets/images/downarrow.png"/></a>';
					 ?>
					 </td>   
					 <td align="center">
					  <input type="text" name="order[]" id="ord<?php echo $row->product_id;?>" size="3" value="<?php echo $row->product_ordering; ?>" <?php if (!$saveOrder) echo 'disabled'?> class="inputordering" style="text-align: center" />
					 </td>      
					<?php }?>  
   
                    <td class="name-column tableProductCell wopshop_admin_products_id_cell" scope="col" align="center">
                        <?php echo $row->product_id?>
                    </td>
                </tr>
                <tr><td class='wopshop_admin_product_tr_line' colspan='11'></td></tr>
<?php
                    }
                }
?>
            </tbody>
        </table> 
		<div class='wopshop_products_pagination_block'>
			<?php echo $this->pagination;?>
		</div>
        <input type="hidden" value="products" name="page">
		<input type = "hidden" name = "task" value = "display" />
		<?php if ($category_id) {?>
			<input type = "hidden" name = "category_id" value = "<?php echo $category_id ?>" />
		<?php }?>
    </form>
<div id="ajax-response"></div>
<br class="clear">
</div>
</div>


<script>
    function setValue(name, value){
        jQuery('input[name="category_id"]').val(value);
    }
</script>