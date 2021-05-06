<?php
class WshopAdminModel{
    
    public $lang;
    protected $tableFieldPublish = 'publish';
    protected $tablename;
    
    public function __construct() {
        $this->lang = Factory::getConfig()->getLang();
    }

    function search($search){
        return '<p class="search-box">
            <input id="plugin-search-input" type="search" value="'.$search.'" name="s">
            <input id="search-submit" class="button" type="submit" value="'._WOP_SHOP_SEARCH.'" name="search-submit">
        </p>';
    }
    
    function countersList($publish, $page, $tab, $count_all, $count_publish, $count_unpublish){
        if($publish == '') $class_all = 'class="current"'; else $class_all = '';
        if($publish == '1') $class_publish = 'class="current"'; else $class_publish = '';
        if($publish == '0') $class_unpublish = 'class="current"'; else $class_unpublish = '';
        $html = 
        '<ul class="subsubsub">
            <li class="all">
                <a '.$class_all.' href="admin.php?page='.$page.'&tab='.$tab.'">'._WOP_SHOP_QUERY_ALL.' <span class="count">('.$count_all.')</span></a>
                |
            </li>
            <li class="active">
                <a '.$class_publish.' href="admin.php?page='.$page.'&tab='.$tab.'&publish=1">'._WOP_SHOP_QUERY_PUBLISH.' <span class="count">('.$count_publish.')</span></a>
                |
            </li>
            <li class="inactive">
                <a '.$class_unpublish.' href="admin.php?page='.$page.'&tab='.$tab.'&publish=0">'._WOP_SHOP_QUERY_UNPUBLISH.' <span class="count">('.$count_unpublish.')</span></a>
            </li>
        </ul>';
        return $html;
    }

    function getListLanguages(){
        global $wpdb;
        $name_table = $wpdb->prefix.'wshop_languages';
        return $listLanguages = $wpdb->get_results( "SELECT * FROM ".$name_table." WHERE `publish` > 0", ARRAY_A);
    } 
    
    public function getBulkActions($actions, $which = 'top' ) {
        $two = '2';
        if ( empty( $actions ) )
                return;
        $output =  "<label for='bulk-action-selector-" . esc_attr( $which ) . "' class='screen-reader-text'>" . __( 'Select bulk action' ) . "</label>";
        $output .= "<select name='action$two' id='bulk-action-selector-" . esc_attr( $which ) . "'>\n";
        $output .= "<option value='-1' selected='selected'>" . _WOP_SHOP_ACTION_ACTIONS . "</option>\n";
        foreach ( $actions as $name => $title ) {
                $class = 'edit' == $name ? ' class="hide-if-no-js"' : '';

                $output .= "\t<option value='$name'$class>$title</option>\n";
        }
        $output .= "</select>\n";
        $output .= get_submit_button( _WOP_SHOP_ACTION_APPLY, 'action', false, false, array( 'id' => "doaction$two" ) );
        return $output."\n";
    }     
    
    public static function getPagination($total_items, $per_page = 20, $which = 'top' ) {
        $total_pages = ceil($total_items / $per_page);
        $class[$per_page] = 'selected="selected"';
        $select = '<select name="per_page" onchange="document.getElementById(\'listing\').submit();"><option value="5" '.$class['5'].'>5</option><option value="10" '.$class['10'].'>10</option><option value="20" '.$class['20'].'>20</option><option value="50" '.$class['50'].'>50</option><option value="100" '.$class['100'].'>100</option></select>';

        $infinite_scroll = false;
//            if ( isset( $this->_pagination_args['infinite_scroll'] ) ) {
//                    $infinite_scroll = $this->_pagination_args['infinite_scroll'];
//            }

        $output = '<span class="displaying-num">' . sprintf( _n( '1 item', '%s items', $total_items ), number_format_i18n( $total_items ) ) . '</span>';

        $current = self::get_pagenum();

        $current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );

        $current_url = remove_query_arg( array( 'hotkeys_highlight_last', 'hotkeys_highlight_first' ), $current_url );

        $page_links = array();

        $disable_first = $disable_last = '';
        if ( $current == 1 ) {
                $disable_first = ' disabled';
        }
        if ( $current == $total_pages ) {
                $disable_last = ' disabled';
        }
        $page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
                'first-page' . $disable_first,
                esc_attr__( 'Go to the first page' ),
                //esc_url( remove_query_arg( 'paged', $current_url ) ),
                esc_url( add_query_arg( 'paged', 1, $current_url ) ),
                '&laquo;'
        );

        $page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
                'prev-page' . $disable_first,
                esc_attr__( 'Go to the previous page' ),
                esc_url( add_query_arg( 'paged', max( 1, $current-1 ), $current_url ) ),
                '&lsaquo;'
        );

        if ( 'bottom' == $which ) {
                $html_current_page = $current;
        } else {
                $html_current_page = sprintf( "%s<input class='current-page' id='current-page-selector' title='%s' type='text' name='paged' value='%s' size='%d' />",
                        '<label for="current-page-selector" class="screen-reader-text">' . __( 'Select Page' ) . '</label>',
                        esc_attr__( 'Current page' ),
                        $current,
                        strlen( $total_pages )
                );
        }
        $html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
        $page_links[] = '<span class="paging-input">' . sprintf( _x( '%1$s of %2$s', 'paging' ), $html_current_page, $html_total_pages ) . '</span>';

        $page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
                'next-page' . $disable_last,
                esc_attr__( 'Go to the next page' ),
                esc_url( add_query_arg( 'paged', min( $total_pages, $current+1 ), $current_url ) ),
                '&rsaquo;'
        );

        $page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
                'last-page' . $disable_last,
                esc_attr__( 'Go to the last page' ),
                esc_url( add_query_arg( 'paged', $total_pages, $current_url ) ),
                '&raquo;'
        );

        $pagination_links_class = 'pagination-links';
        if ( ! empty( $infinite_scroll ) ) {
                $pagination_links_class = ' hide-if-js';
        }
        $links = $total_pages < 2 ? "\n<span class='$pagination_links_class'>" .$select . '</span>' : "\n<span class='$pagination_links_class'>" . join( "\n", $page_links ) .$select . '</span>';
        $output .= $links;

        if ( $total_pages ) {
                $page_class = $total_pages < 1 ? ' one-page' : '';
        } else {
                $page_class = ' no-pages';
        }
        return "<div class='tablenav-pages{$page_class}'>$output</div>";
    } 
    
    public function get_pagenum() {
        $pagenum = isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 0;
        return max( 1, $pagenum );
    }
    
    public function publish(array $cid, $flag){
        $field = $this->tableFieldPublish;
        
        foreach($cid as $id){
            $table = Factory::getTable($this->getTableName());
            $table->load($id);
            $table->$field = $flag;
            $table->store();
		}
    }
    
    protected function getTableName(){
		if (empty($this->tablename)){
			$r = null;
            preg_match('/(.*)WshopAdminModel/i', get_class($this), $r);
            $this->tablename = strtolower($r[1]);
		}
        
		return $this->tablename;
	}
    
    protected function setTableName($tableName){
        $this->tablename = $tableName;
    }
}