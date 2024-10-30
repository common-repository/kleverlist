(function( $ ) {
	'use strict';
	/*** API Verification Code Start ***/
	$( document ).ready( function() {
		let allServiceCheckbox = '.kleverlist-checkbox';

		$( document ).on( 'change', allServiceCheckbox, function() {
			let integrationId = $(this).val();
			$('.klever-list-btn-padd').removeClass('active');
			$(this).closest('.klever-list-btn-padd').addClass('active');
			
			// Hide all integration inputs
    		$( '.kleverlist-integration-forms' ).hide();

    		// Show the corresponding integration inputs based on checkbox value
    		$( '#kleverlist_'+integrationId+'_settings' ).show();

			// Unchecked other checkboxes
			$( allServiceCheckbox ).not( this ).prop( 'checked', false ); 
		});
	});
	
	$( document ).on( 'submit', '#kleverlist_sendy_settings', function( e ){		
		e.preventDefault();
		connectSendyAPI();
	});

	function connectSendyAPI()
	{
		const loader = document.getElementById('loader');
		const submit_button = document.getElementById('settings_submit_button');
		const service_api_key = document.getElementById('service_api_key');
		const service_domain_name = document.getElementById('domain_name');
		let service_name = $('input[name="kleverlist_service[]"]:checked').val();
		
		let api_key = $( '#service_api_key' ).val(),
			domain_name = $( '#domain_name' ).val(),
			responseClass = '.kleverlist-response',
			data = {
				'action': 'kleverlist_sendy_settings',
				'nonce': kleverlist_object.nonce,
				'api_key': api_key,
				'domain_name': domain_name,
				'service_name': service_name,
			};
		
		loader.classList.remove('hidden');
		submit_button.disabled = true;
		service_api_key.disabled = true;
		service_domain_name.disabled = true;
		$.ajax({
			type: "post",
			url: kleverlist_object.ajax_url,
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
						loader.classList.add('hidden');
                        submit_button.disabled = false;
                        service_api_key.disabled = false;
                        service_domain_name.disabled = false;
                        $( responseClass ).hide();

                        if( response.status ){							
							location.reload();
						}
					}, 2000 );
				}
			}
		});
	}
	
	$( document ).on( 'submit', '#kleverlist_mailchimp_settings', function( e ){		
		e.preventDefault();
		connectMailchimpAPI();
	});

	function connectMailchimpAPI()
	{
		const formInput =  "form#kleverlist_mailchimp_settings :input";
		const mailchimp_loader = '#mailchimp_loader';
		const submit_button = document.getElementById('mailchimp_submit_button');
		let apikey = $('#mailchimp_apikey').val();
		let apiurl = $('#mailchimp_api_url').val();
		let service_name = $('input[name="kleverlist_service[]"]:checked').val();

		let responseClass = '.kleverlist-response',
			data = {
			'action': 'kleverlist_mailchimp_setting',
			'security': kleverlist_object.nonce,
			'apikey': apikey,
			'apiurl': apiurl,
			'service_name': service_name,
		};
		$( mailchimp_loader ).removeClass('hidden');

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
						$( mailchimp_loader ).addClass('hidden');		
						$( formInput ).each( function(){
							$( this ).prop("disabled", false);
						});				
					}, 2000 );
				}
			}
		});
	}
	/*** API Verification Code End ***/

	/*** Generate List Code Start ***/	
	$( document ).on( 'submit', '#kleverlist_brands_settings', function( e ){		
		e.preventDefault();
		generateSendyList();
	});

	function generateSendyList(){		 
		const loader = document.getElementById('brand_loader');
		const dropdownInput = document.getElementById("sendy_brands");
		const generate_list_btn = document.getElementById("generate_lists");
		const remove_btn = document.getElementById("kleverlist_remove_settings");

		let responseClass = '.kleverlist-response-brands';
		let brand_id = $('#sendy_brands').val(),
			data = {
				'action': 'kleverlist_generate_lists',
				'_nonce': kleverlist_object.nonce,
				'brand_id': brand_id,
			};
		
			loader.classList.remove('hidden');
			dropdownInput.disabled = true;
			generate_list_btn.disabled = true;
			remove_btn.disabled = true;
			$.ajax({
				type: "post",
				url: kleverlist_object.ajax_url,
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
							$( responseClass ).hide();
							
							loader.classList.add('hidden');
							dropdownInput.disabled = false;
							generate_list_btn.disabled = false;
							remove_btn.disabled = false;

							if( response.status ){
								location.reload();
							}
						}, 2000 );
					}
				}
			});

	}
	/*** Generate List Code End ***/

	/*** Load Mailchimp Audience Code Start ***/
	$( document ).on( 'submit', '#kleverlist_mailchimp_audience_settings', function( e ){	
		e.preventDefault();
		LoadMailchimpAudience();
	});

	function LoadMailchimpAudience(){		 
		const loader = document.getElementById('audience_loader');
		const formInput     = "form#kleverlist_mailchimp_audience_settings :input";
		const remove_button = document.getElementById('kleverlist_remove_settings');
		const dropdownInput = document.getElementById("mailchimp_audience");

		let responseClass = '.kleverlist-response-mailchimp-audience';
		let user_audience = $('#mailchimp_audience').val(),
			data = {
				'action': 'kleverlist_load_mailchimp_audience',
				'security': kleverlist_object.nonce,
				'user_audience': user_audience,
			};
	
		loader.classList.remove('hidden');
		// Check if the form exists
		if ( $('#kleverlist_mailchimp_audience_settings' ).length) {
			$( formInput ).each( function(){
				$( this ).attr( "disabled", "disabled" );
				$( remove_button ).attr( "disabled", "disabled" );
			});
		}

		$.ajax({
			type: "post",
			url: kleverlist_object.ajax_url,
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
						$( responseClass ).hide();							
						loader.classList.add('hidden');

						$( formInput ).each( function(){
							$( this ).prop("disabled", false);
							$( remove_button ).prop("disabled", false);
						});	

						if( response.status ){
							location.reload();
						}
					}, 2000 );
				}
			}
		});

	}
	/*** Load Mailchimp Audience Code End ***/
	
	/*** Sendy Mapping Form Save Code Start ***/
	$( document ).on( 'submit', '#kleverlist_mapping_settings', function( e ){
		e.preventDefault();
		sendyMapping();
	});

	function sendyMapping(){
		const loader = document.getElementById('loader');
		const formInput =  "form#kleverlist_mapping_settings :input";
		
		let kleverlist_sendy_mapping_user_fullname = ( $("#kleverlist_sendy_mapping_user_fullname").prop('checked') == true ) ? '1' : '0';		
		let kleverlist_sendy_mapping_user_firstname = ( $("#kleverlist_sendy_mapping_user_firstname").prop('checked') == true ) ? '1' : '0';				
		let kleverlist_sendy_mapping_user_lastname  = ( $("#kleverlist_sendy_mapping_user_lastname").prop('checked') == true ) ? '1' : '0';				
		let kleverlist_sendy_mapping_user_username  = ( $("#kleverlist_sendy_mapping_user_username").prop('checked') == true ) ? '1' : '0';				
				
		let kleverlist_sendy_mapping_user_email_allowed = ( $("#kleverlist_sendy_mapping_user_email_allowed").prop('checked') == true ) ? 'yes' : 'no';
		let responseClass = '.kleverlist-response';
		let data = null;
				
		if( kleverlist_object.is_kleverlist_premium !== 'yes' ){
			data = {
				'action': 'kleverlist_mapping_settings',
				'_nonce_': kleverlist_object.nonce,
				'kleverlist_sendy_mapping_user_email_allowed': kleverlist_sendy_mapping_user_email_allowed,
				'kleverlist_sendy_mapping_user_fullname': kleverlist_sendy_mapping_user_fullname,				
				'kleverlist_sendy_mapping_user_firstname': kleverlist_sendy_mapping_user_firstname,
				'kleverlist_sendy_mapping_user_lastname': kleverlist_sendy_mapping_user_lastname,
				'kleverlist_sendy_mapping_user_username': kleverlist_sendy_mapping_user_username,
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
						$( formInput ).each( function(){
							$( this ).prop("disabled", false);
						});

					}, 2000 );
				}
			}
		});
	}
	/*** Sendy Mapping Form Save Code End ***/

	/*** Mailchimp Mapping Form Save Code Start ***/	
	$( document ).on( 'submit', '#kleverlist_mailchimp_mapping_settings', function( e ){
		e.preventDefault();
		mailchimpMapping();
	});
	
	function mailchimpMapping(){
		const loader      = document.getElementById('loader');
		const formInput   =  "form#kleverlist_mailchimp_mapping_settings :input";
		let responseClass = '.kleverlist-response';

		let user_email = ( $("#kleverlist_mailchimp_user_email").prop('checked') == true ) ? 'yes' : 'no';
		let firstname  = ( $("#kleverlist_mailchimp_firstname").prop('checked') == true ) ? '1' : '0';						
		let lastname   = ( $("#kleverlist_mailchimp_lastname").prop('checked') == true ) ? '1' : '0';						
		let username  	= ( $("#kleverlist_mailchimp_username").prop('checked') == true ) ? '1' : '0';				
		
				

		let data = null;
		
		
		if( kleverlist_object.is_kleverlist_premium !== 'yes' ){
			data = {
				'action': 'kleverlist_mailchimp_mapping_settings',
				'security': kleverlist_object.nonce,
				'user_email': user_email,
				'firstname': firstname,				
				'lastname': lastname,	
				'username': username,			
			};
		}
			
		loader.classList.remove('hidden');
				
		if ( $('#kleverlist_mailchimp_mapping_settings' ).length) {
			$( formInput ).each( function(){
				$( this ).attr( "disabled", "disabled" );
			});
		}
				
		$.ajax({
			type: "post",
			url: kleverlist_object.ajax_url,
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
						
						if ( $('#kleverlist_mailchimp_mapping_settings' ).length) {
							$( formInput ).each( function(){
								$( this ).prop("disabled", false);
							});
						}

					}, 2000 );
				}
			}
		});
	}
	/*** Mailchimp Mapping Form Save Code End ***/	

	/*** Sendy Tag Management Form Save Code Start ***/	
	$( document ).on( 'submit', '#kleverlist_sendy_tags_settings', function( e ){
		e.preventDefault();
		kleverlistSendyBasicTags();
	});

	function kleverlistSendyBasicTags(){
		const loader      = document.getElementById('loader');
		const formInput   =  "form#kleverlist_sendy_tags_settings :input";
		let responseClass = '.kleverlist-response';

		let order_processing = ( $("#kleverlist_sendy_order_processing_tag").prop('checked') == true ) ? '1' : '0';				
		let order_completed = ( $("#kleverlist_sendy_order_completed_tag").prop('checked') == true ) ? '1' : '0';		
		let remove_order_processing_tag = null;
		if( order_completed === '1' ){
			remove_order_processing_tag = ( $("#kleverlist_sendy_remove_order_processing_tag").prop('checked') == true ) ? '1' : '0';
		}
		let data = null;
				
		

		if( kleverlist_object.is_kleverlist_premium !== 'yes' ){
			data = {
				'action': 'kleverlist_sendy_tags_settings',
				'security': kleverlist_object.nonce,		
				'order_processing': order_processing,	
				'order_completed': order_completed,				
				'remove_order_processing_tag': remove_order_processing_tag,			
			};
		}		
			
		loader.classList.remove('hidden');
				
		if ( $('#kleverlist_sendy_tags_settings' ).length) {
			$( formInput ).each( function(){
				$( this ).attr( "disabled", "disabled" );
			});
		}
				
		$.ajax({
			type: "post",
			url: kleverlist_object.ajax_url,
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
						
						if ( $('#kleverlist_sendy_tags_settings' ).length) {
							$( formInput ).each( function(){
								$( this ).prop("disabled", false);
							});
						}

					}, 2000 );
				}
			}
		});
	}

	/*** Mailchimp Tag Management Form Save Code Start ***/	
	$( document ).on( 'submit', '#kleverlist_mailchimp_tags_settings', function( e ){
		e.preventDefault();
		kleverlistMailchimpBasicTags();
	});

	function kleverlistMailchimpBasicTags(){		
		const loader      = document.getElementById('loader');
		const formInput   =  "form#kleverlist_mailchimp_tags_settings :input";
		let responseClass = '.kleverlist-response';

		let order_processing = ( $("#kleverlist_mailchimp_order_processing").prop('checked') == true ) ? '1' : '0';				
		let order_completed = ( $("#kleverlist_mailchimp_order_completed").prop('checked') == true ) ? '1' : '0';		

		let remove_order_processing_tag = null;
		if( order_completed === '1' ){
			remove_order_processing_tag = ( $("#kleverlist_mailchimp_remove_order_processing_tag").prop('checked') == true ) ? '1' : '0';
		}
		let data = null;
				
		

		if( kleverlist_object.is_kleverlist_premium !== 'yes' ){
			data = {
				'action': 'kleverlist_mailchimp_tags_settings',
				'security': kleverlist_object.nonce,		
				'order_processing': order_processing,	
				'order_completed': order_completed,			
				'remove_order_processing_tag': remove_order_processing_tag,				
			};
		}		
			
		loader.classList.remove('hidden');
				
		if ( $('#kleverlist_mailchimp_tags_settings' ).length) {
			$( formInput ).each( function(){
				$( this ).attr( "disabled", "disabled" );
			});
		}
				
		$.ajax({
			type: "post",
			url: kleverlist_object.ajax_url,
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
						
						if ( $('#kleverlist_mailchimp_tags_settings' ).length) {
							$( formInput ).each( function(){
								$( this ).prop("disabled", false);
							});
						}

					}, 2000 );
				}
			}
		});
	}
	/*** Mailchimp Tag Management Form Save Code End ***/	

	/*** Aweber Tag Management Form Save Code Start ***/	
	$( document ).on( 'submit', '#kleverlist_aweber_tags_settings', function( e ){
		e.preventDefault();
		kleverlistaweberBasicTags();
	});

	function kleverlistaweberBasicTags(){
		const loader      = document.getElementById('loader');
		const formInput   =  "form#kleverlist_aweber_tags_settings :input";
		let responseClass = '.kleverlist-response';

		let order_processing = ( $("#kleverlist_aweber_order_processing_tag").prop('checked') == true ) ? '1' : '0';				
		let order_completed = ( $("#kleverlist_aweber_order_completed_tag").prop('checked') == true ) ? '1' : '0';		
		let remove_order_processing_tag = null;
		if( order_completed === '1' ){
			remove_order_processing_tag = ( $("#kleverlist_aweber_remove_order_processing_tag").prop('checked') == true ) ? '1' : '0';
		}
		let data = null;
				
		

		if( kleverlist_object.is_kleverlist_premium !== 'yes' ){
			data = {
				'action': 'kleverlist_aweber_tags_settings',
				'security': kleverlist_object.nonce,		
				'order_processing': order_processing,	
				'order_completed': order_completed,				
				'remove_order_processing_tag': remove_order_processing_tag,			
			};
		}		
			
		loader.classList.remove('hidden');
				
		if ( $('#kleverlist_aweber_tags_settings' ).length) {
			$( formInput ).each( function(){
				$( this ).attr( "disabled", "disabled" );
			});
		}
				
		$.ajax({
			type: "post",
			url: kleverlist_object.ajax_url,
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
						
						if ( $('#kleverlist_aweber_tags_settings' ).length) {
							$( formInput ).each( function(){
								$( this ).prop("disabled", false);
							});
						}

					}, 2000 );
				}
			}
		});
	}
	/*** Aweber Tag Management Form Save Code END ***/
	
	/*** Remove Button Code Start ***/
	$( document ).on( 'click', '#kleverlist_remove_settings', function( e ){
		e.preventDefault();
		removeApiInfo();
	});

	function removeApiInfo(){
		if( confirm('Are you sure you want to remove') ){
			const loader = document.getElementById('loader');
			const sendyFormInput =  "form#kleverlist_brands_settings :input";
			const mailchimpFormInput =  "form#kleverlist_mailchimp_audience_settings :input";
			const remove_button = document.getElementById("kleverlist_remove_settings");
			const dropdownInput = document.getElementById("sendy_brands");
			const generate_list_btn = document.getElementById("generate_lists");

			let data = {
				'action': 'kleverlist_remove_api_info',
				'__nonce': kleverlist_object.nonce,				
			};
			$(loader).removeClass('hidden');

			// Check if the form exists
			if ( $('#kleverlist_brands_settings' ).length) {
				$( sendyFormInput ).each( function(){
					$( this ).attr( "disabled", "disabled" );
					$( remove_button ).attr( "disabled", "disabled" );
				});
			}
			
			if ( $('#kleverlist_mailchimp_audience_settings' ).length) {
				$( mailchimpFormInput ).each( function(){
					$( this ).attr( "disabled", "disabled" );
					$( remove_button ).attr( "disabled", "disabled" );
				});
			}
			
			$.ajax({
				type: "post",
				url: kleverlist_object.ajax_url,
				data: data,
				success: function ( response ) {
					if( response!='' ){
						if( response.status ){			
							$(loader).addClass('hidden');									
							if ( $('#kleverlist_brands_settings' ).length) {
								$( sendyFormInput ).each( function(){
									$( this ).attr( "disabled", "disabled" );
									$( remove_button ).prop("disabled", false);
								});
							}

							if ( $('#kleverlist_mailchimp_audience_settings' ).length) {
								$( mailchimpFormInput ).each( function(){
									$( this ).attr( "disabled", "disabled" );
									$( remove_button ).prop("disabled", false);
								});
							}
							
							if (response.redirect_uri) {
							    window.location.href = response.redirect_uri;
							}
						}
					}
				}
			});
		}
	}
	/*** Remove Button Code End ***/

	/*** Sendy Option Hide / Show Code Start ***/
	$( document ).on( 'change','#mapping_integration_type', function(e){
		e.preventDefault();       
		if( this.value == "sendy" ){
			console.log( "type ===", this.value );
			$('.kleverlist-sendy-integration-section').removeClass("hide-block");
			$('.kleverlist-sendy-integration-section').addClass("show-block");
		} else {
			console.log( "type ===", this.value );
			$('.kleverlist-sendy-integration-section').removeClass("show-block");
			$('.kleverlist-sendy-integration-section').addClass("hide-block");
		}
	});
	/*** Sendy Option Hide / Show Code End ***/

	/*** Premium Plan Notice Popup Code Start ***/	
	$(document).ready(function() {		
		$( document ).on( 'click', '.kleverlist-premium-btn', function( e ){
			e.preventDefault();
			$('#kleverlist-notice-popup').fadeOut(500);
		});	

		$( document ).on( 'click', '.kleverlist-free-plan', function( e ){
			e.preventDefault();			
			$('#kleverlist-notice-popup').show();
		});		
	});
	/*** Premium Plan Notice Popup Code End ***/

	


	

})( jQuery );
