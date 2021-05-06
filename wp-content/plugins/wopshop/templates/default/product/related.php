<?php 
$in_row = $this->config->product_count_related_in_row;
?>
<?php if (count($this->related_prod)){?>    
    <div class="related_header">
        <?php print _WOP_SHOP_RELATED_PRODUCTS?>
    </div>
    <div class="wshop_list_product">
        <div class = "wshop list_related">
            <?php foreach($this->related_prod as $k=>$product) : ?>        
                <?php if ($k % $in_row == 0) : ?>
                    <div class = "row-fluid">
                <?php endif; ?>
            
                <div class="sblock<?php echo $in_row?>">
                    <div class="wshop_related block_product">
                        <?php include(dirname(__FILE__)."/../".$this->folder_list_products."/".$product->template_block_product);?>
                    </div>
                </div>

                <?php if ($k % $in_row == $in_row - 1) : ?>
                    <div class = "clearfix"></div>
                    </div>
                <?php endif; ?>
                
            <?php endforeach; ?>
            
            <?php if ($k % $in_row != $in_row - 1) : ?>
                <div class = "clearfix"></div>
                </div>
            <?php endif; ?>
        </div>
    </div> 
<?php }?>