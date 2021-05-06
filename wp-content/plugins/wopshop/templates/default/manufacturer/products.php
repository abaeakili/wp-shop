<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="wshop" id="wshop_plugin">

    <h1><?php print $this->manufacturer->name?></h1>
    
    <div class="manufacturer_description">
        <?php print $this->manufacturer->description;?>
    </div>
    
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