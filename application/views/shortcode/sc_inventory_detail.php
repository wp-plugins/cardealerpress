<?php
namespace Wordpress\Plugins\CarDealerPress\Inventory\Api;

	$this->vms->tracer = 'Obtaining requested sc inventory.';
	$inventory = $this->vms->get_inventory()->please( $this->sc_atts );

	$state = $this->company->state;
	$city = $this->company->city;
	$c_code = $this->company->country_code;

	$sc_content =' <div style="display: none; width: 100%; text-align: center;" id="cdp-ajax-status">0 | 0</div>';
	$sc_content .= '<div id="sc-detail-container" >';
	
	if( $this->sc_search ){
		$sc_content .= '<div id="sc-detail-search-container">';
		//Mobile
		$sc_content .= '<div id="sc-detail-mobile-wrapper"><div id="mobile-image-wrapper"><img id="mobile-action-button" src="'.cdp_get_image_source().'search_icon.png" /></div></div>';
		//SaleClass
		$sc_content .= cdp_create_saleclass_dd_sc($this->sc_flag_saleclass, $this->sc_default_saleclass);
		//Makes
		$sc_content .= '<div class="sc-search-item makes">';
		$sc_content .= '<label class="sc-search-label">Makes</label>';
		$sc_content .= '<select id="sc-search-make" class="inventory-select" onchange="cdp_vehicle_caller(this.value, \'make\');">';
		$makes = cdp_generate_make_options( $this->vms, $this->sc_default_saleclass, $this->options['vehicle_management_system']['data']['makes_new'], FALSE, $this->sc_dealer_ID );
		$sc_content .= isset( $makes['display'] ) ? $makes['display'] : '';
		$sc_content .= '</select></div>';
		//Models
		$models = cdp_generate_model_options( $this->vms, $this->sc_default_saleclass, $makes['val'], FALSE );
		$disabled = isset( $models['disabled'] ) ? 'disabled' : '';
		$sc_content .= '<div class="sc-search-item models">';
		$sc_content .= '<label class="sc-search-label">Models</label>';
		$sc_content .= '<select id="sc-search-model" class="inventory-select" '.$disabled.' onchange="cdp_vehicle_caller(this.value, \'model\');">';
		$sc_content .= isset( $models['display'] ) ? $models['display'] : '';
		$sc_content .= '</select></div>';
		//Trims
		$trims = cdp_generate_trim_options( $this->vms, $this->sc_default_saleclass, $makes['val'], $models['val'], FALSE );
		$disabled = isset( $trims['disabled'] ) ? 'disabled' : '';
		$sc_content .= '<div class="sc-search-item trims">';
		$sc_content .= '<label class="sc-search-label">Trims</label>';
		$sc_content .= '<select id="sc-search-trim" class="inventory-select" '.$disabled.' onchange="cdp_vehicle_caller(this.value, \'trim\');">';
		$sc_content .= isset( $trims['display'] ) ? $trims['display'] : '';
		$sc_content .= '</select></div>';
		$sc_content .= '</div>';
	}
	
	if( function_exists('gravity_form_enqueue_scripts') ){
		if( $this->sc_form ){
			$sc_content .= '<div style="display: none;" form="'.$this->sc_form.'" id="sc-detail-form-id"></div>';
			gravity_form_enqueue_scripts($this->sc_form, true);
		}
	}
	
	if( empty( $inventory ) ) {
		$sc_content .= '<div class="sc-not-found"><h2><strong>Search criteria did not return any results.</strong></h2></div>';
	} else {
		foreach( $inventory as $inventory_item ){
			$vehicle = itemize_vehicle($inventory_item);

			$sc_content .= get_sc_detail_vehicles( $vehicle, $this->vrs, $this->company, $this->options, $this->options['vehicle_management_system']['custom_contact'], $this->sc_form );
		}
	}
	$sc_content .= '</div>';
	
	//AJAX Helpers
	$sc_content .= '<div style="display: none;" id="cdp-ajax-loader">';
	$sc_content .= $this->sc_form ? '<div key="form">'.$this->sc_form.'</div>' : '';
	foreach( $this->sc_atts as $key => $att ){
		$sc_content .= '<div key="'.$key.'">'.$att.'</div>';
	}
	$sc_content .= '</div>';
	
	
	$this->sc_content = $sc_content;

?>
