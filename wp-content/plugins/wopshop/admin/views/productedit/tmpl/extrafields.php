<?php
if ( ! defined( 'ABSPATH' ) ) {
 exit; // Exit if accessed directly
}
$groupname = "";
?>
<div id="tabExtraFields" class="tab">

    <div id="product_extra_fields" class="tab-pane">
        <div class="col100" id="extra_fields_space">
            <?php print $this->tmpl_extra_fields;?>
        </div>
        <div class="clr"></div>
    </div>
    
    <?php /*<table class="admintable" >
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
</table>    */?>
</div>
