<?php

FLBuilder::register_settings_form('col', array(
	'title' => __('Column Settings', 'fl-builder'),
	'tabs'  => array(
		'style'         => array(
			'title'         => __('Style', 'fl-builder'),
			'sections'      => array(
				'general'       => array(
					'title'         => '',
					'fields'        => array(
						'size'          => array(
							'type'          => 'text',
							'label'         => __('Column Width', 'fl-builder'),
							'default'       => '',
							'description'   => '%',
							'maxlength'     => '5',
							'size'          => '6',
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'equal_height'  => array(
							'type'          => 'select',
							'label'         => __('Equalize Column Heights', 'fl-builder'),
							'help'   		=> __('Setting this to yes will make all of the columns in this group the same height regardless of how much content is in each of them.', 'fl-builder'),
							'default'       => 'no',
							'options'       => array(
								'no'          => __( 'No', 'fl-builder' ),
								'yes'         => __( 'Yes', 'fl-builder' ),
							),
							'preview'         => array(
								'type'            => 'none'
							)
						)
					)
				),
				'text'          => array(
					'title'         => __('Text', 'fl-builder'),
					'fields'        => array(
						'text_color'    => array(
							'type'          => 'color',
							'label'         => __('Color', 'fl-builder'),
							'show_reset'    => true,
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'link_color'    => array(
							'type'          => 'color',
							'label'         => __('Link Color', 'fl-builder'),
							'show_reset'    => true,
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'hover_color'    => array(
							'type'          => 'color',
							'label'         => __('Link Hover Color', 'fl-builder'),
							'show_reset'    => true,
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'heading_color'  => array(
							'type'          => 'color',
							'label'         => __('Heading Color', 'fl-builder'),
							'show_reset'    => true,
							'preview'         => array(
								'type'            => 'none'
							)
						)
					)
				),
				'background'    => array(
					'title'         => __('Background', 'fl-builder'),
					'fields'        => array(
						'bg_type'      => array(
							'type'          => 'select',
							'label'         => __('Type', 'fl-builder'),
							'default'       => 'color',
							'options'       => array(
								'none'          => _x( 'None', 'Background type.', 'fl-builder' ),
								'color'         => _x( 'Color', 'Background type.', 'fl-builder' ),
								'photo'         => _x( 'Photo', 'Background type.', 'fl-builder' ),
							),
							'toggle'        => array(
								'color'         => array(
									'sections'      => array('bg_color')
								),
								'photo'         => array(
									'sections'      => array('bg_photo', 'bg_overlay')
								),
							),
							'preview'         => array(
								'type'            => 'none'
							)
						)
					)
				),
				'bg_color'     => array(
					'title'         => __('Background Color', 'fl-builder'),
					'fields'        => array(
						'bg_color'      => array(
							'type'          => 'color',
							'label'         => __('Color', 'fl-builder'),
							'show_reset'    => true,
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'bg_opacity'    => array(
							'type'          => 'text',
							'label'         => __('Opacity', 'fl-builder'),
							'default'       => '100',
							'description'   => '%',
							'maxlength'     => '3',
							'size'          => '5',
							'preview'         => array(
								'type'            => 'none'
							)
						)
					)
				),
				'bg_photo'     => array(
					'title'         => __('Background Photo', 'fl-builder'),
					'fields'        => array(
						'bg_image'      => array(
							'type'          => 'photo',
							'label'         => __('Photo', 'fl-builder'),
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'bg_repeat'     => array(
							'type'          => 'select',
							'label'         => __('Repeat', 'fl-builder'),
							'default'       => 'none',
							'options'       => array(
								'no-repeat'     => _x( 'None', 'Background repeat.', 'fl-builder' ),
								'repeat'        => _x( 'Tile', 'Background repeat.', 'fl-builder' ),
								'repeat-x'      => _x( 'Horizontal', 'Background repeat.', 'fl-builder' ),
								'repeat-y'      => _x( 'Vertical', 'Background repeat.', 'fl-builder' )
							),
							'help'          => __('Repeat applies to how the image should display in the background. Choosing none will display the image as uploaded. Tile will repeat the image as many times as needed to fill the background horizontally and vertically. You can also specify the image to only repeat horizontally or vertically.', 'fl-builder'),
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'bg_position'   => array(
							'type'          => 'select',
							'label'         => __('Position', 'fl-builder'),
							'default'       => 'center center',
							'options'       => array(
								'left top'      => __('Left Top', 'fl-builder'),
								'left center'   => __('Left Center', 'fl-builder'),
								'left bottom'   => __('Left Bottom', 'fl-builder'),
								'right top'     => __('Right Top', 'fl-builder'),
								'right center'  => __('Right Center', 'fl-builder'),
								'right bottom'  => __('Right Bottom', 'fl-builder'),
								'center top'    => __('Center Top', 'fl-builder'),
								'center center' => __( 'Center', 'fl-builder' ),
								'center bottom' => __('Center Bottom', 'fl-builder')
							),
							'help'          => __('Position will tell the image where it should sit in the background.', 'fl-builder'),
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'bg_attachment' => array(
							'type'          => 'select',
							'label'         => __('Attachment', 'fl-builder'),
							'default'       => 'scroll',
							'options'       => array(
								'scroll'        => __( 'Scroll', 'fl-builder' ),
								'fixed'         => __( 'Fixed', 'fl-builder' )
							),
							'help'          => __('Attachment will specify how the image reacts when scrolling a page. When scrolling is selected, the image will scroll with page scrolling. This is the default setting. Fixed will allow the image to scroll within the background if fill is selected in the scale setting.', 'fl-builder'),
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'bg_size'       => array(
							'type'          => 'select',
							'label'         => __('Scale', 'fl-builder'),
							'default'       => 'cover',
							'options'       => array(
								''              => _x( 'None', 'Background scale.', 'fl-builder' ),
								'contain'       => __( 'Fit', 'fl-builder'),
								'cover'         => __( 'Fill', 'fl-builder')
							),
							'help'          => __('Scale applies to how the image should display in the background. You can select either fill or fit to the background.', 'fl-builder'),
							'preview'         => array(
								'type'            => 'none'
							)
						)
					)
				),
				'bg_overlay'     => array(
					'title'         => __('Background Overlay', 'fl-builder'),
					'fields'        => array(
						'bg_overlay_color'      => array(
							'type'          => 'color',
							'label'         => __('Overlay Color', 'fl-builder'),
							'show_reset'    => true,
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'bg_overlay_opacity'    => array(
							'type'          => 'text',
							'label'         => __('Overlay Opacity', 'fl-builder'),
							'default'       => '50',
							'description'   => '%',
							'maxlength'     => '3',
							'size'          => '5',
							'preview'         => array(
								'type'            => 'none'
							)
						)
					)
				),
				'border'       => array(
					'title'         => __('Border', 'fl-builder'),
					'fields'        => array(
						'border_type'   => array(
							'type'          => 'select',
							'label'         => __('Type', 'fl-builder'),
							'default'       => '',
							'help'          => __('The type of border to use. Double borders must have a width of at least 3px to render properly.', 'fl-builder'),
							'options'       => array(
								''       => _x( 'None', 'Border type.', 'fl-builder' ),
								'solid'  => _x( 'Solid', 'Border type.', 'fl-builder' ),
								'dashed' => _x( 'Dashed', 'Border type.', 'fl-builder' ),
								'dotted' => _x( 'Dotted', 'Border type.', 'fl-builder' ),
								'double' => _x( 'Double', 'Border type.', 'fl-builder' )
							),
							'toggle'        => array(
								''              => array(
									'fields'        => array()
								),
								'solid'         => array(
									'fields'        => array('border_color', 'border_opacity', 'border_top', 'border_bottom', 'border_left', 'border_right')
								),
								'dashed'        => array(
									'fields'        => array('border_color', 'border_opacity', 'border_top', 'border_bottom', 'border_left', 'border_right')
								),
								'dotted'        => array(
									'fields'        => array('border_color', 'border_opacity', 'border_top', 'border_bottom', 'border_left', 'border_right')
								),
								'double'        => array(
									'fields'        => array('border_color', 'border_opacity', 'border_top', 'border_bottom', 'border_left', 'border_right')
								)
							),
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'border_color'  => array(
							'type'          => 'color',
							'label'         => __('Color', 'fl-builder'),
							'show_reset'    => true,
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'border_opacity' => array(
							'type'          => 'text',
							'label'         => __('Opacity', 'fl-builder'),
							'default'       => '100',
							'description'   => '%',
							'maxlength'     => '3',
							'size'          => '5',
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'border_top'    => array(
							'type'          => 'text',
							'label'         => __('Top Width', 'fl-builder'),
							'default'       => '1',
							'description'   => 'px',
							'maxlength'     => '3',
							'size'          => '5',
							'placeholder'   => '0',
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'border_bottom' => array(
							'type'          => 'text',
							'label'         => __('Bottom Width', 'fl-builder'),
							'default'       => '1',
							'description'   => 'px',
							'maxlength'     => '3',
							'size'          => '5',
							'placeholder'   => '0',
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'border_left'   => array(
							'type'          => 'text',
							'label'         => __('Left Width', 'fl-builder'),
							'default'       => '1',
							'description'   => 'px',
							'maxlength'     => '3',
							'size'          => '5',
							'placeholder'   => '0',
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'border_right'  => array(
							'type'          => 'text',
							'label'         => __('Right Width', 'fl-builder'),
							'default'       => '1',
							'description'   => 'px',
							'maxlength'     => '3',
							'size'          => '5',
							'placeholder'   => '0',
							'preview'         => array(
								'type'            => 'none'
							)
						)
					)
				),
			)
		),
		'advanced'      => array(
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
							'placeholder'   => '0',
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
							'placeholder'   => '0',
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
							'placeholder'   => '0',
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
							'placeholder'   => '0',
							'preview'         => array(
								'type'            => 'none'
							)
						)
					)
				),
				'padding'       => array(
					'title'         => __('Padding', 'fl-builder'),
					'fields'        => array(
						'padding_top'   => array(
							'type'          => 'text',
							'label'         => __( 'Top', 'fl-builder' ),
							'default'       => '',
							'description'   => 'px',
							'maxlength'     => '4',
							'size'          => '5',
							'placeholder'   => '0',
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'padding_bottom' => array(
							'type'          => 'text',
							'label'         => __( 'Bottom', 'fl-builder' ),
							'default'       => '',
							'description'   => 'px',
							'maxlength'     => '4',
							'size'          => '5',
							'placeholder'   => '0',
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'padding_left'  => array(
							'type'          => 'text',
							'label'         => __( 'Left', 'fl-builder' ),
							'default'       => '',
							'description'   => 'px',
							'maxlength'     => '4',
							'size'          => '5',
							'placeholder'   => '0',
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'padding_right' => array(
							'type'          => 'text',
							'label'         => __( 'Right', 'fl-builder' ),
							'default'       => '',
							'description'   => 'px',
							'maxlength'     => '4',
							'size'          => '5',
							'placeholder'   => '0',
							'preview'         => array(
								'type'            => 'none'
							)
						)
					)
				),
				'responsive'    => array(
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
							'help'          => __( 'Choose whether to show or hide this column at different device sizes.', 'fl-builder' ),
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'medium_size'   => array(
							'type'          => 'select',
							'label'         => __('Medium Device Width', 'fl-builder'),
							'help'          => __( 'The width of this column on medium devices such as tablets.', 'fl-builder' ),
							'options'       => array(
								'default'       => __('Default', 'fl-builder'),
								'custom'        => __('Custom', 'fl-builder'),
							),
							'toggle'               => array(
								'custom'               => array(
									'fields'               => array('custom_medium_size')
								)
							),
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'custom_medium_size' => array(
							'type'          => 'text',
							'label'         => __('Custom Medium Device Width', 'fl-builder'),
							'default'       => '100',
							'description'   => '%',
							'maxlength'     => '5',
							'size'          => '6',
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'responsive_size' => array(
							'type'          => 'select',
							'label'         => __('Small Device Width', 'fl-builder'),
							'help'          => __( 'The width of this column on small devices such as phones.', 'fl-builder' ),
							'options'       => array(
								'default'       => __('Default', 'fl-builder'),
								'custom'        => __('Custom', 'fl-builder'),
							),
							'toggle'               => array(
								'custom'               => array(
									'fields'               => array('custom_responsive_size')
								)
							),
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'custom_responsive_size' => array(
							'type'          => 'text',
							'label'         => __( 'Custom Small Device Width', 'fl-builder' ),
							'default'       => '100',
							'description'   => '%',
							'maxlength'     => '5',
							'size'          => '6',
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
							'help'          => __( "A unique ID that will be applied to this column's HTML. Must start with a letter and only contain dashes, underscores, letters or numbers. No spaces.", 'fl-builder' ),
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'class'         => array(
							'type'          => 'text',
							'label'         => __('CSS Class', 'fl-builder'),
							'help'          => __( "A class that will be applied to this column's HTML. Must start with a letter and only contain dashes, underscores, letters or numbers. Separate multiple classes with spaces.", 'fl-builder' ),
							'preview'         => array(
								'type'            => 'none'
							)
						)
					)
				)
			)
		)
	)
));