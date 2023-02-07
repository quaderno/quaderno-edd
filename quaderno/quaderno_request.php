<?php
/**
* Quaderno Base
*
* @package   Quaderno PHP
* @author    Quaderno <support@quaderno.io>
* @copyright Copyright (c) 2015-2023, Quaderno
* @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
*/

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

/* General interface that implements the calls to the message coding and transport library */
class QuadernoRequest {

	private $http_method = 'GET';
	private $username = '';
	private $password = 'foo';
	private $content_type = 'application/json';
	private $api_url = 'https://quadernoapp.com/api/v1/';
	private $api_version = '20170628';
	private $request_methods = null;
	private $request_endpoint = null;
	private $request_body = null;
	private $response = null;
	private $error_message = '';

	public function __construct() {
		global $edd_options;
		$this->username = $edd_options['edd_quaderno_token'];
		$this->api_url = $edd_options['edd_quaderno_url'];
	}

	private function build_request_url() {
		$url = $this->api_url;

		// Add request methods
		if ( ! is_null( $this->request_methods ) && is_array( $this->request_methods ) && count( $this->request_methods ) > 0 ) {
			$url .= implode( '/', $this->request_methods ) . '/';
		}

		// Add the request_endpoint
		if ( ! is_null( $this->request_endpoint ) && '' != $this->request_endpoint ) {
			$url .= $this->request_endpoint . '.json';
		}

		// Add the request body if this is a GET request
		if ( 'GET' == $this->http_method && ! is_null( $this->request_body ) && is_array( $this->request_body ) && count( $this->request_body ) > 0 ) {
			$url .= '?' . build_query( $this->request_body );
		}

		return $url;
	}

	public function exec() {
    $args = array(
  	  'method' => $this->http_method,
  	  'headers' => array(
  	    'Authorization' => 'Basic ' . base64_encode( $this->username . ':' . $this->password ),
  	    'Content-Type' => $this->content_type,
  	    'Accept' => 'application/json; api_version=' . $this->api_version
  	  ),
  	  'timeout' => 70,
      'sslverify' => false
  	);

  	// Add the request body if we've got one
  	if ( 'GET' != $this->http_method && !is_null( $this->request_body ) && is_array( $this->request_body ) && count( $this->request_body ) > 0 ) {
  		$args['body'] = json_encode( $this->request_body );
  	}

  	// Get results
  	$this->response = wp_remote_request($this->build_request_url(), $args);

		// Process errors
		if ( is_wp_error($this->response) ) {
			$this->error_message = __( 'There was a problem connecting to the API.', 'edd-quaderno' );
			$this->response = null;
			return false;
		}
		
		if ( '299' < $this->response['response']['code'] ) {
			$this->error_message = $this->response['response']['message'];
			$this->response = null;
			return false;
		}

		return true;
	}

	public function get_response_body() {
		if ( ! is_null( $this->response ) ) {
			return json_decode( $this->response['body'] );
		}

		return array();
	}
	
	public function find($model, $params = null) {
		$this->request_methods = array( $model );
		$this->request_body = $params;
		return $this->exec();
	}

	public function findByID($model, $id) {
		$this->request_methods = array( $model, $id );
		return $this->exec();
	}

	public function save($model, $id, $data) {
		if ( !is_null($id) ) {
			$this->http_method = 'PUT';
			$this->request_methods = array( $model, $id );
		} else {
			$this->http_method = 'POST';
			$this->request_methods = array( $model );
		}
		$this->request_body = $data;
		return $this->exec();
	}

	public function saveNested($parentmodel, $parentid, $model, $data) {
		$this->http_method = 'POST';
		$this->request_methods = array( $parentmodel, $parentid, $model );
		$this->request_body = $data;
		return $this->exec();
	}

	public function delete($model, $id) {
		$this->http_method = 'DELETE';
		$this->request_methods = array( $model, $id );
		return $this->exec();
	}

	public function deleteNested($parentmodel, $parentid, $model, $id) {
		$this->http_method = 'DELETE';
		$this->request_methods = array( $parentmodel, $parentid, $model, $id );
		return $this->exec();
	}

	public function calculate($model, $params) {
		$this->request_methods = array( $model );
		$this->request_endpoint = 'calculate';
		$this->request_body = $params;
		return $this->exec();
	}

	public function validate($model, $params) {
		$this->request_methods = array( $model );
		$this->request_endpoint = 'validate';
		$this->request_body = $params;
		return $this->exec();
	}

	public function deliver($model, $id) {
		$this->request_methods = array( $model, $id );
		$this->request_endpoint = 'deliver';
		return $this->exec();
	}

}
