                jQuery(document).ready( function($) {
                    $('#wshop_add_nav_vendorinfo').on('click', function() {
                        var vendorinfo= jQuery('input[name="vendorinfo"]').val();
                        if(!vendorinfo){ alert('Enter vendor info'); return false; }
                        jQuery.ajax({
                            type: "POST",
                            url: 'admin-ajax.php?page=options&tab=menu&action=setmenu',
                            data: 'url='+encodeURIComponent('controller=vendorinfo&task=info'),
                            dataType: 'html',
                            error: function(jqXHR, exception) {if (jqXHR.status === 0) {alert('Not connect.\n Verify Network.');} else if (jqXHR.status == 404) {alert('Requested page not found. [404]');} else if (jqXHR.status == 500) {alert('Internal Server Error [500].');} else if (exception === 'parsererror') {alert('Requested JSON parse failed.');} else if (exception === 'timeout') {alert('Time out error.');} else if (exception === 'abort') {alert('Ajax request aborted.');} else {jQuery(".message-process").html('Query Error');alert('Uncaught Error.\n' + jqXHR.responseText);}},
                            beforeSend:function() {},
                            success: function(html){
                                wpNavMenu.addLinkToMenu(html,'Vendor Info');
                            }
                        });
                        return false;
                    });
                    $('#wshop_add_nav_category').on('click', function() {
                        var name = jQuery(this).text();
                        var url = 'controller=category&task=view';
                        var category_id = jQuery('input[name="category_id"]').val();
                        if(!category_id){ alert('Set Category ID'); return false; }
                        url+='&category_id='+category_id;

                        jQuery.ajax({
                            type: "POST",
                            url: 'admin-ajax.php?page=options&tab=menu&action=setmenu',
                            data: 'url='+encodeURIComponent(url),
                            dataType: 'html',
                            error: function(jqXHR, exception) {if (jqXHR.status === 0) {alert('Not connect.\n Verify Network.');} else if (jqXHR.status == 404) {alert('Requested page not found. [404]');} else if (jqXHR.status == 500) {alert('Internal Server Error [500].');} else if (exception === 'parsererror') {alert('Requested JSON parse failed.');} else if (exception === 'timeout') {alert('Time out error.');} else if (exception === 'abort') {alert('Ajax request aborted.');} else {jQuery(".message-process").html('Query Error');alert('Uncaught Error.\n' + jqXHR.responseText);}},
                            beforeSend:function() {},
                            success: function(html){
                                wpNavMenu.addLinkToMenu(html, name);
                            }
                        });
                        return false;
                    });
                    $('#wshop_add_nav_user').on('click', function() {
                        var url = 'controller=user';
                        var user_id = jQuery('select[name="user"] option:selected').val();
                        if(user_id){ url+='&task='+user_id; }
                        var name = jQuery('select[name="user"] option:selected').text();
                        jQuery.ajax({
                            type: "POST",
                            url: 'admin-ajax.php?page=options&tab=menu&action=setmenu',
                            data: 'url='+encodeURIComponent(url),
                            dataType: 'html',
                            error: function(jqXHR, exception) {if (jqXHR.status === 0) {alert('Not connect.\n Verify Network.');} else if (jqXHR.status == 404) {alert('Requested page not found. [404]');} else if (jqXHR.status == 500) {alert('Internal Server Error [500].');} else if (exception === 'parsererror') {alert('Requested JSON parse failed.');} else if (exception === 'timeout') {alert('Time out error.');} else if (exception === 'abort') {alert('Ajax request aborted.');} else {jQuery(".message-process").html('Query Error');alert('Uncaught Error.\n' + jqXHR.responseText);}},
                            beforeSend:function() {},
                            success: function(html){
                                wpNavMenu.addLinkToMenu(html, name);
                            }
                        });
                        return false;
                    });
                    $('#wshop_add_nav_cart').on('click', function() {
                        var url = 'controller=cart&task=view';
                        var name = jQuery(this).text();
                        jQuery.ajax({
                            type: "POST",
                            url: 'admin-ajax.php?page=options&tab=menu&action=setmenu',
                            data: 'url='+encodeURIComponent(url),
                            dataType: 'html',
                            error: function(jqXHR, exception) {if (jqXHR.status === 0) {alert('Not connect.\n Verify Network.');} else if (jqXHR.status == 404) {alert('Requested page not found. [404]');} else if (jqXHR.status == 500) {alert('Internal Server Error [500].');} else if (exception === 'parsererror') {alert('Requested JSON parse failed.');} else if (exception === 'timeout') {alert('Time out error.');} else if (exception === 'abort') {alert('Ajax request aborted.');} else {jQuery(".message-process").html('Query Error');alert('Uncaught Error.\n' + jqXHR.responseText);}},
                            beforeSend:function() {},
                            success: function(html){
                                wpNavMenu.addLinkToMenu(html, name);
                            }
                        });
                        return false;
                    });
                    $('#wshop_add_nav_wishlist').on('click', function() {
                        var url = 'controller=wishlist&task=view';
                        var name = jQuery(this).text();
                        jQuery.ajax({
                            type: "POST",
                            url: 'admin-ajax.php?page=options&tab=menu&action=setmenu',
                            data: 'url='+encodeURIComponent(url),
                            dataType: 'html',
                            error: function(jqXHR, exception) {if (jqXHR.status === 0) {alert('Not connect.\n Verify Network.');} else if (jqXHR.status == 404) {alert('Requested page not found. [404]');} else if (jqXHR.status == 500) {alert('Internal Server Error [500].');} else if (exception === 'parsererror') {alert('Requested JSON parse failed.');} else if (exception === 'timeout') {alert('Time out error.');} else if (exception === 'abort') {alert('Ajax request aborted.');} else {jQuery(".message-process").html('Query Error');alert('Uncaught Error.\n' + jqXHR.responseText);}},
                            beforeSend:function() {},
                            success: function(html){
                                wpNavMenu.addLinkToMenu(html, name);
                            }
                        });
                        return false;
                    });
                    $('#wshop_add_nav_search').on('click', function() {
                        var url = 'controller=search&task=view';
                        var name = jQuery(this).text();
                         jQuery.ajax({
                             type: "POST",
                             url: 'admin-ajax.php?page=options&tab=menu&action=setmenu',
                             data: 'url='+encodeURIComponent(url),
                             dataType: 'html',
                             error: function(jqXHR, exception) {if (jqXHR.status === 0) {alert('Not connect.\n Verify Network.');} else if (jqXHR.status == 404) {alert('Requested page not found. [404]');} else if (jqXHR.status == 500) {alert('Internal Server Error [500].');} else if (exception === 'parsererror') {alert('Requested JSON parse failed.');} else if (exception === 'timeout') {alert('Time out error.');} else if (exception === 'abort') {alert('Ajax request aborted.');} else {jQuery(".message-process").html('Query Error');alert('Uncaught Error.\n' + jqXHR.responseText);}},
                             beforeSend:function() {},
                             success: function(html){
                                 wpNavMenu.addLinkToMenu(html, name);
                             }
                         });
                         return false;
                    });
                    $('#wshop_add_nav_listmanufacturer').on('click', function() {
                        var name = jQuery(this).text();
                        var url = 'controller=manufacturer&task=display';
                        jQuery.ajax({
                            type: "POST",
                            url: 'admin-ajax.php?page=options&tab=menu&action=setmenu',
                            data: 'url='+encodeURIComponent(url),
                            dataType: 'html',
                            error: function(jqXHR, exception) {if (jqXHR.status === 0) {alert('Not connect.\n Verify Network.');} else if (jqXHR.status == 404) {alert('Requested page not found. [404]');} else if (jqXHR.status == 500) {alert('Internal Server Error [500].');} else if (exception === 'parsererror') {alert('Requested JSON parse failed.');} else if (exception === 'timeout') {alert('Time out error.');} else if (exception === 'abort') {alert('Ajax request aborted.');} else {jQuery(".message-process").html('Query Error');alert('Uncaught Error.\n' + jqXHR.responseText);}},
                            beforeSend:function() {},
                            success: function(html){
                                wpNavMenu.addLinkToMenu(html, name);
                            }
                        });
                        return false;
                    });

                    $('#wshop_add_nav_vendor_id').on('click', function() {
                        var url = 'controller=category&task=view';
                        var vendor_id = jQuery('input[name="vendor_id"]').val();
                        if(!vendor_id){ alert('Set Vendor ID'); return false; }
                        url+='&vendor_id='+vendor_id;
                        var manufacturer_id = jQuery('input[name="manufacturer_id"]').val();
                        if(manufacturer_id){ url+='&manufacturer_id='+manufacturer_id; }
                        var label_id = jQuery('input[name="label_id"]').val();
                        if(label_id){ url+='&label_id='+label_id; }
                        var category_id = jQuery('input[name="category_id"]').val();
                        if(category_id){ url+='&category_id='+category_id; }
                        var fprice_from = jQuery('input[name="fprice_from"]').val();
                        if(fprice_from){ url+='&fprice_from='+fprice_from; }
                        var fprice_to = jQuery('input[name="fprice_to"]').val();
                        if(fprice_to){ url+='&fprice_to='+fprice_to; }

                        wpNavMenu.addLinkToMenu(url,'Category');
                        return false;
                    });
                    $('#wshop_add_nav_manufacturer_id').on('click', function() {
                        var name = jQuery(this).text();
                        var url = 'controller=manufacturer&task=view';
                        var manufacturer_id = jQuery('input[name="manufacturer_id"]').val();
                        if(!manufacturer_id){ alert('Set Manufacturer ID'); return false; }
                        url+='&manufacturer_id='+manufacturer_id;
                        var label_id = jQuery('input[name="label_id"]').val();
                        if(label_id){ url+='&label_id='+label_id; }
                        var category_id = jQuery('input[name="category_id"]').val();
                        if(category_id){ url+='&category_id='+category_id; }
                        jQuery.ajax({
                            type: "POST",
                            url: 'admin-ajax.php?page=options&tab=menu&action=setmenu',
                            data: 'url='+encodeURIComponent(url),
                            dataType: 'html',
                            error: function(jqXHR, exception) {if (jqXHR.status === 0) {alert('Not connect.\n Verify Network.');} else if (jqXHR.status == 404) {alert('Requested page not found. [404]');} else if (jqXHR.status == 500) {alert('Internal Server Error [500].');} else if (exception === 'parsererror') {alert('Requested JSON parse failed.');} else if (exception === 'timeout') {alert('Time out error.');} else if (exception === 'abort') {alert('Ajax request aborted.');} else {jQuery(".message-process").html('Query Error');alert('Uncaught Error.\n' + jqXHR.responseText);}},
                            beforeSend:function() {},
                            success: function(html){
                                wpNavMenu.addLinkToMenu(html, name);
                            }
                        });
                        return false;
                    });
                    $('#wshop_add_nav_listcategory').on('click', function() {
                        var name = jQuery(this).text();
                        var url = 'controller=category&task=display';

                        jQuery.ajax({
                            type: "POST",
                            url: 'admin-ajax.php?page=options&tab=menu&action=setmenu',
                            data: 'url='+encodeURIComponent(url),
                            dataType: 'html',
                            error: function(jqXHR, exception) {if (jqXHR.status === 0) {alert('Not connect.\n Verify Network.');} else if (jqXHR.status == 404) {alert('Requested page not found. [404]');} else if (jqXHR.status == 500) {alert('Internal Server Error [500].');} else if (exception === 'parsererror') {alert('Requested JSON parse failed.');} else if (exception === 'timeout') {alert('Time out error.');} else if (exception === 'abort') {alert('Ajax request aborted.');} else {jQuery(".message-process").html('Query Error');alert('Uncaught Error.\n' + jqXHR.responseText);}},
                            beforeSend:function() {},
                            success: function(html){
                                wpNavMenu.addLinkToMenu(html, name);
                            }
                        });
                        return false;
                    });
                    $('#wshop_add_nav_listproducts').on('click', function() {
                        var url = 'controller=products&task=display';
                        var listproducts = jQuery('select[name="listproducts"] option:selected').val();
                        var name = jQuery(this).text()+' '+jQuery('select[name="listproducts"] option:selected').text();
                        if(listproducts){ url+='&task='+listproducts; }
                        var category_id = jQuery('input[name="listproducts_category_id"]').val();
                        if(category_id){ url+='&category_id='+category_id; }
                        var manufacturer_id = jQuery('input[name="listproducts_manufacturer_id"]').val();
                        if(manufacturer_id){ url+='&manufacturer_id='+manufacturer_id; }
                        var label_id = jQuery('input[name="listproducts_label_id"]').val();
                        if(label_id){ url+='&label_id='+label_id; }
                        var vendor_id = jQuery('input[name="listproducts_vendor_id"]').val();
                        if(vendor_id){url+='&vendor_id='+vendor_id;}
                        var fprice_from = jQuery('input[name="listproducts_fprice_from"]').val();
                        if(fprice_from){ url+='&fprice_from='+fprice_from; }
                        var fprice_to = jQuery('input[name="listproducts_fprice_to"]').val();
                        if(fprice_to){ url+='&fprice_to='+fprice_to; }
                        jQuery.ajax({
                            type: "POST",
                            url: 'admin-ajax.php?page=options&tab=menu&action=setmenu',
                            data: 'url='+encodeURIComponent(url),
                            dataType: 'html',
                            error: function(jqXHR, exception) {if (jqXHR.status === 0) {alert('Not connect.\n Verify Network.');} else if (jqXHR.status == 404) {alert('Requested page not found. [404]');} else if (jqXHR.status == 500) {alert('Internal Server Error [500].');} else if (exception === 'parsererror') {alert('Requested JSON parse failed.');} else if (exception === 'timeout') {alert('Time out error.');} else if (exception === 'abort') {alert('Ajax request aborted.');} else {jQuery(".message-process").html('Query Error');alert('Uncaught Error.\n' + jqXHR.responseText);}},
                            beforeSend:function() {},
                            success: function(html){
                                wpNavMenu.addLinkToMenu(html, name);
                            }
                        });
                        return false;
                    });
                    $('#wshop_add_nav_product').on('click', function() {
                        var product_id = jQuery('input[name="oneproduct_product_id"]').val();
                        var name = jQuery(this).text();
                        var url = 'controller=product&task=view';

                        if(!product_id){
                            alert('error');
                            return false;
                        }
                        url+='&product_id='+product_id;
                        jQuery.ajax({
                            type: "POST",
                            url: 'admin-ajax.php?page=options&tab=menu&action=setmenu',
                            data: 'url='+encodeURIComponent(url),
                            dataType: 'html',
                            error: function(jqXHR, exception) {if (jqXHR.status === 0) {alert('Not connect.\n Verify Network.');} else if (jqXHR.status == 404) {alert('Requested page not found. [404]');} else if (jqXHR.status == 500) {alert('Internal Server Error [500].');} else if (exception === 'parsererror') {alert('Requested JSON parse failed.');} else if (exception === 'timeout') {alert('Time out error.');} else if (exception === 'abort') {alert('Ajax request aborted.');} else {jQuery(".message-process").html('Query Error');alert('Uncaught Error.\n' + jqXHR.responseText);}},
                            beforeSend:function() {},
                            success: function(html){

                                wpNavMenu.addLinkToMenu(html, name);
                            }
                        });
                        return false;
                    });
                });