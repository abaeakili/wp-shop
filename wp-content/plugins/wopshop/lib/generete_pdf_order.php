<?php
/**
* @version      1.0.0 01.06.2016
* @author       MAXXmarketing GmbH
* @package      WOPshop
* @copyright    Copyright (C) 2010 http://www.wop-agentur.com. All rights reserved.
* @license      GNU/GPL
*/
include("pdf_config.php");
include("tcpdf/tcpdf.php");

class JorderPDF extends TCPDF{
    
    var $pdfcolors = array(array(0,0,0), array(200,200,200), array(155,155,155));
    
    function addNewPage(){
        $this->addPage();
        $this->addTitleHead();
    }
    
	function addTitleHead(){
		$config = Factory::getConfig();
        $vendorinfo = $this->_vendorinfo;
		$this->Image($config->path.'/assets/images/header.jpg',1,1,$config->pdf_header_width,$config->pdf_header_height);
		$this->Image($config->path.'/assets/images/footer.jpg',1,265,$config->pdf_footer_width,$config->pdf_footer_height);
        $this->SetFont('freesans','',8);
        $this->SetXY(115,12);
        $this->SetTextColor($this->pdfcolors[2][0], $this->pdfcolors[2][1], $this->pdfcolors[2][2]);
        $_vendor_info = array();
        $_vendor_info[] = $vendorinfo->adress;
        $_vendor_info[] = $vendorinfo->zip." ".$vendorinfo->city;
        if ($vendorinfo->phone) $_vendor_info[] = _WOP_SHOP_CONTACT_PHONE.": ".$vendorinfo->phone;
        if ($vendorinfo->fax) $_vendor_info[] = _WOP_SHOP_CONTACT_FAX . ": ".$vendorinfo->fax;
        if ($vendorinfo->email) $_vendor_info[] = _WOP_SHOP_EMAIL.": ".$vendorinfo->email;
        do_action_ref_array('onBeforeAddTitleHead', array(&$vendorinfo, &$pdf, &$_vendor_info, &$this));
        $str_vendor_info = implode("\n",$_vendor_info);
        $this->MultiCell(80, 3, $str_vendor_info, 0, 'R');
        $this->SetTextColor($this->pdfcolors[0][0], $this->pdfcolors[0][1], $this->pdfcolors[0][2]);
	}
}

function generatePDF($order){
    
    $config = Factory::getConfig();
    $vendorinfo = $order->getVendorInfo();
    if ($order->user_id){
        $user = Factory::getTable('usershop');
        $user->load($order->user_id);        
    }
    
    $pdf = new JorderPDF();

    do_action_ref_array('onBeforeCreatePdfOrder', array(&$order, &$vendorinfo, &$pdf));
    
    $pdf->_vendorinfo = $vendorinfo;
    $pdf->SetFont('freesans','',8);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetMargins(0,0,0);
	$pdf->addNewPage();
    $pdf->SetAutoPageBreak(false);
    
    $y = 55;
	$pdf->SetXY(20,$y);
	$pdf->setfontsize(6);
	$pdf->SetTextColor($pdf->pdfcolors[0][0], $pdf->pdfcolors[0][1], $pdf->pdfcolors[0][2]);
	$pdf->MultiCell(80,3, $vendorinfo->company_name.", ".$vendorinfo->adress.", ".$vendorinfo->zip." ".$vendorinfo->city,0,'L');
	    
	$pdf->SetXY(110, $y);
	$pdf->SetFont('freesansb','',11);
	$pdf->SetTextColor($pdf->pdfcolors[0][0], $pdf->pdfcolors[0][1], $pdf->pdfcolors[0][2]);
	$pdf->MultiCell(80,3,_WOP_SHOP_EMAIL_BILL,0,'R');
	
    $y+=10;
	$pdf->SetFont('freesans','',11);
	$pdf->SetXY(20,$y);
    $pdf->MultiCell(80,4.5,$order->firma_name."\n".$order->f_name." ".$order->l_name." ".$order->m_name."\n".$order->street." ".$order->home." ".$order->apartment."\n".$order->zip." ".$order->city."\n".$order->country, 0,'L');
	
	$pdf->SetFont('freesansi','',11);
	$pdf->SetXY(110,$y);
	$pdf->MultiCell(80,4.5,_WOP_SHOP_ORDER_SHORT_NR." ".$order->order_number."\n"._WOP_SHOP_ORDER_FROM." ".$order->order_date,0,'R');
    if ($config->date_invoice_in_invoice){
        $y+=12;
        $pdf->SetXY(110,$y);
        $pdf->MultiCell(80,4.5,_WOP_SHOP_INVOICE_DATE." ".strftime($config->store_date_format, strtotime($order->invoice_date)), 0, 'R');
    }
    if ($config->user_number_in_invoice && $order->user_id && $user->number){
        $y+=11;
        $pdf->SetXY(110,$y);
        $pdf->MultiCell(80,4.5,_WOP_SHOP_USER_NUMBER." ".$user->number, 0, 'R');
    }

	$pdf->SetDrawColor($pdf->pdfcolors[0][0], $pdf->pdfcolors[0][1], $pdf->pdfcolors[0][2]);
	$pdf->SetFont('freesans','',7);
    
    if ( $vendorinfo->identification_number){
        $pdf->SetXY(115,102);
        $pdf->MultiCell(35, 4, _WOP_SHOP_IDENTIFICATION_NUMBER, 1, 'L');
        $pdf->SetXY(150,102);
        $pdf->MultiCell(40, 4, $vendorinfo->identification_number, 1, 'R');
    }
    if ($vendorinfo->tax_number){
        $pdf->SetXY(115,106);
        $pdf->MultiCell(35, 4, _WOP_SHOP_TAX_NUMBER, 1, 'L');
        $pdf->SetXY(150,106);
        $pdf->MultiCell(40, 4, $vendorinfo->tax_number, 1, 'R');
    }
    
    $width_filename	= 65;
    if (!$config->show_product_code_in_order) $width_filename = 87;
	$pdf->setfillcolor($pdf->pdfcolors[1][0], $pdf->pdfcolors[1][1], $pdf->pdfcolors[1][2]);
	$pdf->Rect(20,116,170,4,'F');
	$pdf->SetFont('freesansb','',7.5);
	$pdf->SetXY(20,116);
	$pdf->MultiCell($width_filename, 4, _WOP_SHOP_NAME_PRODUCT, 1, 'L');
    
    if ($config->show_product_code_in_order){
        $pdf->SetXY(85,116);
        $pdf->MultiCell(22, 4, _WOP_SHOP_EAN_PRODUCT, 1, 'L');
    }
    
    $pdf->SetXY(107,116);
    $pdf->MultiCell(18, 4, _WOP_SHOP_QUANTITY, 1, 'L');
    
    $pdf->SetXY(125,116);
    $pdf->MultiCell(25, 4, _WOP_SHOP_SINGLEPRICE, 1, 'L');
	$pdf->SetXY(150,116);
	$pdf->MultiCell(40, 4,_WOP_SHOP_TOTAL, 1,'R');
    	
    $y = 120;
	foreach($order->products as $prod){
    
        $pdf->SetFont('freesans','',7);
        $pdf->SetXY(20, $y + 2);
        $pdf->MultiCell($width_filename, 4, $prod->product_name, 0, 'L');
        if ($prod->manufacturer!=''){
            $pdf->SetXY(20, $pdf->getY());
            $pdf->MultiCell($width_filename, 4, _WOP_SHOP_MANUFACTURER.": ".$prod->manufacturer, 0, 'L');
        }
        if ($prod->product_attributes!="" || $prod->product_freeattributes!="" || $prod->delivery_time || $prod->extra_fields!=''){
            if ($prod->delivery_time){
                $pdt = _WOP_SHOP_DELIVERY_TIME.": ".$prod->delivery_time;
            }else{
                $pdt = "";
            }
            $pdf->SetXY(23, $pdf->getY());
            $pdf->SetFont('freesans','',6);
            $attribute = sprintAtributeInOrder($prod->product_attributes, "pdf");
            $attribute .= sprintFreeAtributeInOrder($prod->product_freeattributes, "pdf");
            $attribute .= sprintExtraFiledsInOrder($prod->extra_fields,"pdf");
            $attribute .= $prod->_ext_attribute;
            $attribute .= $pdt;
            $pdf->MultiCell(62, 4, $attribute, 0, 'L');
            $pdf->SetFont('freesans','',7);
        }
        $y2 = $pdf->getY() + 2;
        
        if ($config->show_product_code_in_order){
            $pdf->SetXY(85, $y + 2);
            $pdf->MultiCell(22, 4, $prod->product_ean, 0, 'L');
            $y3 = $pdf->getY() + 2;
        }else{
            $y3 = $pdf->getY();
        }
        
        $pdf->SetXY(107, $y + 2);
        $pdf->MultiCell(18, 4, formatqty($prod->product_quantity).$prod->_qty_unit, 0 , 'L');
        $y4 = $pdf->getY() + 2;
        
        $pdf->SetXY(125, $y + 2);
        $pdf->MultiCell(25, 4, formatprice($prod->product_item_price, $order->currency_code, 0, -1), 0 , 'L');
        
        if ($prod->_ext_price){
           $pdf->SetXY(125, $pdf->getY());
           $pdf->MultiCell(25, 4, $prod->_ext_price, 0 , 'R');
        }

        if ($config->show_tax_product_in_cart && $prod->product_tax>0){
            $pdf->SetXY(125, $pdf->getY());
            $pdf->SetFont('freesans','',6);
            $text = productTaxInfo($prod->product_tax, $order->display_price);
            $pdf->MultiCell(25, 4, $text, 0 , 'L');
        }
        if ($config->cart_basic_price_show && $prod->basicprice>0){
            $pdf->SetXY(125, $pdf->getY());
            $pdf->SetFont('freesans','',6);
            $text = _WOP_SHOP_BASIC_PRICE.": ".sprintBasicPrice($prod);
            $pdf->MultiCell(25, 4, $text, 0 , 'L');            
        }
        $y5 = $pdf->getY() + 2;
        
        $pdf->SetFont('freesans','',7);
        $pdf->SetXY(150, $y + 2);
        $pdf->MultiCell(40, 4, formatprice($prod->product_quantity * $prod->product_item_price, $order->currency_code, 0, -1), 0 , 'R');
        
        if ($prod->_ext_price_total){           
           $pdf->SetXY(150, $pdf->getY());
           $pdf->MultiCell(40, 4, $prod->_ext_price_total, 0 , 'R');
        }
        
        if ($config->show_tax_product_in_cart && $prod->product_tax>0){
            $pdf->SetXY(150, $pdf->getY());
            $pdf->SetFont('freesans','',6);
            $text = productTaxInfo($prod->product_tax, $order->display_price);
            $pdf->MultiCell(40, 4, $text, 0 , 'R');
        }
        $y6 = $pdf->getY() + 2;
        
        $yn = max($y2, $y3, $y4, $y5, $y6);
        
        $pdf->Rect(20, $y, 170, $yn - $y );
        $pdf->Rect(20, $y, 130, $yn - $y );
        
        if ($config->show_product_code_in_order){
            $pdf->line(85, $y, 85, $yn);
        }
        $pdf->line(107, $y, 107, $yn);
        $pdf->line(125, $y, 125, $yn);
        
        $y = $yn; 
        
        if ($y > 260){
            $pdf->addNewPage();
            $y = 60;
        }
	}
    
	if ($y > 240){
        $pdf->addNewPage();
        $y = 60;
    }
	
	$pdf->SetFont('freesans','',10);
    
    if (($config->hide_tax || count($order->order_tax_list)==0) && $order->order_discount==0 && $order->order_payment==0 && $config->without_shipping) $hide_subtotal = 1; else $hide_subtotal = 0;
		
    if (!$hide_subtotal){
	    $pdf->SetXY(20,$y);
	    $pdf->Rect(20,$y,170,5,'F');
	    $pdf->MultiCell(130,5,_WOP_SHOP_SUBTOTAL,'1','R');	
	    $pdf->SetXY(150,$y);	
	    $pdf->MultiCell(40,5,formatprice($order->order_subtotal, $order->currency_code, 0, -1).$order->_pdf_ext_subtotal,'1','R');
    }else{
        $y = $y - 5;
    }
    
    do_action_ref_array('onGeneratePdfOrderAfterSubtotal', array(&$order, &$pdf, &$y, &$hide_subtotal));
    
    if ($order->order_discount > 0){
        $y = $y + 5;     
        $pdf->SetXY(20,$y);
        $pdf->Rect(20,$y,170,5,'F');
        $pdf->MultiCell(130,5,_WOP_SHOP_RABATT_VALUE.$order->_pdf_ext_discount_text,'1','R');
        $pdf->SetXY(150,$y);
        $pdf->MultiCell(40,5, "-".formatprice($order->order_discount, $order->currency_code, 0, -1).$order->_pdf_ext_discount,'1','R');       
    }
	
    if (!$config->without_shipping){
	    $pdf->SetXY(20,$y + 5);
	    $pdf->Rect(20,$y + 5,170,5,'F');
	    $pdf->MultiCell(130,5,_WOP_SHOP_SHIPPING_PRICE,'1','R');
	    $pdf->SetXY(150,$y + 5);
	    $pdf->MultiCell(40,5,formatprice($order->order_shipping, $order->currency_code, 0, -1).$order->_pdf_ext_shipping,'1','R');
        if ($order->order_package>0 || $config->display_null_package_price){
            $y=$y+5;
            $pdf->SetXY(20,$y + 5);
            $pdf->Rect(20,$y + 5,170,5,'F');
            $pdf->MultiCell(130,5,_WOP_SHOP_PACKAGE_PRICE,'1','R');
            $pdf->SetXY(150,$y + 5);
            $pdf->MultiCell(40,5,formatprice($order->order_package, $order->currency_code, 0, -1).$order->_pdf_ext_shipping_package,'1','R');
        }
    }else{
        $y = $y - 5;
    }
    
    if ($order->order_payment != 0){
        $y = $y + 5;     
        $pdf->SetXY(20,$y+5);
        $pdf->Rect(20,$y+5,170,5,'F');
        $pdf->MultiCell(130,5, $order->payment_name,'1','R');
        $pdf->SetXY(150,$y+5);
        $pdf->MultiCell(40,5, formatprice($order->order_payment, $order->currency_code, 0, -1).$order->_pdf_ext_payment, '1','R');
    }
        
    $show_percent_tax = 0;        
    if (count($order->order_tax_list)>1 || $config->show_tax_in_product) $show_percent_tax = 1;
    if ($config->hide_tax) $show_percent_tax = 0;
	
	do_action_ref_array('onBeforeCreatePdfOrderBeforeEndTotal', array(&$order, &$pdf, &$y));
	
    if (!$config->hide_tax){
        foreach($order->order_tax_list as $percent=>$value){
	        $pdf->SetXY(20,$y + 10);
	        $pdf->Rect(20,$y + 10,170,5,'F');
            $text = displayTotalCartTaxName($order->display_price);
            if ($show_percent_tax) $text = $text." ".formattax($percent)."%";
	        $pdf->MultiCell(130,5,$text ,'1','R');        
            $pdf->SetXY(150,$y + 10);
            $pdf->MultiCell(40,5,formatprice($value, $order->currency_code, 0, -1).$order->_pdf_ext_tax[$percent],'1','R');
            $y = $y + 5;
        }
    }
    
    $text_total = _WOP_SHOP_ENDTOTAL;
    if (($config->show_tax_in_product || $config->show_tax_product_in_cart) && (count($order->order_tax_list)>0)){
        $text_total = _WOP_SHOP_ENDTOTAL_INKL_TAX;
    }
    
    $pdf->SetFont('freesansb','',10);
	$pdf->SetXY(20,$y + 10);
	$pdf->Rect(20,$y + 10,170, 5.1,'F');
	$pdf->MultiCell(130, 5 , $text_total,'1','R');
	
	$pdf->SetXY(150,$y + 10);
	$pdf->MultiCell(40,5,formatprice($order->order_total, $order->currency_code, 0, -1).$order->_pdf_ext_total,'1','R');
    if ($config->display_tax_id_in_pdf && $order->tax_number){
        $y = $y+5.2;
        $pdf->SetFont('freesans','',7);
        $pdf->SetXY(20,$y + 10);        
        $pdf->MultiCell(170, 4 , _WOP_SHOP_TAX_NUMBER.": ".$order->tax_number,'1','L');
    }
    
    do_action_ref_array('onBeforeCreatePdfOrderAfterEndTotal', array(&$order, &$pdf, &$y));
    
    $y = $y + 10; 
    
    if ($config->show_delivery_time_checkout && ($order->delivery_times_id || $order->delivery_time)){
        if ($y > 250){ $pdf->addNewPage(); $y = 60; }
        $deliverytimes = Factory::getAllDeliveryTime();
        $delivery = $deliverytimes[$order->delivery_times_id];
        if ($delivery==""){
            $delivery = $order->delivery_time;
        }
        $y = $y+8;
        $pdf->SetFont('freesans','',7);
        $pdf->SetXY(20, $y);
        $pdf->MultiCell(170, 4, _WOP_SHOP_ORDER_DELIVERY_TIME.": ".$delivery, '0','L');
    }
    
    if ($config->show_delivery_date && !datenull($order->delivery_date)){
        if ($y > 250){ $pdf->addNewPage(); $y = 60; }
        $delivery_date_f = formatdate($order->delivery_date); 
        $y = $y+6;
        $pdf->SetFont('freesans','',7);
        $pdf->SetXY(20, $y);
        $pdf->MultiCell(170, 4, _WOP_SHOP_DELIVERY_DATE.": ".$delivery_date_f, '0','L');
    }
    
    if ($config->weight_in_invoice){
        if ($y > 250){ $pdf->addNewPage(); $y = 60; }
        $y = $y+6;
        $pdf->SetFont('freesans','',7);
        $pdf->SetXY(20, $y);
        $pdf->MultiCell(170, 4 , _WOP_SHOP_WEIGHT_PRODUCTS.": ".formatweight($order->weight), '0','L');
    }
    
    if (!$config->without_payment && $config->payment_in_invoice){
        if ($y > 240){ $pdf->addNewPage(); $y = 60; }
        $y = $y+6;
        $pdf->SetFont('freesansb','',7);
        $pdf->SetXY(20, $y);
        $pdf->MultiCell(170, 4, _WOP_SHOP_PAYMENT_INFORMATION, '0','L');
        
        $y = $y+4;
        $pdf->SetFont('freesans','',7);
        $pdf->SetXY(20, $y);
        $pdf->MultiCell(170, 4, $order->payment_name, '0','L');
        $payment_descr = trim(trim($order->payment_information)."\n".$order->payment_description);
        if ($payment_descr!=''){
            $y = $y+4;
            $pdf->SetXY(20, $y);
            $pdf->MultiCell(170, 4,  strip_tags($payment_descr), '0','L');
            $y = $pdf->getY()-4;
        }        
    }
    
    if (!$config->without_shipping && $config->shipping_in_invoice){
        if ($y > 250){ $pdf->addNewPage(); $y = 60; }
        $y = $y+6;
        $pdf->SetFont('freesansb','',7);
        $pdf->SetXY(20, $y);
        $pdf->MultiCell(170, 4, _WOP_SHOP_SHIPPING_INFORMATION, '0','L');
        
        $y = $y+4;
        $pdf->SetFont('freesans','',7);
        $pdf->SetXY(20, $y);
        $pdf->MultiCell(170, 4, $order->shipping_information, '0','L');
    }
	
	$y = $y + 20;
    if ($y > 240){
        $pdf->addNewPage();
        $y = 60;
    }
    
	$pdf->SetFont('freesans','',7);
    
    $show_bank_in_order = 1;
    $order_description = '';
    if ($order->payment_method_id){
        $pm_method = Factory::getTable('paymentMethod');
        $pm_method->load($order->payment_method_id);
        $show_bank_in_order = $pm_method->show_bank_in_order;
        $order_description = $pm_method->order_description;
    }
    
    if ($order_description){
        $pdf->SetXY(20, $y);
        $pdf->MultiCell(170,4, $order_description,'0','L');
        $y = $pdf->getY();
    }
    
    $y2 = 0;
    if ($show_bank_in_order){
    if ($vendorinfo->benef_bank_info || $vendorinfo->benef_bic || $vendorinfo->benef_conto || $vendorinfo->benef_payee || $vendorinfo->benef_iban || $vendorinfo->benef_swift){
	    $pdf->SetXY(115, $y);
	    $pdf->Rect(115, $y, 75,4,'F');
	    $pdf->MultiCell(75,4,_WOP_SHOP_BANK,'1','L');
    }
    
    if ($vendorinfo->benef_bank_info){
        $y2 += 4;
	    $pdf->SetXY(115, $y2 + $y);
	    $pdf->MultiCell(75,4,_WOP_SHOP_BENEF_BANK_NAME,'1','L');
    }
    
    if ($vendorinfo->benef_bic){
        $y2 += 4;
	    $pdf->SetXY(115, $y2 + $y);
	    $pdf->MultiCell(75,4,_WOP_SHOP_BENEF_BIC,'1','L');
    }
    
    if ($vendorinfo->benef_conto){
        $y2 += 4;
	    $pdf->SetXY(115, $y2 + $y);
	    $pdf->MultiCell(75,4,_WOP_SHOP_BENEF_CONTO,'1','L');
    }
    
    if ($vendorinfo->benef_payee){
        $y2 += 4;
        $pdf->SetXY(115, $y2 + $y);
        $pdf->MultiCell(75,4,_WOP_SHOP_BENEF_PAYEE,'1','L');
    }
	
    if ($vendorinfo->benef_iban){
        $y2 += 4;
	    $pdf->SetXY(115, $y2 + $y);
	    $pdf->MultiCell(75,4,_WOP_SHOP_BENEF_IBAN,'1','L');
    }
    
    if ($vendorinfo->benef_bic_bic){
        $y2 += 4;
        $pdf->SetXY(115, $y2 + $y);
        $pdf->MultiCell(75,4,_WOP_SHOP_BIC_BIC,'1','L');
    }
	
    if ($vendorinfo->benef_swift){
        $y2 += 4;
	    $pdf->SetXY(115, $y2 + $y);
	    $pdf->MultiCell(75,4,_WOP_SHOP_BENEF_SWIFT,'1','L');
    }
	
    if ($vendorinfo->interm_name || $vendorinfo->interm_swift){
        $y2 += 4;
	    $pdf->Rect(115,$y2 + $y,75,4,'F');
	    $pdf->SetXY(115, $y2 + $y);
	    $pdf->MultiCell(75,4,_WOP_SHOP_INTERM_BANK,'1','L');
    }
	
    if ($vendorinfo->interm_name){
        $y2 += 4;
	    $pdf->SetXY(115, $y2 + $y);
	    $pdf->MultiCell(75,4,_WOP_SHOP_INTERM_NAME,'1','L');
    }
	
    if ($vendorinfo->interm_swift){
        $y2 += 4;
	    $pdf->SetXY(115, $y2 + $y);
	    $pdf->MultiCell(75,4,_WOP_SHOP_INTERM_SWIFT,'1','L');
    }
    
    
    $y2 = 0;
    if ($vendorinfo->benef_bank_info){
        $y2 += 4;
        $pdf->SetXY(115, $y2 + $y);
        $pdf->MultiCell(75,4,$vendorinfo->benef_bank_info,'0','R');
    }
    
    if ($vendorinfo->benef_bic){
	    $y2 += 4;
        $pdf->SetXY(115, $y2 + $y);
	    $pdf->MultiCell(75,4,$vendorinfo->benef_bic,'0','R');
    }
	
    if ($vendorinfo->benef_conto){
	    $y2 += 4;
        $pdf->SetXY(115, $y2 + $y);
	    $pdf->MultiCell(75,4,$vendorinfo->benef_conto,'0','R');
    }
	
    if ($vendorinfo->benef_payee){
        $y2 += 4;
        $pdf->SetXY(115, $y2 + $y);
        $pdf->MultiCell(75,4,$vendorinfo->benef_payee,'0','R');
    }
    
    if ($vendorinfo->benef_iban){
	    $y2 += 4;
        $pdf->SetXY(115, $y2 + $y);
	    $pdf->MultiCell(75,4,$vendorinfo->benef_iban,'0','R');
    }
    
    if ($vendorinfo->benef_bic_bic){
        $y2 += 4;
        $pdf->SetXY(115, $y2 + $y);
        $pdf->MultiCell(75,4,$vendorinfo->benef_bic_bic,'0','R');
    }
	
    if ($vendorinfo->benef_swift){
	    $y2 += 4;
        $pdf->SetXY(115, $y2 + $y);
	    $pdf->MultiCell(75,4,$vendorinfo->benef_swift,'0','R');
    }
	
    $y2 += 4;
	if ($vendorinfo->interm_name){
	    $y2 += 4;
        $pdf->SetXY(115, $y2 + $y);
	    $pdf->MultiCell(75,4,$vendorinfo->interm_name,'0','R');
    }
	
    if ($vendorinfo->interm_swift){
	    $y2 += 4;
        $pdf->SetXY(115, $y2 + $y);
	    $pdf->MultiCell(75,4,$vendorinfo->interm_swift,'0','R');
    }
    }
        
    if ($vendorinfo->additional_information){
        if ($y2 + $y > 240){
            $pdf->addNewPage();
            $y = 50;
            $y2 = 0;
        }
        $y2 += 6;
        $pdf->SetXY(20, $y2 + $y);
        $pdf->MultiCell(170,4,$vendorinfo->additional_information,'0','L');
    }
    
    if ($config->show_return_policy_text_in_pdf){
        $pdf->SetAutoPageBreak(1);
        $y = $pdf->getY();
        if ($y>240){
            $pdf->addNewPage();
            $y = 50;
        }
        $list = $order->getReturnPolicy();
        $listtext = array();
        foreach($list as $v){
            $listtext[] = $v->text;
        }
        $rptext = implode("\n\n", $listtext);
        $rptext = strip_tags($rptext);
        do_action_ref_array('onBeforeCreatePdfOrderRPText', array(&$order, &$pdf, &$rptext));
        $y += 6;
        $pdf->SetXY(20, $y);
        $pdf->MultiCell(170,4,$rptext,'0','L');
        $pdf->SetAutoPageBreak(false);
    }
    	
    if ($order->pdf_file!=''){
        $name_pdf = $order->pdf_file;
    }else{
        $name_pdf = $order->order_id."_".md5(uniqid(rand(0,100))).".pdf";
    }
    do_action_ref_array('onBeforeCreatePdfOrderEnd', array(&$order, &$pdf, &$name_pdf));
	$pdf->Output($config->pdf_orders_path."/".$name_pdf ,'F');
	return $name_pdf;
}
?>