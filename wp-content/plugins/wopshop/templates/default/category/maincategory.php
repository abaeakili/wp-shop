<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div class="wshop" id="wshop_plugin">
    <div class="category_description">
        <?php print $this->category->description?>
    </div>

    <div class="wshop_list_category">
    <?php if (count($this->categories)) : ?>
    
        <?php foreach ($this->categories as $k => $category) : ?>
            <?php if ($k % $this->count_category_to_row == 0) : ?>
                <div class = "row-fluid">
            <?php endif; ?>
        
            <div class = "sblock<?php echo $this->count_category_to_row;?> wshop_categ category">
                <div class="sblock2 image">
                    <a href = "<?php print $category->category_link;?>">
                        <img class = "wshop_img" src = "<?php print $this->image_category_path;?>/<?php if ($category->category_image) print $category->category_image; else print $this->noimage;?>" alt="<?php print htmlspecialchars($category->name);?>" title="<?php print htmlspecialchars($category->name);?>" />
                    </a>
                </div>
                <div class="sblock2">
                    <div class="category_name">
                        <a class = "product_link" href = "<?php print $category->category_link?>">
                            <?php print $category->name?>
                        </a>
                    </div>
                    <p class = "category_short_description">
                        <?php print $category->short_description?>
                    </p>
                </div>
            </div>
            
            <?php if ($k % $this->count_category_to_row == $this->count_category_to_row - 1) : ?>
                <div class = "clearfix"></div>
                </div>
            <?php endif; ?>
        <?php endforeach;?>
        
        <?php if ($k % $this->count_category_to_row != $this->count_category_to_row - 1) : ?>
            <div class = "clearfix"></div>
            </div>
        <?php endif; ?>
        
    <?php endif; ?>
    </div>
</div>