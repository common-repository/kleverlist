(function( $ ) {
	'use strict';

	/********** Sendy Global Settings Code Start **********/
	$( document ).on( 'submit', '#kleverlist_global_settings', function( e ){		
		e.preventDefault();
		kleverlistSendyGlobalSettings();
	});

	function kleverlistSendyGlobalSettings()
	{
		const loader = document.getElementById('global_loader');
		const formInput =  "form#kleverlist_global_settings :input";

		let user_resubscribe = ( $("#kleverlist_user_resubscribe").prop('checked') == true ) ? '1' : '0';
		let resubscribe_order_action = $('input[name="kleverlist_sendy_global_resubscribe_order_action"]:checked').val();
		if( user_resubscribe !== '1' ){
			resubscribe_order_action = null;
		}

		let active_all_products = ( $("#klerverlist_sendy_active_all_products").prop('checked') == true ) ? '1' : '0';
		let active_all_on_order_processing = ( $("#kleverlist_sendy_global_active_all_order_processing_action").prop('checked') == true ) ? 'yes' : 'no';
		let active_all_on_order_complete = ( $("#kleverlist_sendy_global_active_all_order_complete_action").prop('checked') == true ) ? 'yes' : 'no';

		if( active_all_products !== '1' ){
			active_all_on_order_processing = null;
			active_all_on_order_complete = null;
		}

		
		
		let privacy_radio = $('input[name="kleverlist_global_privacy_radio"]:checked').val();
		let sendy_list_id = $('#global_list').val();
		let	responseClass = '.kleverlist-gloabal-response';
		let data = null;
		
		
		
		if( kleverlist_object.is_kleverlist_premium !== 'yes' ){
			data = {
				'action': 'kleverlist_global_settings',
				'global_nonce': kleverlist_object.nonce,
				'sendy_list_id': sendy_list_id,										
				'user_resubscribe': user_resubscribe,	
				'resubscribe_order_action': resubscribe_order_action,	
				'active_all_products': active_all_products,				
				'active_all_on_order_processing': active_all_on_order_processing,		
				'active_all_on_order_complete': active_all_on_order_complete,				
			};
		}		
		
		loader.classList.remove('hidden');
		
		$( formInput ).each( function(){
			$( this ).attr( "disabled", "disabled" );
		});

		$.ajax({
			type: "post",
			url: kleverlist_object.ajax_url,
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
	/********** Sendy Global Settings Code End **********/

	/********** MailChimp Global Settings Code Start **********/
	$( document ).on( 'submit', '#kleverlist_mailchimp_global_settings', function( e ){		
		e.preventDefault();
		kleverlistMailchimpGlobalSettings();
	});

	function kleverlistMailchimpGlobalSettings(){		
		const loader = document.getElementById('global_loader');
		const formInput =  "form#kleverlist_mailchimp_global_settings :input";

		let user_resubscribe = ( $("#kleverlist_mailchimp_user_resubscribe").prop('checked') == true ) ? '1' : '0';
		let resubscribe_order_action = $('input[name="kleverlist_mailchimp_global_resubscribe_order_action"]:checked').val();
		if( user_resubscribe !== '1' ){
			resubscribe_order_action = null;
		}

		let activity_insights = ( $("#kleverlist_mailchimp_global_activity_insights").prop('checked') == true ) ? '1' : '0';
		let activity_insights_action = $('input[name="kleverlist_mailchimp_global_activity_insights_order_action"]:checked').val();
		if( activity_insights !== '1' ){
			activity_insights_action = null;
		}

		
		
		let privacy_radio = $('input[name="kleverlist_mailchimp_global_privacy_radio"]:checked').val();
		
		let active_all_products = ( $("#klerverlist_mailchimp_active_all_products").prop('checked') == true ) ? '1' : '0';
		let active_all_action = $('input[name="kleverlist_mailchimp_global_active_all_order_action"]:checked').val();
		let audience_id = $('#kleverlist_mailchimp_global_audience').val();
		let	responseClass = '.kleverlist-gloabal-response';
		let data = null;
		
		

		if( kleverlist_object.is_kleverlist_premium !== 'yes' ){
			data = {
				'action': 'kleverlist_mailchimp_global_settings',
				'global_mc_nonce': kleverlist_object.nonce,
				'audience_id': audience_id,										
				'user_resubscribe': user_resubscribe,		
				'resubscribe_order_action': resubscribe_order_action,	
				// 'activity_insights': activity_insights,	
				// 'activity_insights_action': activity_insights_action,	
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
			url: kleverlist_object.ajax_url,
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
	/********** MailChimp Global Settings Code End **********/

	

    /************ 1-Click Activation Code Start ************/
    // $(document).on('change', '#klerverlist_sendy_active_all_products', function (e) {
    //     e.preventDefault();
    //     let targetInputClass = $(this).data('target-input-class');
    //     let toggleSingleclickInput = $(targetInputClass);
        
    //     if ($(this).is(':checked')) {
    //         toggleSingleclickInput.removeClass('hide-input').addClass('show-input');
    //     } else {
    //         toggleSingleclickInput.removeClass('show-input').addClass('hide-input');
    //     }
    // });

    $(document).on('change', '.kleverlist-active-all-toggle', function (e) {
	    e.preventDefault();
	    let targetInputClass = $(this).data('target-input-class');
	    let active_all_input = $(targetInputClass);

	    if ($(this).is(':checked')) {
	        active_all_input.removeClass('hide-input').addClass('show-input');
	    } else {
	        active_all_input.removeClass('show-input').addClass('hide-input');
	    }
	});
    /************ 1-Click Activation Code End ************/

    if( kleverlist_object.is_kleverlist_premium !== 'yes' ){    	
    	jQuery(document).ready(function($) {
			// Uncheck the checkboxes using a loop
			const checkboxesToUncheck = [
				'#kleverlist_sendy_mapping_user_company_name', //Sendy Mapping Option
				'#kleverlist_sendy_mapping_user_country', //Sendy Mapping Option
				'#kleverlist_sendy_mapping_user_address_line_1', //Sendy Mapping Option
				'#kleverlist_sendy_mapping_user_address_line_2', //Sendy Mapping Option
				'#kleverlist_sendy_mapping_user_town_city', //Sendy Mapping Option
				'#kleverlist_sendy_mapping_user_province_county_district', //Sendy Mapping Option
				'#kleverlist_sendy_mapping_user_postcode', //Sendy Mapping Option
				'#kleverlist_sendy_mapping_user_phone', //Sendy Mapping Option
				'#klerverlist_privacy_consent', //Sendy Global Option

				'#klerverlist_mailchimp_privacy_consent', //MailChimp Global Option
				'#kleverlist_mailchimp_company_name', //MailChimp Mapping Option
				'#kleverlist_mailchimp_country', //MailChimp Mapping Option
				'#kleverlist_mailchimp_country', //MailChimp Mapping Option
				'#kleverlist_mailchimp_address_line_1', //MailChimp Mapping Option
				'#kleverlist_mailchimp_address_line_2', //MailChimp Mapping Option
				'#kleverlist_mailchimp_user_town_city', //MailChimp Mapping Option
				'#kleverlist_mailchimp_user_province', //MailChimp Mapping Option
				'#kleverlist_mailchimp_user_postcode', //MailChimp Mapping Option
				'#kleverlist_mailchimp_user_phone', //MailChimp Mapping Option
				'#klerverlist_mailchimp_product_tag_allow', //MailChimp Global Option

				'#klerverlist_aweber_privacy_consent', //AWeber Global Option
			];
			for (const checkbox of checkboxesToUncheck) {
				$(checkbox).prop('checked', false);
			}

			// Update the class names using a loop
			const elementsToUpdate = [
				'.kleverlist-mailchimp-global-privacy-input', 
				'.kleverlist-radio-options.kleverlist-mailchimp-global-privacy-input', 
				'.kleverlist-data.kleverlist-mailchimp-global-privacy-input',
				'.kleverlist-radio-options.kleverlist-global-privacy-input',
				'.kleverlist-global-privacy-input',
			];
			for (const element of elementsToUpdate) {
				$(element).removeClass('show-input').addClass('hide-input');
			}
		});
    }

    $(document).on('change', '.kleverlist-resubscribe-toggle,.kleverlist-activity-insights-toggle', function (e) {
	    e.preventDefault();
	    
	    let targetInputClass = $(this).data('target-input-class');
	    let targetRadioInput = $(targetInputClass);
	    
	    if ($(this).is(':checked')) {
	        targetRadioInput.removeClass('hide-input').addClass('show-input');
	    } else {
	        targetRadioInput.removeClass('show-input').addClass('hide-input');
	    }
	});    
})( jQuery );