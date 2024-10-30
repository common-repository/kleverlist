(function( $ ) {
    'use strict';
    $( document ).ready( function() {
        let security, ajax_url;

        if (typeof kleverlist_aweber_object !== 'undefined') {
            security = kleverlist_aweber_object.nonce;
            ajax_url = kleverlist_aweber_object.ajax_url;        
        }
       
        
        // AWeber API Connect Call
        $( document ).on( 'submit', '#kleverlist_aweber_settings', function( e ){      
            e.preventDefault();
            connectAWeber(security, ajax_url);
        });

        /************ Subscribe to a list for order processing ************/
        // Page load checkbox cheked check
        if( $( '#aweber_spi_order_processing' ).is( ':checked' ) ) {            
            $( '.order_processing_aweber_special_product_list_field' ).css('display', 'none' ).removeClass( 'hidden' ).show();
        }

        // On chceckbox checked check   
        $( document ).on( 'change','#aweber_spi_order_processing', function(e){
            e.preventDefault();            
            if( $( this ).is( ':checked' )) {
                $( '.order_processing_aweber_special_product_list_field' ).css('display', 'none' ).removeClass( 'hidden' ).show();
            } else if ( ! $( this) .is( ':checked' ) && $( '.order_processing_aweber_special_product_list_field' ).css( 'display' ) !== 'none' ) {
                $( '.order_processing_aweber_special_product_list_field' ).hide();
            }
        });

        /************ Subscribe to a list for order completed ************/   
        // Page load checkbox cheked check
        if( $( '#aweber_spi_order_completed' ).is( ':checked' ) ) {            
            $( '.order_completed_aweber_special_product_list_field' ).css('display', 'none' ).removeClass( 'hidden' ).show();
        }

        // On chceckbox checked check	
        $( document ).on( 'change','#aweber_spi_order_completed', function(e){
            e.preventDefault();            
            if( $( this ).is( ':checked' )) {
                $( '.order_completed_aweber_special_product_list_field' ).css('display', 'none' ).removeClass( 'hidden' ).show();
            } else if ( ! $( this) .is( ':checked' ) && $( '.order_completed_aweber_special_product_list_field' ).css( 'display' ) !== 'none' ) {
                $( '.order_completed_aweber_special_product_list_field' ).hide();
            }
        });

        // AWeber List Choose Code Call
        $( document ).on( 'submit', '#kleverlist_aweber_account_list_settings', function( e ){       
            e.preventDefault();
            generateAWeberList(security, ajax_url);
        });
    });

    /*** AWeber API Connect Call Code Start ***/  
    function connectAWeber(security, ajax_url){
        const loader = document.getElementById('aweber_loader');
        const responseClass = '.kleverlist-response';
        const textareaInput = document.getElementById('kleverlist_aweber_auth_code');
        const buttonInput = document.getElementById('aweber_submit_button');
        let auth_code = document.getElementById('kleverlist_aweber_auth_code').value;
        let service_name = $('input[name="kleverlist_service[]"]:checked').val();
        loader.classList.remove('hidden');
        
        const inputElementsToToggle = [textareaInput, buttonInput];

        if (auth_code == '') {
            $(responseClass).addClass('error');
            $(responseClass).html("Authorization Code required");

            setTimeout(function () {
                $(responseClass).html('');
                $( responseClass ).removeClass('error');
                loader.classList.add('hidden');
            }, 2000);
        } else {
            let data = {
                'action': 'kleverlist_aweber_settings',
                'security': security,
                'auth_code': auth_code,
                'service_name': service_name,
            };
            
            // Disable input elements before making the AJAX request
            kleverListToggleInputElements(inputElementsToToggle, true);

            $.ajax({
                type: "post",
                url: ajax_url,
                data: data,
                success: function ( response ) {
                    if( response != '' ){
                        // Enable input elements after the AJAX request is complete
                        kleverListToggleInputElements(inputElementsToToggle, false);

                        $( responseClass ).show();
                        $( responseClass ).html('');
                        $( responseClass ).removeClass('error');
                        $( responseClass ).removeClass('success');
                        if( response.status ){
                            $( responseClass ).addClass('success');                      
                            $( responseClass ).html( response.message );                                                    
                        } else {
                            $( responseClass ).addClass('error');
                            $( responseClass ).html( response.message ); 
                        }

                        setTimeout( function () {
                            loader.classList.add('hidden');
                            $( responseClass ).hide();

                            // Enable input elements after the AJAX request is complete
                            kleverListToggleInputElements(inputElementsToToggle, false);
                            
                            if (response.status && response.page_url != '') {
                                window.location.href = response.page_url;
                            }

                        }, 2000 );
                    }
                }
            });
        }
    }
    /*** AWeber API Connect Call Code End ***/  

    /*** AWeber List Choose Code Start ***/  
    function generateAWeberList(security, ajax_url){
        const loader = document.getElementById('aweber_list_loader');
        const dropdownInput = document.getElementById("aweber_account_list");
        const aweber_account_btn = document.getElementById("aweber_account_btn");
        const remove_btn = document.getElementById("kleverlist_remove_settings");

        const inputElementsToToggle = [dropdownInput, aweber_account_btn,remove_btn];
        let responseClass = '.kleverlist-response-aweber-list';
        let aweber_account_id = $('#aweber_account_list').val();
        
        if (aweber_account_id == '') {
            $(responseClass).addClass('error');
            $(responseClass).html("Please choose list from dropdown");

            setTimeout(function () {
                $(responseClass).html('');
                $( responseClass ).removeClass('error');
                loader.classList.add('hidden');
            }, 2000);
        } else {
            let data = {
                'action': 'kleverlist_aweber_choose_list',
                'security': security,
                'aweber_account_id': aweber_account_id,
            };
            
            loader.classList.remove('hidden');
            
            // Disable input elements before making the AJAX request
            kleverListToggleInputElements(inputElementsToToggle, true);

            $.ajax({
                type: "post",
                url: ajax_url,
                data: data,
                success: function ( response ) {
                    if( response!='' ){
                        // Enable input elements after the AJAX request is complete
                        kleverListToggleInputElements(inputElementsToToggle, false);

                        $( responseClass ).show();
                        $( responseClass ).html('');
                        $( responseClass ).removeClass('error');
                        $( responseClass ).removeClass('success');
                        if( response.status ){
                            $( responseClass ).addClass('success');
                            $( responseClass ).html( response.message );    
                                                
                        }else{
                            $( responseClass ).addClass('error');
                            $( responseClass ).html( response.message ); 
                        }

                        setTimeout( function () {
                            $( responseClass ).hide();
                            
                            loader.classList.add('hidden');
                            // Enable input elements after the AJAX request is complete
                            kleverListToggleInputElements(inputElementsToToggle, false);

                            if( response.status ){
                                location.reload();
                            }
                        }, 2000 );
                    }
                }
            });
        }
    }
    /*** AWeber List Choose Code End ***/  

    /********** AWeber Global Settings Code Start **********/
    $( document ).on( 'submit', '#kleverlist_aweber_global_settings', function( e ){
        e.preventDefault();
        kleverlisAWeberpGlobalSettings();
    });



    function kleverlisAWeberpGlobalSettings(){
        const loader = document.getElementById('global_loader');
        const formInput =  "form#kleverlist_aweber_global_settings :input";

        let user_resubscribe = ( $("#kleverlist_aweber_user_resubscribe").prop('checked') == true ) ? '1' : '0';
        let resubscribe_order_action = $('input[name="kleverlist_aweber_global_resubscribe_order_action"]:checked').val();
        if( user_resubscribe !== '1' ){
            resubscribe_order_action = null;
        }

        
        
        let privacy_radio = $('input[name="kleverlist_aweber_global_privacy_radio"]:checked').val();
        
        let active_all_products = ( $("#klerverlist_aweber_active_all_products").prop('checked') == true ) ? '1' : '0';
        let active_all_action = $('input[name="kleverlist_aweber_global_active_all_order_action"]:checked').val();
        let aweber_account_id = $('#aweber_account_list_display').data('id');
        let responseClass = '.kleverlist-gloabal-response';
        let data = null;
        
        

        if( kleverlist_aweber_object.is_kleverlist_premium !== 'yes' ){
            data = {
                'action': 'kleverlist_aweber_global_settings',
                'security': kleverlist_aweber_object.nonce,
                'aweber_account_id': aweber_account_id, 
                'user_resubscribe': user_resubscribe,       
                'resubscribe_order_action': resubscribe_order_action,   
                'active_all_products': active_all_products,     
                'active_all_action': active_all_action, 
            };
        }       
        
        loader.classList.remove('hidden');
        
        $( formInput ).each( function(){
            $( this ).attr( "disabled", "disabled" );
        });

        $.ajax({
            type: "post",
            url: kleverlist_aweber_object.ajax_url,
            data: data,
            success: function ( response ) {
                if( response!='' ){
                    $( responseClass ).show();
                    $( responseClass ).html('');
                    
                    if( response.status ){
                        $( responseClass ).addClass('success');                     
                        $( responseClass ).html( response.message );                                                
                    }else{
                        $( responseClass ).addClass('error');
                        $( responseClass ).html( response.message ); 
                    }

                    setTimeout( function () {
                        if( response.status ){
                            location.reload();
                        }

                        $( responseClass ).removeClass('error');
                        $( responseClass ).removeClass('success');
                        $( responseClass ).html('');
                        $( responseClass ).hide();
                        
                        loader.classList.add('hidden');
                        $( formInput ).each( function(){
                            $( this ).prop("disabled", false);
                        });

                    }, 2000 );
                }
            }
        });
    }
    /********** AWeber Global Settings Code Start **********/

    /*** AWeber Mapping Form Save Code Start ***/    
    $( document ).on( 'submit', '#kleverlist_aweber_mapping_settings', function( e ){
        e.preventDefault();
        aweberMapping();
    });
    
    function aweberMapping(){
        const loader      = document.getElementById('loader');
        const formInput   =  "form#kleverlist_aweber_mapping_settings :input";
        let responseClass = '.kleverlist-response';

        let user_email = ( $("#kleverlist_aweber_user_email").prop('checked') == true ) ? 'yes' : 'no';
        let firstname  = ( $("#kleverlist_aweber_firstname").prop('checked') == true ) ? '1' : '0';                      
        let lastname   = ( $("#kleverlist_aweber_lastname").prop('checked') == true ) ? '1' : '0';                       
        let username    = ( $("#kleverlist_aweber_username").prop('checked') == true ) ? '1' : '0';              
        
                

        let data = null;
        
        
        if( kleverlist_aweber_object.is_kleverlist_premium !== 'yes' ){
            data = {
                'action': 'kleverlist_aweber_mapping_settings',
                'security': kleverlist_aweber_object.nonce,
                'user_email': user_email,
                'firstname': firstname,             
                'lastname': lastname,   
                'username': username,           
            };
        }
            
        loader.classList.remove('hidden');
                
        if ( $('#kleverlist_aweber_mapping_settings' ).length) {
            $( formInput ).each( function(){
                $( this ).attr( "disabled", "disabled" );
            });
        }
                
        $.ajax({
            type: "post",
            url: kleverlist_aweber_object.ajax_url,
            data: data,
            success: function ( response ) {
                if( response!='' ){
                    $( responseClass ).show();
                    $( responseClass ).html('');
                    $( responseClass ).removeClass('error');
                    $( responseClass ).removeClass('success');
                    if( response.status ){
                        $( responseClass ).addClass('success');
                        $( responseClass ).html( response.message );    
                                            
                    }else{
                        $( responseClass ).addClass('error');
                        $( responseClass ).html( response.message ); 
                    }

                    setTimeout( function () {
                        if( response.status ){
                            location.reload();
                        }

                        $( responseClass ).html('');
                        $( responseClass ).hide();
                        loader.classList.add('hidden');
                        
                        if ( $('#kleverlist_aweber_mapping_settings' ).length) {
                            $( formInput ).each( function(){
                                $( this ).prop("disabled", false);
                            });
                        }

                    }, 2000 );
                }
            }
        });
    }
    /*** AWeber Mapping Form Save Code End ***/  

    /********* Toggle Disabled Attribute of Input Elements Start *********/
    function kleverListToggleInputElements(elements, disable) {
        elements.forEach(function (element) {
            if (disable) {
                $(element).prop("disabled", true);
            } else {
                $(element).prop("disabled", false);
            }
        });
    }
    /********* Toggle Disabled Attribute of Input Elements End *********/

    /************ Aweber Bulk Product Assign start************/    
    $( document ).on( 'click', '.kleverlist-aweber-bulk-list-apply', function( e ){
        e.preventDefault();
        kleverlistaweberBulkListAssign();
    }); 

    function kleverlistaweberBulkListAssign(){
        let kleverlist_aweber_bulk_list_order_processing_checkbox = ( $("#kleverlist_aweber_bulk_list_order_processing_checkbox").prop('checked') == true ) ? '1' : '0';                
        let kleverlist_aweber_bulk_list_order_completed_checkbox = ( $("#kleverlist_aweber_bulk_list_order_completed_checkbox").prop('checked') == true ) ? '1' : '0';     
        let kleverlist_aweber_bulk_choosen_list = $('#kleverlist_aweber_bulk_choosen_list').val();
        
        let selectedProductIds = [];
    
        $('.check-column input[type="checkbox"]:checked').each(function() {
            var productId = $(this).val();
            selectedProductIds.push(productId);
        });
        
        let responseClass = '.kleverlist-aweber-bulk-response';
        let data = {
            'action': 'kleverlist_aweber_bulk_list_settings',
            'security': kleverlist_aweber_object.nonce,             
            'kleverlist_aweber_bulk_list_order_processing_checkbox': kleverlist_aweber_bulk_list_order_processing_checkbox,             
            'kleverlist_aweber_bulk_list_order_completed_checkbox': kleverlist_aweber_bulk_list_order_completed_checkbox, 
            'kleverlist_aweber_bulk_choosen_list': kleverlist_aweber_bulk_choosen_list, 
            'ids':selectedProductIds            
        };
        $.ajax({
            type: "post",
            url: kleverlist_aweber_object.ajax_url,
            data: data,
            success: function ( response ) {
                if(response!=''){
                    $(responseClass).html('');
                    $(responseClass).show();
                    if(response.status){
                        $(responseClass).html(kleverlistAddNoticeMessage('success',response.message));
                    }else{
                        $(responseClass).html(kleverlistAddNoticeMessage('error',response.message));
                    }

                    setTimeout(function () {
                        if(response.status){                          
                            $(responseClass).hide();
                            location.reload();
                        }
                    }, 2000 );
                }
            }
        });    
    }

    function kleverlistAddNoticeMessage($notice_type,$messsage){
        return `<div class="notice notice-${$notice_type} is-dismissible"><p>${$messsage}</p><button type="button" class="notice-dismiss"></div>`;
    }

    $(document).on('click','.kleverlist-aweber-bulk-response .notice-dismiss',function() {
        $(this).closest('.notice.is-dismissible').remove();
    });
    /************ Aweber Bulk Product Assign End************/  
})( jQuery );
