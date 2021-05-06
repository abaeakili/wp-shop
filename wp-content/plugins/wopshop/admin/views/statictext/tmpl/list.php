<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
displaySubmenuConfigs('statictext');
?>
<div class="wrap">
    <h2>
        <?php echo _WOP_SHOP_STATIC_TEXT; ?>
        <a href="admin.php?page=configuration&tab=statictext&task=edit&row=0" class="add-new-h2"><?php echo _WOP_SHOP_NEW; ?></a>
    </h2>
    <form id="listing" method="GET" action="admin.php">
        <table class="wp-list-table widefat fixed posts">
            <thead>
                <tr>
                    <?php if($this->orderby == 'alias') $class_name = 'sorted'; else $class_name = 'sortable';?>
                    <th id="order_title" class="manage-column column-order_title <?php echo $class_name; ?> <?php echo $this->order; ?>" scope="col" width="80%">
                        <a href="admin.php?page=configuration&tab=statictext&orderby=alias&order=<?php echo $this->order; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_STATIC_TEXT_PAGE; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th width="10%"></th>
                    <?php if($this->orderby == 'id') $class_name = 'sorted'; else $class_name = 'sortable';?>
                    <th id="order_id" class="manage-column column-order_id <?php echo $class_name; ?> <?php echo $this->order; ?>" scope="col" width="10%">
                        <a href="admin.php?page=configuration&tab=statictext&orderby=id&order=<?php echo $this->order; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_STATIC_TEXT_PAGE; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                </tr>
            </thead>
            <tbody id="the-list">
                <?php if(count($this->statictext) == 0){ ?>
                <tr class="no-items">
                <td class="colspanchange" colspan="3"><?php echo _WOP_SHOP_QUERY_RESULT_NULL; ?></td>
                </tr>
                <?php  
                }else{
                    foreach($this->statictext as $index=>$st){?>
                    <tr class="<?php if($index%2) echo 'alt';?>">
                        <td class="name-column" scope="col">
                            <strong>
                            <?php echo $st->alias; ?>
                            </strong>
                            <div class="row-actions">
                                <span class="edit">
                                <a class="" title="<?php echo _WOP_SHOP_EDIT; ?>" href="admin.php?page=configuration&tab=statictext&task=edit&row=<?php echo $st->id; ?>"><?php echo _WOP_SHOP_EDIT; ?></a>
                                </span>
                            </div>
                        </td>
                        <td align="center">
                            <?php if (!in_array($st->alias, $this->config->sys_static_text)){?>
                                <a href='admin.php?page=configuration&tab=statictext&task=delete&row=<?php print $st->id?>'><img alt="<?php echo _WOP_SHOP_DOWN; ?>" src="<?php echo WOPSHOP_PLUGIN_URL?>assets/images/publish_r.png"/></a>
                            <?php }?>
                        </td>
                        <td align="center">
                            <?php print $st->id; ?>
                        </td>
                    </tr>
                <?php }
                } ?>
            </tbody>
        </table>

        <input type="hidden" value="configuration" name="page">
        <input type="hidden" value="statictext" name="tab">
    </form>
    <div id="ajax-response"></div>
    <br class="clear">
</div>


