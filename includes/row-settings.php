<?php

$global_settings = FLBuilderModel::get_global_settings();

FLBuilder::register_settings_form('row', array(
	'title' => __('Row Settings', 'fl-builder'),
	'tabs' => array(
		'style'         => array(
			'title'         => __('Style', 'fl-builder'),
			'sections'      => array(
				'general'       => array(
					'title'         => '',
					'fields'        => array(
						'width'         => array(
							'type'          => 'select',
							'label'         => __('Width', 'fl-builder'),
							'default'       => $global_settings->row_width_default,
							'options'       => array(
								'fixed'         => __('Fixed', 'fl-builder'),
								'full'          => __('Full Width', 'fl-builder')
							),
							'toggle'        => array(
								'full'         => array(
									'fields'        => array('content_width')
								)
							),
							'help'          => __('Full width rows span the width of the page from edge to edge. Fixed rows are no wider than the Row Max Width set in the Global Settings.', 'fl-builder'),
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'content_width'  => array(
							'type'          => 'select',
							'label'         => __('Content Width', 'fl-builder'),
							'default'       => $global_settings->row_content_width_default,
							'options'       => array(
								'fixed'         => __('Fixed', 'fl-builder'),
								'full'          => __('Full Width', 'fl-builder')
							),
							'help'          => __('Full width content spans the width of the page from edge to edge. Fixed content is no wider than the Row Max Width set in the Global Settings.', 'fl-builder'),
							'preview'         => array(
								'type'            => 'none'
							)
						),
					)
				),
				'colors'        => array(
					'title'         => __('Colors', 'fl-builder'),
					'fields'        => array(
						'text_color'    => array(
							'type'          => 'color',
							'label'         => __('Text Color', 'fl-builder'),
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
							'default'       => 'none',
							'options'       => array(
								'none'          => _x( 'None', 'Background type.', 'fl-builder' ),
								'color'         => _x( 'Color', 'Background type.', 'fl-builder' ),
								'photo'         => _x( 'Photo', 'Background type.', 'fl-builder' ),
								'video'         => _x( 'Video', 'Background type.', 'fl-builder' ),
								'slideshow'     => array(
									'label'         => _x( 'Slideshow', 'Background type.', 'fl-builder' ),
									'premium'       => true
								),
								'parallax'     => array(
									'label'         => _x( 'Parallax', 'Background type.', 'fl-builder' ),
									'premium'       => true
								)
							),
							'toggle'        => array(
								'color'         => array(
									'sections'      => array('bg_color')
								),
								'photo'         => array(
									'sections'      => array('bg_photo', 'bg_overlay')
								),
								'video'         => array(
									'sections'      => array('bg_video', 'bg_overlay')
								),
								'slideshow'     => array(
									'sections'      => array('bg_slideshow', 'bg_overlay')
								),
								'parallax'      => array(
									'sections'      => array('bg_parallax', 'bg_overlay')
								)
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
				'bg_video'     => array(
					'title'         => __('Background Video', 'fl-builder'),
					'fields'        => array(
						'bg_video'      => array(
							'type'          => 'video',
							'label'         => __('Video', 'fl-builder'),
							'help'          => __('An HTML5 video to use as the background of this row. Supported types are MP4, WebM and Ogg.', 'fl-builder'),
							'preview'         => array(
								'type'            => 'refresh'
							)
						),
						'bg_video_fallback' => array(
							'type'          => 'photo',
							'label'         => __('Fallback Photo', 'fl-builder'),
							'help'          => __('A photo that will be displayed if the video fails to load.', 'fl-builder'),
							'preview'         => array(
								'type'            => 'refresh'
							)
						)
					)
				),
				'bg_slideshow' => array(
					'title'         => __('Background Slideshow', 'fl-builder'),
					'fields'        => array(
						'ss_source'     => array(
							'type'          => 'select',
							'label'         => __('Source', 'fl-builder'),
							'default'       => 'wordpress',
							'options'       => array(
								'wordpress'     => __('Media Library', 'fl-builder'),
								'smugmug'       => 'SmugMug'
							),
							'help'          => __('Pull images from the WordPress media library or a gallery on your SmugMug site by inserting the RSS feed URL from SmugMug. The RSS feed URL can be accessed by using the get a link function in your SmugMug gallery.', 'fl-builder'),
							'toggle'        => array(
								'wordpress'      => array(
									'fields'        => array('ss_photos')
								),
								'smugmug'        => array(
									'fields'        => array('ss_feed_url')
								)
							),
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'ss_photos'     => array(
							'type'          => 'multiple-photos',
							'label'         => __('Photos', 'fl-builder'),
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'ss_feed_url'   => array(
							'type'          => 'text',
							'label'         => __('Feed URL', 'fl-builder'),
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'ss_speed'      => array(
							'type'          => 'text',
							'label'         => __('Speed', 'fl-builder'),
							'default'       => '3',
							'size'          => '5',
							'description'   => _x( 'seconds', 'Value unit for form field of time in seconds. Such as: "5 seconds"', 'fl-builder' ),
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'ss_transition' => array(
							'type'          => 'select',
							'label'         => __('Transition', 'fl-builder'),
							'default'       => 'fade',
							'options'       => array(
								'none'              => _x( 'None', 'Slideshow transition type.', 'fl-builder' ),
								'fade'              => __('Fade', 'fl-builder'),
								'kenBurns'          => __('Ken Burns', 'fl-builder'),
								'slideHorizontal'   => __('Slide Horizontal', 'fl-builder'),
								'slideVertical'     => __('Slide Vertical', 'fl-builder'),
								'blinds'            => __('Blinds', 'fl-builder'),
								'bars'              => __('Bars', 'fl-builder'),
								'barsRandom'        => __('Random Bars', 'fl-builder'),
								'boxes'             => __('Boxes', 'fl-builder'),
								'boxesRandom'       => __('Random Boxes', 'fl-builder'),
								'boxesGrow'         => __('Boxes Grow', 'fl-builder')
							),
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'ss_transitionDuration' => array(
							'type'          => 'text',
							'label'         => __('Transition Speed', 'fl-builder'),
							'default'       => '1',
							'size'          => '5',
							'description'   => _x( 'seconds', 'Value unit for form field of time in seconds. Such as: "5 seconds"', 'fl-builder' ),
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'ss_randomize'  => array(
							'type'          => 'select',
							'label'         => __('Randomize Photos', 'fl-builder'),
							'default'       => 'false',
							'options'       => array(
								'false'         => __('No', 'fl-builder'),
								'true'          => __('Yes', 'fl-builder')
							),
							'preview'         => array(
								'type'            => 'none'
							)
						)
					)
				),
				'bg_parallax'   => array(
					'title'         => __('Background Parallax', 'fl-builder'),
					'fields'        => array(
						'bg_parallax_image' => array(
							'type'          => 'photo',
							'label'         => __('Photo', 'fl-builder'),
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'bg_parallax_speed' => array(
							'type'          => 'select',
							'label'         => __('Speed', 'fl-builder'),
							'default'       => 'fast',
							'options'       => array(
								'2'             => __('Fast', 'fl-builder'),
								'5'             => _x( 'Medium', 'Speed.', 'fl-builder' ),
								'8'             => __('Slow', 'fl-builder')
							),
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
								''              => _x( 'None', 'Border type.', 'fl-builder' ),
								'solid'         => _x( 'Solid', 'Border type.', 'fl-builder' ),
								'dashed'        => _x( 'Dashed', 'Border type.', 'fl-builder' ),
								'dotted'        => _x( 'Dotted', 'Border type.', 'fl-builder' ),
								'double'        => _x( 'Double', 'Border type.', 'fl-builder' )
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
							'default'       => '0',
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
							'default'       => '0',
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
							'label'         => __('Top', 'fl-builder'),
							'default'       => '',
							'description'   => 'px',
							'maxlength'     => '4',
							'size'          => '5',
							'placeholder'   => $global_settings->row_margins,
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'margin_bottom' => array(
							'type'          => 'text',
							'label'         => __('Bottom', 'fl-builder'),
							'default'       => '',
							'description'   => 'px',
							'maxlength'     => '4',
							'size'          => '5',
							'placeholder'   => $global_settings->row_margins,
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'margin_left'   => array(
							'type'          => 'text',
							'label'         => __('Left', 'fl-builder'),
							'default'       => '',
							'description'   => 'px',
							'maxlength'     => '4',
							'size'          => '5',
							'placeholder'   => $global_settings->row_margins,
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'margin_right'  => array(
							'type'          => 'text',
							'label'         => __('Right', 'fl-builder'),
							'default'       => '',
							'description'   => 'px',
							'maxlength'     => '4',
							'size'          => '5',
							'placeholder'   => $global_settings->row_margins,
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
							'label'         => __('Top', 'fl-builder'),
							'default'       => '',
							'description'   => 'px',
							'maxlength'     => '4',
							'size'          => '5',
							'placeholder'   => $global_settings->row_padding,
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'padding_bottom' => array(
							'type'          => 'text',
							'label'         => __('Bottom', 'fl-builder'),
							'default'       => '',
							'description'   => 'px',
							'maxlength'     => '4',
							'size'          => '5',
							'placeholder'   => $global_settings->row_padding,
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'padding_left'  => array(
							'type'          => 'text',
							'label'         => __('Left', 'fl-builder'),
							'default'       => '',
							'description'   => 'px',
							'maxlength'     => '4',
							'size'          => '5',
							'placeholder'   => $global_settings->row_padding,
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'padding_right' => array(
							'type'          => 'text',
							'label'         => __('Right', 'fl-builder'),
							'default'       => '',
							'description'   => 'px',
							'maxlength'     => '4',
							'size'          => '5',
							'placeholder'   => $global_settings->row_padding,
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
							'help'          => __( 'Choose whether to show or hide this row at different device sizes.', 'fl-builder' ),
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
							'help'          => __( "A unique ID that will be applied to this row's HTML. Must start with a letter and only contain dashes, underscores, letters or numbers. No spaces.", 'fl-builder' ),
							'preview'         => array(
								'type'            => 'none'
							)
						),
						'class'         => array(
							'type'          => 'text',
							'label'         => __('Class', 'fl-builder'),
							'help'          => __( "A class that will be applied to this row's HTML. Must start with a letter and only contain dashes, underscores, letters or numbers. Separate multiple classes with spaces.", 'fl-builder' ),
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