<?php
class ProductFieldGroupsWshopAdminController extends WshopAdminController {
    
    function __construct(  ) {
        
        parent::__construct(  );
        
    }
   
    function display( $cachable = false, $urlparams = false ) {
        
        $config  = Factory::getConfig(  );
        $model   = $this->getModel( "productfieldgroups" );
        $rows    = $model->getList(  );
        $actions = array( 'delete' => _WOP_SHOP_DELETE );
        $bulk    = $model->getBulkActions( $actions );
        $view    = $this->getView( "productfieldgroups" );
        
        $view->setLayout( "list" );
        $view->assign( 'rows', $rows );
        $view->assign( 'bulk', $bulk );
        
        do_action_ref_array( 'onBeforeDisplayProductsFieldGroups', array( &$view ) );
        
        $view->display(  );
        
    }
    
    function edit(  ) {
        
        global $wpdb;
        
        $id        = (int)$_REQUEST['id'];
        $query     = "SELECT * FROM `{$wpdb->prefix}wshop_products_extra_field_groups` WHERE `id` = '" . esc_sql( $id ) . "' ";
        $row       = $wpdb->get_row( $query );
        $_lang     = $this->getModel( 'languages' );
        $languages = $_lang->getAllLanguages( 1 );
        $multilang = count( $languages ) > 1;
        $view      = $this->getView( 'productfieldgroups' );
        
        $view->setLayout( 'edit' );
        $view->assign( 'row', $row );
        $view->assign( 'languages', $languages );
        $view->assign( 'multilang', $multilang );
        
        do_action_ref_array( 'onBeforeEditProductFieldGroups', array( &$view ) );
        
        $view->display(  );
        
    }

    function save(  ) {
        
        $id   = Request::getInt( 'id' );
        $post = Request::get( 'post' );
        $row  = Factory::getTable( 'productFieldGroup' );
        
        do_action_ref_array( 'onBeforeSaveProductFieldGroup', array( &$post ) );
        $row->bind( $post );
        
        if( !$post['id'] ) {
            
            $row->ordering = null;
            $row->ordering = $row->getNextOrder(  );
            
        }
        
        if( !$row->store(  ) ) {
            
            addMessage( _WOP_SHOP_ERROR_SAVE_DATABASE, 'error' );
            return 0;
            
        }
        
        do_action_ref_array( 'onAfterSaveProductFieldGroup', array( &$row ) );
        $this->setRedirect( 'admin.php?page=options&tab=productfieldgroups' );	
        
    }

    function delete(  ) {
        
        $id_list  = Request::getVar( 'rows' );
        $obj = Factory::getTable( 'productFieldGroup' );
        
        foreach( $id_list as $id ) {
            
            $obj->delete( $id );
            
        }
        
        do_action_ref_array( 'onAfterRemoveProductFieldGroup', array( &$id_list ) ); 
        $this->setRedirect( 'admin.php?page=options&tab=productfieldgroups' );
        
        return 1;
        
    }
    
    function orderup(  ) {
        
        $fieldgroups = Factory::getAdminModel( 'productfieldgroups' );
        $fieldgroups->reorder(  );
        $this->setRedirect( 'admin.php?page=options&tab=productfieldgroups' );
        
    }

    function orderdown(  ) {
        
        $fieldgroups = Factory::getAdminModel( 'productfieldgroups' );
        $fieldgroups->reorder(  );
        $this->setRedirect( 'admin.php?page=options&tab=productfieldgroups' );
        
    }
    
    function saveorder(  ) {
        
        $cid   = Request::getVar( 'rows' );
        $order = Request::getVar( 'order', array(), 'post', 'array' );                
        
        foreach( $cid as $k => $id ) {
            
            $table = Factory::getTable( 'ProductFieldGroups' );
            $table->load( $id );
            
            if( $table->ordering != $order[$k] ) {
                
                $table->ordering = $order[$k];
                $table->store(  );
                
            }
        }
        
        $this->setRedirect("admin.php?page=options&tab=productfieldgroups");
        
    }
   
}