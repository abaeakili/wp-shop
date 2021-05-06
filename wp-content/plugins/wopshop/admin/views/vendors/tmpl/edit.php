<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
$vendor=$this->vendor;
$lists=$this->lists;
?>
<form method="POST" action="admin.php?page=options&tab=vendors&task=save" enctype="multipart/form-data">
<div class="wrap">
    <h3><?php $this->config->admin_show_attributes; echo $vendor->id ? _WOP_SHOP_VENDORS.' / '.$vendor->shop_name : _WOP_SHOP_VENDORS; ?></h3>
	<?php print $this->tmp_html_start?>
<div class="col100">
<fieldset class="adminform">
<table class="admintable">
	<tr>
     <td class="key">
       <?php echo _WOP_SHOP_PUBLISH;?>
     </td>
     <td>
       <input type="checkbox" name="publish" value="1" <?php if ($vendor->publish) echo 'checked="checked"'?> />
     </td>
   </tr>
	<tr>
	  <td class="key">
	    <?php echo _WOP_SHOP_USER_FIRSTNAME;?>*
	  </td>
	  <td>
	    <input type="text" class="inputbox" size="40" name="f_name" value="<?php echo $vendor->f_name ?>" />
	  </td>
	</tr>

	<tr>
	  <td class="key">
	    <?php echo _WOP_SHOP_USER_LASTNAME;?>*
	  </td>
	  <td>
	    <input type="text" class="inputbox" size="40" name="l_name" value="<?php echo $vendor->l_name ?>" />
	  </td>
	</tr>
		
	<tr>
	  <td class="key">
	    <?php echo _WOP_SHOP_STORE_NAME;?>*
	  </td>
	  <td>
	    <input type="text" class="inputbox" size="40" name="shop_name" value="<?php echo $vendor->shop_name ?>" />
	  </td>
	</tr>

	<tr>
	  <td class="key">
	    <?php echo _WOP_SHOP_STORE_COMPANY;?>*
	  </td>
	  <td>
	    <input type="text" class="inputbox" size="40" name="company_name" value="<?php echo $vendor->company_name ?>" />
	  </td>
	</tr>
    
    <tr>
      <td class="key">
        <?php echo _WOP_SHOP_LOGO." ("._WOP_SHOP_URL.")";?>
      </td>
      <td>
        <input type="text" class="inputbox" size="80" name="logo" value="<?php echo $vendor->logo ?>" />
      </td>
    </tr>    

	<tr>
	  <td class="key">
	    <?php echo _WOP_SHOP_URL;?>
	  </td>
	  <td>
	    <input type="text" class="inputbox" size="80" name="url" value="<?php echo $vendor->url ?>" />
	  </td>
	</tr>

	<tr>
	  <td class="key">
	    <?php echo _WOP_SHOP_ADRESS?>
	  </td>
	  <td>
	    <input type="text" class="inputbox" size="40" name="adress" value="<?php echo $vendor->adress ?>" />
	  </td>
	</tr>

	<tr>
	  <td class="key">
	    <?php echo _WOP_SHOP_CITY?>
	  </td>
	  <td>
	    <input type="text" class="inputbox" size="40" name="city" value="<?php echo $vendor->city ?>" />
	  </td>
	</tr>

	<tr>
	  <td class="key">
	    <?php echo _WOP_SHOP_ZIP?>
	  </td>
	  <td>
	    <input type="text" class="inputbox" size="40" name="zip" value="<?php echo $vendor->zip ?>" />
	  </td>
	</tr>

	<tr>
	  <td class="key">
	    <?php echo _WOP_SHOP_STATE?>
	  </td>
	  <td>
	    <input type="text" class="inputbox" size="40" name="state" value="<?php echo $vendor->state ?>" />
	  </td>
	</tr>

	<tr>
	  <td class="key">
	    <?php echo _WOP_SHOP_COUNTRY?>*
	  </td>
	  <td>
	    <?php echo $lists['country'];?>
	  </td>
	</tr>

	<tr>
	  <td class="key">
	    <?php echo _WOP_SHOP_TELEFON?>
	  </td>
	  <td>
	    <input type="text" class="inputbox" size="40" name="phone" value="<?php echo $vendor->phone ?>" />
	  </td>
	</tr>

	<tr>
	  <td class="key">
	    <?php echo _WOP_SHOP_FAX?>
	  </td>
	  <td>
	    <input type="text" class="inputbox" size="40" name="fax" value="<?php echo $vendor->fax ?>" />
	  </td>
	</tr>

    <tr>
      <td class="key">
        <?php echo _WOP_SHOP_EMAIL?>*
      </td>
      <td>
        <input type="text" class="inputbox" size="40" name="email" value="<?php echo $vendor->email ?>" />
      </td>
    </tr>  
			    
    <tr>
      <td class="key">
        <?php echo _WOP_SHOP_USER_ID." ("._WOP_SHOP_MANAGER.")"?>
      </td>
      <td>
        <input type="text" class="inputbox" name="user_id" value="<?php echo $vendor->user_id ?>" />
      </td>
    </tr>   

</table>
</fieldset>
</div>
<div class="clr"></div>

<div class="col100">
<fieldset class="adminform">
    <legend><?php echo _WOP_SHOP_BANK ?></legend>
    <table class="admintable" width="100%" >
    <tr>
     <td class="key">
       <?php echo _WOP_SHOP_BENEF_BANK_NAME;?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox" name="benef_bank_info" value="<?php echo $vendor->benef_bank_info?>" />
     </td>
    </tr>

    <tr>
     <td class="key">
       <?php echo _WOP_SHOP_BENEF_BIC;?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox" name="benef_bic" value="<?php echo $vendor->benef_bic?>" />
     </td>
    </tr>
    <tr>
     <td class="key">
       <?php echo _WOP_SHOP_BENEF_CONTO;?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox" name="benef_conto" value="<?php echo $vendor->benef_conto?>" />
     </td>
    </tr>
    <tr>
     <td class="key">
       <?php echo _WOP_SHOP_BENEF_PAYEE;?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox" name="benef_payee" value="<?php echo $vendor->benef_payee?>" />
     </td>
    </tr>
    <tr>
     <td class="key">
       <?php echo _WOP_SHOP_BENEF_IBAN;?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox" name="benef_iban" value="<?php echo $vendor->benef_iban?>" />
     </td>
    </tr>
	<tr>
     <td class="key">
       <?php echo _WOP_SHOP_BIC_BIC;?>
     </td>
     <td>
       <input size="55" type = "text" class = "inputbox" name = "benef_bic_bic" value = "<?php echo $vendor->benef_bic_bic?>" />
     </td>
    </tr>
    <tr>
     <td class="key">
       <?php echo _WOP_SHOP_BENEF_SWIFT;?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox" name="benef_swift" value="<?php echo $vendor->benef_swift?>" />
     </td>
    </tr>
    </table>
</fieldset>
</div>
<div class="clr"></div>

<div class="col100">
<fieldset class="adminform">
    <legend><?php echo _WOP_SHOP_INTERM_BANK ?></legend>
    <table class="admintable" width="100%" >
    <tr>
     <td class="key">
       <?php echo _WOP_SHOP_INTERM_NAME;?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox" name="interm_name" value="<?php echo $vendor->interm_name?>" />
     </td>
    </tr>
    <tr>
     <td class="key">
       <?php echo _WOP_SHOP_INTERM_SWIFT;?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox" name="interm_swift" value="<?php echo $vendor->interm_swift?>" />
     </td>
    </tr>
    </table>
</fieldset>
</div>
<div class="clr"></div>

<div class="col100">
<fieldset class="adminform">
    <table class="admintable" width="100%" >
    <tr>
     <td class="key">
       <?php echo _WOP_SHOP_IDENTIFICATION_NUMBER;?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox" name="identification_number" value="<?php echo $vendor->identification_number?>" />
     </td>
    </tr>
    <tr>
     <td class="key">
       <?php echo _WOP_SHOP_TAX_NUMBER;?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox" name="tax_number" value="<?php echo $vendor->tax_number?>" />
     </td>
    </tr>
    <tr>
     <td class="key">
       <?php echo _WOP_SHOP_ADDITIONAL_INFORMATION;?>
     </td>
     <td>
        <textarea rows="5" cols="55" name="additional_information"><?php echo $vendor->additional_information?></textarea>
     </td>
    </tr>
    <?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>
    </table>
</fieldset>
</div>
<div class="clr"></div>
</div>

<br>    
<div clas="submit">
    <p class="submit">
        <input class="button button-primary" type="submit" value="<?php echo _WOP_SHOP_ACTION_SAVE; ?>" name="submit">
        <a class="button" href="admin.php?page=options&tab=vendors"><?php echo _WOP_SHOP_BACK; ?></a>
    </p> 
</div>
    <input type="hidden" value="<?php echo $vendor->id; ?>" name="id">
</form>
