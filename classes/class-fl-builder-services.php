<?php

/**
 * Helper class for connecting to third party services.
 *
 * @since 1.5.4
 */
final class FLBuilderServices {
	
	/**
	 * Data for working with each supported third party service.
	 *
	 * @since 1.5.4
	 * @access private
	 * @var array $services_data
	 */  
	static private $services_data = array(
		'activecampaign'    => array(
			'type'              => 'autoresponder',
			'name'              => 'ActiveCampaign',
			'class'             => 'FLBuilderServiceActiveCampaign'
		),
		'aweber'            => array(
			'type'              => 'autoresponder',
			'name'              => 'AWeber',
			'class'             => 'FLBuilderServiceAWeber'
		),
		'campaign-monitor'  => array(
			'type'              => 'autoresponder',
			'name'              => 'Campaign Monitor',
			'class'             => 'FLBuilderServiceCampaignMonitor'
		),
		'constant-contact'  => array(
			'type'              => 'autoresponder',
			'name'              => 'Constant Contact',
			'class'             => 'FLBuilderServiceConstantContact'
		),
		'email-address'     => array(
			'type'              => 'autoresponder',
			'name'              => 'Email Address',
			'class'             => 'FLBuilderServiceEmailAddress'
		),
		'getresponse'       => array(
			'type'              => 'autoresponder',
			'name'              => 'GetResponse',
			'class'             => 'FLBuilderServiceGetResponse'
		),
		'hatchbuck'         => array(
			'type'              => 'autoresponder',
			'name'              => 'Hatchbuck',
			'class'             => 'FLBuilderServiceHatchbuck'
		),
		'icontact'          => array(
			'type'              => 'autoresponder',
			'name'              => 'iContact',
			'class'             => 'FLBuilderServiceIContact'
		),
		'infusionsoft'      => array(
			'type'              => 'autoresponder',
			'name'              => 'Infusionsoft',
			'class'             => 'FLBuilderServiceInfusionsoft'
		),
		'madmimi'           => array(
			'type'              => 'autoresponder',
			'name'              => 'Mad Mimi',
			'class'             => 'FLBuilderServiceMadMimi'
		),
		'mailchimp'         => array(
			'type'              => 'autoresponder',
			'name'              => 'MailChimp',
			'class'             => 'FLBuilderServiceMailChimp'
		),
		'mailpoet'          => array(
			'type'              => 'autoresponder',
			'name'              => 'MailPoet',
			'class'             => 'FLBuilderServiceMailPoet'
		),
		'sendinblue'        => array(
			'type'              => 'autoresponder',
			'name'              => 'SendinBlue',
			'class'             => 'FLBuilderServiceSendinBlue'
		)
	);

	/**
	 * Get an array of services data of a certain type such as "autoresponder".
	 * If no type is specified, all services will be returned.
	 *
	 * @since 1.5.4
	 * @param string $type The type of service data to return.
	 * @return array An array of services and related data.
	 */  
	static public function get_services_data( $type = null ) 
	{
		$services = array();
		
		// Return all services.
		if ( ! $type ) {
			$services = self::$services_data;
		}
		// Return services of a specific type.
		else {
			
			foreach ( self::$services_data as $key => $service ) {
				if ( $service['type'] == $type ) {
					$services[ $key ] = $service;
				}
			}
		}
		
		return $services;
	}

	/**
	 * Get an instance of a service helper class. 
	 *
	 * @since 1.5.4
	 * @param string $type The type of service.
	 * @return object
	 */  
	static public function get_service_instance( $service ) 
	{
		$services = self::get_services_data();
		$data     = $services[ $service ];
		
		// Make sure the base class is loaded.
		if ( ! class_exists( 'FLBuilderService' ) ) {
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-service.php';
		}
		
		// Make sure the service class is loaded.
		if ( ! class_exists( $data['class'] ) ) {
			require_once FL_BUILDER_DIR . 'classes/class-fl-builder-service-' . $service . '.php';
		}
		
		return new $data['class']();
	}
	
	/**
	 * Save the API connection of a service and retrieve account settings markup.
	 *
	 * Called via the fl_ajax_fl_builder_connect_service action.
	 *
	 * @since 1.5.4
	 * @return string The JSON encoded response.
	 */  
	static public function connect_service() 
	{
		$saved_services = FLBuilderModel::get_services();
		$post_data      = FLBuilderModel::get_post_data();
		$response       = array( 
			'error'         => false, 
			'html'          => '' 
		);
		
		// Validate the service data.
		if ( ! isset( $post_data['service'] ) || empty( $post_data['service'] ) ) {
			$response['error'] = _x( 'Error: Missing service type.', 'Third party service such as MailChimp.', 'fl-builder' );
		}
		else if ( ! isset( $post_data['fields'] ) || 0 === count( $post_data['fields'] ) ) {
			$response['error'] = _x( 'Error: Missing service data.', 'Connection data such as an API key.', 'fl-builder' );
		}
		else if ( ! isset( $post_data['fields']['service_account'] ) || empty( $post_data['fields']['service_account'] ) ) {
			$response['error'] = _x( 'Error: Missing account name.', 'Account name for a third party service such as MailChimp.', 'fl-builder' );
		}
		
		// Get the service data.
		$service         = $post_data['service'];
		$service_account = $post_data['fields']['service_account'];
		
		// Does this account already exist? 
		if ( isset( $saved_services[ $service ][ $service_account ] ) ) {
			$response['error'] = _x( 'Error: An account with that name already exists.', 'Account name for a third party service such as MailChimp.', 'fl-builder' );
		}
		
		// Try to connect to the service.
		if ( ! $response['error'] ) {
			
			$instance   = self::get_service_instance( $service );
			$connection = $instance->connect( $post_data['fields'] ); 
			
			if ( $connection['error'] ) {
				$response['error'] = $connection['error'];   
			}
			else {
				
				FLBuilderModel::update_services( 
					$service, 
					$service_account,
					$connection['data'] 
				);
				
				$response['html'] = self::render_account_settings( $service, $service_account );
			}
		}
		
		// Return the response.
		echo json_encode( $response );
		
		die();
	}
	
	/**
	 * Render the connection settings or account settings for a service.
	 *
	 * Called via the fl_ajax_fl_builder_render_service_settings action.
	 *
	 * @since 1.5.4
	 * @return string The JSON encoded response.
	 */
	static public function render_settings() 
	{
		$post_data          = FLBuilderModel::get_post_data();
		$saved_services     = FLBuilderModel::get_services();
		$module             = FLBuilderModel::get_module( $post_data['node_id'] );
		$settings           = $module->settings;
		$service            = $post_data['service'];
		$response           = array( 
			'error'             => false, 
			'html'              => '' 
		);
		
		// Render the settings to connect a new account. 
		if ( isset( $post_data['add_new'] ) || ! isset( $saved_services[ $service ] ) ) {
			$response['html'] = self::render_connect_settings( $service );
		}
		// Render the settings to select a connected account.
		else {
			$account = isset( $settings->service_account ) ? $settings->service_account : '';
			$response['html'] = self::render_account_settings( $service, $account );
		}
		
		// Return the response.
		echo json_encode( $response );
		
		die();
	}
	
	/**
	 * Render the settings to connect to a new account.
	 *
	 * @since 1.5.4
	 * @return string The settings markup.
	 */
	static public function render_connect_settings( $service ) 
	{   
		ob_start();
		
		FLBuilder::render_settings_field( 'service_account', array(
			'row_class'     => 'fl-builder-service-connect-row',
			'class'         => 'fl-builder-service-connect-input',
			'type'          => 'text',
			'label'         => __( 'Account Name', 'fl-builder' ),
			'help'          => __( 'Used to identify this connection within the accounts list and can be anything you like.', 'fl-builder' ),
			'preview'       => array(
				'type'          => 'none'
			)
		)); 
		
		$instance = self::get_service_instance( $service );
		echo $instance->render_connect_settings();
		
		FLBuilder::render_settings_field( 'service_connect_button', array(
			'row_class'     => 'fl-builder-service-connect-row',
			'class'         => 'fl-builder-service-connect-button',
			'type'          => 'button',
			'label'         => __( 'Connect', 'fl-builder' )
		)); 
		
		return ob_get_clean();
	}
	
	/**
	 * Render the account settings for a saved connection.
	 *
	 * @since 1.5.4
	 * @param string $service The service id such as "mailchimp".
	 * @param string $active The name of the active account, if any.
	 * @return string The account settings markup.
	 */ 
	static public function render_account_settings( $service, $active = '' ) 
	{
		ob_start();
		
		$saved_services             = FLBuilderModel::get_services();
		$settings                   = new stdClass();
		$settings->service_account  = $active;
		$options                    = array( '' => __( 'Choose...', 'fl-builder' ) );
		
		// Build the account select options. 
		foreach ( $saved_services[ $service ] as $account => $data ) {
			$options[ $account ] = $account;
		}
		
		$options['add_new_account'] = __( 'Add Account...', 'fl-builder' );
		
		// Render the account select. 
		FLBuilder::render_settings_field( 'service_account', array(
			'row_class'     => 'fl-builder-service-account-row',
			'class'         => 'fl-builder-service-account-select',
			'type'          => 'select',
			'label'         => __( 'Account', 'fl-builder' ),
			'options'       => $options,
			'preview'       => array(
				'type'          => 'none'
			)
		), $settings);
		
		// Render additional service fields if we have a saved account.
		if ( ! empty( $active ) && isset( $saved_services[ $service ][ $active ] ) ) {
			
			$post_data  = FLBuilderModel::get_post_data();
			$module     = FLBuilderModel::get_module( $post_data['node_id'] );
			$instance   = self::get_service_instance( $service );
			$response   = $instance->render_fields( $active, $module->settings );
			
			if ( ! $response['error'] ) {
				echo $response['html'];
			}
		}
		
		return ob_get_clean();
	}
	
	/**
	 * Render the markup for service specific fields. 
	 *
	 * Called via the fl_ajax_fl_builder_render_service_fields action.
	 *
	 * @since 1.5.4
	 * @return string The JSON encoded response.
	 */ 
	static public function render_fields() 
	{
		$post_data  = FLBuilderModel::get_post_data();
		$module     = FLBuilderModel::get_module( $post_data['node_id'] );
		$instance   = self::get_service_instance( $post_data['service'] );
		$response   = $instance->render_fields( $post_data['account'], $module->settings );
		
		echo json_encode( $response );
		
		die();
	}
	
	/**
	 * Delete a saved account from the database.
	 *
	 * Called via the fl_ajax_fl_builder_delete_service_account action.
	 *
	 * @since 1.5.4
	 * @return void
	 */ 
	static public function delete_account() 
	{
		$post_data = FLBuilderModel::get_post_data();
		
		if ( ! isset( $post_data['service'] ) || ! isset( $post_data['account'] ) ) {
			return;
		}
		
		FLBuilderModel::delete_service_account( $post_data['service'], $post_data['account'] );
	}
}