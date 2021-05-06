<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class UploadWshopAdminModel extends WshopAdminModel {
    public $table_name;
 
    public function __construct() {
        parent::__construct();
    }
    public function ImageUpload($file, $targets, $bool, $thumbSize = 3, $sizeArray_thumb, $fullSize = 3, $sizeArray_full){

        $result = array();
        
        if ($file['error'] == 4 or !is_array($file) or !is_array($targets)){
            $result['upload'] = 0;
            return $result;
        }
        if ($file['error']) {
            $result['upload'] = 1;
            $result['error'] = _WOP_SHOP_UPLOAD_ERROR;
            return $result;
        }
        $filename = $file['name'];
        $ext = substr(strrchr($filename, '.'), 1);

        $file_exist = 0;
        $i = 0;
        while($file_exist < 1 or $i > 100){
            $fileuniq = uniqid();
            $f_name = $fileuniq.'.'.$ext;
            $file_exist = $this->_UploadImageUniqename($targets['target'].$f_name);
            $i++;
        }
        if($i > 100){
            $result['upload'] = 1;
            $result['error'] = _WOP_SHOP_UPLOAD_ERROR;
            return $result;
        }

        if(move_uploaded_file($file['tmp_name'], $targets['target'].$f_name)){
            if($bool['thumb']){
                $this->_UploadImageSetThumb($targets['target'].$f_name, $targets['target_thumb'].'thumb_'.$f_name, $thumbSize, $sizeArray_thumb);
            }
            if($bool['full']){
                $this->_UploadImageSetThumb($targets['target'].$f_name, $targets['target_full'].'full_'.$f_name, $fullSize, $sizeArray_full);
            }
            $result['upload'] = 1;
            $result['error'] = '';
            $result['name'] = $f_name;
            return $result;
        }else{
            $result['upload'] = 1;
            $result['error'] = _WOP_SHOP_UPLOAD_ERROR;
            return $result;
        }
    }
    
    public function UploadImage($file, $target, $bool_thumb, $thumbSize = 3, $target_thumb, $sizeArray){
        $result = array();
        
        if ($file['error'] == 4 or !is_array($file) or !$target){
            $result['upload'] = 0;
            return $result;
        }
        if ($file['error']) {
            $result['upload'] = 1;
            $result['error'] = _WOP_SHOP_UPLOAD_ERROR;
            return $result;
        }
        $filename = $file['name'];
        $ext = substr(strrchr($filename, '.'), 1);

        $file_exist = 0;
        $i = 0;
        while($file_exist < 1 or $i > 100){
            $fileuniq = uniqid();
            $f_name = $fileuniq.'.'.$ext;
            $file_exist = $this->_UploadImageUniqename($target.$f_name);
            $i++;
        }
        if($i > 100){
            $result['upload'] = 1;
            $result['error'] = _WOP_SHOP_UPLOAD_ERROR;
            return $result;
        }

        if(move_uploaded_file($file['tmp_name'], $target.$f_name)){
            if($bool_thumb){
                $this->_UploadImageSetThumb($target.$f_name, $target_thumb.'thumb_'.$f_name, $thumbSize, $sizeArray);
            }
            $result['upload'] = 1;
            $result['error'] = '';
            $result['name'] = $f_name;
            return $result;
        }else{
            $result['upload'] = 1;
            $result['error'] = _WOP_SHOP_UPLOAD_ERROR;
            return $result;
        }
    }
    function _UploadImageUniqename($target){
        if(file_exists($target)) return 0; else return 1;
    }
    function _UploadImageSetThumb($target, $target_thumb, $thumbSize, $sizeArray){
        $size = getimagesize ($target);

        //if($size[0] > $size[1]){ $g = $size[0]/$size[1]; }else{ $g = $size[1]/$size[0]; }
        $g = $size[1]/$size[0];
        if($thumbSize == 1){
            $_width_image = $sizeArray['_width_image'];
            $_height_image = $sizeArray['_height_image'];
        }
        if($thumbSize == 2){
            $_width_image = $sizeArray['_width_image'];
            $_height_image = $sizeArray['_height_image'];
        }
        if($thumbSize == 3){
            $_width_image = $size[0];
            $_height_image = $size[1];
        }
        
        if((int)$_width_image != 0){
            $_height_image = $_width_image*$g;
        }else{
            $_width_image = $_height_image*$g;
        }
        wp_crop_image( $target, 0, 0, $size[0], $size[1], $_width_image, $_height_image, true, $target_thumb);
    }
}
