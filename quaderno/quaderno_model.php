<?php
/**
* Quaderno Load
*
* Interface for every model
*
* @package   Quaderno PHP
* @author    Quaderno <support@quaderno.io>
* @copyright Copyright (c) 2015-2023, Quaderno
* @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
*/

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

abstract class QuadernoModel extends QuadernoClass {
	/**
	*  Find for QuadernoModel objects
	* If $params is a single value, it returns a single object
	* If $params is null or an array, it returns an array of objects
	* When request fails, it returns false 
	*/
	public static function find( $params = array('page' => 1) ) {
		$return = false;
		$class = get_called_class();
		$request = new QuadernoRequest();

		if ( !is_array($params) ) {
			// Searching for an ID
			if ( $request->findByID(static::$model, $params) ) {
				$return = $request->get_response_body();
			}
		} else {
			if ( $request->find(static::$model, $params) ) {
				$return = array();
				$response = $request->get_response_body();
				$length = count($response);
				for ($i = 0; $i < $length; $i++)
					$return[$i] = $response[$i];
			}
		}

		return $return;
	}

	/**
	* Save for QuadernoModel objects
	* Export object data to the model
	* Returns true or false whether the request is accepted or not
	*/
	public function save() {
		$new_object = false;
		$new_data = false;
		$return = false;
		$request = new QuadernoRequest();

		/**
		* 1st step - New object to be created 
		* Check if the current object has not been created yet
		*/
		if ( is_null($this->id) ) {
			// Not yet created, let's do it
			$new_object = true;

			/* Update data with the response */
			if ( $request->save( static::$model, $this->id, $this->data ) ) {
				$this->data = get_object_vars( $request->get_response_body() );
				$return = true;
			}
			elseif (isset($request->error_message)) {
				$this->errors = $request->error_message;
			}
		}

		/**
		* 2nd step - Payments to be created
		* Check if there are any payments stored and not yet created
		*/
		if (isset($this->payments_array) && count($this->payments_array)) {
			foreach ( $this->payments_array as $index => $p )
				if ( is_null( $p->id ) ) {
					// The payment does not have ID -> Not yet created
					if ( $request->saveNested( static::$model, $this->id, 'payments', $p->data ) ) {
						$p->data = get_object_vars( $request->get_response_body() );
						$this->data = get_object_vars(self::find( $this->id ));
					}
					elseif ( isset( $request->error_message ) ) {
						$this->errors = $request->error_message;
					}
				}
		}

		/**
		* 3rd step - Update object
		* Update object - This is only necessary when it's not a new object, or new payments have been created.
		*/
		if ( !$new_object || $new_data ) {
			if ( $request->save(static::$model, $this->id, $this->data) ) {
				$return = true;
				$this->data = get_object_vars( $request->get_response_body() );
			}
			elseif ( isset( $request->error_message ) ) {
				$this->errors = $request->error_message;
			}
		}

		return $return;
	}

	/**
	* Delete for QuadernoModel objects
	* Delete object from the model
	* Returns true or false whether the request is accepted or not
	*/
	public function delete() {
		$return = false;
		$request = new QuadernoRequest();

		if ( $request->delete(static::$model, $this->id) ) {
			$return = true;
			$this->data = array();
		}
		elseif ( isset( $request->error_message ) ) {
			$this->errors = $request->error_message;
		}

		return $return;
	}

}
?>