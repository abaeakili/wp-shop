<?php

class pm_debit extends PaymentRoot{
    
    function showPaymentForm($params, $pmconfigs){
        if (!isset($params['acc_holder'])) $params['acc_holder'] = '';
        if (!isset($params['bank_iban'])) $params['bank_iban'] = '';
        if (!isset($params['bank_bic'])) $params['bank_bic'] = '';
        if (!isset($params['bank'])) $params['bank'] = '';
    	include(dirname(__FILE__)."/paymentform.php");
    }

    function getDisplayNameParams(){
        $names = array('acc_holder' => _WOP_SHOP_ACCOUNT_HOLDER, 'bank_iban' => _WOP_SHOP_IBAN, 'bank_bic' => _WOP_SHOP_BIC_BIC, 'bank' => _WOP_SHOP_BANK );
        return $names;
    }
}
?>