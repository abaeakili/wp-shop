<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<script type="text/javascript">var liveurl = '<?php print URI::root()?>';</script>
<div class="wshop">
    <h1><?php print _WOP_SHOP_SEARCH ?></h1>
    
    <form action="<?php print $this->action?>" name="form_ad_search" method="post" onsubmit="return validateFormAdvancedSearch('form_ad_search')">
    <input type="hidden" name="setsearchdata" value="1">
    <table class = "wshop" cellpadding = "6" cellspacing="0">
      <?php print $this->_tmp_ext_search_html_start;?>
      <tr>
  	    <td width="120">
  		    <?php print _WOP_SHOP_SEARCH_TEXT?>
	    </td>
        <td>
          <input type = "text" name = "search" class = "inputbox" style = "width:300px" />
        </td>
      </tr>
      <tr>
          <td width="120">
              <?php print _WOP_SHOP_SEARCH_FOR?>
        </td>
        <td>
          <input type="radio" name="search_type" value="any" id="search_type_any" checked="checked" /> <label for="search_type_any"><?php print _WOP_SHOP_ANY_WORDS?></label>
          <input type="radio" name="search_type" value="all" id="search_type_all" /> <label for="search_type_all"><?php print _WOP_SHOP_ALL_WORDS?></label>
          <input type="radio" name="search_type" value="exact" id="search_type_exact" /> <label for="search_type_exact"><?php print _WOP_SHOP_EXACT_WORDS?></label>
        </td>
      </tr>
      <tr>
        <td>
          <?php print _WOP_SHOP_SEARCH_CATEGORIES ?>
        </td>
        <td> 
          <?php print $this->list_categories ?><br />
          <input type = "checkbox" name = "include_subcat" id = "include_subcat" value = "1" />
          <label for = "include_subcat"><?php print _WOP_SHOP_SEARCH_INCLUDE_SUBCAT ?></label>
        </td>
      </tr>
      <tr>
        <td>
          <?php print _WOP_SHOP_SEARCH_MANUFACTURERS ?>    
        </td>
        <td>
          <?php print $this->list_manufacturers ?>
        </td>
      </tr>
      <?php if (getDisplayPriceShop()){?>
      <tr>
        <td>
          <?php print _WOP_SHOP_SEARCH_PRICE_FROM ?>      
        </td>
        <td>
          <input type = "text" class = "inputbox" name = "price_from" id = "price_from" /> <?php print $this->config->currency_code?>
        </td>
      </tr>
      <tr>
        <td>
          <?php print _WOP_SHOP_SEARCH_PRICE_TO ?>      
        </td>
        <td>
          <input type = "text" class = "inputbox" name = "price_to" id = "price_to" /> <?php print $this->config->currency_code?>
        </td>
      </tr>
      <?php }?>
      <tr>
        <td>
          <?php print _WOP_SHOP_SEARCH_DATE_FROM ?>      
        </td>
        <td><input id="date_from" class="datepicker" type="text" size="40" name="date_from"></td>
      </tr>
      <tr>
        <td>
          <?php print _WOP_SHOP_SEARCH_DATE_TO ?>      
        </td>
        <td><input id="date_to" class="datepicker" type="text" size="40" name="date_to"></td>
      </tr>      
      <tr>
        <td colspan="2" id="list_characteristics"><?php print $this->characteristics?></td>
      </tr>
      <?php print $this->_tmp_ext_search_html_end;?>
    </table>    
    <div style="padding:6px;">
    <input type = "submit" class="button" value = "<?php print _WOP_SHOP_SEARCH ?>" />  
    </div>
    </form>
</div>

<?php wp_add_inline_script('jquery-ui-datepicker', "jQuery(document).ready(function (){
                                                        jQuery('#date_from').datepicker({dateFormat: 'yy-mm-dd'});
                                                        jQuery('#date_to').datepicker({dateFormat: 'yy-mm-dd'});
                                                    })"); ?>