<?php 
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

displaySubmenuOptions('addons');
?>
<div class="wrap">
    <h2>
        <?php echo _WOP_SHOP_ADDONS; ?>
    </h2>
    <form action="admin.php?page=options&tab=addons" method = "post" name = "adminForm">
        <?php echo $this->tmp_html_start; ?>
        
        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <?php echo $this->bulk;?>
            </div>
            <br class="clear">
        </div>
        <table class="wp-list-table widefat fixed posts">
            <thead>
                <tr>                    
                    <th class="manage-column column-cb check-column wopshop-admin-list-check" width="50">
                        <input id="cb-select-all-1" type="checkbox" />
                    </th>
                    <th align="left">
                        <?php echo _WOP_SHOP_TITLE; ?>
                    </th>
                    <th style="width: 100px; text-align: center;">
                        <?php echo _WOP_SHOP_STATUS; ?>
                    </th>
                    <th style="width: 120px; text-align: center;">
                        <?php echo _WOP_SHOP_VERSION; ?>
                    </th>
                    <?php /*<th width="120" class="center">
                        <?php echo _WOP_SHOP_DESCRIPTION; ?>
                    </th> */?>
                    <th style="width: 60px; text-align: center;">
                        <?php echo _WOP_SHOP_KEY; ?>
                    </th>
                    <th style="width: 120px; text-align: center;">
                        <?php echo _WOP_SHOP_CONFIG; ?>
                    </th>
                    <th style="width: 70px; text-align: center;">
                        <?php echo _WOP_SHOP_DELETE; ?>
                    </th>
                    <th style="width: 40px; text-align: center;">
                        <?php echo _WOP_SHOP_ID; ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($this->rows) == 0) : ?>
                    <tr class="no-items">
                        <td class="colspanchange" colspan="8"><?php echo _WOP_SHOP_QUERY_RESULT_NULL; ?></td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($this->rows as $index => $row) : ?>
                        <tr class="<?php echo ($index % 2) ? 'alt' : ''; ?>">
                            <td class="check-column wopshop-admin-list-check">
                                <input id="cid_<?php echo $row->id; ?>" type="checkbox" value="<?php echo $row->id; ?>" name="cid[]" />
                            </td>
                            <td>
                                <?php echo $row->name;?>
                            </td>
                            <td style="text-align: center;">
                                <?php if ($row->publish) : ?>
                                    <a href="admin.php?page=options&tab=addons&task=unpublish&cid[]=<?php echo $row->id; ?>" title="<?php echo _WOP_SHOP_ACTION_UNPUBLISH; ?>">
                                        <span class="glyphicon wshop-icon glyphicon-ok-sign wshop-green-icon" aria-hidden="true"></span>
                                    </a>
                                <?php else : ?>
                                    <a href="admin.php?page=options&tab=addons&task=publish&cid[]=<?php echo $row->id; ?>" title="<?php echo _WOP_SHOP_ACTION_PUBLISH; ?>">
                                        <span class="glyphicon wshop-icon glyphicon-remove-sign wshop-red-icon" aria-hidden="true"></span>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <?php echo $row->version;?>
                                <?php if ($row->version_file_exist) : ?>
                                    <a href="admin.php?page=options&tab=addons&task=version&id=<?php echo $row->id; ?>">                                        
                                        <span class="glyphicon wshop-icon glyphicon-info-sign" aria-hidden="true"></span>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <?php /*
                            <td style="text-align: center;">
                                <?php if ($row->info_file_exist) : ?>
                                    <a href="admin.php?page=options&tab=addons&task=info&id=<?php echo $row->id; ?>">
                                        <span class="glyphicon wshop-icon glyphicon-info-sign" aria-hidden="true"></span>
                                    </a>
                                <?php endif; ?>
                            </td>*/?>
                            <td style="text-align: center;">
                                <?php if ($row->usekey) : ?>
                                    <a href="admin.php?page=options&tab=licensekeyaddon&alias=<?php echo $row->alias?>&back=<?php echo $this->back; ?>">
                                        <span class="glyphicon wshop-icon glyphicon-edit" aria-hidden="true"></span>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <?php if ($row->config_file_exist) : ?>
                                    <a href="admin.php?page=options&tab=addons&task=edit&id=<?php echo $row->id?>">
                                        <span class="glyphicon wshop-icon glyphicon-edit" aria-hidden="true"></span>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <a href="admin.php?page=options&tab=addons&task=delete&id=<?php echo $row->id?>" onclick="return confirm('<?php print _WOP_SHOP_DELETE_ALL_DATA; ?>');">
                                    <span class="glyphicon wshop-icon glyphicon-remove-circle" aria-hidden="true"></span>
                                </a>
                            </td>
                            <td style="text-align: center;">
                                <?php echo $row->id;?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>   
            </tbody>
        </table>
        <?php echo $this->tmp_html_end; ?>
    </form>
</div>
<br class="clear" />