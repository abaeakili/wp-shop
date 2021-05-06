<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="wrap">
    <form action="admin.php?page=options&tab=importexport&task=save" method="post" name="adminForm" id="adminForm">
    <input type="hidden" name="ie_id" value="<?php print $ie_id;?>" />
    <div class="buttons">
        <input type="submit" class="button-primary" value="<?php echo _WOP_SHOP_EXPORT." '".$name."'";?>">
        <a href="admin.php?page=options&tab=importexport" class="button-secondary"><?php echo _WOP_SHOP_BACK_TO.' "'._WOP_SHOP_PANEL_IMPORT_EXPORT.'"'; ?></a>
    </div>
    <br/>
    <?php print _WOP_SHOP_FILE_NAME?>: <input type="text" name="params[filename]" value="<?php print $ie_params['filename']?>" size="45"><br/>
    <br/>
    <?php if($count) {?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th class="title" width ="10">
                      #
                    </th>
                    <th class="center" width="40%">
                        <?php echo _WOP_SHOP_NAME; ?>
                    </th>
                    <th class="center">
                        <?php echo _WOP_SHOP_DATE; ?>
                    </th>
                    <th class="center">
                        <?php echo _WOP_SHOP_DELETE; ?>
                    </th>                  
                </tr>
            </thead>
            <tbody id="the-list">
            <?php
            $i=0;
            foreach($files as $row){
            ?>
            <tr class="row<?php echo $i % 2;?>">
                <td>
                    <?php echo $i+1;?>
                </td>    
                <td>
                    <a target="_blank" href="<?php print $config->importexport_live_path.$_importexport->alias."/".$row; ?>"><?php echo $row;?></a>
                </td>
                <td>
                    <?php print date("d.m.Y H:i:s", filemtime($config->importexport_path.$_importexport->alias."/".$row)); ?>
                </td>    
                <td class="center">
                    <a href='admin.php?page=options&tab=importexport&task=filedelete&ie_id=<?php print $ie_id;?>&file=<?php print $row?>' onclick="return confirm('<?php print _WOP_SHOP_DELETE?>');">
                        <img src="<?php echo WOPSHOP_PLUGIN_URL?>assets/images/trash.png">
                    </a>
                </td>
            </tr>
            <?php
            $i++;  
            }
            ?>
            </tbody>
        </table>    
    <?php }?>

    </form>
</div>    