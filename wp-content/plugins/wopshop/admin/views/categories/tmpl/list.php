<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$categories = $this->categories; 
$i = 0;
$count = count($categories); 
$saveOrder = $this->filter_order_Dir=="desc" && $this->filter_order=="ordering";
?>
<div class="wrap">
    <h2>
        <?php echo _WOP_SHOP_MENU_CATEGORIES; ?>
        <a href="admin.php?page=categories&task=edit&row=0" class="add-new-h2"><?php echo _WOP_SHOP_NEW_CATEGORY; ?></a>
    </h2>
    <form action="" method="POST">
    	<?php print $this->tmp_html_filter?> 
    	
        <?php echo $this->search; ?>
    </form>    
	<form id="listing" class="adminForm" action = "admin.php?page=categories" method = "post" name = "adminForm">
        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <?php echo $this->bulk;?>
            </div>
            <?php echo $this->pagination;?>
            <br class="clear">          
        </div>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th id="cb" class="manage-column column-cb check-column wopshop-admin-list-check" scope="col">
                        <input id="cb-select-all-1" type="checkbox">
                    </th>
                    <?php if($this->filter_order == 'name') $class_name = 'sorted'; else $class_name = 'sortable';?>
                    <th id="order_title" class="column-primary manage-column column-order_title <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col" width="200">
                        <a href="admin.php?page=categories&filter_order=name&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_TITLE; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th class="manage-column column-order_description" scope="col">
                        <span class="status_head tips"><?php echo _WOP_SHOP_DESCRIPTION; ?></span>
                    </th>
                    <th width="80">
                      <?php echo _WOP_SHOP_CATEGORY_PRODUCTS;?>
                    </th>
					<?php if($this->filter_order == 'ordering') $class_name = 'sorted'; else $class_name = 'sortable';?>
					<th colspan="2" id="ordering" class="ordering center manage-column column-ordering <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col" width="100">
						<a href="admin.php?page=categories&filter_order=ordering&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
							<span class="status_head tips"><?php echo _WOP_SHOP_ORDERING; ?></span>
							<span class="sorting-indicator"></span>
						</a>						
					</th>
					<th id="saveorder" class="save_ordering center manage-column" scope="col" width="100">
		<?php if ($saveOrder){?>
						<a class="saveorder" onclick="saveorder();" href="#"><img src="<?php echo WOPSHOP_PLUGIN_URL;?>assets/images/save.png"/></a>
		<?php }?>						
					</th>					
                    <th class="manage-column" scope="col" width="100">
                        <?php echo _WOP_SHOP_PUBLISH; ?>
                    </th>  
                    <?php if($this->filter_order == 'id') $class_id = 'sorted'; else $class_id = 'sortable';?>
                    <th class="manage-column column-order_id <?php echo $class_id; ?> <?php echo $this->filter_order_Dir; ?>" scope="col" width="50">
                        <a href="admin.php?page=categories&filter_order=id&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_ID; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>                  
                </tr>
            </thead>
            <tbody id="the-list">
                <?php 
                    foreach($categories as $k=>$category) {?>
                <tr class="<?php if($k%2) echo 'alt';?>">
                        <td class="check-column wopshop-admin-list-check" scope="col">
                            <input id="cid_<?php echo $category->category_id; ?>" type="checkbox" value="<?php echo $category->category_id; ?>" name="rows[]">
                        </td> 
                        <td class="column-primary name-column" scope="col">
                            <strong>
                            <?php print $category->space; ?><?php echo $category->name;?>
                            </strong>
                            <div class="row-actions">
                                <span class="edit">
                                <a class="" title="<?php echo _WOP_SHOP_EDIT; ?>" href="admin.php?page=categories&task=edit&category_id=<?php echo $category->category_id; ?>"><?php echo _WOP_SHOP_EDIT; ?></a>
                                |
                                </span>
                                <span class="trash">
                                <a class="submitdelete" title="<?php echo _WOP_SHOP_DELETE; ?>" href="admin.php?page=categories&task=delete&rows[]=<?php echo $category->category_id; ?>&action=-1"><?php echo _WOP_SHOP_DELETE; ?></a>
                                </span>
                            </div>                            
                        </td>
                        <td class="name-column" scope="col">
                            <?php echo $category->short_description;?>
                        </td>
                        <td align="center">
                          <?php if (isset($this->countproducts[$category->category_id])){?>
                          <a href="admin.php?page=products&category_id=<?php echo $category->category_id?>">
                            (<?php print intval($this->countproducts[$category->category_id]);?>) <img src="<?php echo WOPSHOP_PLUGIN_URL ?>assets/images/tree.gif" border="0" />
                          </a>
                          <?php }else{?>
                          (0)
                          <?php }?>
                        </td>                        
                        <td align = "right" width = "20">
                             <?php if ($saveOrder && $category->isPrev) echo '<a href = "admin.php?page=categories&task=order&id='.$category->category_id.'&move=-1"><img alt="' . _WOP_SHOP_UP . '" src="'.WOPSHOP_PLUGIN_URL.'assets/images/uparrow.png"/></a>'; ?>
                         </td>
                         <td align = "left" width = "20"> 
                             <?php if ($saveOrder && $category->isNext) echo '<a href = "admin.php?page=categories&task=order&id='.$category->category_id.'&move=1"><img alt="' . _WOP_SHOP_DOWN . '" src="'.WOPSHOP_PLUGIN_URL.'assets/images/downarrow.png"/></a>'; ?>
                         </td>

                        <td align="center">
                            <input   <?php if (!$saveOrder) echo 'disabled'?> type="text" name="order[]" id="ord<?php echo $category->category_id;?>" size="3" value="<?php echo $category->ordering;?>" class="inputordering" />
                        </td>
												
                        <td class="name-column" scope="col" align="center">
                            <?php
                              echo $published=($category->category_publish) ? ('<a href = "admin.php?page=categories&task=unpublish&rows[]='.$category->category_id.'"><img alt="' . _WOP_SHOP_DOWN . '" src="'.WOPSHOP_PLUGIN_URL.'assets/images/tick.png"/></a>') : ('<a href = "admin.php?page=categories&task=publish&rows[]='.$category->category_id.'"><img alt="' . _WOP_SHOP_DOWN . '" src="'.WOPSHOP_PLUGIN_URL.'assets/images/publish_x.png"/></a>');                           
                            ?>                            
                        </td>
                        <td class="name-column" scope="col" align="center">
                            <?php echo $category->category_id?>
                        </td>
                </tr>                      
				<?php   } ?>                
            </tbody>
        </table>        
        <input type="hidden" value="categories" name="page">
        <input type = "hidden" name = "task" value = "display" />	
    </form>
<div id="ajax-response"></div>
<br class="clear">
</div>