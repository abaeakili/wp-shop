<?php 
/**
* @version      1.0.0 30.01.2017
* @author       MAXXmarketing GmbH
* @package      WOPshop
* @copyright    Copyright (C) 2010 http://www.wop-agentur.com. All rights reserved.
* @license      GNU/GPL
*/
//defined('_JEXEC') or die('Restricted access');

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="wshop vendordetailinfo" id="wshop_plugin">
    <?php if ($this->header) : ?>
        <h1><?php print $this->header ?></h1>
    <?php endif; ?>
    
    <div class = "row-fluid">
        <div class = "span6">
            <table class="vendor_info">
            <tr>
                <td class="name">
                    <?php print _WOP_SHOP_F_NAME ?>: 
                </td>
                <td>
                    <?php print $this->vendor->f_name ?>
                </td>
            </tr>

            <tr>
                <td class="name">
                    <?php print _WOP_SHOP_L_NAME ?>:
                </td>
                <td>
                    <?php print $this->vendor->l_name ?>
                </td>
            </tr>        
            <tr>
                <td class="name">
                    <?php print _WOP_SHOP_FIRMA_NAME ?>:
                </td>
                <td>
                    <?php print $this->vendor->company_name ?>
                </td>
            </tr>

            <tr>
                <td class="name">
                    <?php print _WOP_SHOP_EMAIL ?>:
                </td>
                <td>
                    <?php print $this->vendor->email ?>
                </td>
            </tr>        
            <tr>
                <td  class="name">
                    <?php print _WOP_SHOP_STREET_NR ?>:
                </td>
                <td>
                    <?php print $this->vendor->adress ?>
                </td>
            </tr>

            <tr>
                <td class="name">
                    <?php print _WOP_SHOP_ZIP ?>:
                </td>
                <td>
                    <?php print $this->vendor->zip ?>
                </td>
            </tr>        
            <tr>
                <td class="name">
                    <?php print _WOP_SHOP_CITY ?>:
                </td>
                <td>
                    <?php print $this->vendor->city ?>
                </td>
            </tr>        
            <tr>
                <td class="name">
                    <?php print _WOP_SHOP_STATE ?>:
                </td>
                <td>
                    <?php print $this->vendor->state ?>
                </td>
            </tr>

            <tr>
                <td class="name">
                    <?php print _WOP_SHOP_COUNTRY ?>:
                </td>
                <td>
                    <?php print $this->vendor->country ?>
                </td>
            </tr>

            <tr>
                <td class="name">
                    <?php print _WOP_SHOP_TELEFON ?>:
                </td>
                <td>
                    <?php print $this->vendor->phone ?>
                </td>
            </tr>
            
            <tr>
                <td class="name">
                    <?php print _WOP_SHOP_FAX ?>:
                </td>
                <td>
                    <?php print $this->vendor->fax ?>
                </td>
            </tr>
            </table>
        </div>
        <div class = "span6 vendor_logo">
            <?php if ($this->vendor->logo!="") : ?>
                <img src="<?php print $this->vendor->logo?>" alt="<?php print htmlspecialchars($this->vendor->shop_name);?>" />
            <?php endif; ?>
        </div>
    </div>
</div>    