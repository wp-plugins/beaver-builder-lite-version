<?php

/**
 * Helper class for adding an email address as a service.
 *
 * @since 1.6.0
 */
final class FLBuilderServiceEmailAddress extends FLBuilderService {

	/**
	 * The ID for this service.
	 *
	 * @since 1.6.0
	 * @var string $id
	 */  
	public $id = 'email-address';
	
	/**
	 * Test the API connection.
	 *
	 * @since 1.6.0
	 * @param array $fields {
	 *      @type string $email A valid email address.
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
		if ( ! isset( $fields['email'] ) || empty( $fields['email'] ) ) {
			$response['error'] = __( 'Error: You must provide an email address.', 'fl-builder' );
		}
		// Store the connection data.
		else {
			$response['data'] = array( 'email' => $fields['email'] );
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
		
		FLBuilder::render_settings_field( 'email', array(
			'row_class'     => 'fl-builder-service-connect-row',
			'class'         => 'fl-builder-service-connect-input',
			'type'          => 'text',
			'label'         => __( 'Email Address', 'fl-builder' ),
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
		$response = array( 
			'error'  => false, 
			'html'   => '' 
		);
		
		return $response;
	}

	/** 
	 * Send the subscription info to the saved email address.
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
			$response['error'] = __( 'There was an error subscribing. The account is no longer connected.', 'fl-builder' );
		}
		else {
			
			$subject = __( 'Subscribe Form Signup', 'fl-builder' );
			$message = __( 'Email', 'fl-builder' ) . ': ' . $email;
			
			if ( $name ) {
				$message .= "\n" . __( 'Name', 'fl-builder' ) . ': ' . $name;
			}
			
			$result = wp_mail( $account_data['email'], $subject, $message );
			
			if ( ! $result ) {
				$response['error'] = __( 'There was an error subscribing. Please try again.', 'fl-builder' );
			}
		}
		
		return $response;
	}
}