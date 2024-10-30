(function( $ ) {
	'use strict';	
	$( document ).ready( function() {	
        /************ Subscribe to a list for order completed ************/   
        // Page load checkbox cheked check
        $('#kleverlist_sendy_bulk_list_subscribe_radio').prop( 'checked', true );
        
        if( $( '#spi' ).is( ':checked' ) ) {            
            $( '.special_product_list_field' ).css('display', 'none' ).removeClass( 'hidden' ).show();
        }

        // On chceckbox checked check	
        $( document ).on( 'change','#spi', function(e){
            e.preventDefault();            
            if( $( this ).is( ':checked' )) {
                $( '.special_product_list_field' ).css('display', 'none' ).removeClass( 'hidden' ).show();
            } else if ( ! $( this) .is( ':checked' ) && $( '.special_product_list_field' ).css( 'display' ) !== 'none' ) {
                $( '.special_product_list_field' ).hide();
            }
        });

        // Check if the element has a specific class
        if ( $( '.kleverlist-pro-featured-unsubscribe' ).hasClass( 'kleverlist-free-plan' ) ) {
            // Uncheck the checkbox
            $( '#unsubscribe_product' ).prop( 'checked', false );
        }        
        /************ Subscribe to a list for order completed ************/   

        /************ Subscribe to a list for order processing ************/
        // Page load checkbox cheked check
        if( $( '#spi_order_processing' ).is( ':checked' ) ) {            
            $( '.order_processing_special_product_list_field' ).css('display', 'none' ).removeClass( 'hidden' ).show();
        }

        // On chceckbox checked check   
        $( document ).on( 'change','#spi_order_processing', function(e){
            e.preventDefault();                        
            if( $( this ).is( ':checked' )) {
                $( '.order_processing_special_product_list_field' ).css('display', 'none' ).removeClass( 'hidden' ).show();
            } else if ( ! $( this) .is( ':checked' ) && $( '.order_processing_special_product_list_field' ).css( 'display' ) !== 'none' ) {
                $( '.order_processing_special_product_list_field' ).hide();
            }
        });

        // Check if the element has a specific class
        if ( $( '.kleverlist-pro-featured-unsubscribe-order-processing' ).hasClass( 'kleverlist-free-plan' ) ) {
            // Uncheck the checkbox
            $( '#order_processing_unsubscribe_product' ).prop( 'checked', false );
        }
        /************ Subscribe to a list for order processing ************/

        /************ 1-Click Activation Code Start ************/
        if( kleverlist_wc_object.active_all_order_processing_action === 'yes' ){
            $( "#order_processing_special_product_list option" ).each( function (i,v) {
                if( kleverlist_wc_object.defualt_pro_list_order_processing!='' ){
                    $(this).removeAttr('selected'); 
                    if( this.value === kleverlist_wc_object.defualt_pro_list_order_processing ){
                        $(this).attr('selected','selected');
                    }
                }
            }); 
        }

        if( kleverlist_wc_object.active_all_order_complete_action === 'yes' ){
            $( "#special_product_list option" ).each( function (i,v) {
                if( kleverlist_wc_object.defualt_pro_list_order_complete!='' ){                
                    $(this).removeAttr('selected'); 
                    if( this.value === kleverlist_wc_object.defualt_pro_list_order_complete ){
                        $(this).attr('selected','selected');
                    }
                }
            }); 
        }
        /************ 1-Click Activation Code End ************/

        $( document ).on( 'click', '.kleverlist-free-plan', function( e ){
            e.preventDefault();         
            $('#kleverlist-notice-popup').show();
            $( '#wp-content-wrap' ).css({'opacity':'0.5'});
        });

        $( document ).on( 'click', '.kleverlist-premium-btn', function( e ){
            e.preventDefault();
            $('#kleverlist-notice-popup').hide();
            $( '#wp-content-wrap' ).css({'opacity':'1'});
        }); 

               
        

        /************ Sendy Bulk Product Assign ************/    
        $( document ).on( 'click', '.kleverlist-sendy-bulk-list-apply', function( e ){
            e.preventDefault();
            kleverlistSendyBulkListAssign();
        }); 

        function kleverlistSendyBulkListAssign(){
            let kleverlist_sendy_bulk_choosen_list = $('#kleverlist_sendy_bulk_choosen_list').val();

            let kleverlist_sendy_bulk_list_order_processing_checkbox = ( $("#kleverlist_sendy_bulk_list_order_processing_checkbox").prop('checked') == true ) ? '1' : '0';                
            let kleverlist_sendy_bulk_list_order_completed_checkbox = ( $("#kleverlist_sendy_bulk_list_order_completed_checkbox").prop('checked') == true ) ? '1' : '0';     

            let kleverlist_sendy_bulk_list_subscribe_unsubscribe_radio = $('input[name="kleverlist_sendy_bulk_list_subscribe_unsubscribe_radio"]:checked').val();
            
            let selectedProductIds = [];
        
            $('.check-column input[type="checkbox"]:checked').each(function() {
                var productId = $(this).val();
                selectedProductIds.push(productId);
            });
            let responseClass = '.kleverlist-sendy-bulk-response';
            let data = {
                'action': 'kleverlist_sendy_bulk_list_settings',
                'security': kleverlist_wc_object.nonce,             
                'kleverlist_sendy_bulk_list_order_processing_checkbox': kleverlist_sendy_bulk_list_order_processing_checkbox,             
                'kleverlist_sendy_bulk_list_order_completed_checkbox': kleverlist_sendy_bulk_list_order_completed_checkbox, 
                'kleverlist_sendy_bulk_choosen_list': kleverlist_sendy_bulk_choosen_list, 
                'kleverlist_sendy_bulk_list_subscribe_unsubscribe_radio': kleverlist_sendy_bulk_list_subscribe_unsubscribe_radio, 
                'ids':selectedProductIds            
            };
            $.ajax({
                type: "post",
                url: kleverlist_wc_object.ajax_url,
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

        $(document).on('click','.kleverlist-sendy-bulk-response .notice-dismiss',function() {
            $(this).closest('.notice.is-dismissible').remove();
        });
        /************ Sendy Bulk Product Assign ************/        
	});
})( jQuery );
    