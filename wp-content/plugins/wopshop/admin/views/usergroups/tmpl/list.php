<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
displaySubmenuOptions('usergroups');
$rows = $this->rows;
?>
<div class="wrap">
    <h2>
        <?php echo _WOP_SHOP_PANEL_USERGROUPS; ?>
        <a href="admin.php?page=options&tab=usergroups&task=edit&row=0" class="add-new-h2"><?php echo _WOP_SHOP_NEW_USERGROUP; ?></a>
    </h2>
    <form method="POST" action="admin.php?page=options&tab=usergroups">
        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <?php echo $this->bulk;?>
            </div>
            <?php //echo $this->pagination;?>
            <br class="clear">            
        </div>
        <table class="wp-list-table widefat fixed posts">
            <thead>
                <tr>
                    <th id="cb" class="manage-column column-cb check-column" style="" scope="col">
                        <input id="cb-select-all-1" type="checkbox">
                    </th>
                    <?php if($this->filter_order == 'usergroup_name') $class_name = 'sorted'; else $class_name = 'sortable';?>
                    <th id="order_title" class="manage-column column-order_title <?php echo $class_name; ?> <?php echo $this->filter_order_Dir; ?>" scope="col" width="30%">
                        <a href="admin.php?page=options&tab=usergroups&filter_order=usergroup_name&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_NAME; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th class="manage-column" scope="col" width="30%">
                        <?php echo _WOP_SHOP_DESCRIPTION; ?>
                    </th>
                    <th class="manage-column" scope="col">
                        <?php echo _WOP_SHOP_DISCOUNT; ?>
                    </th>
                    <th class="manage-column" scope="col">
                        <?php echo _WOP_SHOP_USERGROUP_IS_DEFAULT_DESCRIPTION; ?>
                    </th>
                    <?php if($this->filter_order == 'usergroup_id') $class_publish = 'sorted'; else $class_publish = 'sortable';?>
                    <th class="manage-column column-order_status <?php echo $class_publish; ?> <?php echo $this->filter_order_Dir; ?>" scope="col">
                        <a href="admin.php?page=options&tab=usergroups&filter_order=usergroup_id&filter_order_Dir=<?php echo $this->filter_order_Dir; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_ID; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                </tr>
            </thead>
            <tbody id="the-list">
                <?php if(count($rows) == 0){ ?>
                <tr class="no-items">
                <td class="colspanchange" colspan="3"><?php echo _WOP_SHOP_QUERY_RESULT_NULL; ?></td>
                </tr>
                <?php 
                }else{
                    foreach($rows as $index=>$data){?>
                    <tr class="<?php if($index%2) echo 'alt';?>">
                        <th class="check-column" scope="col">
                            <label class="screen-reader-text" for="cb-select-<?php echo $data->usergroup_id; ?>"><?php echo _WOP_SHOP_ACTION_CHECK; ?></label>
                            <input id="user_<?php echo $data->usergroup_id; ?>" type="checkbox" value="<?php echo $data->usergroup_id; ?>" name="rows[]">
                        </th>
                        <td class="name-column" scope="col">
                            <strong>
                            <?php echo $data->usergroup_name; ?>
                            </strong>
                            <div class="row-actions">
                                <span class="edit">
                                <a class="" title="<?php echo _WOP_SHOP_EDIT; ?>" href="admin.php?page=options&tab=usergroups&task=edit&row=<?php echo $data->usergroup_id; ?>"><?php echo _WOP_SHOP_EDIT; ?></a>
                                |
                                </span>
                                <span class="trash">
                                <a class="submitdelete" title="<?php echo _WOP_SHOP_DELETE; ?>" href="admin.php?page=options&tab=usergroups&task=delete&rows[]=<?php echo $data->usergroup_id; ?>&action=-1"><?php echo _WOP_SHOP_DELETE; ?></a>
                                </span>
                            </div>
                        </td>
                        <td class="code-column " scope="col">
                            <?php echo $data->usergroup_description; ?>
                        </td>
                        <td class="code-column " scope="col">
                            <?php echo $data->usergroup_discount; ?> %
                        </td>
                        <td class="code-column " scope="col">
                            <?php
                            if($data->usergroup_is_default) echo '<center><img src="'.WOPSHOP_PLUGIN_URL.'assets/images/icon-16-default.png" ></center>';
                            else echo '<center><img src="'.WOPSHOP_PLUGIN_URL.'assets/images/icon-16-notdefault.png" ></center>';
                            ?>
                        </td>                        
                        <td class="status-column">
                            <?php echo $data->usergroup_id; ?>
                        </td>
                    </tr>
                <?php }
                } ?>
            </tbody>
        </table>

        <input type="hidden" value="options" name="page">
        <input type="hidden" value="usergroups" name="tab">
    </form>
    <div id="ajax-response"></div>
    <br class="clear">
</div>


