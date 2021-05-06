<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$rows = $this->products; 
$i = 0;
$text_search = $this->text_search;
$count = count($rows);
$saveOrder = $this->filter_order_Dir=="asc" && $this->filter_order=="ordering";
?>
<div class="wrap">
    <h2>
        <?php echo _WOP_SHOP_MENU_PRODUCTS; ?>
        <a href="admin.php?page=products&task=edit&row=0" class="add-new-h2"><?php echo _WOP_SHOP_NEW_PRODUCT; ?></a>
    </h2>
    <?php echo $this->top_counters; ?>
    <form action="" method="POST">
        <?php echo $this->search; ?>
    </form>    
    <form id="listing" action="" method="get">
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
                    <?php if($this->orderby == 'name_image') $class_image= 'sorted'; else $class_image= 'sortable';?>
                    <th class="manage-column column-order_image <?php echo $class_image; ?> <?php echo $this->order; ?>" scope="col">
                        <a href="admin.php?page=products&orderby=name_image&order=<?php echo $this->order; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_IMAGE; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <?php if($this->orderby == 'name') $class_name = 'sorted'; else $class_name = 'sortable';?>
                    <th id="order_title" class="manage-column column-order_title <?php echo $class_name; ?> <?php echo $this->order; ?>" scope="col">
                        <a href="admin.php?page=products&orderby=name&order=<?php echo $this->order; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_TITLE; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <?php if($this->orderby == 'category') $class_category = 'sorted'; else $class_category = 'sortable';?>
                    <th class="manage-column column-order_category <?php echo $class_category; ?> <?php echo $this->order; ?>" scope="col">
                        <a href="admin.php?page=products&orderby=category&order=<?php echo $this->order; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_CATEGORY; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <?php if($this->orderby == 'manufacturer') $class_manufacturer = 'sorted'; else $class_manufacturer = 'sortable';?>
                    <th class="manage-column column-order_manufacturer <?php echo $class_manufacturer; ?> <?php echo $this->order; ?>" scope="col">
                        <a href="admin.php?page=products&orderby=manufacturer&order=<?php echo $this->order; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_MANUFACTURER; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <?php if($this->orderby == 'ean') $class_ean = 'sorted'; else $class_ean = 'sortable';?>
                    <th class="manage-column column-order_ean <?php echo $class_ean; ?> <?php echo $this->order; ?>" scope="col">
                        <a href="admin.php?page=products&orderby=ean&order=<?php echo $this->order; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_EAN_PRODUCT; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <?php if($this->orderby == 'qty') $class_qty = 'sorted'; else $class_qty = 'sortable';?>
                    <th class="manage-column column-order_qty <?php echo $class_qty; ?> <?php echo $this->order; ?>" scope="col" >
                        <a href="admin.php?page=products&orderby=qty&order=<?php echo $this->order; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_QUANTITY_PRODUCT; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <?php if($this->orderby == 'price') $class_price = 'sorted'; else $class_price = 'sortable';?>
                    <th class="manage-column column-order_price <?php echo $class_price; ?> <?php echo $this->order; ?>" scope="col" width="50">
                        <a href="admin.php?page=products&orderby=price&order=<?php echo $this->order; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_PRODUCT_PRICE; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <?php if($this->orderby == 'hits') $class_hits = 'sorted'; else $class_hits = 'sortable';?>
                    <th class="manage-column column-order_hits <?php echo $class_hits; ?> <?php echo $this->order; ?>" scope="col" width="50">
                        <a href="admin.php?page=products&orderby=hits&order=<?php echo $this->order; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_HITS; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <?php if($this->orderby == 'date') $class_date = 'sorted'; else $class_date = 'sortable';?>
                    <th class="manage-column column-order_date <?php echo $class_date; ?> <?php echo $this->order; ?>" scope="col" width="100">
                        <a href="admin.php?page=products&orderby=date&order=<?php echo $this->order; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_DATE; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <?php if($this->orderby == 'id') $class_date = 'sorted'; else $class_date = 'sortable';?>
                    <th class="manage-column column-order_id <?php echo $class_id; ?> <?php echo $this->order; ?>" scope="col" width="50">
                        <a href="admin.php?page=products&orderby=id&order=<?php echo $this->order; ?>">
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
                    foreach($rows as $k=>$row) {/*print_r($row);*/?>
                <tr class="<?php if($k%2) echo 'alt';?>">
                        <th class="check-column" scope="col">
                            <input id="cid_<?php echo $row->product_id; ?>" type="checkbox" value="<?php echo $row->product_id; ?>" name="cid[]">
                        </th> 
                        <th class="check-column" scope="col">
                            <img src="<?php echo WOPSHOP_PLUGIN_URL.'files/img_products/thumb/'.$row->image; ?>">
                        </th>
                        </td>
                        <td class="name-column" scope="col">
                            <strong>
                            <?php echo $row->name;?>
                            </strong>
                            <div class="row-actions">
                                <span class="edit">
                                <a class="" title="<?php echo _WOP_SHOP_EDIT; ?>" href="admin.php?page=products&task=edit&product_id=<?php echo $row->product_id; ?>"><?php echo _WOP_SHOP_EDIT; ?></a>
                                |
                                </span>
                                <span class="trash">
                                <a class="submitdelete" title="<?php echo _WOP_SHOP_DELETE; ?>" href="admin.php?page=products&task=delete&rows[]=<?php echo $row->product_id; ?>&action=-1"><?php echo _WOP_SHOP_DELETE; ?></a>
                                </span>
                            </div>                            
                        </td>
                        <td class="name-column" scope="col">
                            <?php echo $row->namescats;?>
                        </td>
                        <td align="center">
                          <?php echo $row->man_name;?>
                        </td>                        
                        <td align = "center">
                            <?php echo $row->ean;?>
                        </td>
                        <td align = "left" width = "20"> 
                            <?php if ($row->unlimited){
                                print _WOP_SHOP_UNLIMITED;
                            }else{
                                echo $row->qty;
                            }
                            ?>
                        </td>
                        <td align="center" width="10">
                            <?php echo formatprice($row->product_price); ?>
                        </td>
                        <td class="name-column" scope="col" align="center">
                            <?php echo $row->hits;?>
                        </td>
                        <td class="name-column" scope="col" align="center">
                            <?php echo $row->product_date_added;?>
                        </td>
                        <td class="name-column" scope="col" align="center">
                            <?php echo $row->product_id?>
                        </td>
                </tr>                      
<?php                        
                    }                   
                }
?>                
            </tbody>
        </table>        
        <input type="hidden" value="products" name="page">      
    </form>
<div id="ajax-response"></div>
<br class="clear">
</div>