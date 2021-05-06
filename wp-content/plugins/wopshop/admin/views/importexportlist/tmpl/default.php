<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
displaySubmenuOptions('importexport');

$rows=$this->rows;
$i=0;
?>
<div class="wrap">
    <h2>
        <?php echo _WOP_SHOP_PANEL_IMPORT_EXPORT; ?>
    </h2>
    <form id="listing" method="GET" action="admin.php">
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
                    <th class="title" width ="10">
                      #
                    </th>
                    <th class="center" width="25%">
                        <?php echo _WOP_SHOP_TITLE; ?>
                    </th>
                    <th class="center">
                        <?php echo _WOP_SHOP_DESCRIPTION; ?>
                    </th>
                    <th class="center" width="10%">
                        <?php echo _WOP_SHOP_AUTOMATIC_EXECUTION; ?>
                    </th>
                    <th class="center" width="10%">
                        <?php echo _WOP_SHOP_DELETE; ?>
                    </th>
                    <th class="center" width="10%">
                        <?php echo _WOP_SHOP_ID; ?>
                    </th>                    
                </tr>
            </thead>
            <tbody id="the-list">
                <?php if(count($rows) == 0){ ?>
                <tr class="no-items">
                <td class="colspanchange" colspan="6"><?php echo _WOP_SHOP_QUERY_RESULT_NULL; ?></td>
                </tr>
                <?php 
                }else{
                    foreach($rows as $row){?>
                    <tr>
                        <td>
                            <?php echo $i+1;?>
                        </td>

                        <td class="name-column" scope="col">
                            <strong>
                                <a href="admin.php?page=options&tab=importexport&task=view&ie_id=<?php echo $row->id; ?>"><?php echo $row->name; ?></a>
                            </strong>
                        </td>

                        <td>
                            <?php echo $row->description;?>
                        </td>
                        <td class="center">
                            <a href='admin.php?page=options&tab=importexport&task=setautomaticexecution&cid=<?php print $row->id?>'>
                                <?php if ($row->steptime>0){?>
                                    <img src="<?php echo WOPSHOP_PLUGIN_URL?>assets/images/publish_x.png">
                                <?php }else{ ?>
                                    <img src="<?php echo WOPSHOP_PLUGIN_URL?>assets/images/tick.png">
                                <?php }?>
                            </a>
                        </td> 
                        <td class="center">
                            <a href="admin.php?page=options&tab=importexport&task=remove&cid=<?php echo $row->id; ?>"><img src="<?php echo WOPSHOP_PLUGIN_URL?>assets/images/trash.png"></a>
                        </td>                         
                        <td class="center">
                            <?php print $row->id;?>
                        </td>    
                    </tr>
                    <?php $i++; ?>
                <?php }
                } ?>
            </tbody>
        </table>

        <input type="hidden" value="options" name="page">
        <input type="hidden" value="importexport" name="tab">
    </form>
    <div id="ajax-response"></div>
    <br class="clear">
</div>