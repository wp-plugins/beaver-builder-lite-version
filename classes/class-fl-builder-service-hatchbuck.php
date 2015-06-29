<?php

/**
 * Helper class for the Hatchbuck API.
 *
 * @since 1.5.8
 */
final class FLBuilderServiceHatchbuck extends FLBuilderService {

	/**
	 * The ID for this service.
	 *
	 * @since 1.5.8
	 * @var string $id
	 */  
	public $id = 'hatchbuck';

	/**
	 * The API url for this service.
	 *
	 * @since 1.5.8
	 * @access private
	 * @var string $api_url
	 */  
	private $api_url = 'https://api.hatchbuck.com/api/v1/contact/';
	
	/**
	 * Test the API connection.
	 *
	 * @since 1.5.8
	 * @param array $fields {
	 *      @type string $api_key A valid API key.
	 * }
	 * @return array{
	 *      @type bool|string $error The error message or false if no error.
	 *      @type array $data An array of data used to make the connection.
	 * }
	 */  
	public function connect( $fields = array() ) 
	{
		$response = array( 
			'error'  => false,
			'data'   => array()
		);
		
		// Make sure we have an API key.
		if ( ! isset( $fields['api_key'] ) || empty( $fields['api_key'] ) ) {
			$response['error'] = __( 'Error: You must provide an API key.', 'fl-builder' );
		}
		// Try to connect and store the connection data.
		else {
			
			$result = wp_remote_post( $this->api_url . 'search?api_key=' . $fields['api_key'], array(
				'method'	=> 'POST',
				'timeout'	=> 60,
				'headers' 	=> array(
					'Content-Type' => 'application/json'
				),
				'body' 		=> array(),
			) );
			
			if ( 401 == $result['response']['code'] ) {
				$response['error'] = __( 'Error: Please check your API key.', 'fl-builder' );
			}
			else {
				$response['data'] = array( 'api_key' => $fields['api_key'] );
			}
		}
		
		return $response;
	}

	/**
	 * Renders the markup for the connection settings.
	 *
	 * @since 1.5.8
	 * @return string The connection settings markup.
	 */  
	public function render_connect_settings() 
	{
		ob_start();
		
		FLBuilder::render_settings_field( 'api_key', array(
			'row_class'     => 'fl-builder-service-connect-row',
			'class'         => 'fl-builder-service-connect-input',
			'type'          => 'text',
			'label'         => __( 'API Key', 'fl-builder' ),
			'help'          => __( 'Your API key can be found in your Hatchbuck account under Account Settings > Web API.', 'fl-builder' ),
			'preview'       => array(
				'type'          => 'none'
			)
		)); 
		
		return ob_get_clean();
	}

	/**
	 * Render the markup for service specific fields. 
	 *
	 * @since 1.5.8
	 * @param string $account The name of the saved account.
	 * @param object $settings Saved module settings.
	 * @return array {
	 *      @type bool|string $error The error message or false if no error.
	 *      @type string $html The field markup.
	 * }
	 */  
	public function render_fields( $account, $settings ) 
	{
		$response = array( 
			'error'  => false, 
			'html'   => $this->render_tag_field( $settings )
		);
		
		return $response;
	}

	/**
	 * Render markup for the tag field. 
	 *
	 * @since 1.5.8
	 * @param object $settings Saved module settings.
	 * @return string The markup for the tag field.
	 * @access private
	 */  
	private function render_tag_field( $settings ) 
	{
		ob_start();
		
		FLBuilder::render_settings_field( 'list_id', array(
			'row_class'     => 'fl-builder-service-field-row',
			'class'         => 'fl-builder-service-list-select',
			'type'          => 'text',
			'label'         => _x( 'Tag', 'A tag to add to contacts in Hatchbuck when they subscribe.', 'fl-builder' ),
			'preview'       => array(
				'type'          => 'none'
			)
		), $settings); 
		
		return ob_get_clean();
	}

	/** 
	 * Subscribe an email address to Hatchbuck.
	 *
	 * @since 1.5.8
	 * @param object $settings A module settings object.
	 * @param string $email The email to subscribe.
	 * @param string $name Optional. The full name of the person subscribing.
	 * @return array {
	 *      @type bool|string $error The error message or false if no error.
	 * }
	 */  
	public function subscribe( $settings, $email, $name = false )
	{
		$contact_id	  = null;
		$account_data = $this->get_account_data( $settings->service_account );
		$response     = array( 'error' => false );
		
		if ( ! $account_data ) {
			$response['error'] = __( 'There was an error subscribing to Hatchbuck. The account is no longer connected.', 'fl-builder' );
		}
		else {
			
			// Build the data array.
			$data = array( 
				'emails' => array(
					array(
						'address' => $email,
						'type'	  => 'Work'
					)
				),
				'status' => array(
					'name' => 'Lead'
				)
			);
			
			// Check if the contact exists.
			$result = wp_remote_post( $this->api_url . 'search?api_key=' . $account_data['api_key'], array(
				'method'	=> 'POST',
				'timeout'	=> 60,
				'headers' 	=> array(
					'Content-Type' => 'application/json'
				),
				'body' 		=> json_encode( $data ),
			) );
			
			// Return if we have an API key error.
			if ( 401 == $result['response']['code'] ) {
				$response['error'] = __( 'There was an error subscribing to Hatchbuck. The API key is invalid.', 'fl-builder' );
				return $response; // Invalid API key.
			}
			// Contact already exists.
			else if ( 200 == $result['response']['code'] ) {
				$result_data = json_decode( $result['body'] );
				$contact_id  = $result_data[0]->contactId;
			}
			// Generic error. Contact not found should be 400.
			else if ( 400 != $result['response']['code'] ) {
				$response['error'] = __( 'There was an error subscribing to Hatchbuck.', 'fl-builder' );
				return $response;
			}
			
			// Add the contact if it doesn't exist.
			if ( ! $contact_id ) {
			
				// Add the name to the data array if we have one.
				if ( $name ) {
					
					$names = explode( ' ', $name );
					
					if ( isset( $names[0] ) ) {
						$data['firstName'] = $names[0];
					}
					if ( isset( $names[1] ) ) {
						$data['lastName'] = $names[1];
					}
				}
				
				// Add the contact to Hatchbuck.
				$result = wp_remote_post( $this->api_url . '?api_key=' . $account_data['api_key'], array(
					'method'	=> 'POST',
					'timeout'	=> 60,
					'headers' 	=> array(
						'Content-Type' => 'application/json'
					),
					'body' 		=> json_encode( $data ),
				) );
				
				// Return if we have an error.
				if ( 200 != $result['response']['code'] ) {
					$response['error'] = __( 'There was an error subscribing to Hatchbuck.', 'fl-builder' );
					return $response;
				}
				
				// Get the result data that contains the new contact ID.
				$result_data = json_decode( $result['body'] );
				$contact_id  = $result_data->contactId;
			}
			
			// Add the tag to the contact. 
			$result = wp_remote_post( $this->api_url  . $contact_id . '/Tags?api_key=' . $account_data['api_key'], array(
				'method'	=> 'POST',
				'timeout'	=> 60,
				'headers' 	=> array(
					'Content-Type' => 'application/json'
				),
				'body' 		=> json_encode( array(
					array( 'name' => $settings->list_id )
				) ),
			) );
		}
		
		return $response;
	}
}