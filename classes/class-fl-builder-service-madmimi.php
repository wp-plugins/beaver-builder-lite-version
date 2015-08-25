<?php

/**
 * Helper class for the Mad Mimi API.
 *
 * @since 1.5.2
 */
final class FLBuilderServiceMadMimi extends FLBuilderService {

	/**
	 * The ID for this service.
	 *
	 * @since 1.5.2
	 * @var string $id
	 */  
	public $id = 'madmimi';

	/**
	 * @since 1.5.2
	 * @var object $api_instance
	 * @access private
	 */  
	private $api_instance = null;

	/**
	 * Get an instance of the API.
	 *
	 * @since 1.5.2
	 * @param string $api_email The email address associated with the API key.
	 * @param string $api_key A valid API key.
	 * @return object The API instance.
	 */  
	public function get_api( $api_email, $api_key ) 
	{
		if ( $this->api_instance ) {
			return $this->api_instance;
		}
		if ( ! class_exists( 'MadMimi' ) ) {
			require_once FL_BUILDER_DIR . 'includes/vendor/madmimi/MadMimi.class.php';
		}
		
		$this->api_instance = new MadMimi( $api_email, $api_key );
		
		return $this->api_instance;
	}
	
	/**
	 * Test the API connection.
	 *
	 * @since 1.5.2
	 * @param array $fields {
	 *      @type string $api_email A valid email address.
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
		
		// Make sure we have an email address.
		if ( ! isset( $fields['api_email'] ) || empty( $fields['api_email'] ) ) {
			$response['error'] = __( 'Error: You must provide an email address.', 'fl-builder' );
		}
		// Make sure we have an API key.
		else if ( ! isset( $fields['api_key'] ) || empty( $fields['api_key'] ) ) {
			$response['error'] = __( 'Error: You must provide an API key.', 'fl-builder' );
		}
		// Try to connect and store the connection data.
		else {
			
			$api = $this->get_api( $fields['api_email'], $fields['api_key'] );
			
			libxml_use_internal_errors( true );

			if ( ! simplexml_load_string( $api->Lists() ) ) {
				$response['error'] = __( 'Unable to connect to Mad Mimi. Please check your credentials.', 'fl-builder' );
			}
			else {
				$response['data'] = array( 
					'api_email' => $fields['api_email'],
					'api_key' => $fields['api_key']
				);
			}
		}
		
		return $response;
	}

	/**
	 * Renders the markup for the connection settings.
	 *
	 * @since 1.5.2
	 * @return string The connection settings markup.
	 */  
	public function render_connect_settings() 
	{
		ob_start();
		
		FLBuilder::render_settings_field( 'api_email', array(
			'row_class'     => 'fl-builder-service-connect-row',
			'class'         => 'fl-builder-service-connect-input',
			'type'          => 'text',
			'label'         => __( 'Email Address', 'fl-builder' ),
			'help'          => __( 'The email address associated with your Mad Mimi account.', 'fl-builder' ),
			'preview'       => array(
				'type'          => 'none'
			)
		)); 
		
		FLBuilder::render_settings_field( 'api_key', array(
			'row_class'     => 'fl-builder-service-connect-row',
			'class'         => 'fl-builder-service-connect-input',
			'type'          => 'text',
			'label'         => __( 'API Key', 'fl-builder' ),
			'help'          => __( 'Your API key can be found in your Mad Mimi account under Account > Settings &amp; Billing > API.', 'fl-builder' ),
			'preview'       => array(
				'type'          => 'none'
			)
		)); 
		
		return ob_get_clean();
	}

	/**
	 * Render the markup for service specific fields. 
	 *
	 * @since 1.5.2
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
		$api            = $this->get_api( $account_data['api_email'], $account_data['api_key'] );
		$response       = array( 
			'error'         => false, 
			'html'          => '' 
		);
		
		libxml_use_internal_errors( true );

		$result = simplexml_load_string( $api->Lists() );
		
		if ( ! $result ) {
			$response['error'] = __( 'There was a problem retrieving your lists. Please check your API credentials.', 'fl-builder' );
		}
		else {
			$response['html'] = $this->render_list_field( $result, $settings );
		}
		
		return $response;
	}

	/**
	 * Render markup for the list field. 
	 *
	 * @since 1.5.2
	 * @param array $lists List data from the API.
	 * @param object $settings Saved module settings.
	 * @return string The markup for the list field.
	 * @access private
	 */  
	private function render_list_field( $lists, $settings ) 
	{
		ob_start();
		
		$options = array( '' => __( 'Choose...', 'fl-builder' ) );
		
		foreach ( $lists->list as $list ) {
			$options[ ( string ) $list['id'] ] = $list['name'];
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
	 * Subscribe an email address to Mad Mimi.
	 *
	 * @since 1.5.2
	 * @param object $settings A module settings object.
	 * @param string $email The email to subscribe.
	 * @param string $name Optional. The full name of the person subscribing.
	 * @return array {
	 *      @type bool|string $error The error message or false if no error.
	 * }
	 */  
	public function subscribe( $settings, $email, $name = false )
	{
		$account_data = $this->get_account_data( $settings->service_account );
		$response     = array( 'error' => false );
		
		if ( ! $account_data ) {
			$response['error'] = __( 'There was an error subscribing to Mad Mimi. The account is no longer connected.', 'fl-builder' );
		}
		else {
			
			$api    = $this->get_api( $account_data['api_email'], $account_data['api_key'] );
			$data   = array( 
				'email'     => $email,
				'add_list'  => $settings->list_id
			);
			
			if ( $name ) {
				
				$names = explode( ' ', $name );
				
				if ( isset( $names[0] ) ) {
					$data['firstName'] = $names[0];
				}
				if ( isset( $names[1] ) ) {
					$data['lastName'] = $names[1];
				}
			}
			
			ob_start();
			$api->AddUser( $data, true );
			$request = ob_get_clean();
			
			if ( stristr( $request, 'Unable to authenticate' ) ) {
				$response['error'] = __( 'There was an error subscribing to Mad Mimi. The account is no longer connected.', 'fl-builder' );
			}
		}
		
		return $response;
	}
}