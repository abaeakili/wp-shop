<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
displaySubmenuOptions('units');

$i = 0;
?>
<div class="wrap">
    <h2>
        <?php echo _WOP_SHOP_LIST_UNITS_MEASURE; ?>
        <a href="admin.php?page=options&tab=units&task=edit" class="add-new-h2"><?php echo _WOP_SHOP_ADD; ?></a>
    </h2>
    <form id="listing" method="GET">
    <div class="tablenav top">
        <div class="alignleft actions bulkactions">
            <?php echo $this->bulk;?>
        </div>
        <br class="clear">
    </div>
    <table class="wp-list-table widefat fixed">
    <thead>
                <tr>                    
                    <th class="manage-column column-cb check-column wopshop-admin-list-check" width="50">
                        <input id="cb-select-all-1" type="checkbox" />
                    </th>
                    <th align="left">
                        <?php echo _WOP_SHOP_TITLE; ?>
                    </th>
                    <th style="width: 100px; text-align: center;">
                        <?php echo _WOP_SHOP_EDIT; ?>
                    </th>
                    <th style="width: 40px; text-align: center;">
                        <?php echo _WOP_SHOP_ID; ?>
                    </th>
                </tr>
    </thead> 
	<tbody>
		<?php if (count($this->rows) == 0) : ?>
			<tr class="no-items">
				<td class="colspanchange" colspan="4"><?php echo _WOP_SHOP_QUERY_RESULT_NULL; ?></td>
			</tr>
		<?php else : ?>
			<?php foreach ($this->rows as $index => $row) : ?>
				<tr class="<?php echo ($index % 2) ? 'alt' : ''; ?>">
					<td class="check-column wopshop-admin-list-check">
						<input id="cid_<?php echo $row->id; ?>" type="checkbox" value="<?php echo $row->id; ?>" name="cid[]" />
					</td>
					<td>
						<a href="admin.php?page=options&tab=units&task=edit&id=<?php echo $row->id?>" ><?php echo $row->name;?></a>
					</td>
					<td align="center">
						<a href="admin.php?page=options&tab=units&task=edit&id=<?php echo $row->id?>" ><img src='<?php echo WOPSHOP_PLUGIN_URL?>assets/images/icon-16-edit.png'></a>
					</td>
					<td align="center">
						<?php echo $row->id;?>
					</td>					
				</tr>
			<?php endforeach; ?>			
		<?php endif; ?> 
	</tbody>
    </table>
        <input type="hidden" value="options" name="page">
        <input type="hidden" value="units" name="tab">
	<?php echo $this->tmp_html_end; ?>	
    </form>
</div>
