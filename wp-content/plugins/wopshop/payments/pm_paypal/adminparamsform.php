<?php
/**
* @version      1.0.0 01.06.2016
* @author       MAXXmarketing GmbH
* @package      WOPshop
* @copyright    Copyright (C) 2010 http://www.wop-agentur.com. All rights reserved.
* @license      GNU/GPL
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>

<div class="col100">
    <fieldset class="adminform">
        <table class="admintable" width = "100%" >
            <tr>
                <td style="width:250px;" class="key">
                    <?php echo _WOP_SHOP_TESTMODE; ?>
                </td>
                <td>
                    <?php
                    echo HTML::_('select.booleanlist', 'pm_params[testmode]', 'class = "inputbox" size = "1"', $params['testmode']);
                    echo " " . HTML::tooltip(_WOP_SHOP_PAYPAL_TESTMODE_DESCRIPTION);
                    ?>
                </td>
            </tr>
            <tr>
                <td  class="key">
                    <?php echo _WOP_SHOP_PAYPAL_EMAIL; ?>
                </td>
                <td>
                    <input type = "text" class = "inputbox" name = "pm_params[email_received]" size="45" value = "<?php echo $params['email_received'] ?>" />
                    <?php echo HTML::tooltip(_WOP_SHOP_PAYPAL_EMAIL_DESCRIPTION); ?>
                </td>
            </tr>
            <tr>
                <td class="key">
                    <?php echo _WOP_SHOP_TRANSACTION_END; ?>
                </td>
                <td>
                    <?php
                    echo HTML::_('select.genericlist', $orders->getAllOrderStatus(), 'pm_params[transaction_end_status]', 'class = "inputbox" size = "1"', 'status_id', 'name', $params['transaction_end_status']);
                    echo " " . HTML::tooltip(_WOP_SHOP_PAYPAL_TRANSACTION_END_DESCRIPTION);
                    ?>
                </td>
            </tr>
            <tr>
                <td class="key">
                    <?php echo _WOP_SHOP_TRANSACTION_PENDING; ?>
                </td>
                <td>
                    <?php
                    echo HTML::_('select.genericlist', $orders->getAllOrderStatus(), 'pm_params[transaction_pending_status]', 'class = "inputbox" size = "1"', 'status_id', 'name', $params['transaction_pending_status']);
                    echo " " . HTML::tooltip(_WOP_SHOP_PAYPAL_TRANSACTION_PENDING_DESCRIPTION);
                    ?>
                </td>
            </tr>
            <tr>
                <td class="key">
                    <?php echo _WOP_SHOP_TRANSACTION_FAILED; ?>
                </td>
                <td>
                    <?php
                    echo HTML::_('select.genericlist', $orders->getAllOrderStatus(), 'pm_params[transaction_failed_status]', 'class = "inputbox" size = "1"', 'status_id', 'name', $params['transaction_failed_status']);
                    echo " " . HTML::tooltip(_WOP_SHOP_PAYPAL_TRANSACTION_FAILED_DESCRIPTION);
                    ?>
                </td>
            </tr>
            <tr>
                <td class="key">
                    <?php echo _WOP_SHOP_CHECK_DATA_RETURN; ?>
                </td>
                <td>
                    <?php echo HTML::_('select.booleanlist', 'pm_params[checkdatareturn]', 'class = "inputbox"', $params['checkdatareturn']); ?>
                </td>
            </tr>
            <tr>
                <td class="key">
                    <?php echo _WOP_SHOP_OVERRIDING_ADDRESSES ?>
                </td>
                <td>
                    <?php echo HTML::_('select.booleanlist', 'pm_params[address_override]', 'class = "inputbox"', $params['address_override']); ?>
                </td>
            </tr>
            <tr>
                <td class="key">
                    <?php echo _WOP_SHOP_SSL_VERSION ?>
                </td>
                <td>
                    <?php echo HTML::_('select.genericlist', $ssl_options, 'pm_params[CURLOPT_SSLVERSION]', 'class = "inputbox"', 'id', 'name', $params['CURLOPT_SSLVERSION']); ?>
                </td>
            </tr>
        </table>
    </fieldset>
</div>
<div class="clr"></div>