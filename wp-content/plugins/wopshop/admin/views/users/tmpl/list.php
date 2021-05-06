<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="wrap">
    <h2>
        <?php echo _WOP_SHOP_USER_LIST; ?>
        <a href="admin.php?page=clients&task=edit&user_id=0" class="add-new-h2"><?php echo _WOP_SHOP_NEW; ?></a>
    </h2>
	<form action="" method="POST" name="search">
        <?php echo $this->search; ?>
        <p class="search-box"><?php echo $this->lists['publish']; ?></p>
    </form>
    <?php echo $this->top_counters; ?>
    <form action="admin.php" id="listing" method="GET" name = "adminForm">
        <input type="hidden" name="page" value="clients">
        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <?php echo $this->bulk;?>
            </div>
            <?php print $this->tmp_html_filter?>
            <?php //echo $this->pagination;?>
            <br class="clear">
        </div>
        <table class="wp-list-table widefat fixed posts">
            <thead>
                <tr>
                    <th id="cb" class="manage-column column-cb check-column wopshop-admin-list-check" scope="col">
                        <input id="cb-select-all-1" type="checkbox" />
                    </th>
                    <?php if($this->orderby == 'u_name') $class_name = 'sorted'; else $class_name = 'sortable';?>
                    <th id="order_u_name" class="manage-column column-order_u_name <?php echo $class_name; ?> <?php echo $this->order; ?>" scope="col">
                        <a href="admin.php?page=clients&orderby=u_name&order=<?php echo $this->order; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_USERNAME; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <?php if($this->orderby == 'f_name') $class_name = 'sorted'; else $class_name = 'sortable';?>
                    <th id="order_f_name" class="manage-column column-order_f_name <?php echo $class_name; ?> <?php echo $this->order; ?>" scope="col">
                        <a href="admin.php?page=clients&orderby=f_name&order=<?php echo $this->order; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_NAME; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <?php if($this->orderby == 'l_name') $class_name = 'sorted'; else $class_name = 'sortable';?>
                    <th id="order_l_name" class="manage-column column-order_l_name <?php echo $class_name; ?> <?php echo $this->order; ?>" scope="col">
                        <a href="admin.php?page=clients&orderby=l_name&order=<?php echo $this->order; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_L_NAME; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <?php if($this->orderby == 'U.email') $class_name = 'sorted'; else $class_name = 'sortable';?>
                    <th id="order_Uemail" class="manage-column column-order_Uemail <?php echo $class_name; ?> <?php echo $this->order; ?>" scope="col">
                        <a href="admin.php?page=clients&orderby=U.email&order=<?php echo $this->order; ?>">
                            <span class="status_head tips"><?php echo _WOP_SHOP_EMAIL; ?></span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th class="manage-column" scope="col" width="80px">
                        <span class="status_head"><?php echo _WOP_SHOP_GROUP; ?></span>
                    </th>
                    <th class="manage-column" scope="col" width="80px">
                        <span class="status_head"><?php echo _WOP_SHOP_ORDERS; ?></span>
                    </th>
                    <th class="manage-column" scope="col" width="50px">
                        <?php echo _WOP_SHOP_ID; ?>
                    </th>                    
                </tr>
            </thead>
            <tbody id="the-list">
                <?php if(count($this->rows) == 0){ ?>
                <tr class="no-items">
                <td class="colspanchange" colspan="8"><?php echo _WOP_SHOP_QUERY_RESULT_NULL; ?></td>
                </tr>
                <?php 
                }else{
                    foreach($this->rows as $k=>$v){?>
                    <tr class="<?php if($k%2) echo 'alt';?>">
                        <td class="check-column wopshop-admin-list-check" scope="col">
                            <input id="user_<?php echo $v->user_id; ?>" type="checkbox" value="<?php echo $v->user_id; ?>" name="rows[]" />
                        </td>
                        <td class="name-column" scope="col">
                            <strong>
                            <?php echo $v->u_name?>
                            </strong>
                            <div class="row-actions">
                                <span class="edit">
                                <a class="" title="<?php echo _WOP_SHOP_EDIT; ?>" href="admin.php?page=clients&task=edit&user_id=<?php echo $v->user_id; ?>"><?php echo _WOP_SHOP_EDIT; ?></a>
                                |
                                </span>
                                <span class="trash">
                                <a class="submitdelete" title="<?php echo _WOP_SHOP_DELETE; ?>" href="admin.php?page=clients&task=delete&rows[]=<?php echo $v->user_id; ?>"><?php echo _WOP_SHOP_DELETE; ?></a>
                                </span>
                            </div>
                        </td>
                        <td class="code-column " scope="col">
                            <?php echo $v->f_name?>
                        </td>
                        <td class="code2-column " scope="col">
                            <?php echo $v->l_name?>
                        </td>
                        <td class="status-column">
                            <?php echo $v->email; ?>
                        </td>
                        <td class="status-column">
                            <?php echo $v->usergroup_name; ?>
                        </td>
                        <td class="status-column">
                            <?php echo "<a href='admin.php?page=orders&client_id=".$v->user_id."' target='_blank'>"._WOP_SHOP_ORDERS."</a>";?>
                        </td>
                        <td class="code-column " scope="col">
                            <?php echo $v->user_id; ?>
                        </td>                        
                    </tr>
                <?php }
                } ?>
            </tbody>
        </table>
    </form>
    <div id="ajax-response"></div>
    <br class="clear">
</div>