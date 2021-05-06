<?php
class OrdersWshopAdminModel extends WshopAdminModel {
    function getCountAllOrders($filters) {
        global $wpdb;
        $where = "";
        if ($filters['status_id']){
            $where .= " and O.order_status = '".esc_sql($filters['status_id'])."'";
        }
	if($filters['user_id']) $where .= " and O.user_id = '".esc_sql($filters['user_id'])."'";
        if ($filters['text_search']){
            $search = esc_sql($filters['text_search']);
            $where .= " and (O.`order_number` like '%".$search."%' or O.`f_name` like '%".$search."%' or O.`l_name` like '%".$search."%' or O.`email` like '%".$search."%' or O.`firma_name` like '%".$search."%' or O.`d_f_name` like '%".$search."%' or O.`d_l_name` like '%".$search."%' or O.`d_firma_name` like '%".$search."%' or O.order_add_info like '%".$search."%') ";
        }
        if (!$filters['notfinished']) $where .= "and O.order_created='1' ";
        if ($filters['year']!=0) $year = $filters['year']; else $year="%";
        if ($filters['month']!=0) $month = $filters['month']; else $month="%";
        if ($filters['day']!=0) $day = $filters['day']; else $day="%";
        $where .= " and O.order_date like '".$year."-".$month."-".$day." %'";
        
        if ($filters['vendor_id']){
            $where .= " and OI.vendor_id='".esc_sql($filters['vendor_id'])."'";
            $query = "SELECT COUNT(distinct O.order_id) FROM `".$wpdb->prefix."wshop_orders` as O
                  left join `".$wpdb->prefix."wshop_order_item` as OI on OI.order_id=O.order_id
                  where 1 $where ORDER BY O.order_id DESC";
        }else{
            $query = "SELECT COUNT(O.order_id) FROM `".$wpdb->prefix."wshop_orders` as O where 1 ".$where;
        }
		do_action_ref_array('onBeforeQueryGetCountAllOrders', array(&$query, &$filters));
        return $wpdb->get_var($query);
    }

    function getAllOrders($limitstart, $limit, $filters, $filter_order, $filter_order_Dir){
        global $wpdb;
		$_limit = '';
        $where = "";
        if ($filters['status_id']){
            $where .= " and O.order_status = '".esc_sql($filters['status_id'])."'";
        }
	if($filters['user_id']) $where .= " and O.user_id = '".esc_sql($filters['user_id'])."'";
        if ($filters['text_search']){
            $search = esc_sql($filters['text_search']);
            $where .= " and (O.`order_number` like '%".$search."%' or O.`f_name` like '%".$search."%' or O.`l_name` like '%".$search."%' or O.`email` like '%".$search."%' or O.`firma_name` like '%".$search."%' or O.`d_f_name` like '%".$search."%' or O.`d_l_name` like '%".$search."%' or O.`d_firma_name` like '%".$search."%' or O.order_add_info like '%".$search."%') ";
        }

        if (!$filters['notfinished']) $where .= "and O.order_created='1' ";
        if ($filters['year']!=0) $year = $filters['year']; else $year="%";
        if ($filters['month']!=0) $month = $filters['month']; else $month="%";
        if ($filters['day']!=0) $day = $filters['day']; else $day="%";
        $where .= " and O.order_date like '".$year."-".$month."-".$day." %'";
        
        $order = $filter_order." ".$filter_order_Dir;
		
        if($limit > 0) {
            $_limit = " LIMIT " . $limitstart . " , " . $limit;
        }		
        
        if ($filters['vendor_id']){
            $where .= " and OI.vendor_id='".esc_sql($filters['vendor_id'])."'";
            $query = "SELECT distinct O.* FROM `".$wpdb->prefix."wshop_orders` as O
                  left join `".$wpdb->prefix."wshop_order_item` as OI on OI.order_id=O.order_id
                  where 1 $where ORDER BY ".$order;
        }else{
            $query = "SELECT O.*, V.l_name as v_name, V.f_name as v_fname, concat(O.l_name,' ',O.f_name) as name FROM `".$wpdb->prefix."wshop_orders` as O
                  left join `".$wpdb->prefix."wshop_vendors` as V on V.id=O.vendor_id
                  where 1 $where ORDER BY ".$order." ".$_limit;
        }
		do_action_ref_array('onBeforeQueryGetAllOrders', array(&$query, &$filters, &$filter_order, &$filter_order_Dir));
        return $wpdb->get_results($query);
    }
    
    function getAllOrderStatus($order = null, $orderDir = null) {
        global $wpdb;
        $ordering = "status_id";
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $query = "SELECT status_id, status_code, `name_".$this->lang."` as name FROM `".$wpdb->prefix."wshop_order_status` ORDER BY ".$ordering;
		extract(ws_add_trigger(get_defined_vars(), "before"));
        return $wpdb->get_results($query);
    }
    
    function getMinYear(){
        global $wpdb;
        $query = "SELECT min(order_date) FROM `".$wpdb->prefix."wshop_orders`";
        
        $res = substr($wpdb->get_var($query),0, 4);
        if (intval($res)==0) $res = "2010";
		extract(ws_add_trigger(get_defined_vars(), "before"));
        return $res;
    }
    
	
    function changeProductQTYinStock($change = "-", $order_id){
        global $wpdb;

        $query = "SELECT OI.*, P.unlimited FROM `".$wpdb->prefix."wshop_order_item` as OI left join `".$wpdb->prefix."wshop_products` as P on P.product_id=OI.product_id
                  WHERE order_id = '".esc_sql($order_id)."'";
        
        $items = $wpdb->get_results($query);

        foreach($items as $item){
            if ($item->unlimited) continue;
            
            if ($item->attributes!=""){
                $attributes = json_decode($item->attributes, 1);
            }else{
                $attributes = array();
            }            
            if (!is_array($attributes)) $attributes = array();
            
            $allattribs = $this->getAllAttributes(1);

            $dependent_attr = array();
            foreach($attributes as $k=>$v){
                if ($allattribs[$k]->independent==0){
                    $dependent_attr[$k] = $v;
                }
            }

            if (count($dependent_attr)){
                $where="";
                foreach($dependent_attr as $k=>$v){
                    $where.=" and `attr_$k`='".intval($v)."'";
                }
                
                $query = "update `".$wpdb->prefix."wshop_products_attr` set `count`=`count`  ".$change." ".$item->product_quantity." where product_id='".intval($item->product_id)."' ".$where;
                $wpdb->query($query);
                $query="select sum(count) as qty from `".$wpdb->prefix."wshop_products_attr` where product_id='".intval($item->product_id)."' and `count`>0 ";
                $qty = $wpdb->get_var($query);
                
                $query = "UPDATE `".$wpdb->prefix."wshop_products` SET product_quantity = '".$qty."' WHERE product_id = '".intval($item->product_id)."'";
                $wpdb->query($query);

            }else{
                $query = "UPDATE `".$wpdb->prefix."wshop_products` SET product_quantity = product_quantity ".$change." ".$item->product_quantity." WHERE product_id = '".intval($item->product_id)."'";
                $wpdb->query($query);
            }

            
        }

        if ($change=='-'){
            $product_stock_removed = 1;
        }else{
            $product_stock_removed = 0;
        }
        $query = "update ".$wpdb->prefix."wshop_orders set product_stock_removed=".$product_stock_removed." WHERE order_id = '".esc_sql($order_id)."'";
        $wpdb->query($query);
    }
    function getAllAttributes($resformat = 0){
        global $wpdb;
        if (!is_array($attributes)){
            $ordering = "A.attr_ordering";
            $ordering = "G.ordering, A.attr_ordering";
            $query = "SELECT A.attr_id, A.`name_".$this->lang."` as name, A.`description_".$this->lang."` as description, A.attr_type, A.independent, A.allcats, A.cats, A.attr_ordering, G.`name_".$this->lang."` as groupname
                      FROM `".$wpdb->prefix."wshop_attr` as A left join `".$wpdb->prefix."wshop_attr_groups` as G on A.`group`=G.id
                      ORDER BY ".$ordering;
            $rows = $wpdb->get_results($query);
            foreach($rows as $k=>$v){
                if ($v->allcats){
                    $rows[$k]->cats = array();
                }else{
                    $rows[$k]->cats = json_decode($v->cats, 1);
                }
            }
        }
        $attributes = $rows;
        if ($resformat==0){
            return $attributes;
        }
        if ($resformat==1){
            $attributes_format1 = array();
            foreach($attributes as $v){
                $attributes_format1[$v->attr_id] = $v;
            }
            return $attributes_format1;
        }
        if ($resformat==2){
            $attributes_format2 = array();
            $attributes_format2['independent']= array();
            $attributes_format2['dependent']= array();
            foreach($attributes as $v){
                if ($v->independent) $key_dependent = "independent"; else $key_dependent = "dependent";
                $attributes_format2[$key_dependent][$v->attr_id] = $v;
            }
            return $attributes_format2;
        }
    }
    
    function saveOrderItem($order_id, $post, $old_items){
        global $wpdb;
        if (!isset($post['product_name'])) $post['product_name'] = array();

        $edit_order_items = array();
        foreach($post['product_name'] as $k=>$v){
            $order_item_id = intval($post['order_item_id'][$k]);
            $edit_order_items[] = $order_item_id;
            $order_item = Factory::getTable('orderItem');
            $order_item->order_item_id = $order_item_id;
            $order_item->order_id = $order_id;
            $order_item->product_id = $post['product_id'][$k];
            $order_item->product_ean = $post['product_ean'][$k];
            $order_item->product_name = $post['product_name'][$k];
            $order_item->product_quantity = saveAsPrice($post['product_quantity'][$k]);
            $order_item->product_item_price = $post['product_item_price'][$k];
            $order_item->product_tax = $post['product_tax'][$k];
            $order_item->product_attributes = $post['product_attributes'][$k];
            $order_item->product_freeattributes = $post['product_freeattributes'][$k];
            $order_item->weight = $post['weight'][$k];
            if (isset($post['delivery_times_id'][$k])){
                $order_item->delivery_times_id = $post['delivery_times_id'][$k];
            }else{
                $order_item->delivery_times_id = 0;
            }
            $order_item->vendor_id = $post['vendor_id'][$k];
            $order_item->thumb_image = $post['thumb_image'][$k];
            $order_item->files = json_encode(array());
            $order_item->store();
            unset($order_item);
        }

        foreach($old_items as $k=>$v){
            if (!in_array($v->order_item_id, $edit_order_items)){
                $order_item = Factory::getTable('orderItem');
                $order_item->delete($v->order_item_id);
            }
        }
        extract(ws_add_trigger(get_defined_vars(), "before"));
        return 1;
       
    }
    function getAllItems($order_id){
        $config = Factory::getConfig();
        global $wpdb;
        $query = "SELECT OI.* FROM `".$wpdb->prefix."wshop_order_item` as OI WHERE OI.order_id = '".esc_sql($order_id)."'";
        $items = $wpdb->get_results($query);
        if ($config->display_delivery_time_for_product_in_order_mail){
            $query = "select id, `name_".$config->cur_lang."` as name from ".$wpdb->prefix."wshop_delivery_times";
            $_rows = $wpdb->get_results($query);
            $rows = array();
            foreach($_rows as $row){
                $rows[$row->id] = $row->name;
            }
            unset($_rows);
            foreach($items as $k=>$v){
                $items[$k]->delivery_time = $rows[$v->delivery_times_id];
            }
        }
        return $items;
    }
    function getVendorIdForItems($order_id){
        $items = $this->getAllItems($order_id);
        $vendors = array();
        foreach($items as $v){
            $vendors[] = $v->vendor_id;
        }
        $vendors = array_unique($vendors);
        if (count($vendors)==0){
            return 0;
        }elseif (count($vendors)>1){
            return -1;
        }else{
            return $vendors[0];
        }
    }
    function updateNextOrderNumber(){
        global $wpdb;
        $query = "update `".$wpdb->prefix."wshop_config` set next_order_number=next_order_number+1";
        $_rows = $wpdb->get_results($query);
    }
    /*function getTaxExt(){
        if ($this->order_tax_ext == "") return array();
        return json_decode($this->order_tax_ext, 1);
    }*/
}