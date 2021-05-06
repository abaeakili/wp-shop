<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$lists=$this->lists;
displaySubmenuConfigs('statictext');
$rows=$this->rows;
$i=0;
?>
<form action="admin.php?page=configuration&task=save" method="post" name="adminForm" id="adminForm">
<?php print $this->tmp_html_start?>
<table class="table table-striped">
<thead>
  <tr>
    <th class="title" width ="10">
      #
    </th>
    <th width="20">
      <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
    </th>
    <th align="left">
      <?php echo _WOP_SHOP_PAGE; ?>
    </th>
    <th width = "50">
        <?php echo _WOP_SHOP_USE_FOR_RETURN_POLICY;?>
    </th>
    <th width="50">
        <?php echo _WOP_SHOP_EDIT;?>
    </th>
    <th width = "50">
        <?php echo _WOP_SHOP_DELETE;?>
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
     <?php echo JHtml::_('grid.id', $i, $row->id);?>
   </td>
   <td>
    <a href='index.php?option=com_WOP_SHOPping&controller=config&task=statictextedit&id=<?php print $row->id?>'>
    <?php if (defined("_JSHP_STPAGE_".$row->alias)) print constant("_JSHP_STPAGE_".$row->alias); else print $row->alias;?>
    </a>
   </td>
   <td align="center">
     <?php
       echo $use_for_return_policy=($row->use_for_return_policy) ? ('<a href="javascript:void(0)" onclick="return listItemTask(\'cb'.$i. '\', \'unpublish\')"><img title="' . _WOP_SHOP_YES . '" alt="" src="'.WOPSHOP_PLUGIN_URL.'assets/images/tick.png"></a>') : ('<a href="javascript:void(0)" onclick="return listItemTask(\'cb' . $i . '\', \'publish\')"><img title="'._WOP_SHOP_NO.'" alt="" src="'.WOPSHOP_PLUGIN_URL.'assets/images/publish_x.png"></a>');
     ?>       
   </td>
   <td align="center">
        <a href='index.php?option=com_WOP_SHOPping&controller=config&task=statictextedit&id=<?php print $row->id?>'><img src='<?php echo WOPSHOP_PLUGIN_URL; ?>assets/images/icon-16-edit.png'></a>
   </td>
   <td align="center">
   <?php if (!in_array($row->alias, $config->sys_static_text)){?>
    <a href='index.php?option=com_WOP_SHOPping&controller=config&task=deletestatictext&id=<?php print $row->id?>' onclick="return confirm('<?php print _WOP_SHOP_DELETE?>')"><img src='<?php echo WOPSHOP_PLUGIN_URL; ?>assets/images/publish_r.png'></a>
    <?php }?>
   </td>
   <td align="center">
    <?php print $row->id;?>
   </td>
   </tr>
<?php
$i++;
}
?>
<?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>
</table>
<?php print $this->tmp_html_end?>
</form>