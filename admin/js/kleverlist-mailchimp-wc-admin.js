(function( $ ) {
	'use strict';	
	$( document ).ready( function() {	
        /************ Subscribe to a list for order completed ************/   
        // Page load checkbox cheked check
        if( $( '#mc_spi_order_completed' ).is( ':checked' ) ) {            
            $( '.order_completed_mc_special_product_list_field' ).css('display', 'none' ).removeClass( 'hidden' ).show();
        }

        // On chceckbox checked check	
        $( document ).on( 'change','#mc_spi_order_completed', function(e){
            e.preventDefault();            
            if( $( this ).is( ':checked' )) {
                $( '.order_completed_mc_special_product_list_field' ).css('display', 'none' ).removeClass( 'hidden' ).show();
            } else if ( ! $( this) .is( ':checked' ) && $( '.order_completed_mc_special_product_list_field' ).css( 'display' ) !== 'none' ) {
                $( '.order_completed_mc_special_product_list_field' ).hide();
            }
        });       

        // Check if the element has a specific class
        if ( $( '.kleverlist-pro-featured-addtagto_product-order-processing' ).hasClass( 'kleverlist-free-plan' ) ) {
            // Uncheck the checkbox
            $( '#order_processing_addtagto_product' ).prop( 'checked', false );
        }

        // Check if the element has a specific class
        if ( $( '.kleverlist-pro-featured-addtagto_product-order-completed' ).hasClass( 'kleverlist-free-plan' ) ) {
            // Uncheck the checkbox
            $( '#order_completed_addtagto_product' ).prop( 'checked', false );
        }
        /************ Subscribe to a list for order completed ************/   

        /************ Subscribe to a list for order processing ************/
        // Page load checkbox cheked check
        if( $( '#mc_spi_order_processing' ).is( ':checked' ) ) {            
            $( '.order_processing_mc_special_product_list_field' ).css('display', 'none' ).removeClass( 'hidden' ).show();
        }

        // On chceckbox checked check   
        $( document ).on( 'change','#mc_spi_order_processing', function(e){
            e.preventDefault();            
            if( $( this ).is( ':checked' )) {
                $( '.order_processing_mc_special_product_list_field' ).css('display', 'none' ).removeClass( 'hidden' ).show();
            } else if ( ! $( this) .is( ':checked' ) && $( '.order_processing_mc_special_product_list_field' ).css( 'display' ) !== 'none' ) {
                $( '.order_processing_mc_special_product_list_field' ).hide();
            }
        });

        /************ Mailchimp Bulk Product Assign ************/    
        $( document ).on( 'click', '.kleverlist-mailchimp-bulk-list-apply', function( e ){
            e.preventDefault();
            kleverlistMailchimpBulkListAssign();
        }); 

        function kleverlistMailchimpBulkListAssign(){
            let kleverlist_mailchimp_bulk_list_order_processing_checkbox = ( $("#kleverlist_mailchimp_bulk_list_order_processing_checkbox").prop('checked') == true ) ? '1' : '0';                
            let kleverlist_mailchimp_bulk_list_order_completed_checkbox = ( $("#kleverlist_mailchimp_bulk_list_order_completed_checkbox").prop('checked') == true ) ? '1' : '0';     
            let kleverlist_mailchimp_bulk_choosen_audience = $('#kleverlist_mailchimp_bulk_choosen_audience').val();
            
            let selectedProductIds = [];
        
            $('.check-column input[type="checkbox"]:checked').each(function() {
                var productId = $(this).val();
                selectedProductIds.push(productId);
            });
            
            let responseClass = '.kleverlist-mailchimp-bulk-response';
            let data = {
                'action': 'kleverlist_mailchimp_bulk_list_settings',
                'security': kleverlist_mcwc_object.nonce,             
                'kleverlist_mailchimp_bulk_list_order_processing_checkbox': kleverlist_mailchimp_bulk_list_order_processing_checkbox,             
                'kleverlist_mailchimp_bulk_list_order_completed_checkbox': kleverlist_mailchimp_bulk_list_order_completed_checkbox, 
                'kleverlist_mailchimp_bulk_choosen_audience': kleverlist_mailchimp_bulk_choosen_audience, 
                'ids':selectedProductIds            
            };
            $.ajax({
                type: "post",
                url: kleverlist_mcwc_object.ajax_url,
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

        $(document).on('click','.kleverlist-mailchimp-bulk-response .notice-dismiss',function() {
            $(this).closest('.notice.is-dismissible').remove();
        });
        /************ Mailchimp Bulk Product Assign ************/  
	});
})( jQuery );
