<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
    $groupname="";
?>
<table class="admintable" >
<?php foreach($this->fields as $field){ ?>
<?php if ($groupname!=$field->groupname){ $groupname=$field->groupname;?>
<tr>
    <td><b><?php print $groupname;?></b></td>
</tr>
<?php }?>
<tr>
   <td class="key">
     <div style="padding-left:10px;"><?php echo $field->name;?></div>
   </td>
   <td>
     <?php echo $field->values;?>
   </td>
</tr>
<?php }?>
</table>
