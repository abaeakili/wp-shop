<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class ClientsWshopAdminController extends WshopAdminController {
    function __construct() {
        parent::__construct();
    }
    function display(){
        $context = "list.admin.clients";
        $filter_order_Dir = getStateFromRequest($context.'filter_order_Dir', 'order', 'asc');
        $filter_order = getStateFromRequest($context.'filter_order', 'orderby', 'u_name');
        $text_search = getStateFromRequest($context.'text_search', 's', '');
        $per_page = getStateFromRequest($context.'per_page', 'per_page', 20);
        $paged = getStateFromRequest($context.'paged', 'paged', 1);

        $start = ($paged-1)*$per_page;

        $model = $this->getModel('products');
        $search = $model->search($text_search);

        $users = $this->getModel("users");

        $total = $users->getCountAllUsers($text_search);

        $actions = array(
            'delete' => _WOP_SHOP_DELETE
            //,
            //'publish' => _WOP_SHOP_ACTION_PUBLISH,
            //'unpublish' => _WOP_SHOP_ACTION_UNPUBLISH,
        );
        $bulk = $model->getBulkActions($actions);

        $rows = $users->getAllUsers($start, $per_page, $text_search, $filter_order, $filter_order_Dir);

        //$pagination = $model->getPagination($count_products, $per_page);

        if($filter_order_Dir == 'asc') $filter_order_Dir = 'desc'; else $filter_order_Dir = 'asc';

        $view=$this->getView("users");
        $view->setLayout("list");
        $view->assign('rows', $rows);
        $view->assign('pageNav', $pageNav);
        $view->assign('text_search', $text_search);
        $view->assign('orderby', $filter_order);
        $view->assign('order', $filter_order_Dir);
        $view->assign('search', $search);
        $view->assign('bulk', $bulk);
		do_action_ref_array('onBeforeDisplayUsers', array(&$view));
        $view->display();
    }
    
    function edit(){
        //global $wpdb;
        $config = Factory::getConfig();

        $me =  Factory::getUser();
        $user_id = Request::getInt("user_id");
        $user = Factory::getTable('usershop');
        $user->load($user_id);

        //$user_site = new User($user_id);

        $_countries = $this->getModel("countries");
        $countries = $_countries->getAllCountries(0);
        $lists['country'] = HTML::_('select.genericlist', $countries,'country','class = "inputbox" size = "1"','country_id','name', $user->country);
        $lists['d_country'] = HTML::_('select.genericlist', $countries,'d_country','class = "inputbox endes" size = "1"','country_id','name', $user->d_country); 
        $user->birthday = getDisplayDate($user->birthday, $config->field_birthday_format);
        $user->d_birthday = getDisplayDate($user->d_birthday, $config->field_birthday_format);
        $option_title = array();

        foreach($config->user_field_title as $key => $value){
            $option_title[] = HTML::_('select.option', $key, $value, 'title_id', 'title_name' );
        }
        $lists['select_titles'] = HTML::_('select.genericlist', $option_title,'title','class = "inputbox"','title_id','title_name', $user->title );
        $lists['select_d_titles'] = HTML::_('select.genericlist', $option_title,'d_title','class = "inputbox endes"','title_id','title_name', $user->d_title );

        $client_types = array();
        foreach ($config->user_field_client_type as $key => $value) {
            $client_types[] = HTML::_('select.option', $key, $value, 'id', 'name' );
        }
        $lists['select_client_types'] = HTML::_('select.genericlist', $client_types,'client_type','class = "inputbox" ','id','name', $user->client_type);

        $_usergroups = $this->getModel("userGroups");
        $usergroups = $_usergroups->getAllUsergroups();
        $lists['usergroups'] = HTML::_('select.genericlist', $usergroups, 'usergroup_id', 'class = "inputbox" size = "1"', 'usergroup_id', 'usergroup_name', $user->usergroup_id);
        //$lists['block'] = HTML::_('select.booleanlist',  'block', 'class="inputbox" size="1"', $user_site->get('block') );

        //filterHTMLSafe($user, ENT_QUOTES);

        $tmp_fields = $config->getListFieldsRegister();
        $config_fields = $tmp_fields['editaccount'];
        $count_filed_delivery = $config->getEnableDeliveryFiledRegistration('editaccount');

        $view=$this->getView("users", 'html');
        $view->setLayout("edit");
	$view->assign('config', $config);
        $view->assign('user', $user);  
        $view->assign('me', $me);       
        $view->assign('user_site', $user_site);
        $view->assign('lists', $lists);
        $view->assign('config_fields', $config_fields);
        $view->assign('count_filed_delivery', $count_filed_delivery);
		do_action_ref_array('onBeforeEditUsers', array(&$view));
        $view->display();
    }

    function save(){
        if ( !empty($_POST) && check_admin_referer('client_edit','name_of_nonce_field') )
        {
            $config = Factory::getConfig();
            global $wpdb;

            //$user_id = (int)$_REQUEST['user_id'];
            $user_id = Request::getInt('user_id');
            //$cid = Request::getVar("cid");
            $post = $_POST;
			do_action_ref_array( 'onBeforeSaveUser', array(&$post) );
            if($user_id > 0){
                if($post['password'] != '' || $post['password2'] != ''){
                    if($post['password'] != $post['password2']){
                        $this->setRedirect("admin.php?page=clients&task=edit&user_id=".$user_id, 'Error verify pass', 'error');
                    }
                }
            }else{
                if($post['password'] == ''){
                    $this->setRedirect("admin.php?page=clients&task=edit&user_id=".$user_id, 'Error empty pass');
                }
                if($post['password'] != $post['password2']){
                    $this->setRedirect("admin.php?page=clients&task=edit&user_id=".$user_id, 'Error verify pass');
                }
                if(!sanitize_email($post['email'])){
                    $this->setRedirect("admin.php?page=clients&task=edit&user_id=".$user_id, 'Error email');
                }
            }

            $userdata = array(
                'ID' => $user_id
                ,'user_login' =>  $post['u_name']
                ,'user_email' =>  $post['email']
            );
            if($post['password'] != ''){
                $userdata['user_pass'] = wp_hash_password($post['password']);
            }
            if($user_id == 0){
                $userdata['user_registered'] = date('Y-m-d H:i:s');
            }

            $ress = wp_insert_user( $userdata );
            if(is_array($ress->errors)){
                $error = '';
                foreach($ress->errors as $index=>$err){
                    $error.= implode('</p><p>',$err);
                }
                $this->setRedirect("admin.php?page=clients&task=edit&user_id=".$user_id, $error);
                return;
            }else{
                $usr_id = $ress;
            }

            $fields = $wpdb->get_results("SHOW FIELDS FROM ".$wpdb->prefix."wshop_users");
            $obj = array();

            foreach($fields as $index=>$field){
                $filed_name = $field->Field;
                if(isset($post[$filed_name])) $obj[$filed_name] = $post[$filed_name]; else $obj[$filed_name] = null;
            }
            $obj['user_id'] = $usr_id;
            if($user_id > 0){
                echo "1";
                $wpdb->update( $wpdb->prefix."wshop_users", $obj, array('user_id'=>$usr_id));
                $wpdb->show_errors();                $wpdb->hide_errors();                $wpdb->print_error();
            }else{
                echo "2";
                $wpdb->insert($wpdb->prefix."wshop_users", $obj);
                $wpdb->show_errors();                $wpdb->hide_errors();                $wpdb->print_error();
            }
			do_action_ref_array( 'onAfterSaveUser', array(&$obj) );
            $this->setRedirect("admin.php?page=clients");
        }
    }
    
    function delete(){
        $cid = Request::getVar('rows');

		do_action_ref_array( 'onBeforeRemoveUser', array(&$cid) );
		foreach ($cid as $id){
			wp_delete_user($id);

			$user_shop = Factory::getTable('usershop');
			$user_shop->delete((int)$id); 
		}
		do_action_ref_array( 'onAfterRemoveUser', array(&$cid) );
        $this->setRedirect("admin.php?page=clients");
    }

	function get_userinfo() {
		global $wpdb;
		$id = Request::getInt('user_id');
		if(!$id){
			print '{}';
			die;
		}
		$query = "SELECT * FROM `".$wpdb->prefix."wshop_users` WHERE `user_id` = ".esc_sql($id);
        $user = $wpdb->get_row($query, ARRAY_A);
		echo json_encode((array)$user);
		die();
	}	
    
    
}