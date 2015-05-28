<?php

namespace Wordpress\Plugins\CarDealerPress\Inventory\Api;

class http_request {

	const timeout = 20;

	public $url = null;

	public $group = null;

	public $request_parameters = array(
		'timeout' => http_request::timeout,
		'headers' => array(
			'Referer' => NULL,
			'X-WordPress-Version' => NULL,
			'X-Plugin-Version' => NULL
		)
	);

	function __construct( $url , $group ) {
		global $wp , $wp_version;
		$this->url = $url;
		$this->group = $group;
		$plugin_options = get_option( 'cardealerpress_settings' );
		$this->request_parameters[ 'headers' ][ 'Referer' ] = $wp ? site_url() . '/' . $wp->request : site_url().'/';
		$this->request_parameters[ 'headers' ][ 'X-WordPress-Version' ] = $wp_version;
		$this->request_parameters[ 'headers' ][ 'X-Plugin-Version' ] = cdp_plugin::$plugin_information[ 'Version' ];	
	}

	function cached() {
		return wp_cache_get( $this->url , $this->group );
	}

	function get_file( $sanitize = false ) {
		$response = wp_remote_request( $this->url , $this->request_parameters );
		$this->cache_file( $response );
		if( wp_remote_retrieve_response_code( $response ) == 200 ) {
			if( $sanitize == true ) {
				$response[ 'body' ] = wp_kses_data( $response[ 'body' ] );
			}
			return $response;
		} else {
			if( is_wp_error( $response) ) {
				$error_message = $response->get_error_message();
				$error_code = $response->get_error_message();
			} else {
				$error_code = wp_remote_retrieve_response_code( $response );
				$error_message = wp_remote_retrieve_response_message( $response );
			}
			$error_array = array( 'code' => $error_code , 'message' => $error_message );
			return $error_array;
		}
	}

	function cache_file( $data ) {
		return wp_cache_add( $this->url , $data , $this->group , 7200 );
	}

}

?>