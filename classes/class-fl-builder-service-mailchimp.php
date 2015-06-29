<?php

/**
 * Helper class for the MailChimp API.
 *
 * @since 1.5.4
 */
final class FLBuilderServiceMailChimp extends FLBuilderService {

	/**
	 * The ID for this service.
	 *
	 * @since 1.5.4
	 * @var string $id
	 */  
	public $id = 'mailchimp';

	/**
	 * @since 1.5.4
	 * @var object $api_instance
	 * @access private
	 */  
	private $api_instance = null;

	/**
	 * Get an instance of the API.
	 *
	 * @since 1.5.4
	 * @param string $api_key A valid API key.
	 * @return object The API instance.
	 */  
	public function get_api( $api_key ) 
	{
		if ( $this->api_instance ) {
			return $this->api_instance;
		}
		if ( ! class_exists( 'Mailchimp' ) ) {
			require_once FL_BUILDER_DIR . 'includes/vendor/mailchimp/mailchimp.php';
		}
		
		$this->api_instance = new Mailchimp( $api_key );
		
		return $this->api_instance;
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
			
			$api = $this->get_api( $fields['api_key'] );

			try {
				$api->helper->ping();
				$response['data'] = array( 'api_key' => $fields['api_key'] );
			} 
			catch ( Mailchimp_Invalid_ApiKey $e ) {
				$response['error'] = $e->getMessage();
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
			'help'          => __( 'Your API key can be found in your MailChimp account under Account > Extras > API Keys.', 'fl-builder' ),
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
		$api            = $this->get_api( $account_data['api_key'] );
		$response       = array( 
			'error'         => false, 
			'html'          => '' 
		);
		
		// Lists field
		try {
			
			if ( ! isset( $post_data['list_id'] ) ) {
				$lists = $api->lists->getList();
				$response['html'] .= $this->render_list_field( $lists, $settings );
			}
		} 
		catch ( Mailchimp_Error $e ) {
			$response['error'] = $e->getMessage();
		}
		
		// Groups field
		try {
			
			if ( isset( $post_data['list_id'] ) || isset( $settings->list_id ) ) {
				
				if ( isset( $post_data['list_id'] ) ) {
					$list_id = $post_data['list_id'];
				}
				else {
					$list_id = $settings->list_id;
				}
				
				$groups = $api->lists->interestGroupings( $list_id );
				$response['html'] .= $this->render_groups_field( $list_id, $groups, $settings );
			}
		} 
		catch ( Mailchimp_Error $e ) {}
		
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
		
		foreach ( $lists['data'] as $list ) {
			$options[ $list['id'] ] = $list['name'];
		}
		
		FLBuilder::render_settings_field( 'list_id', array(
			'row_class'     => 'fl-builder-service-field-row',
			'class'         => 'fl-builder-service-list-select fl-builder-mailchimp-list-select',
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
	 * Render markup for the groups field. 
	 *
	 * @since 1.6.0
	 * @param string $list_id The ID of the list for this groups.
	 * @param array $groups An array of group data.
	 * @param object $settings Saved module settings.
	 * @return string The markup for the group field.
	 * @access private
	 */  
	private function render_groups_field( $list_id, $groups, $settings ) 
	{
		if ( ! is_array( $groups ) || 0 === count( $groups ) ) {
			return;
		}
		
		ob_start();
		
		$options = array( '' => __( 'No Group', 'fl-builder' ) );
		
		foreach ( $groups as $group ) {
			foreach ( $group['groups'] as $subgroup ) {
				$options[ $list_id . '_' . $group['id'] . '_' . $subgroup['id'] ] = $group['name'] . ' - ' . $subgroup['name'];
			}
		}
		
		FLBuilder::render_settings_field( 'groups', array(
			'row_class'     => 'fl-builder-service-field-row',
			'class'         => 'fl-builder-mailchimp-group-select',
			'type'          => 'select',
			'label'         => _x( 'Groups', 'MailChimp list group.', 'fl-builder' ),
			'multi-select'	=> true,
			'options'       => $options,
			'preview'       => array(
				'type'          => 'none'
			)
		), $settings); 
		
		return ob_get_clean();
	}

	/** 
	 * Subscribe an email address to MailChimp.
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
			$response['error'] = __( 'There was an error subscribing to MailChimp. The account is no longer connected.', 'fl-builder' );
		}
		else {
			
			$api     = $this->get_api( $account_data['api_key'] );
			$double  = apply_filters( 'fl_builder_mailchimp_double_option', true );
			$welcome = apply_filters( 'fl_builder_mailchimp_welcome', true );
			$email   = array( 'email' => $email );
			$data    = array();
			
			// Name
			if ( $name ) {
				
				$names = explode( ' ', $name );
				
				if ( isset( $names[0] ) ) {
					$data['FNAME'] = $names[0];
				}
				if ( isset( $names[1] ) ) {
					$data['LNAME'] = $names[1];
				}
			}
			
			// Groups
			if ( isset( $settings->groups ) && is_array( $settings->groups ) ) {
				
				$groups = array();
				
				// Build the array of saved group data.
				for ( $i = 0; $i < count( $settings->groups ); $i++ ) {
					
					if ( empty( $settings->groups[ $i ] ) ) {
						continue;
					}
					
					$group_data = explode( '_', $settings->groups[ $i ] );
					
					if ( $group_data[0] != $settings->list_id ) {
						continue;
					}
					if ( ! isset( $groups[ $group_data[1] ] ) ) {
						$groups[ $group_data[1] ] = array();
					}
					
					$groups[ $group_data[1] ][] = $group_data[2];
				}
				
				// Get the subgroup names from the API and add to the $data array.
				if ( count( $groups ) > 0 ) {
				
					$groups_result = $api->lists->interestGroupings( $settings->list_id );
					
					if ( is_array( $groups_result ) && count( $groups_result ) > 0 ) {
						
						foreach ( $groups_result as $group ) {
							
							if ( ! isset( $groups[ $group['id'] ] ) ) {
								continue;
							}
							
							$subgroup_names = array();
							
							foreach ( $group['groups'] as $subgroup ) {
								
								if ( in_array( $subgroup['id'], $groups[ $group['id'] ] ) ) {
									$subgroup_names[] = $subgroup['name'];
								}
							}
							
							if ( 0 === count( $subgroup_names ) ) {
								unset( $groups[ $group['id'] ] );
							}
							else {
								$groups[ $group['id'] ] = $subgroup_names;
							}
						}
						
						$i = 0;
						
						foreach ( $groups as $group_id => $subgroups ) {
							$data['groupings'][ $i ]['id']     = $group_id;
							$data['groupings'][ $i ]['groups'] = $subgroups;
							$i++;
						}
					}
				}
			}
			
			// Subscribe
			try {
				$api->lists->subscribe( $settings->list_id, $email, $data, 'html', (bool) $double, true, false, (bool) $welcome );
			} 
			catch( Mailchimp_List_AlreadySubscribed $e ) {
				return $response;
			} 
			catch ( Mailchimp_Error $e ) {
				$response['error'] = sprintf(
					__( 'There was an error subscribing to MailChimp. %s', 'fl-builder' ),
					$e->getMessage()
				);
			}
		}
		
		return $response;
	}
}