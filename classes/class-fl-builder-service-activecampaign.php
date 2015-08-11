<?php

/**
 * Helper class for the ActiveCampaign API.
 *
 * @since 1.6.0
 */
final class FLBuilderServiceActiveCampaign extends FLBuilderService {

	/**
	 * The ID for this service.
	 *
	 * @since 1.6.0
	 * @var string $id
	 */  
	public $id = 'activecampaign';

	/**
	 * @since 1.6.0
	 * @var object $api_instance
	 * @access private
	 */  
	private $api_instance = null;

	/**
	 * Get an instance of the API.
	 *
	 * @since 1.6.0
	 * @param string $api_url A valid API url.
	 * @param string $api_key A valid API key.
	 * @return object The API instance.
	 */  
	public function get_api( $api_url, $api_key ) 
	{
		if ( $this->api_instance ) {
			return $this->api_instance;
		}
		if ( ! class_exists( 'ActiveCampaign' ) ) {
			require_once FL_BUILDER_DIR . 'includes/vendor/activecampaign/ActiveCampaign.class.php';
		}
		
		$this->api_instance = new ActiveCampaign( $api_url, $api_key );
		
		return $this->api_instance;
	}
	
	/**
	 * Test the API connection.
	 *
	 * @since 1.6.0
	 * @param array $fields {
	 *      @type string $api_url A valid API url.
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
		
		// Make sure we have an API url.
		if ( ! isset( $fields['api_url'] ) || empty( $fields['api_url'] ) ) {
			$response['error'] = __( 'Error: You must provide an API URL.', 'fl-builder' );
		}
		// Make sure we have an API key.
		else if ( ! isset( $fields['api_key'] ) || empty( $fields['api_key'] ) ) {
			$response['error'] = __( 'Error: You must provide an API key.', 'fl-builder' );
		}
		// Try to connect and store the connection data.
		else {
			
			$api = $this->get_api( $fields['api_url'], $fields['api_key'] );
			
			if ( ! (int) $api->credentials_test() ) {
				$response['error'] = __( 'Error: Please check your API URL and API key.', 'fl-builder' );
			}
			else {
				$response['data'] = array( 
					'api_url' => $fields['api_url'],
					'api_key' => $fields['api_key'] 
				);
			}
		}
		
		return $response;
	}

	/**
	 * Renders the markup for the connection settings.
	 *
	 * @since 1.6.0
	 * @return string The connection settings markup.
	 */  
	public function render_connect_settings() 
	{
		ob_start();
		
		FLBuilder::render_settings_field( 'api_url', array(
			'row_class'     => 'fl-builder-service-connect-row',
			'class'         => 'fl-builder-service-connect-input',
			'type'          => 'text',
			'label'         => __( 'API URL', 'fl-builder' ),
			'help'          => __( 'Your API url can be found in your ActiveCampaign account under My Settings > API.', 'fl-builder' ),
			'preview'       => array(
				'type'          => 'none'
			)
		));
		
		FLBuilder::render_settings_field( 'api_key', array(
			'row_class'     => 'fl-builder-service-connect-row',
			'class'         => 'fl-builder-service-connect-input',
			'type'          => 'text',
			'label'         => __( 'API Key', 'fl-builder' ),
			'help'          => __( 'Your API key can be found in your ActiveCampaign account under My Settings > API.', 'fl-builder' ),
			'preview'       => array(
				'type'          => 'none'
			)
		)); 
		
		return ob_get_clean();
	}

	/**
	 * Render the markup for service specific fields. 
	 *
	 * @since 1.6.0
	 * @param string $account The name of the saved account.
	 * @param object $settings Saved module settings.
	 * @return array {
	 *      @type bool|string $error The error message or false if no error.
	 *      @type string $html The field markup.
	 * }
	 */  
	public function render_fields( $account, $settings ) 
	{
		$post_data      = FLBuilderModel::get_post_data();
		$account_data   = $this->get_account_data( $account );
		$api            = $this->get_api( $account_data['api_url'], $account_data['api_key'] );
		$response       = array( 
			'error'         => false, 
			'html'          => '' 
		);
		
		$lists = $api->api( 'list/list?ids=all' );
		$response['html'] = $this->render_list_field( $lists, $settings );
		
		return $response;
	}

	/**
	 * Render markup for the list field. 
	 *
	 * @since 1.6.0
	 * @param array $lists List data from the API.
	 * @param object $settings Saved module settings.
	 * @return string The markup for the list field.
	 * @access private
	 */  
	private function render_list_field( $lists, $settings ) 
	{
		ob_start();
		
		$options = array( '' => __( 'Choose...', 'fl-builder' ) );
		
		foreach ( (array) $lists as $list ) {
			if ( is_object( $list ) && isset( $list->id ) ) {
				$options[ $list->id ] = $list->name;
			}
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
	 * Subscribe an email address to ActiveCampaign.
	 *
	 * @since 1.6.0
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
			$response['error'] = __( 'There was an error subscribing to ActiveCampaign. The account is no longer connected.', 'fl-builder' );
		}
		else {
			
			$api     = $this->get_api( $account_data['api_url'], $account_data['api_key'] );
			$data 	 = array(
				'email'             => $email,
				'p'                 => array( $settings->list_id ),
				'status'            => 1,
				'instantresponders' => array( 1 )
			);
			
			// Name
			if ( $name ) {
				
				$names = explode( ' ', $name );
				
				if ( isset( $names[0] ) ) {
					$data['first_name'] = $names[0];
				}
				if ( isset( $names[1] ) ) {
					$data['last_name'] = $names[1];
				}
			}
			
			// Subscribe
			$result = $api->api( 'contact/add', $data );
			
			if ( ! $result->success && isset( $result->error ) && ! stristr( $result->error, 'duplicate' ) ) {
				
				if ( stristr( $result->error, 'access' ) ) {
					$response['error'] = __( 'Error: Invalid API data.', 'fl-builder' );
				}
				else {
					$response['error'] = $result->error;
				}
			}
		}
		
		return $response;
	}
}