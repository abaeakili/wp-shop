<div class="wrap shopping">
    <div id="cpanel">
        <?php 
        if(is_array($this->items)){
            foreach($this->items as $key=>$item){
                if($item[3] != 1) continue; ?>
                <div style="float:left;">
                    <div class="icon">
                        <a href="<?php echo $item[1]?>">
                            <img src="<?php print WOPSHOP_PLUGIN_URL?>assets/images/<?php print $item[2]; ?>" alt="">
                            <span><?php echo $item[0]; ?></span>
                        </a>
                    </div>
                </div>
        <?php
            }
        }
        ?>
    </div>
</div>
 