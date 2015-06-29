<?php

FLBuilder::register_settings_form('global', array(
	'title' => sprintf( _x( '%s Settings', '%s stands for custom branded "Page Builder" name.', 'fl-builder' ), FLBuilderModel::get_branding() ),
	'tabs' => array(
		'general'  => array(
			'title'         => __('General', 'fl-builder'),
			'description'   => __('Note: These settings apply to all posts and pages.', 'fl-builder'),
			'sections'      => array(
				'page_heading'  => array(
					'title'         => __('Default Page Heading', 'fl-builder'),
					'fields'        => array(
						'show_default_heading' => array(
							'type'                 => 'select',
							'label'                => _x( 'Show', 'General settings form field label. Intended meaning: "Show page heading?"', 'fl-builder' ),
							'default'              => '0',
							'options'              => array(
								'0'                     => __('No', 'fl-builder'),
								'1'                     => __('Yes', 'fl-builder')
							),
							'toggle'               => array(
								'0'                    => array(
									'fields'               => array('default_heading_selector')
								)
							),
							'help'                     => __('Choosing no will hide the default theme heading for the "Page" post type. You will also be required to enter some basic CSS for this to work if you choose no.', 'fl-builder'),
						),
						'default_heading_selector' => array(
							'type'                     => 'text',
							'label'                    => __('CSS Selector', 'fl-builder'),
							'default'                  => '.fl-post-header',
							'help'                     => __('Enter a CSS selector for the default page heading to hide it.', 'fl-builder')
						)
					)
				),
				'rows'          => array(
					'title'         => __('Rows', 'fl-builder'),
					'fields'        => array(
						'row_margins'       => array(
							'type'              => 'text',
							'label'             => __('Margins', 'fl-builder'),
							'default'           => '0',
							'maxlength'         => '3',
							'size'              => '5',
							'description'       => 'px'
						),
						'row_padding'       => array(
							'type'              => 'text',
							'label'             => __('Padding', 'fl-builder'),
							'default'           => '20',
							'maxlength'         => '3',
							'size'              => '5',
							'description'       => 'px'
						),
						'row_width'         => array(
							'type'              => 'text',
							'label'             => __('Max Width', 'fl-builder'),
							'default'           => '1100',
							'maxlength'         => '4',
							'size'              => '5',
							'description'       => 'px',
							'help'                     => __('All rows will default to this width. You can override this and make a row full width in the settings for each row.', 'fl-builder')
						),
						'row_width_default' => array(
							'type'    => 'select',
							'label'   => __( 'Default Row Width', 'fl-builder' ),
							'default' => 'fixed',
							'options' => array(
								'fixed' => __( 'Fixed', 'fl-builder' ),
								'full'  => __( 'Full Width', 'fl-builder' )
							),
							'toggle'        => array(
								'full'         => array(
									'fields'        => array('row_content_width_default')
								)
							),
						),
						'row_content_width_default' => array(
							'type'    => 'select',
							'label'   => __( 'Default Row Content Width', 'fl-builder' ),
							'default' => 'fixed',
							'options' => array(
								'fixed' => __( 'Fixed', 'fl-builder' ),
								'full'  => __( 'Full Width', 'fl-builder' )
							),
						)
					)
				),
				'modules'       => array(
					'title'         => __('Modules', 'fl-builder'),
					'fields'        => array(
						'module_margins'    => array(
							'type'              => 'text',
							'label'             => __('Margins', 'fl-builder'),
							'default'           => '20',
							'maxlength'         => '3',
							'size'              => '5',
							'description'       => 'px'
						)
					)
				),
				'responsive'    => array(
					'title'         => __('Responsive Layout', 'fl-builder'),
					'fields'        => array(
						'responsive_enabled'   => array(
							'type'                 => 'select',
							'label'                => _x( 'Enabled', 'General settings form field label. Intended meaning: "Responsive layout enabled?"', 'fl-builder' ),
							'default'              => '1',
							'options'              => array(
								'0'                     => __('No', 'fl-builder'),
								'1'                     => __('Yes', 'fl-builder')
							),
							'toggle'               => array(
								'1'                    => array(
									'fields'               => array('auto_spacing', 'responsive_breakpoint', 'medium_breakpoint')
								)
							)
						),
						'auto_spacing'         => array(
							'type'                 => 'select',
							'label'                => _x( 'Enable Auto Spacing', 'General settings form field label. Intended meaning: "Enable auto spacing for responsive layouts?"', 'fl-builder' ),
							'default'              => '1',
							'options'              => array(
								'0'                     => __('No', 'fl-builder'),
								'1'                     => __('Yes', 'fl-builder')
							),
							'help'              => __('When auto spacing is enabled, the builder will automatically adjust the margins and padding in your layout once the small device breakpoint is reached. Most users will want to leave this enabled.', 'fl-builder')
						),
						'medium_breakpoint' => array(
							'type'              => 'text',
							'label'             => __('Medium Device Breakpoint', 'fl-builder'),
							'default'           => '992',
							'maxlength'         => '4',
							'size'              => '5',
							'description'       => 'px',
							'help'              => __('The browser width at which the layout will adjust for medium devices such as tablets.', 'fl-builder')
						),
						'responsive_breakpoint' => array(
							'type'              => 'text',
							'label'             => __('Small Device Breakpoint', 'fl-builder'),
							'default'           => '768',
							'maxlength'         => '4',
							'size'              => '5',
							'description'       => 'px',
							'help'              => __('The browser width at which the layout will adjust for small devices such as phones.', 'fl-builder')
						)
					)
				)
			)
		)
	)
));