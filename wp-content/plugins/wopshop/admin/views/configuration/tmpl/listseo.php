<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$lists=$this->lists;
displaySubmenuConfigs('seo');
$rows=$this->rows;
$i=0;
?>
<form action="admin.php?page=configuration&task=save" method="post" name="adminForm" id="adminForm">
<?php print $this->tmp_html_start?>
<?php wp_nonce_field('config','config_nonce_field'); ?>
<table class="wp-list-table widefat">
<thead>
  <tr>
    <th class="title" width ="30">
      #
    </th>
    <th align="left" width="35%">
      <?php echo _WOP_SHOP_PAGE; ?>
    </th>
    <th align="left">
      <?php echo _WOP_SHOP_TITLE; ?>
    </th>    
    <th width="50">
        <?php echo _WOP_SHOP_EDIT;?>
    </th>
    <th width="40">
        <?php echo _WOP_SHOP_ID;?>
    </th>
  </tr>
</thead>  
<?php foreach($rows as $row){?>
  <tr class="row<?php echo $i % 2;?>">
   <td>
     <?php echo $i+1;?>
   </td>
   <td>
    <a href='admin.php?page=configuration&task=seoedit&id=<?php print $row->id?>'>
    <?php if (defined("_JSHP_SEOPAGE_".$row->alias)) print constant("_JSHP_SEOPAGE_".$row->alias); else print $row->alias;?>
    </a>
   </td>
   <td>
    <?php print $row->title;?>
   </td>   
   <td align="center">
        <a href='admin.php?page=configuration&task=seoedit&id=<?php print $row->id?>'><img src='<?php echo WOPSHOP_PLUGIN_URL;?>assets/images/icon-16-edit.png'></a>
   </td>
   <td align="center">
    <?php print $row->id;?>
   </td>
   </tr>
<?php
$i++;
}
?>
</table>
<?php print $this->tmp_html_end?>
</form>