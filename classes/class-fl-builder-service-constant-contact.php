<?php

/**
 * Helper class for the Constant Contact API.
 *
 * @since 1.5.4
 */
final class FLBuilderServiceConstantContact extends FLBuilderService {

	/**
	 * The ID for this service.
	 *
	 * @since 1.5.4
	 * @var string $id
	 */  
	public $id = 'constant-contact';

	/**
	 * The api url for this service.
	 *
	 * @since 1.5.4
	 * @var string $api_url
	 */  
	public $api_url = 'https://api.constantcontact.com/v2/';
	
	/**
	 * Test the API connection.
	 *
	 * @since 1.5.4
	 * @param array $fields {
	 *      @type string $api_key A valid API key.
	 *      @type string $access_token A valid access token.
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
		// Make sure we have an access token.
		else if ( ! isset( $fields['access_token'] ) || empty( $fields['access_token'] ) ) {
			$response['error'] = __( 'Error: You must provide an access token.', 'fl-builder' );
		}
		// Try to connect and store the connection data.
		else {
			
			$url      = $this->api_url . 'lists?api_key=' . $fields['api_key'] . '&access_token=' . $fields['access_token'];
			$request  = json_decode( wp_remote_retrieve_body( wp_remote_get( $url ) ) );
			
			if ( ! is_array( $request ) || ( isset( $request[0] ) && isset( $request[0]->error_message ) ) ) {
				$response['error'] = sprintf( __( 'Error: Could not connect to Constant Contact. %s', 'fl-builder' ), $request[0]->error_message );
			}
			else {
				$response['data'] = array( 
					'api_key'       => $fields['api_key'],
					'access_token'  => $fields['access_token'] 
				);
			}
		}
		
		return $response;
	}

	/**
	 * Renders the markup for the connection settings.
	 *
	 * @since 1.5.4
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
			'help'          => __( 'Your Constant Contact API key.', 'fl-builder' ),
			'preview'       => array(
				'type'          => 'none'
			)
		)); 
		
		FLBuilder::render_settings_field( 'access_token', array(
			'row_class'     => 'fl-builder-service-connect-row',
			'class'         => 'fl-builder-service-connect-input',
			'type'          => 'text',
			'label'         => __( 'Access Token', 'fl-builder' ),
			'help'          => __( 'Your Constant Contact access token.', 'fl-builder' ),
			'description'   => sprintf( __( 'You must register a <a%s>Developer Account</a> with Constant Contact to obtain an API key and access token. Please see <a%s>Getting an API key</a> for complete instructions.', 'fl-builder' ), ' href="https://constantcontact.mashery.com/member/register" target="_blank"', ' href="https://developer.constantcontact.com/home/api-keys.html" target="_blank"' ),
			'preview'       => array(
				'type'          => 'none'
			)
		)); 
		
		return ob_get_clean();
	}

	/**
	 * Render the markup for service specific fields. 
	 *
	 * @since 1.5.4
	 * @param string $account The name of the saved account.
	 * @param object $settings Saved module settings.
	 * @return array {
	 *      @type bool|string $error The error message or false if no error.
	 *      @type string $html The field markup.
	 * }
	 */  
	public function render_fields( $account, $settings ) 
	{
		$account_data   = $this->get_account_data( $account );
		$api_key        = $account_data['api_key'];
		$access_token   = $account_data['access_token'];
		$url            = $this->api_url . 'lists?api_key=' . $api_key . '&access_token=' . $access_token;
		$request        = json_decode( wp_remote_retrieve_body( wp_remote_get( $url ) ) );
		$response       = array( 
			'error'         => false, 
			'html'          => '' 
		);
		
		if ( ! is_array( $request ) || ( isset( $request[0] ) && isset( $request[0]->error_message ) ) ) {
			$response['error'] = sprintf( __( 'Error: Could not connect to Constant Contact. %s', 'fl-builder' ), $request[0]->error_message );
		}
		else {
			$response['html'] = $this->render_list_field( $request, $settings );
		}
		
		return $response;
	}

	/**
	 * Render markup for the list field. 
	 *
	 * @since 1.5.4
	 * @param array $lists List data from the API.
	 * @param object $settings Saved module settings.
	 * @return string The markup for the list field.
	 * @access private
	 */  
	private function render_list_field( $lists, $settings ) 
	{
		ob_start();
		
		$options = array( '' => __( 'Choose...', 'fl-builder' ) );
		
		foreach ( $lists as $list ) {
			$options[ $list->id ] = $list->name;
		}
		
		FLBuilder::render_settings_field( 'list_id', array(
			'row_class'     => 'fl-builder-service-field-row',
			'class'         => 'fl-builder-service-list-select',
			'type'          => 'select',
			'label'         => _x( 'List', 'An email list from a third party provider.', 'fl-builder' ),
			'options'       => $options,
			'preview'       => array(
				'type'          => 'none'
			)
		), $settings); 
		
		return ob_get_clean();
	}

	/** 
	 * Subscribe an email address to Constant Contact.
	 *
	 * @since 1.5.4
	 * @param object $settings A module settings object.
	 * @param string $email The email to subscribe.
	 * @param string $name Optional. The full name of the person subscribing.
	 * @return array {
	 *      @type bool|string $error The error message or false if no error.
	 * }
	 */  
	public function subscribe( $settings, $email, $name = false )
	{
		$account_data   = $this->get_account_data( $settings->service_account );
		$response       = array( 'error' => false );
		
		if ( ! $account_data ) {
			$response['error'] = __( 'There was an error subscribing to Constant Contact. The account is no longer connected.', 'fl-builder' );
		}
		else {
			
			$api_key        = $account_data['api_key'];
			$access_token   = $account_data['access_token'];
			$url            = $this->api_url . 'contacts?api_key=' . $api_key . '&access_token=' . $access_token . '&email=' . $email;
			$request        = wp_remote_get( $url );
			$contact        = json_decode( wp_remote_retrieve_body( $request ) );
			$list_id        = $settings->list_id;
			
			// This contact exists.
			if ( ! empty( $contact->results ) ) {
			 
				$args = array();
				$data = $contact->results[0];
				
				// Check if already subscribed to this list.
				if ( ! empty( $data->lists ) ) {
					
					// Return early if already added.
					foreach ( $data->lists as $key => $list ) {
						if ( isset( $list->id ) && $list_id == $list->id ) {
							return $response;
						}
					}
					
					// Add an existing contact to the list.
					$new_list                             = new stdClass;
					$new_list->id                         = $list_id;
					$new_list->status                     = 'ACTIVE';
					$data->lists[ count( $data->lists ) ] = $new_list;
				} 
				else {
					
					// Add an existing contact that has no list.
					$data->lists      = array();
					$new_list         = new stdClass;
					$new_list->id     = $list_id;
					$new_list->status = 'ACTIVE';
					$data->lists[0]   = $new_list;
				}
			   
				$args['body']                      = json_encode( $data );
				$args['method']                    = 'PUT';
				$args['headers']['Content-Type']   = 'application/json';
				$args['headers']['Content-Length'] = strlen( $args['body'] );
				$url                               = $this->api_url . 'contacts/' . $contact->results[0]->id . '?api_key=' . $api_key . '&access_token=' . $access_token . '&action_by=ACTION_BY_VISITOR';
				$update                            = wp_remote_request( $url, $args );
				$res                               = json_decode( wp_remote_retrieve_body( $update ) );
	
				if ( isset( $res->error_key ) ) {
					$response['error'] = sprintf( __( 'There was an error subscribing to Constant Contact. %s', 'fl-builder' ), $res->error_key );
				}
			}
			// Add a new contact.
			else {
				
				$args                                         = $data = array();
				$data['email_addresses']                      = array();
				$data['email_addresses'][0]['id']             = $list_id;
				$data['email_addresses'][0]['status']         = 'ACTIVE';
				$data['email_addresses'][0]['confirm_status'] = 'CONFIRMED';
				$data['email_addresses'][0]['email_address']  = $email;
				$data['lists']                                = array();
				$data['lists'][0]['id']                       = $list_id;
				
				if ( $name ) {
					
					$names = explode( ' ', $name );
					
					if ( isset( $names[0] ) ) {
						$data['first_name'] = $names[0];
					}
					if ( isset( $names[1] ) ) {
						$data['last_name'] = $names[1];
					}
				}
	
				$args['body']                      = json_encode( $data );
				$args['headers']['Content-Type']   = 'application/json';
				$args['headers']['Content-Length'] = strlen( json_encode( $data ) );
				$url                               = $this->api_url . 'contacts?api_key=' . $api_key . '&access_token=' . $access_token . '&action_by=ACTION_BY_VISITOR';
				$create                            = wp_remote_post( $url, $args );
	
				if ( isset( $create->error_key ) ) {
					$response['error'] = sprintf( __( 'There was an error subscribing to Constant Contact. %s', 'fl-builder' ), $create->error_key );
				}
			}
		}
		
		return $response;
	}
}