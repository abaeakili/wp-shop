<?php if ($this->allow_review || $this->config->show_hits){?>
<div class="block_rating_hits">
    <table>
        <tr>
            <?php if ($this->config->show_hits){?>
                <td><?php print _WOP_SHOP_HITS?>: </td>
                <td><?php print $this->product->hits;?></td>
            <?php } ?>
            
            <?php if ($this->allow_review && $this->config->show_hits){?>
                <td> | </td>
            <?php } ?>
            
            <?php if ($this->allow_review){?>
                <td>
                    <?php print _WOP_SHOP_RATING?>: 
                </td>
                <td>
                    <?php print showMarkStar($this->product->average_rating);?>                    
                </td>
            <?php } ?>
        </tr>
    </table>
</div>
<?php } ?>