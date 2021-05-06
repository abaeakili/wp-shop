<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class CategoriesWshopAdminModel extends WshopAdminModel {
    public $table_name;
 
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix.'wshop_categories';
        parent::__construct();
    }
    
    function getTreeAllCategories($filter = array(), $order = null, $orderDir = null) {
		global $wpdb;
        $lang = $this->lang;
		//$lang = Factory::getLang();
        $query = "SELECT ordering, category_id, category_parent_id, `name_".$lang."` as name, `short_description_".$lang."` as short_description, `description_".$lang."` as description, category_publish, category_image FROM `".$this->table_name."` ORDER BY category_parent_id, ". $this->_allCategoriesOrder($order, $orderDir);
		extract(ws_add_trigger(get_defined_vars(), "before"));
        $all_cats = $wpdb->get_results($query);

        $categories = array();
        if (count($all_cats)){
            foreach($all_cats as $key=>$category){
                $category->isPrev = 0; $category->isNext = 0;
                if (isset($all_cats[$key-1]) && $category->category_parent_id == $all_cats[$key-1]->category_parent_id){
                    $category->isPrev = 1;
                }
                if (isset($all_cats[$key+1]) && $category->category_parent_id == $all_cats[$key+1]->category_parent_id){
                    $category->isNext = 1;
                }
                
                if (!$category->category_parent_id){
                    recurseTree($category, 0, $all_cats, $categories, 0);
                }
            }
        }

        if (count($categories)){
			if (isset($filter['text_search']) && !empty($filter['text_search'])){
                $originalCategories = $categories;
                $filter['text_search'] = strtolower($filter['text_search']);

                foreach ($categories as $key => $category){
                    if (strpos(strtolower($category->name), $filter['text_search']) === false && strpos(strtolower($category->short_description), $filter['text_search']) === false && strpos(strtolower($category->description), $filter['text_search']) === false){
                        unset($categories[$key]);
                    }
                }

                if (count($categories)){
                    foreach ($categories as $key => $category){
                        $categories[$key]->name = "<span class = 'wshop_green'>".$categories[$key]->name."</span>"; 
                        $category_parent_id = $category->category_parent_id;
                        $i = 0;
                        while ($category_parent_id || $i < 1000) {
                            foreach ($originalCategories as $originalKey => $originalCategory){
                                if ($originalCategory->category_id == $category_parent_id){
                                    $categories[$originalKey] = $originalCategory;
                                    $category_parent_id = $originalCategory->category_parent_id;
                                    break;
                                }
                            }
                            $i++;
                        }
                    }
                    
                    ksort($categories);
                }
            }
		
            foreach($categories as $key=>$category){
                $category->space = ''; 
                for ($i = 0; $i < $category->level; $i++){
                    $category->space .= '<span class = "gi">|—</span>';
                }
            }
        }		
        return $categories;		
    }    
 
    function _allCategoriesOrder($order = null, $orderDir = null){
        if ($order && $orderDir){
            $fields = array("name" => "`name_".$this->lang."`", "id" => "`category_id`", "description" => "`description_".$this->lang."`", "ordering" => "`ordering`");
            if (strtolower($orderDir) != "asc") $orderDir = "desc";
            if (!$fields[$order]) return "`ordering` ".$orderDir;
			extract(ws_add_trigger(get_defined_vars(), "before"));
            return $fields[$order]." ".$orderDir;
        }else{
            return "`ordering` asc";
        }
    }    

    function getAllCatCountProducts(){  
        global $wpdb;
        $query = "SELECT category_id, count(product_id) as k FROM `".$wpdb->prefix."wshop_products_to_categories` group by category_id";
		extract(ws_add_trigger(get_defined_vars(), "before"));
        $list = $wpdb->get_results($query);
        $rows = array();
        foreach($list as $row){
            $rows[$row->category_id] = $row->k;
        }        
        return $rows;
    }
    
    function getCategory($cid){
        global $wpdb;
        $query = "SELECT *, `name_".$this->lang."` as name, `short_description_".$this->lang."` as short_description, `description_".$this->lang."` as description, category_publish, category_image FROM ".$this->table_name.' WHERE `category_id` = '.$cid;
		extract(ws_add_trigger(get_defined_vars(), "before"));
        $category = $wpdb->get_row($query);        
        return $category;        
    }

    function getSubCategories($parentId, $order = 'id', $ordering = 'asc') {
        global $wpdb;       
        if ($order=="id") $orderby = "`category_id`";
        if ($order=="name") $orderby = "`name_".$this->lang."`";
        if ($order=="ordering") $orderby = "ordering";
        if (!$orderby) $orderby = "ordering";
        $query = "SELECT `name_".$this->lang."` as name,`short_description_".$this->lang."` as short_description, category_id, category_publish, ordering, category_image FROM ".$this->table_name." WHERE category_parent_id = '".esc_sql($parentId)."'
                   ORDER BY ".$orderby." ".$ordering;
		extract(ws_add_trigger(get_defined_vars(), "before"));
        return $wpdb->get_results($query);
    } 
    
function buildTreeCategory($publish = 1, $is_select = 1, $access = 1) {
        global $wpdb;
	$where = array();
        if ($publish){
            $where[] = "category_publish = '1'";
        }
        /*if ($access){
            $groups = implode(',', $user->getAuthorisedViewLevels());
            $where[] =' access IN ('.$groups.')';
        }*/
        $add_where = "";
        if (count($where)){
            $add_where = " where ".implode(" and ", $where);
        }
        $all_cats = $wpdb->get_results("SELECT `name_".$this->lang."` as name, category_id, category_parent_id, category_publish FROM `".$this->table_name."`".$add_where." ORDER BY category_parent_id, ordering desc" , OBJECT);

        $categories = array();
	if(count($all_cats)) {
            foreach ($all_cats as $key => $value) {
                if(!$value->category_parent_id){
                    $this->recurseTree($value, 0, $all_cats, $categories, $is_select);
                }
            }
        }
        return $categories;
    }

    function recurseTree($cat, $level, $all_cats, &$categories, $is_select) {
    $probil = '';
    if($is_select) {
        for ($i = 0; $i < $level; $i++) {
            $probil .= '-- ';
        }
        $cat->name = ($probil . $cat->name);
        $categories[] = $cat; //JHTML::_('select.option', $cat->category_id, $cat->name,'category_id','name' );
    } else {
        $cat->level = $level;
        $categories[] = $cat;
    }
    
    
    foreach ($all_cats as $categ) {
        if($categ->category_parent_id == $cat->category_id) {
            $this->recurseTree($categ, ++$level, $all_cats, $categories, $is_select);
            $level--;
        }
    }
    return $categories;
}

    function CategoryUpdate($post, $id){
        global $wpdb;
        $wpdb->update( 
            $this->table_name, 
            $post, 
            array( 
                'category_id' => $id
            )
        );
        //$wpdb->show_errors();
        //$wpdb->print_error(); 
    }
    
    function CategoryInsert($post){
        global $wpdb;

        $count = $wpdb->get_var( "SELECT COUNT(*) FROM ".$this->table_name);
        
        $post['ordering'] = $count+1;
        $wpdb->insert( 
            $this->table_name, 
            $post
        );
        return $wpdb->insert_id;
        //$wpdb->show_errors();
        //$wpdb->print_error(); 
    }
    function getAllList($display=0){
        global $wpdb;
        if ($order=="id") $orderby = "`category_id`";
        if ($order=="name") $orderby = "`name_".$this->lang."`";
        if ($order=="ordering") $orderby = "ordering";
        if (!$orderby) $orderby = "ordering";

        $query = "SELECT `name_".$this->lang."` as name, category_id FROM `".$wpdb->prefix."wshop_categories` ORDER BY ordering";
		extract(ws_add_trigger(get_defined_vars(), "before"));
        $list = $wpdb->get_results($query, OBJECT);
        if ($display==1){
            $rows = array();
            foreach($list as $k=>$v){
                $rows[$v->category_id] = $v->name;
            }
            unset($list);
            $list = $rows;
        }
        return $list;
    }

    function uploadImage($post){
        $config = Factory::getConfig();

        $upload = new UploadFile($_FILES['category_image']);
        $upload->setAllowFile(array('jpeg','jpg','gif','png'));
        $upload->setDir($config->image_category_path);
        $upload->setFileNameMd5(0);
        $upload->setFilterName(1);
        if ($upload->upload()){
            $name = $upload->getName();
            do_action_ref_array('onAfterUploadCategoryImage', array(&$post, &$name));
            if ($post['old_image'] && $name!=$post['old_image']){
                @unlink($config->image_category_path."/".$post['old_image']);
            }
            @chmod($config->image_category_path."/".$name, 0777);
            
            if ($post['size_im_category'] < 3){
                if($post['size_im_category'] == 1){
                    $category_width_image = $config->image_category_width; 
                    $category_height_image = $config->image_category_height;
                }else{
                    $category_width_image = Request::getInt('category_width_image'); 
                    $category_height_image = Request::getInt('category_height_image');
                }

                $path_full = $config->image_category_path."/".$name;
                $path_thumb = $config->image_category_path."/".$name;
                //echo $category_width_image.' || '.$category_height_image; die();
                if ($category_width_image || $category_height_image){
                    //echo $path_full.', '.$category_width_image.', '.$category_height_image.', '.$config->image_cut.', '.$config->image_fill.', '.$path_thumb.', '.$config->image_quality.', '.$config->image_fill_color.', '.$config->image_interlace; die();
                    if (!ImageLib::resizeImageMagic($path_full, $category_width_image, $category_height_image, $config->image_cut, $config->image_fill, $path_thumb, $config->image_quality, $config->image_fill_color, $config->image_interlace)) {
                        addMessage(_WOP_SHOP_ERROR_CREATE_THUMBAIL, 'error');
                    saveToLog("error.log", "SaveCategory - Error create thumbail");
                    }
                }
                @chmod($config->image_category_path."/".$name, 0777);
            }
            $category_image = $name;
            do_action_ref_array('onAfterSaveCategoryImage', array(&$post, &$category_image, &$path_full, &$path_thumb));
        }else{
            $category_image = '';
            if ($upload->getError() != 4){
                addMessage(_WOP_SHOP_ERROR_UPLOADING_IMAGE, 'error');
                saveToLog("error.log", "SaveCategory - Error upload image. code: ".$upload->getError());
            }
        }
        return $category_image;
        
    }
    
    function deleteCategory($category_id){
        global $wpdb;
        $wpdb->delete( $wpdb->prefix."wshop_categories", array( 'category_id' => $category_id ) );
    }

}
