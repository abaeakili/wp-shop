<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
displaySubmenuOptions('shippings');
$rows = $this->rows;
$i = 0;
?>
<style>
	.adminForm .wp-list-table{
		vertical-align: middle;
	}
</style>
<div class="wrap">
    <h2>
        <?php echo _WOP_SHOP_SHIPPING_EXT_PRICE_CALC; ?>
    </h2>
	<form id="listing" class="adminForm" action = "admin.php?page=options&tab=shippingextprice" method = "post" name = "adminForm">
        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
					<th align="left" width="300">
					  <?php echo _WOP_SHOP_TITLE;?>
					</th>
					<th>
						<?php echo _WOP_SHOP_DESCRIPTION;?>
					</th>
					<th>
					  <?php echo _WOP_SHOP_ORDERING;?>
					</th>
					<th width="100">
					  <?php echo _WOP_SHOP_PUBLISH;?>
					</th>
					<th width="140">
						<?php echo _WOP_SHOP_CONFIG;?>
					</th>
					<th width="80">
						<?php echo _WOP_SHOP_DELETE;?>
					</th>
					<th width="40">
						<?php echo _WOP_SHOP_ID;?>
					</th>
                </tr>
            </thead>
            <tbody id="the-list">
                <?php 
                $count = count($rows);
                foreach($rows as $i=>$row){
                ?>
                    <tr class="<?php if($i%2) echo 'alt';?>">
                        <td class="column-primary name-column" scope="col">
                            <strong>
                            <?php echo $row->name;?>
                            </strong>
                        </td>
                        <td class="column-primary description-column" scope="col">
                            <strong>
                            <?php echo $row->description;?>
                            </strong>
                        </td>
                        <td class="column-primary ordering-column" scope="col">
						<?php
							 if ($i != 0)
								 echo '<a class="btn btn-micro" href="admin.php?page=options&tab=shippingextprice&task=orderup&id=' . $row->id . '&number=' . $row->ordering . '"><img alt="' . _WOP_SHOP_UP . '" src="'.WOPSHOP_PLUGIN_URL.'assets/images/uparrow.png"/></a>';
						?>
						<?php
							 if ($i!=$count-1 ) echo '<a class="btn btn-micro" href="admin.php?page=options&tab=shippingextprice&task=orderdown&id=' . $row->id . '&number=' . $row->ordering . '"><img alt="' . _WOP_SHOP_DOWN . '" src="'.WOPSHOP_PLUGIN_URL.'assets/images/downarrow.png"/></a>';
						?>							
                        </td>
                        <td class="center column-primary publish-column" scope="col">
							<?php echo $published=($row->published) ? ('<a href = "admin.php?page=options&tab=shippingextprice&task=unpublish&id='.$row->id.'"><img src="'.WOPSHOP_PLUGIN_URL.'assets/images/tick.png"/></a>') : ('<a href = "admin.php?page=options&tab=shippingextprice&task=publish&id='.$row->id.'"><img src="'.WOPSHOP_PLUGIN_URL.'assets/images/publish_x.png"/></a>'); ?>
                        </td>
                        <td class="column-primary config-column" scope="col">
                            <a class="btn btn-micro" href="admin.php?page=options&tab=shippingextprice&task=edit&id=<?php print $row->id?>">
								<i class="glyphicon glyphicon-edit"></i>
							</a>
                        </td>
                        <td class="column-primary delete-column" scope="col">
                            <a class="btn btn-micro" href="admin.php?page=options&tab=shippingextprice&task=delete&id=<?php print $row->id?>">
								<i class="glyphicon wshop-icon glyphicon-remove-circle"></i>
							</a>
                        </td>
                        <td class="column-primary id-column" scope="col">
                            <strong>
                            <?php echo $row->id;?>
                            </strong>
                        </td>
                    </tr>
                <?php }?>
            </tbody>
        </table>
    </form>
    <div id="ajax-response"></div>
    <br class="clear">
</div>