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
                'general'       => array(
                    'title'         => '',
                    'fields'        => array(
                        'class'         => array(
                            'type'          => 'text',
                            'label'         => __('CSS Class', 'fl-builder'),
                            'help'          => __( 'A custom CSS class that will be applied to this column. Spaces only, no dots.', 'fl-builder' ),
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
                )
            )
        )
    )
));