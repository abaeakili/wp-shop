<?php 
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

echo $this->tmp_html_start;
?>
<div class="wshop-content wshop-info">
    <table width="100%" class="wshop-table">
        <tr>
            <td width="50%" valign="top" style="padding:10px;">
                <p style="margin-top:0px;">Anschrift und andere Angaben zum Unternehmen:<br>
                    <br>
                    <strong>MAXX <em>marketing GmbH</em></strong>
                    <br>Englschalkinger Str. 224<br>
                    D-81927 München<br><br>
                    Tel: +49 (0)89 - 929286-0<br>
                    Fax:+49 (0)89 - 929286-75<br>
                    eMail: <strong><a class="link" href="mailto:info@wop-agentur.com">info@wop-agentur.com</a></strong><br><br>
                </p>
                <p><strong>Steueridentifikationsnummer:<br></strong>
                    DE221510498<br><br>
                    <strong>Umsatzsteuer Nummer:<br></strong>
                    143/160/40099
                    <br><br>
                </p>
                <p>
                    <strong>Geschaftsfuhrer:</strong> 
                    <br>Klaus Huber
                </p>
            </td>
            <td valign="top" style="padding:10px;">
                <div style="padding-bottom:20px;">
                    <img src="<?php echo WOPSHOP_PLUGIN_URL.'assets/images/logo.png'?>" />
                    <div style="padding-top:5px;font-size:14px;"><b>Version <?php echo $this->version; ?></b></div>
                </div>
                <div class="info-row clearfix">
                    <span class="glyphicon glyphicon-globe" aria-hidden="true"></span>
                    <div><a href="http://www.wop-agentur.com/" target="_blank">www.wop-agentur.com</a></div>
                </div>
                <div class="info-row clearfix">
                    <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>
                    <div><a href="mailto:info@wop-agentur.com">info@wop-agentur.com</a></div>
                </div>
                <div class="info-row clearfix">
                    <span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span>
                    <div><a href="http://www.wop-agentur.com/forum" target="_blank">Hilfe / Support</a></div>
                </div>
                <div class="info-row clearfix">
                    <span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span>
                    <div><a href="http://www.wop-agentur.com/wopshop/extensions" target="_blank">WOPshop extensions</a></div>
                </div>
            </td>
    </table>
    <?php print $this->tmp_html_end; ?>
</div>