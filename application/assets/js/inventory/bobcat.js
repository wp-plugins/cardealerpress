	if( jQuery('#inventory-listing').length){
		
		jQuery('.inventory_get_gform').click(function(){
			form_name = jQuery(this).attr('name');
			key = jQuery(this).attr('key');
			form_id = jQuery('#inventory-gform-id').attr('form');
		
			if( jQuery('#'+key+' .list-form-wrapper').length ){
				if( jQuery(this).hasClass('active') ){
					jQuery('#'+key+' .list-form-wrapper').remove();
					jQuery(this).removeClass('active');
				} else {
					jQuery('#'+key+' .inventory_get_gform.active').removeClass('active');
					jQuery(this).addClass('active');
				}
			} else {
				jQuery('.inventory_get_gform.active').removeClass('active');
				jQuery('.list-form-wrapper.active').remove();
				jQuery(this).addClass('active');
				var form_data = {};
				jQuery('#'+key+' .form-value').each( function(){
					form_data[jQuery(this).attr('name')] = jQuery(this).text();
				});
				page_url = window.location.href;
				form_data['page_url'] = page_url;
				data = {'key': key, 'form': form_id, 'title': form_name, 'hooks': form_data};
		
				cdp_front_ajax_call( 'get_gform', data, '.inventory-form-container' ).done(function(result) {
					//jQuery('#'+key).siblings('.ajax-loading-message').removeClass('loading');
				}).fail(function() {
					alert('Form failed to load.');
				});
			}
	
			//console.log('FormID: '+form_id+' | Key: '+key+' | Name: '+form_name);
			//console.dir(form_data);
		});

		// inventory Search
		jQuery('#inventory-search-submit').click( function() {
			inventory_search_form( 'false' );
		});
		// inventory Advanced Search Show
		jQuery('#inventory-advance-show').click(function() {
			name = jQuery(this).attr('name');
			if ( name == 'hidden' ) {
			    jQuery('#inventory-search-advance').slideDown();
			    jQuery(this).attr('name', 'active').text('Hide Advanced');
			} else {
			    jQuery('#inventory-search-advance').slideUp();
			    jQuery(this).attr('name', 'hidden').text('Advanced Search');
			}
		});

		// inventory gform dialog
		jQuery('.inventory-more-info-form').click(function(e){
			vin = jQuery(e.target).attr('v_id');
			title = jQuery('#'+vin).find('.title-details').attr('title');
			price = jQuery('#'+vin).find('.inventory-main-price').text();
			stock = jQuery('#'+vin).find('.vehicle-stock').text();
			d_id = jQuery('#'+vin).find('.vehicle-dealer-id').text();

			jQuery('.vehicle-info-form-data input[type="text"], .vehicle-info-form-data textarea').attr('readonly','readonly');
			jQuery('.vehicle-info-form-data-dealer input[type="text"], .vehicle-info-form-data-dealer input').attr('readonly','readonly').attr('style','display: none;');
			jQuery('#list-gform-wrapper .vehicle-info-form-data textarea').text(title + '\n' + price + '\nStock: ' + stock + '\nVin: ' + vin);
			jQuery('#list-gform-wrapper .vehicle-info-form-data-dealer input').val( d_id );
			jQuery('#list-gform-wrapper').dialog({
				autoOpen: true,
				dialogClass: "dialog-list-gform-wrapper",
				modal: true,
				resizable: false,
				width: 420,
				title: title,
				beforeClose: function( event, ui ){
					
				}
			})
		});

	}

	if( jQuery('#inventory-detail').length){
		// inventory Slideshow
		jQuery(document).ready(function() {
			jQuery('#vehicle-images')
			.cycle({
				slides: '> a',
				fx: 'fade',
				pager: '#vehicle-thumbnails',
				pagerTemplate: '<a href="#"><img src="{{href}}" style="width:70px;height:50px;" /></a>'
			});

			jQuery('#vehicle-images > a')
			.lightbox({
				imageClickClose: false,
				loopImages: true,
				fitToScreen: true,
				scaleImages: true,
				xScale: 1.0,
				yScale: 1.0,
				displayDownloadLink: true
			});
		});
		
		// Collapse Toggle
		jQuery('.collapse-toggle').click(function(){
			toggle = jQuery(this).attr('name');
			if( jQuery('.collapse-divider.'+toggle).hasClass('collapsed') ){
				jQuery('.collapse-divider.'+toggle).removeClass('collapsed').slideDown('slow');
			} else {
				jQuery('.collapse-divider.'+toggle).addClass('collapsed').slideUp('slow');
			}
		});

		// inventory Tab Control
		jQuery('.tabs-button').click(function() {
			tab_name = jQuery(this).attr('name');
			jQuery(this).siblings('.active').removeClass('active');
			jQuery(this).parent().parent().find('.tabs-content.active').removeClass('active');
			jQuery(this).addClass('active');
			jQuery('.tabs-content-'+tab_name).addClass('active');
		});

		// Video Dialog
		var video_title = jQuery('#title-year').text() + ' ' + jQuery('#title-make').text() + ' ' + jQuery('#title-model').text();
		jQuery('#video-overlay-wrapper-dm').click(function(e) {
			jQuery('#dm-video-wrapper').dialog({
				autoOpen: true,
				dialogClass: "dialog-video-wrapper",
				modal: true,
				resizable: false,
				width: 640,
				height: 520,
				title: video_title
			})
		});

		jQuery('#video-overlay-wrapper').click(function(e) {
			var video_width;
			if( !video_width ){
				video_width = get_video_width();
				video_width = video_width + 35;//Added for dialog padding
			}
			jQuery('#wp-video-shortcode-wrapper').dialog({
				autoOpen: true,
				dialogClass: "dialog-video-wrapper",
				modal: true,
				resizable: false,
				width: video_width,
				title: video_title,
				beforeClose: function( event, ui ){
					jQuery('.mejs-pause > button').click();
				}
			})

			jQuery('.mejs-play > button').click();
		});

		function get_video_width(){
			results = jQuery('#wp-video-shortcode-wrapper > div').width();
			return results;
		}

	}

	jQuery(document).ready(function(){
		jQuery('#cardealerpress-inventory').parent().addClass('inventory-parentClass');
		
		// AIS iFrame
		var frame = jQuery('<div class="aisframe"><iframe width="785" src="about:blank" height="415" frameborder="0"></iframe></div>');

		frame.appendTo( 'body' );

		jQuery( '.aisframe' ).dialog({
			autoOpen: false,
			dialogClass: "dialog-ais-wrapper",
			modal: true,
			resizable: false,
			width: 820,
			height: 485,
			open: function( event , ui ) { jQuery( '.ui-widget-overlay').click( function() { jQuery( '.aisframe' ).dialog( 'close' ); } ); },
			title: 'Incentives and Rebates'
		});

		jQuery( '.view-available-rebates > a' ).click(
			function() {
				jQuery( '.aisframe' ).dialog( 'open' );
				return false;
			}
		);
	});

	function loadIframe( url ) {
		var iframe = jQuery( 'iframe' );
		if ( iframe.length ) {
			iframe.attr( 'src' , url );
			return false;
		}
		return true;
	}

	function inventory_search_form() {

		form = jQuery('#inventory-search');
		form_url = build_url();

		if ( form_url.length > 1) {
			jQuery(form).attr('action', form_url);
			jQuery('#search-form-submit').click();
		}

	}

	function get_search_params() {
		obj = {};

		if ( jQuery('#inventory-saleclass').val() == 'Certified' ) {
			obj['saleclass'] = 'Used';
			obj['certified'] = 'yes';
		} else {
			obj['saleclass'] = jQuery('#inventory-saleclass').val();
			obj['certified'] = '';
		}

		if( jQuery('#inventory-search-box.invalid').length ){
		obj['search'] = '';
		} else {
			obj['search'] = jQuery('#inventory-search-box').val();
		}

		if( jQuery('#price-range-flag').val() == 'true' ){
			obj['price_from'] = jQuery( "#price-range" ).slider( "values", 0 );
			obj['price_to'] = jQuery( "#price-range" ).slider( "values", 1 );
		}
		if( jQuery('#year-range-flag').val() == 'true' ){
			obj['year_from'] = jQuery( "#year-range" ).slider( "values", 0 );
			obj['year_to'] = jQuery( "#year-range" ).slider( "values", 1 );
		}
		if( jQuery('#odometer-range-flag').val() == 'true' ){
			obj['mileage_from'] = jQuery( "#odometer-range" ).slider( "values", 0 );
			obj['mileage_to'] = jQuery( "#odometer-range" ).slider( "values", 1 );
		}
		obj['vehicleclass'] = jQuery('#inventory-vehicleclass').val();

		return obj;
	}

	function get_filter_params() {
		obj = {};

		obj['make'] = jQuery('#search-make').val();
		obj['model'] = jQuery('#search-model').val();
		obj['trim'] = jQuery('#search-trim').val();

		return obj;
	}

	function get_hidden_params() {
		obj = {};

		obj['rewrite'] = jQuery('#hidden-rewrite').val();
		obj['saleclass'] = jQuery('#hidden-saleclass').val();

		return obj;
	}

	function build_url() {

		s_params = get_search_params();
		f_params = get_filter_params();
		h_params = get_hidden_params();

		url_text = '';
		url_query = '';
		url_return = '';

		jQuery.each(s_params, function(key, value) { // Search Params
			if ( key != 'saleclass' ) {
				if ( value ) {
					if ( url_query.length > 1 ) {
						url_query += '&'+ key + '=' + value;
					} else {
						url_query = key + '=' + value;
					}
				}
			}
		});

		if ( h_params['rewrite'] ) {
			url_text = '/inventory/' + s_params['saleclass'] + '/';

			if ( f_params['make'] ) { // Filter Params (clean)
				url_text += f_params['make'] + '/';
				if ( f_params['model'] ) {
					url_text += f_params['model'] + '/';
					if  ( f_params['trim'] ) {
						if( url_query.length > 1 ){
							url_query += '&trim=' + f_params['trim'];
						} else {
							url_query = 'trim=' + f_params['trim'];
						}
					}
				}
			}

			if ( url_query.length <= 2 ) {
				url_query = '';
			}

		} else {
			url_text = '?taxonomy=inventory' + '&saleclass=' + s_params['salaclass'];
			if ( f_params['make'] ) { // Filter Params (not clean)
				url_text += '&make=' + f_params['make'];
				if ( f_params['model'] ) {
					url_text += '&model=' + f_params['model'];
					if  ( f_params['trim'] ) {
						url_text += '&trim=' + f_params['trim'];
					}
				}
			}

			if ( url_query.length <= 2 ) {
				url_query = '';
			}
		}

		if( url_query.length > 1 ) {
			url_return = url_text + '?' + url_query;
		} else {
			url_return = url_text;
		}

		return url_return;

	}
	
	// AJAX Call
	function cdp_front_ajax_call( fn, params, wrapper ){
		return jQuery.ajax({
			url: ajax_path,
			data: {'action': 'cdp_front_ajax_request', 'fn': fn, 'params': params},
			dataType: 'json',
			beforeSend: function(){

			},
			success: function(data){
				if( data['id'].length > 0 ){
					jQuery('#'+data['id']+' .inventory-form-container').append(data['content']);
				}
			},
			complete: function(){

			},
			error: function(xhr, status, error) {
				//alert('Ajax call failed.');
				//alert(error);
			}
		});
	}
