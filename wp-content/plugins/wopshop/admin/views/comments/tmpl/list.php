<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
displaySubmenuOptions('reviews');
$count = count($this->reviews);
$rows = $this->reviews;
$i = 0;
?>
<div class="wrap">
    <h2>
        <?php echo _WOP_SHOP_REVIEWS; ?>
        <a href="admin.php?page=options&tab=reviews&task=edit&row=0" class="add-new-h2"><?php echo _WOP_SHOP_NEW; ?></a>
    </h2>
    <form  action="" method="POST" name="search">
        <?php print $this->tmp_html_filter?>
        <?php echo $this->search; ?>
    </form>
    <?php 
        echo $this->top_counters;
    ?>
    <form id="listing" class="adminForm" action="admin.php" id="listing" method="GET">
        <input type="hidden" name="page" value="options">
        <input type="hidden" name="tab" value="reviews">
        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <?php 
                echo $this->bulk;
                ?>
            </div>
            <?php 
                echo $this->pagination;
            ?>
            <br class="clear">
        </div>
        <table class="wp-list-table widefat fixed posts">
            <thead>
                 <tr>
                    <th class="title" width ="20">
                      #
                    </th>
                    <th class="check-column" scope="col">
                        <span style='display:block'>
                            <input id="cb-select-all-1" type="checkbox">
                        </span>
                    </th>
                    <th width = "100" align = "left">
                        <?php echo _WOP_SHOP_NAME_PRODUCT; ?>
                    </th>
                    <th>
                        <?php echo _WOP_SHOP_USER; ?>
                    </th>        
                    <th>
                        <?php echo _WOP_SHOP_EMAIL; ?>
                    </th>
                    <th align = "left">
                        <?php echo _WOP_SHOP_PRODUCT_REVIEW; ?>
                    </th>
                    <th>
                        <?php echo _WOP_SHOP_REVIEW_MARK; ?>
                    </th> 
                    <th>
                        <?php echo _WOP_SHOP_DATE; ?> 
                    </th>
                    <th>
                        <?php echo IP; ?>
                    </th>
                    <th width="50" class="center">
                        <?php echo _WOP_SHOP_PUBLISH;?>       
                    </th>
                    <th width="50" class="center">
                        <?php echo _WOP_SHOP_EDIT; ?>
                    </th>
                    <th width="50" class="center">
                        <?php echo _WOP_SHOP_DELETE; ?>
                    </th>
                    <th width="40" class="center">
                        <?php echo _WOP_SHOP_ID; ?>
                    </th>
                </tr>
            </thead>
            <tbody id="the-list">
                <?php if(count($rows) == 0){ ?>
                <tr class="no-items">
                    <td class="colspanchange" colspan="3"><?php echo _WOP_SHOP_QUERY_RESULT_NULL; ?></td>
                </tr>
                <?php
                }else
                foreach ($rows as $row){$j = $i+1;?>
                <tr class="row<?php echo $i % 2;?>">
                   <td>
                     <?php echo $j;?>
                   </td>
                   <td class="check-column">
                     <input id="cid_<?php echo $row->review_id; ?>" type="checkbox" value="<?php echo $row->review_id; ?>" name="rows[]">
                   </td>
                   <td>
                     <?php echo $row->name;?>
                   </td>
                   <td>
                     <?php echo $row->user_name;?>
                   </td> 
                   <td>
                     <?php echo $row->user_email;?>
                   </td>     
                   <td>
                     <?php echo $row->review;?>
                   </td> 
                   <td>
                     <?php echo $row->mark;?>
                   </td> 
                   <td>
                     <?php echo $row->dateadd;?>
                   </td>
                   <td>
                     <?php echo $row->ip;?>
                   </td>
                   <td class="center">
                       <?php echo $published=($row->publish) ? ('<a href = "admin.php?page=options&tab=reviews&task=unpublish&rows[]='.$row->review_id.'"><img alt="' . _WOP_SHOP_DOWN . '" src="'.WOPSHOP_PLUGIN_URL.'assets/images/tick.png"/></a>') : ('<a href = "admin.php?page=options&tab=reviews&task=publish&rows[]='.$row->review_id.'"><img alt="' . _WOP_SHOP_DOWN . '" src="'.WOPSHOP_PLUGIN_URL.'assets/images/publish_x.png"/></a>'); ?>
                   </td> 
                   <td class="center">
                    <a href='admin.php?page=options&tab=reviews&task=edit&cid[]=<?php print $row->review_id?>'>
                        <img src='<?php echo WOPSHOP_PLUGIN_URL; ?>assets/images/icon-16-edit.png'>
                    </a>
                   </td>
                   <td class="center">
                    <a href='admin.php?page=options&tab=reviews&task=remove&cid[]=<?php print $row->review_id?>' onclick="return confirm('<?php print _WOP_SHOP_DELETE?>')">
                        <img src='<?php echo WOPSHOP_PLUGIN_URL; ?>assets/images/remove.png'>
                    </a>
                   </td>
                   <td class="center">
                    <?php print $row->review_id;?>
                   </td>
                </tr>
                <?php
                $i++;
                }
                ?>
            </tbody>
        </table>
        <input type="hidden" name="filter_order" value="<?php echo $this->filter_order?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_order_Dir?>" />      
    </form>
    <div id="ajax-response"></div>
    <br class="clear">
</div>