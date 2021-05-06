<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$menu=getItemsOptionPanelMenu(); 
?>
<div class="clear"></div>
<div class="wshop-content">
    <div class="wopsubmenu">
        <div class="m">
            <ul id="submenu">
            <?php        
            foreach($menu as $key=>$el){
                if (!$el[3]) continue;
                $class="";
                if ($active){
                    if ($key==$active) $class="class='active'";
                }
            ?>
                <li>
                    <a <?php print $class;?> href="<?php print $el[1]?>"><?php print $el[0]?></a>
                </li>
            <?php }?>        
            </ul>    
            <div class="clear"></div>
        </div>
    </div>
</div>