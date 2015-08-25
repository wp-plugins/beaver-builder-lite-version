<?php

/**
 * Helper class for the Infusionsoft API.
 *
 * @since 1.5.7
 */
final class FLBuilderServiceInfusionsoft extends FLBuilderService {

	/**
	 * The ID for this service.
	 *
	 * @since 1.5.7
	 * @var string $id
	 */	 
	public $id = 'infusionsoft';

	/**
	 * @since 1.5.7
	 * @var object $api_instance
	 * @access private
	 */	 
	private $api_instance = null;

	/**
	 * Get an instance of the API.
	 *
	 * @since 1.5.7
	 * @param string $app_id A valid app ID.
	 * @param string $api_key A valid API key.
	 * @return object The API instance.
	 */	 
	public function get_api( $app_id, $api_key ) 
	{
		if ( $this->api_instance ) {
			return $this->api_instance;
		}
		if ( ! class_exists( 'iSDK' ) ) {
			require_once FL_BUILDER_DIR . 'includes/vendor/infusionsoft/isdk.php';
		}
		
		try {
			$this->api_instance = new iSDK();
			$this->api_instance->cfgCon( $app_id, $api_key, 'throw' );
		} 
		catch ( iSDKException $e ) {
			$this->api_instance = new stdClass();
			$this->api_instance->error = sprintf(
				__( 'There was an error connecting to Infusionsoft. %s', 'fl-builder' ),
				$e->getMessage()
			);
		}
		
		return $this->api_instance;
	}
	
	/**
	 * Test the API connection.
	 *
	 * @since 1.5.7
	 * @param array $fields {
	 *		@type string $app_id A valid app ID.
	 *		@type string $api_key A valid API key.
	 * }
	 * @return array{
	 *		@type bool|string $error The error message or false if no error.
	 *		@type array $data An array of data used to make the connection.
	 * }
	 */	 
	public function connect( $fields = array() ) 
	{
		$response = array( 
			'error'	 => false,
			'data'	 => array()
		);
		
		// Make sure we have an API key.
		if ( ! isset( $fields['api_key'] ) || empty( $fields['api_key'] ) ) {
			$response['error'] = __( 'Error: You must provide an API key.', 'fl-builder' );
		}
		// Make sure we have an app ID.
		else if ( ! isset( $fields['app_id'] ) || empty( $fields['app_id'] ) ) {
			$response['error'] = __( 'Error: You must provide an app ID.', 'fl-builder' );
		}
		// Try to connect and store the connection data.
		else {
			
			$api = $this->get_api( $fields['app_id'], $fields['api_key'] );
			
			if ( isset( $api->error ) ) {
				$response['error'] = $api->error;
			}
			else {
				$response['data'] = array( 
					'app_id'  => $fields['app_id'],
					'api_key' => $fields['api_key']
				);
			}
		}
		
		return $response;
	}

	/**
	 * Renders the markup for the connection settings.
	 *
	 * @since 1.5.7
	 * @return string The connection settings markup.
	 */	 
	public function render_connect_settings() 
	{
		ob_start();
		
		FLBuilder::render_settings_field( 'app_id', array(
			'row_class'		=> 'fl-builder-service-connect-row',
			'class'			=> 'fl-builder-service-connect-input',
			'type'			=> 'text',
			'label'			=> __( 'App ID', 'fl-builder' ),
			'help'			=> __( 'Your App ID can be found in the URL for your account. For example, if the URL for your account is myaccount.infusionsoft.com, your App ID would be <strong>myaccount</strong>.', 'fl-builder' ),
			'preview'		=> array(
				'type'			=> 'none'
			)
		));
		
		FLBuilder::render_settings_field( 'api_key', array(
			'row_class'		=> 'fl-builder-service-connect-row',
			'class'			=> 'fl-builder-service-connect-input',
			'type'			=> 'text',
			'label'			=> __( 'API Key', 'fl-builder' ),
			'help'			=> __( 'Your API key can be found in your Infusionsoft account under Admin > Settings > Application > API > Encrypted Key.', 'fl-builder' ),
			'preview'		=> array(
				'type'			=> 'none'
			)
		));
		
		return ob_get_clean();
	}

	/**
	 * Render the markup for service specific fields. 
	 *
	 * @since 1.5.7
	 * @param string $account The name of the saved account.
	 * @param object $settings Saved module settings.
	 * @return array {
	 *		@type bool|string $error The error message or false if no error.
	 *		@type string $html The field markup.
	 * }
	 */	 
	public function render_fields( $account, $settings ) 
	{
		$account_data	= $this->get_account_data( $account );
		$api			= $this->get_api( $account_data['app_id'], $account_data['api_key'] );
		$page			= 0;
		$lists			= array();
		$response		= array( 
			'error'			=> false, 
			'html'			=> '' 
		);
		
		if ( isset( $api->error ) ) {
			$response['error'] = $api->error;
		}
		else {
			
			while ( true ) {
				
				$result = $api->dsQuery(
					'ContactGroup',
					1000,
					$page,
					array( 'Id' => '%' ),
					array( 'Id', 'GroupName' )
				);
				
				$lists = array_merge( $lists, $result );
				
				if ( count( $result ) < 1000 ) {
					break;
				}
	
				$page ++;
			}
			
			$response['html'] = $this->render_list_field( $lists, $settings );
		}
		
		return $response;
	}

	/**
	 * Render markup for the list field. 
	 *
	 * @since 1.5.7
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
			$options[ $list['Id'] ] = $list['GroupName'];
		}
		
		FLBuilder::render_settings_field( 'list_id', array(
			'row_class'		=> 'fl-builder-service-field-row',
			'class'			=> 'fl-builder-service-list-select',
			'type'			=> 'select',
			'label'			=> _x( 'List', 'An email list from a third party provider.', 'fl-builder' ),
			'options'		=> $options,
			'preview'		=> array(
				'type'			=> 'none'
			)
		), $settings); 
		
		return ob_get_clean();
	}

	/** 
	 * Subscribe an email address to Infusionsoft.
	 *
	 * @since 1.5.7
	 * @param object $settings A module settings object.
	 * @param string $email The email to subscribe.
	 * @param string $name Optional. The full name of the person subscribing.
	 * @return array {
	 *		@type bool|string $error The error message or false if no error.
	 * }
	 */	 
	public function subscribe( $settings, $email, $name = false )
	{
		$account_data = $this->get_account_data( $settings->service_account );
		$response	  = array( 'error' => false );
		$data		  = array();
		
		if ( ! $account_data ) {
			$response['error'] = __( 'There was an error subscribing to Infusionsoft. The account is no longer connected.', 'fl-builder' );
		}
		else {
			
			$api = $this->get_api( $account_data['app_id'], $account_data['api_key'] );
			
			if ( isset( $api->error ) ) {
				$response['error'] = $api->error;
			}
			else {
				
				if ( $name ) {
					
					$names = explode( ' ', $name );
					
					if ( isset( $names[0] ) && isset( $names[1] ) ) {
						$data = array( 
							'FirstName' => $names[0],
							'LastName'	=> $names[1],
							'Email'		=> $email
						);
					} 
					else {
						$data = array( 
							'FirstName' => $name,
							'Email'		=> $email
						);
					}
				} 
				else {
					$data = array( 'Email' => $email );
				}
				
				try {
					
					$contact = $api->findByEmail( $email, array( 'Id' ) );
					
					if ( isset( $contact[0] ) && ! empty( $contact[0]['Id'] ) ) {
						$contact_id = $contact[0]['Id'];
						$api->updateCon( $contact_id, $data );
						$group = $api->grpAssign( $contact[0]['Id'], $settings->list_id );
					} 
					else {
						$contact_id = $api->addCon( $data );
						$group_add	= $api->grpAssign( $contact_id, $settings->list_id );
					}
				} 
				catch ( iSDKException $e ) {
					$response['error'] = sprintf(
						__( 'There was an error subscribing to Infusionsoft. %s', 'fl-builder' ),
						$e->getMessage()
					);
				}
			}
		}
		
		return $response;
	}
}