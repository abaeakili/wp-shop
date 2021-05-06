<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
displaySubmenuOptions('languages');
?>
<div class="wrap">
    <h2>
        <?php echo _WOP_SHOP_LIST_LANGUAGE; ?>
    </h2>
    <?php echo $this->top_counters; ?>
    <form action="admin.php?page=options&tab=languages" method="POST">
        <?php echo $this->search; ?>
    </form>
    <form id="listing" class="adminForm" method="GET" action="admin.php">
        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <?php echo $this->bulk;?>
            </div>
            <?php echo $this->pagination;?>
            <br class="clear">
        </div>
        <table class="wp-list-table widefat fixed posts">
            <thead>
                <tr>
                    <th id="cb" class="manage-column column-cb check-column" style="" scope="col">
                        <input id="cb-select-all-1" type="checkbox">
                    </th>
                    <th id="order_title" class="manage-column column-order_title" scope="col" width="50%">
                        <?php echo _WOP_SHOP_LANGUAGE_NAME; ?>
                    </th>
                    <th class="manage-column" scope="col">
                        <?php echo _WOP_SHOP_DEFAULT_FRONT_LANG; ?>
                    </th>
                    <th class="manage-column" scope="col">
                        <?php echo _WOP_SHOP_DEFAULT_LANG_FOR_COPY; ?>
                    </th>

                    <th class="manage-column column-order_status" scope="col">
                        <?php echo _WOP_SHOP_STATUS; ?>
                    </th>
                </tr>
            </thead>
            <tbody id="the-list">
                <?php if(count($this->rows) == 0){ ?>
                <tr class="no-items">
                <td class="colspanchange" colspan="3"><?php echo _WOP_SHOP_QUERY_RESULT_NULL; ?></td>
                </tr>
                <?php 
                }else{
                    foreach($this->rows as $index=>$language){?>
                    <tr class="<?php if($index%2) echo 'alt';?>">
                        <th class="check-column" scope="col">
                            <input id="user_<?php echo $language->id; ?>" type="checkbox" value="<?php echo $language->id; ?>" name="rows[]">
                        </th>
                        <td class="name-column" scope="col">
                            <strong>
                            <?php echo $language->name; ?>
                            </strong>
                            <div class="row-actions">
                                <span class="edit"></span>
                            </div>
                        </td>
                        <td class="code-column " scope="col">
                            <?php 
                            if($language->favorite) echo '<center><img src="'.WOPSHOP_PLUGIN_URL.'assets/images/icon-16-default.png" ></center>';
                            else echo '<center><a href="admin.php?page=options&tab=languages&task=favorite_save&lang_id='.$language->id.'"><img src="'.WOPSHOP_PLUGIN_URL.'assets/images/icon-16-notdefault.png" ></a></center>';
                            ?>
                        </td>
                        <td class="code2-column " scope="col">
                            <?php
                            if($language->favorite_copy) echo '<center><img src="'.WOPSHOP_PLUGIN_URL.'assets/images/icon-16-default.png" ></center>';
                            else echo '<center><a href="admin.php?page=options&tab=languages&task=favorite_copy_save&lang_id='.$language->id.'"><img src="'.WOPSHOP_PLUGIN_URL.'assets/images/icon-16-notdefault.png" ></a></center>';
                            ?>
                        </td>
                        <td class="status-column">
                            <?php echo $published=($language->publish) ? ('<a href = "admin.php?page=options&tab=languages&task=unpublish&rows[]='.$language->id.'"><img alt="' . _WOPSHOP_DOWN . '" src="'.WOPSHOP_PLUGIN_URL.'assets/images/tick.png"/></a>') : ('<a href = "admin.php?page=options&tab=languages&task=publish&rows[]='.$language->id.'"><img alt="' . _WOPSHOP_DOWN . '" src="'.WOPSHOP_PLUGIN_URL.'assets/images/publish_x.png"/></a>'); ?>
                        </td>
                    </tr>
                <?php }
                } ?>
            </tbody>
        </table>

        <input type="hidden" value="options" name="page">
        <input type="hidden" value="languages" name="tab">
    </form>
    <div id="ajax-response"></div>
    <br class="clear">
</div>


