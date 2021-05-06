<?php 
/**
* @version      1.0.0 30.01.2017
* @author       MAXXmarketing GmbH
* @package      WOPshop
* @copyright    Copyright (C) 2010 http://www.wop-agentur.com. All rights reserved.
* @license      GNU/GPL
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="wshop" id="wshop_plugin">
    <h1><?php print $this->vendor->shop_name?></h1>

    <?php if ($this->display_list_products){ ?>
    <div class="wshop_list_product">
    <?php
        include(dirname(__FILE__)."/../".$this->template_block_form_filter);
        if (count($this->rows)){
            include(dirname(__FILE__)."/../".$this->template_block_list_product);
        }else{
            include(dirname(__FILE__)."/../".$this->template_no_list_product);
        }
        if ($this->display_pagination){
            include(dirname(__FILE__)."/../".$this->template_block_pagination);
        }
    ?>
    </div>
    <?php }?>
</div>