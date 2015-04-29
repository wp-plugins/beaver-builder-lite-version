<?php

$global_settings = FLBuilderModel::get_global_settings();

FLBuilder::register_settings_form('module_advanced', array(
	'title'         => __('Advanced', 'fl-builder'),
	'sections'      => array(
		'margins'       => array(
			'title'         => __('Margins', 'fl-builder'),
			'fields'        => array(
				'margin_top'    => array(
					'type'          => 'text',
					'label'         => __( 'Top', 'fl-builder' ),
					'default'       => '',
					'description'   => 'px',
					'maxlength'     => '4',
					'size'          => '5',
					'placeholder'   => $global_settings->module_margins,
					'preview'         => array(
						'type'            => 'none'
					)
				),
				'margin_bottom' => array(
					'type'          => 'text',
					'label'         => __( 'Bottom', 'fl-builder' ),
					'default'       => '',
					'description'   => 'px',
					'maxlength'     => '4',
					'size'          => '5',
					'placeholder'   => $global_settings->module_margins,
					'preview'         => array(
						'type'            => 'none'
					)
				),
				'margin_left'   => array(
					'type'          => 'text',
					'label'         => __( 'Left', 'fl-builder' ),
					'default'       => '',
					'description'   => 'px',
					'maxlength'     => '4',
					'size'          => '5',
					'placeholder'   => $global_settings->module_margins,
					'preview'         => array(
						'type'            => 'none'
					)
				),
				'margin_right'  => array(
					'type'          => 'text',
					'label'         => __( 'Right', 'fl-builder' ),
					'default'       => '',
					'description'   => 'px',
					'maxlength'     => '4',
					'size'          => '5',
					'placeholder'   => $global_settings->module_margins,
					'preview'         => array(
						'type'            => 'none'
					)
				)
			)
		),
		'responsive'   => array(
			'title'         => __('Responsive Layout', 'fl-builder'),
			'fields'        => array(
				'responsive_display' => array(
					'type'          => 'select',
					'label'         => __('Display', 'fl-builder'),
					'options'       => array(
						''                  => __('Always', 'fl-builder'),
						'desktop'           => __('Large Devices Only', 'fl-builder'),
						'desktop-medium'    => __('Large &amp; Medium Devices Only', 'fl-builder'),
						'medium'            => __('Medium Devices Only', 'fl-builder'),
						'medium-mobile'     => __('Medium &amp; Small Devices Only', 'fl-builder'),
						'mobile'            => __('Small Devices Only', 'fl-builder'),
					),
					'help'          => __( 'Choose whether to show or hide this module at different device sizes.', 'fl-builder' ),
					'preview'         => array(
						'type'            => 'none'
					)
				)
			)
		),
		'animation'    => array(
			'title'         => __('Animation', 'fl-builder'),
			'fields'        => array(
				'animation'     => array(
					'type'          => 'select',
					'label'         => __('Style', 'fl-builder'),
					'options'       => array(
						''              => _x( 'None', 'Animation style.', 'fl-builder' ),
						'fade-in'       => _x( 'Fade In', 'Animation style.', 'fl-builder' ),
						'slide-left'    => _x( 'Slide Left', 'Animation style.', 'fl-builder' ),
						'slide-right'   => _x( 'Slide Right', 'Animation style.', 'fl-builder' ),
						'slide-up'      => _x( 'Slide Up', 'Animation style.', 'fl-builder' ),
						'slide-down'    => _x( 'Slide Down', 'Animation style.', 'fl-builder' ),
					),
					'preview'         => array(
						'type'            => 'none'
					)
				),
				'animation_delay' => array(
					'type'          => 'text',
					'label'         => __('Delay', 'fl-builder'),
					'default'       => '0.0',
					'maxlength'     => '4',
					'size'          => '5',
					'description'   => _x( 'seconds', 'Value unit for form field of time in seconds. Such as: "5 seconds"', 'fl-builder' ),
					'help'          => __('The amount of time in seconds before this animation starts.', 'fl-builder'),
					'preview'         => array(
						'type'            => 'none'
					)
				)
			)
		),
		'css_selectors' => array(
			'title'         => __('CSS Selectors', 'fl-builder'),
			'fields'        => array(
				'id'            => array(
					'type'          => 'text',
					'label'         => __('ID', 'fl-builder'),
					'help'          => __( "A unique ID that will be applied to this module's HTML. Must start with a letter and only contain dashes, underscores, letters or numbers. No spaces.", 'fl-builder' ),
					'preview'         => array(
						'type'            => 'none'
					)
				),
				'class'         => array(
					'type'          => 'text',
					'label'         => __('Class', 'fl-builder'),
					'help'          => __( "A class that will be applied to this module's HTML. Must start with a letter and only contain dashes, underscores, letters or numbers. Separate multiple classes with spaces.", 'fl-builder' ),
					'preview'         => array(
						'type'            => 'none'
					)
				)
			)
		)
	)
));