<?php

/**
 * Helper class for the Campaign Monitor API.
 *
 * @since 1.5.4
 */
final class FLBuilderServiceCampaignMonitor extends FLBuilderService {

	/**
	 * The ID for this service.
	 *
	 * @since 1.5.4
	 * @var string $id
	 */  
	public $id = 'campaign-monitor';

	/**
	 * Constructor function.
	 *
	 * @since 1.5.4
	 * @return void
	 */  
	public function __construct() 
	{
		if ( ! class_exists( 'CS_REST_General' ) ) {
			require_once FL_BUILDER_DIR . 'includes/vendor/campaign-monitor/csrest_general.php';
			require_once FL_BUILDER_DIR . 'includes/vendor/campaign-monitor/csrest_clients.php';
			require_once FL_BUILDER_DIR . 'includes/vendor/campaign-monitor/csrest_lists.php';
			require_once FL_BUILDER_DIR . 'includes/vendor/campaign-monitor/csrest_subscribers.php';
		}
	}
	
	/**
	 * Test the API connection.
	 *
	 * @since 1.5.4
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
			
			$api    = new CS_REST_General( array( 'api_key' => $fields['api_key'] ) );
			$result = $api->get_clients();
			
			if ( $result->was_successful() ) {
				$response['data'] = array( 'api_key' => $fields['api_key'] );
			}
			else {
				$response['error'] = __( 'Error: Please check your API key.', 'fl-builder' );
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
			'help'          => __( 'Your API key can be found in your Campaign Monitor account under Account Settings > API Key.', 'fl-builder' ),
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
		$post_data      = FLBuilderModel::get_post_data();
		$account_data   = $this->get_account_data( $account );
		$api            = new CS_REST_General( $account_data );
		$result         = $api->get_clients();
		$response       = array( 
			'error'         => false, 
			'html'          => '' 
		);
		
		if ( $result->was_successful() ) {
			
			if ( ! isset( $post_data['client'] ) ) {
				$response['html'] .= $this->render_client_field( $result, $settings );
			}

			$response['html'] .= $this->render_list_field( $account_data, $settings );
		}
		else {
			$response['error'] = __( 'Error: Please check your API key.', 'fl-builder' );
		}
		
		return $response;
	}

	/**
	 * Render markup for the client field. 
	 *
	 * @since 1.5.4
	 * @param array $clients Client data from the API.
	 * @param object $settings Saved module settings.
	 * @return string The markup for the list field.
	 * @access private
	 */  
	private function render_client_field( $clients, $settings ) 
	{
		ob_start();
		
		$options = array( '' => __( 'Choose...', 'fl-builder' ) );
		
		foreach ( $clients->response as $client ) {
			$options[ $client->ClientID ] = $client->Name;
		}
		
		FLBuilder::render_settings_field( 'client_id', array(
			'row_class'     => 'fl-builder-service-field-row',
			'class'         => 'fl-builder-campaign-monitor-client-select',
			'type'          => 'select',
			'label'         => _x( 'Client', 'A client account in Campaign Monitor.', 'fl-builder' ),
			'options'       => $options,
			'preview'       => array(
				'type'          => 'none'
			)
		), $settings); 
		
		return ob_get_clean();
	}

	/**
	 * Render markup for the list field. 
	 *
	 * @since 1.5.4
	 * @param array $account_data Saved account data.
	 * @param object $settings Saved module settings.
	 * @return string The markup for the list field.
	 * @access private
	 */  
	private function render_list_field( $account_data, $settings ) 
	{
		$post_data = FLBuilderModel::get_post_data();
		
		// Get the client ID. Return an empty string if we don't have one yet.
		if ( isset( $post_data['client'] ) ) {
			$client_id = $post_data['client'];
		}
		else if ( isset( $settings->client_id ) ) {
			$client_id = $settings->client_id;
		}
		else {
			return '';
		}
		
		// Get the list data.
		$api   = new CS_REST_Clients( $client_id, $account_data );
		$lists = $api->get_lists();
		
		// Render the list field.
		ob_start();
		
		$options = array( '' => __( 'Choose...', 'fl-builder' ) );
		
		foreach ( $lists->response as $list ) {
			$options[ $list->ListID ] = $list->Name;
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
	 * Subscribe an email address to Campaign Monitor.
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
		$account_data = $this->get_account_data( $settings->service_account );
		$response     = array( 'error' => false );
		
		if ( ! $account_data ) {
			$response['error'] = __( 'There was an error subscribing to Campaign Monitor. The account is no longer connected.', 'fl-builder' );
		}
		else {
			
			$api    = new CS_Rest_Subscribers( $settings->list_id, $account_data );
			$data   = array(
				'EmailAddress' => $email,
				'Resubscribe'  => true
			);
			
			if ( $name ) {
				$data['Name'] = $name;
			}
			
			$result = $api->add( $data );
	
			if ( ! $result->was_successful() ) {
				$response['error'] = __( 'There was an error subscribing to Campaign Monitor.', 'fl-builder' );
			}
		}
		
		return $response;
	}
}