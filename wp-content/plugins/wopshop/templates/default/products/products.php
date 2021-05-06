<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="wshop" id="wshop">
<?php if ($this->header){?>
<!--<h1 class="listproduct<?php print $this->prefix;?>"><?php print $this->header?></h1>-->
<?php }?>

<?php if ($this->display_list_products){ ?>
<div class="wshop_list_product">
<?php
    include(dirname(__FILE__)."/../".$this->template_block_form_filter);
    if (count($this->rows)){
        include(dirname(__FILE__)."/../".$this->template_block_list_product);
    }elseif($this->willBeUseFilter){
        include(dirname(__FILE__)."/../".$this->template_no_list_product);
    }
    if ($this->display_pagination){
        include(dirname(__FILE__)."/../".$this->template_block_pagination);
    }
?>
</div>
<?php }?>
</div>