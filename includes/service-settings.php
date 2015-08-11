<div class="fl-builder-service-settings">
	<table class="fl-form-table">
		<?php 
		
		// Get the service type.
		$service_type = null;
	
		if ( isset( $section['services'] ) && $section['services'] != 'all' ) {
			$service_type = $section['services'];
		}
		
		// Get the service data.
		$services = FLBuilderServices::get_services_data( $service_type );
		
		// Remove services that don't meet the requirements.
		if ( isset( $services['mailpoet'] ) && ! class_exists( 'WYSIJA' ) ) {
			unset( $services['mailpoet'] );
		}
		
		// Build the select options.
		$options  = array( '' => __( 'Choose...', 'fl-builder' ) );
		
		foreach ( $services as $key => $service ) {
			$options[ $key ] = $service['name'];
		}
		
		// Render the service select.
		FLBuilder::render_settings_field( 'service', array(
			'row_class'     => 'fl-builder-service-select-row',
			'class'         => 'fl-builder-service-select',
			'type'          => 'select',
			'label'         => __( 'Service', 'fl-builder' ),
			'options'       => $options,
			'preview'       => array(
				'type'          => 'none'
			)
		), $settings ); 
		
		?>
	</table>
</div>