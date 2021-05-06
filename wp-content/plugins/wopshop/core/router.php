<?php
/**
* @version      1.0.0 01.06.2016
* @author       MAXXmarketing GmbH
* @package      WOPshop
* @copyright    Copyright (C) 2010 http://www.wop-agentur.com. All rights reserved.
* @license      GNU/GPL
*/

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class WshopRouter extends Object {

    const POST_TYPE = 'shop';
    
    private static $instance;
    
    private $contents = '';
    
    private $message = '';
    
    private $shopPostId = 0;
    
    private $shopBasePageSlug;
    
    /**
    * Url of the main shop page
    *
    * @var string
    */
    private $permalink = null;

    public static function getInstance($options = array()) {        
        if (!self::$instance) {
			self::$instance = new self($options);
		}
        
		return self::$instance;
    }
    
    public function registerRewriteRules(){
        add_action('generate_rewrite_rules', array($this, 'addRewriteRules'));
    }
    
    public function unregisterRewriteRules(){        
        remove_action('generate_rewrite_rules', array($this, 'addRewriteRules'));
    }
    
    public function generatePage(){
        $this->generateShopPostType();

        //add_filter('query_vars', array($this, 'addQueryVars'));
        add_action('pre_get_posts', array($this, 'preGetPosts'));
        add_action('the_post', array($this, 'setPostContents'));
        add_filter('the_title', array($this, 'appendPageTitle'), 10, 2);
        add_filter('single_post_title', array($this, 'appendBrowserTitle'), 10, 2);
        add_filter('edit_post_link', '__return_false');
        add_filter('wp_head', array($this, 'appendMetaTags'));
        add_filter('post_type_link', array($this, 'generatePostLink'), 10, 2);
    }    
    
    /**
	 * Register a shop post type
	 * @static
	 * @return void
	 */
	private static function generateShopPostType() {
        global $wp_post_types;
        
        if (!array_key_exists(self::POST_TYPE, $wp_post_types)){
            $args = array(
                'public' => FALSE,
                'show_ui' => FALSE,
                'exclude_from_search' => TRUE,
                'publicly_queryable' => TRUE,
                'show_in_menu' => FALSE,
                'show_in_nav_menus' => FALSE,
                'supports' => array('title'),
                'has_archive' => FALSE
            );
            
            register_post_type(self::POST_TYPE, $args);
        }
	}
    
    /**
	 * Update WordPress's rewrite rules array with registered routes
	 * Action: generate_rewrite_rules
	 *
	 * @param WP_Rewrite $wp_rewrite
	 */
    public function addRewriteRules(WP_Rewrite $wp_rewrite) {
        //$newRules['^shop(/([^/]+))?(/([^/]+))?(/(.+))*/?$'] = 'index.php?post_type=shop&controller=$matches[2]&task=$matches[4]&query=$matches[6]';
        $newRules['^'.$this->getShopBasePageSlug().'(/(.+))*/?$'] = 'index.php?post_type=' . self::POST_TYPE;

        $rules = (array)$wp_rewrite->rules;
        $wp_rewrite->rules = $newRules + $rules;
    }
    
    /**
	 * Add query vars to WP's recognized query vars
	 *
	 * @param array $vars
	 * @return array
	 */
    public function addQueryVars($vars){
        $route_vars = array('controller', 'task', 'query');
		return array_merge($vars, $route_vars);
    }
        
    public function preGetPosts(WP_Query $query) {
        // Affect only main query
        if (!$query->is_main_query()){
            return;
        }
        $config = Factory::getConfig();
		$a = $query->get('page_id');
		$b = $query->queried_object->ID;
        // display shop only if "post_type == shop", "page_id == main shop page", "queried_object == main shop page", "home page == main shop page"
        if ((isset($query->query['post_type']) && $query->query['post_type'] == self::POST_TYPE) || ($config->shop_base_page && $query->get('page_id') == $config->shop_base_page) || ($config->shop_base_page && isset($query->queried_object->ID) && $query->queried_object->ID == $config->shop_base_page) || ($query->is_home() && get_option('page_on_front') == $config->shop_base_page)) {
            $this->parseRequest();
            $this->contents = $this->getPageContent();
            $this->message = $this->getFrontMessages();
            
            // make sure we get the right post
			$query->query_vars['post_type'] = self::POST_TYPE;
			$query->query_vars['p'] = $this->getShopPostId();
            $query->query_vars['pagename'] = '';
            $query->query_vars['page'] = '';
            $query->query_vars['page_id'] = 0;
            
            // override any vars WordPress set based on the original query
			$query->is_single = TRUE;
			$query->is_singular = TRUE;
			$query->is_404 = FALSE;
			$query->is_home = FALSE;
        }
    }

    /**
	 * Get ID of the placeholder shop post type
	 *
	 * @return int
	 */
	private function getShopPostId() {
		if (!$this->shopPostId) {
			$posts = get_posts(array(
				'post_type' => self::POST_TYPE,
				'post_status' => 'publish',
				'posts_per_page' => 1,
			));

			if ($posts) {
				$this->shopPostId = $posts[0]->ID;
			} else {
				$this->shopPostId = $this->createShopPostType();
			}
		}
        
		return $this->shopPostId;
	}

	/**
	 * Create a new placeholder shop post type
	 *
	 * @return int The ID of the new post
	 */
	private function createShopPostType() {
		$post = array(
			'post_title' => 'Shop',
			'post_status' => 'publish',
			'post_type' => self::POST_TYPE,
		);
        
		$id = wp_insert_post($post);
		if (is_wp_error($id)) {
			return 0;
		}
        
		return $id;
	}
    
    /**
	 * Add our content to post
	 *
	 * @param object $post
	 * @return void
	 */
	public function setPostContents($post) {
        if ($post->post_type == self::POST_TYPE){
			remove_filter('the_content', 'wpautop');
            $post->post_content = $this->message . $this->contents;
            global $pages;
            $pages = array($post->post_content);
        }
	}
    
    public function appendMetaTags() {
        $app = Factory::getApplication();
        if (is_array($app->tags) && $app->tags){
            $meta = '';
            foreach ($app->tags as $id => $content) {
                if ($id != 'title') {
                    $meta .= '<meta name="'.$id.'" content="'.$content.'">'.PHP_EOL;
                }
            }

            echo $meta;
        }
    }
    
    /**
	 * Set the title for the page
	 *
	 * @param string $title
	 * @param int $post_id
	 * @return string
	 */
	public function appendPageTitle($title, $post_id) {
		if ($post_id == $this->getShopPostId()) {
            $config = Factory::getConfig();
			$app = Factory::getApplication();
            if (is_array($app->tags) && $app->tags['title'] != '') {
                $title = $app->tags['title'];
            } else if ($config->shop_base_page && $post = get_post($config->shop_base_page)){
                if ($post->post_status == 'publish'){
                    $title = $post->post_title;
                }
            }
		}
        
        return $title;
	}
    
    /**
	 * Set the title for the browser
	 *
	 * @param string $title
	 * @param object $post
	 * @return string
	 */
	public function appendBrowserTitle($title, $post) {
		return $this->appendPageTitle($title, $post->ID);
	}
    
    /**
	 * Generate shop pages links, for canonical link 
	 *
	 * @param string $permalink
	 * @param object $post
	 * @return string
	 */
    public function generatePostLink($permalink, $post) {
		if (isset($post->ID, $post->post_type) && $post->post_type === self::POST_TYPE) {
            // TODO - generate url by post query(controller, task ...)
            $uri = Uri::getInstance();
            $permalink = (string)$uri;
		}
        
		return $permalink;
	}
    
    /**
	 * Get the contents of the page
	 *
	 * @param WP $query
	 * @return bool|string
	 */
	private function getPageContent() {
        ob_start();
        require_once WOPSHOP_PLUGIN_DIR . 'site/wshopsite.php';
        $contents = ob_get_clean();

		return $contents;
	}
    
    private function parseRequest(){
        $uri = Uri::getInstance();
        $limitstart = $uri->getVar('limitstart', 0);
        if (Request::getInt('limit')){
            $limitstart = 0;
        }
        
        if ($limitstart){
            Request::setVar('limitstart', $limitstart);
        } else {
            Request::_cleanVar($limitstart);
        }
        
        if (get_site_option('permalink_structure')){
            $base_url = $this->getCurrentUri();
            $segments = array();
            $routes = explode('/', $base_url);

            foreach($routes as $route) {
                if (trim($route) != ''){
                    array_push($segments, $route);
                }
            }

            array_shift($segments);
            if ($segments[0] == $this->getShopBasePageSlug()) {
                array_shift($segments);
            }
			do_action_ref_array('onBeforeParseRoute', array(&$this, &$routes, &$segments));
            $controller = array_shift($segments);

            if (!$controller){
                Request::setVar('controller', 'products');
                Request::setVar('task', 'display');
				do_action_ref_array('onAfterParseRoute', array(&$this, &$routes, &$segments, &$controller));
                return;
            }
            
            if ($controller=='category'){
                $catalias = Factory::getAliasCategory();
                $category_id = array_search($segments[1], $catalias, true);
                if (!$category_id) {
                    $category_id = $segments[1];
                }
                Request::setVar('controller', 'category');
                $task = array_shift($segments);
                Request::setVar('task', 'view');
                Request::setVar('category_id', $category_id);
				do_action_ref_array('onAfterParseRoute', array(&$this, &$routes, &$segments, &$controller));
                return;
            } 
            if ($controller=='user'){
                Request::setVar('controller', 'user');
                $task = array_shift($segments);
                Request::setVar('task', $task);
                $order_id = array_shift($segments);
                Request::setVar('order_id', $order_id);
				do_action_ref_array('onAfterParseRoute', array(&$this, &$routes, &$segments, &$controller));
                return;
            }            
            if ($controller=='cart'){
                Request::setVar('controller', 'cart');
                $task = array_shift($segments);
                Request::setVar('task', $task);
				do_action_ref_array('onAfterParseRoute', array(&$this, &$routes, &$segments, &$controller));
                return;
            }
            if ($controller=='wishlist'){
                Request::setVar('controller', 'wishlist');
                $task = array_shift($segments);
                Request::setVar('task', $task);
				do_action_ref_array('onAfterParseRoute', array(&$this, &$routes, &$segments, &$controller));
                return;
            }
            if ($controller=='search'){
                Request::setVar('controller', 'search');
                $task = array_shift($segments);
                Request::setVar('task', $task);
				do_action_ref_array('onAfterParseRoute', array(&$this, &$routes, &$segments, &$controller));
                return;
            }
            if ($controller=='manufacturer'){
                $manalias = Factory::getAliasManufacturer();
                if (isset($segments[1])){
                    $manufacturer_id = array_search($segments[1], $manalias, true);
                    if(!$manufacturer_id) $manufacturer_id = $segments[1];
                }
                $task = array_shift($segments);
                Request::setVar('controller', 'manufacturer');
                Request::setVar('task', $task);
                Request::setVar('manufacturer_id', $manufacturer_id);
				do_action_ref_array('onAfterParseRoute', array(&$this, &$routes, &$segments, &$controller));
                return;
            }
            if ($controller=='products'){
                Request::setVar('controller', 'products');
                $task = array_shift($segments);
                Request::setVar('task', $task);
                $order_id = array_shift($segments);
                Request::setVar('order_id', $order_id);
				do_action_ref_array('onAfterParseRoute', array(&$this, &$routes, &$segments, &$controller));
                return;
            }            
            if ($controller=='product'){
                Request::setVar('controller', 'product');
                $task = array_shift($segments);
                Request::setVar('task', $task);
				if(count($segments)){
					$prod = array_shift($segments);
					$product_id = getProductBySlug($prod);
					Request::setVar('product_id', $product_id);					
				}
				do_action_ref_array('onAfterParseRoute', array(&$this, &$routes, &$segments, &$controller));
                return;
            }
            if ($controller=='checkout'){
                Request::setVar('controller', 'checkout');
                $task = array_shift($segments);
                Request::setVar('task', $task);
				do_action_ref_array('onAfterParseRoute', array(&$this, &$routes, &$segments, &$controller));
                return;
            }
            if ($controller=='content'){
                Request::setVar('controller', 'content');
                $task = array_shift($segments);
                Request::setVar('task', $task);
                $content_page = array_shift($segments);
                Request::setVar('content-page', $content_page);
				do_action_ref_array('onAfterParseRoute', array(&$this, &$routes, &$segments, &$controller));
                return;
            }
			if (isset($controller)){
				Request::setVar('controller', $controller);
				$task = array_shift($segments);
				Request::setVar('task', $task);
				do_action_ref_array('onAfterParseRoute', array(&$this, &$routes, &$segments, &$controller));
			}
        } else {
            $controller = Request::getCmd('controller');
            if (!isset($controller) || $controller=='productlist'){
                Request::setVar('controller', 'products');  
            } 
			do_action_ref_array('onAfterParseRouteUri', array(&$this, &$uri));
        }
        
        return;
    }

    public function build($url, $ssl = null, $langPrefix = null) {
        $config = Factory::getConfig();
        $scheme = null;
        if ($ssl){
            $scheme = 'https';
        }
        
        do_action_ref_array('onBeforeBuildRoute', array(&$this, &$url, &$scheme));

        if (get_site_option('permalink_structure')) {
            $this->permalink = trailingslashit(get_home_url()) . $this->getShopBasePageSlug() . '/';
            parse_str($url, $parts);
            if ($parts['limitstart'] == '' || $parts['limitstart'] == '0'){
                unset($parts['limitstart']);
            }
            if (!$parts['controller']) {
                return $this->permalink;
            }
            if ($parts['controller'] == 'category') {
                $catalias = Factory::getAliasCategory();
                $new_url = $parts['controller'];
                if (isset($parts['task'])) {
                    $new_url .= '/' . $parts['task'];
                }
                if (isset($catalias[$parts['category_id']]))
                    $new_url .= '/' . $catalias[$parts['category_id']];
                else
                    $new_url .= '/' . $parts['category_id'];
                unset($parts['category_id']);
            }
            if ($parts['controller'] == 'user') {
                $new_url = $parts['controller'] . '/' . $parts['task'];
                if ($parts['task'] == 'order' and isset($parts['order_id'])){
                    $new_url .= '/' . $parts['order_id'];
                }
                unset($parts['order_id']);
            }
            if ($parts['controller'] == 'cart') {
                $new_url = $parts['controller'];
                if (!isset($parts['task'])) {
                    $parts['task'] = 'view';
                }
                $new_url .= '/' . $parts['task'];
            }
            if ($parts['controller'] == 'wishlist') {
                $new_url = $parts['controller'];
                if (!isset($parts['task'])) {
                    $parts['task'] = 'view';
                }
                $new_url .= '/' . $parts['task'];
            }
            if ($parts['controller'] == 'search') {
                $new_url = $parts['controller'];
                if (!isset($parts['task'])) {
                    $parts['task'] = 'view';
                }
                $new_url .= '/' . $parts['task'];
            }
            if ($parts['controller'] == 'manufacturer') {
                $new_url = $parts['controller'];
                if (isset($parts['task'])) {
                    $new_url .= '/' . $parts['task'];
                }
                if (isset($parts['manufacturer_id'])) {
                    $manalias = Factory::getAliasManufacturer();
                    if (isset($manalias[$parts['manufacturer_id']]))
                        $new_url .= '/' . $manalias[$parts['manufacturer_id']];
                    else
                        $new_url .= '/' . $parts['manufacturer_id'];
                    unset($parts['manufacturer_id']);
                }
            }
            if ($parts['controller'] == 'productlist') {
                $new_url = $parts['controller'] . '/' . $parts['task'];
                //if(isset($parts['category_id'])) $new_url.= '/'.$parts['category_id'];
                //if(isset($parts['category_id'])) $new_url.= '/'.$parts['category_id'];
                //unset($parts['order_id']);
            }
            if ($parts['controller'] == 'products') {
                $new_url = $parts['controller'];
                if (!isset($parts['task'])) {
                    $parts['task'] = 'view';
                }
                $new_url .= '/' . $parts['task'];
            }
            if ($parts['controller'] == 'content') {
                $new_url = $parts['controller'] . '/' . $parts['task'] . '/' . $parts['page'];
            }
            if ($parts['controller'] == 'checkout') {
                $new_url = $parts['controller'] . '/' . $parts['task'];
            }
            if ($parts['controller'] == 'product') {
                $new_url = $parts['controller'];
                if (isset($parts['task'])) {
                    $new_url .= '/' . $parts['task'];
                }
                if (isset($parts['product_id'])) {
                    $prod_slug = getProductById($parts['product_id']);
                    $new_url .= '/' . $prod_slug;
                    unset($parts['product_id']);
                }
            }
			if (!$new_url){
				$new_url = $parts['controller'] . '/' . $parts['task'];
			}

			do_action_ref_array('onAfterBuildRoute', array(&$this, &$parts, &$new_url));
            unset($parts['controller']);
            unset($parts['task']);
            unset($parts['page']);

            $extra = Uri::buildQuery($parts);
            if ($extra){
                $extra = '?' . $extra;
            }
            $url = trailingslashit($this->permalink . $new_url) . $extra;
        } else if ($config->shop_base_page){
            $this->permalink = get_page_link($config->shop_base_page);
            
            if (strpos($this->permalink, '?')){
                $url = $this->permalink . '&' . $url;
            } else {
                $url = $this->permalink . '?' . $url;
            }
        }
        
        if ($scheme){
            $uri = Uri::getInstance($url);
            $uri->setScheme($scheme);
            $url = (string)$uri;
        }
        
        return $url;
    }
    
    /**
	 * Regenerate Rewrite Rules for new slug
	 *
	 * @param int $post_id
	 */
    public function regenerateRewriteRulesForPages($post_id){
        $config = Factory::getConfig();
        
        if ($config->shop_base_page && $config->shop_base_page == $post_id){
            flush_rewrite_rules();
        }
    }
    
    private function getShopBasePageSlug(){
        if (!$this->shopBasePageSlug){
            $this->shopBasePageSlug = 'shop';
            $config = Factory::getConfig();

            if (isset($config->shop_base_page) && $config->shop_base_page){
                $post = get_post($config->shop_base_page);

                if ($post && $post->post_status == 'publish'){
                    $this->shopBasePageSlug = $post->post_name;
                }
            }
        }
        
        return $this->shopBasePageSlug;
    }
    
	private function getCurrentUri() {
		$basepath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
		$uri = substr($_SERVER['REQUEST_URI'], strlen($basepath));
		if (strstr($uri, '?')) $uri = substr($uri, 0, strpos($uri, '?'));
		$uri = '/' . trim($uri, '/');
		return $uri;
	}
    
    private function getFrontMessages() {
        wp_enqueue_script('alert.js', WOPSHOP_PLUGIN_URL.'assets/js/bootstrap/alert.js', array('jquery'));
        $msgList = $this->getMessageData();
        $alert = array('error' => 'alert-danger', 'warning' => '', 'notice' => 'alert-info', 'message' => 'alert-success');
        ob_start();
        ?>
        <div id="system-message-container">
            <?php if (is_array($msgList) && $msgList) : ?>
                <?php foreach ($msgList as $type => $msgs) : ?>
                    <div class="alert <?php echo $alert[$type]; ?>">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <?php if ($msgs) : ?>
                            <?php foreach ($msgs as $msg) : ?>
                                <p><?php echo $msg; ?></p>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php
        $message = ob_get_clean();
        
        return $message;;
    }

    private function getMessageData() {
        // Initialise variables.
        $lists = array();

        // Get the message queue
        $messages = Factory::getApplication()->getMessageQueue();

        // Build the sorted message list
        if (is_array($messages) && !empty($messages)) {
            foreach ($messages as $msg) {
                if (isset($msg['type']) && isset($msg['message'])) {
                    $lists[$msg['type']][] = $msg['message'];
                }
            }
        }

        return $lists;
    }
}