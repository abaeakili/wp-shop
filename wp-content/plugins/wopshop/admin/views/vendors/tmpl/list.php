<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
displaySubmenuOptions('vendors');
$rows = $this->rows;
$limitstart = $this->limitstart;
?>
<div class="wrap">
    <h2>
        <?php echo _WOP_SHOP_VENDOR_LIST; ?>
        <a href="admin.php?page=options&tab=vendors&task=edit" class="add-new-h2"><?php echo _WOP_SHOP_ADD; ?></a>
    </h2>
    <?php print $this->tmp_html_start?>
    <form action="admin.php?page=options&tab=vendors" method="POST">
    	<?php print $this->tmp_html_filter?>
        <?php echo $this->search; ?>
    </form>
	<form id="listing" class="adminForm" action = "admin.php?page=options&tab=vendors" method = "post" name = "adminForm">
        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <?php echo $this->bulk;?>
            </div>
            <?php echo $this->pagination;?>
            <br class="clear">
            
        </div>
		
		<table class="table table-striped" width="100%">
		<thead>
		<tr>
			 <th width="20">
				#
			 </th>
			<th width="20" id="cb" class="manage-column column-cb check-column" style="" scope="col">
				<input id="cb-select-all-1" type="checkbox">
			</th>
			 </th>
			 <th width="150" align="left">
			   <?php echo _WOP_SHOP_USER_FIRSTNAME?>
			 </th>
			 <th width="150" align="left">
			   <?php echo _WOP_SHOP_USER_LASTNAME?>
			 </th>
			 <th align="left">
			   <?php echo _WOP_SHOP_STORE_NAME?>
			 </th>
			 <th width="150">
			   <?php echo _WOP_SHOP_EMAIL?>
			 </th>
			 <th width="60" class="center">
				<?php echo _WOP_SHOP_DEFAULT;?>    
			</th>	 	      
			 <th width="50" class="center">
				<?php echo _WOP_SHOP_EDIT;?>
			</th>
			 <th width="40" class="center">
				<?php echo _WOP_SHOP_ID;?>
			</th>
		</tr>
		</thead> 
		<?php 
		$i=0; 
		foreach($rows as $row){?>
		<tr class="row<?php echo ($i%2);?>">
			 <td align="center">
				<?php echo $limitstart+$i+1;?>
			 </td>
			<td class="check-column" scope="col" align="center">
				<input id="vendor_<?php echo $row->id; ?>" type="checkbox" value="<?php echo $row->id; ?>" name="rows[]">
			</td>
			 <td>
				<?php echo $row->f_name?>
					<div class="row-actions">
						<span class="edit">
						<a class="" title="<?php echo _WOP_SHOP_EDIT; ?>" href="admin.php?page=options&tab=vendors&task=edit&id=<?php echo $row->id; ?>"><?php echo _WOP_SHOP_EDIT; ?></a>
						|
						</span>
						<span class="trash">
						<a class="submitdelete" title="<?php echo _WOP_SHOP_DELETE; ?>" href="admin.php?page=options&tab=vendors&task=delete&rows[]=<?php echo $row->id; ?>&action=-1"><?php echo _WOP_SHOP_DELETE; ?></a>
						</span>
					</div>		 
			 </td>
			 <td>
				<?php echo $row->l_name;?>
			 </td>
			 <td>
				<?php echo $row->shop_name;?>
			 </td>
			 <td>
				<?php echo $row->email;?>
			 </td>
			 <td class="center">
			 <?php if ($row->main==1) {?>
				<img src='<?php echo WOPSHOP_PLUGIN_URL; ?>assets/images/icon-16-default.png'>
			 <?php }?>
			 </td>
			 <td class="center">
				<a class="btn btn-micro" href='admin.php?page=options&tab=vendors&task=edit&id=<?php print $row->id?>'>
					<img src='<?php echo WOPSHOP_PLUGIN_URL; ?>assets/images/icon-16-edit.png'>
				</a>
			 </td>
			 <td class="center">
				<?php print $row->id?>
			 </td>
		</tr>
		<?php 
		$i++;
		}?>
		<tfoot>
		 <tr>   
			<td colspan="11">
				<div class='wopshop_pagination_block'>
					<?php //echo $this->pagination;?>
				</div>
			</td>
		 </tr>
		</tfoot>
		</table>
        <input type="hidden" value="options" name="page">
        <input type="hidden" value="vendors" name="tab">
		<?php print $this->tmp_html_end?>		
    </form>
    <br class="clear">
</div>


