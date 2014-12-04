<?php

$global_settings = FLBuilderModel::get_global_settings();
        
FLBuilder::register_settings_form('module-advanced', array(
    'title'         => __('Advanced', 'fl-builder'),
    'sections'      => array(
        'general'       => array(
            'title'         => '',
            'fields'        => array(
                'class'         => array(
                    'type'          => 'text',
                    'label'         => __('CSS Class', 'fl-builder'),
                    'help'          => __('A custom CSS class that will be applied to this module. Spaces only, no dots.'),
                    'preview'         => array(
                        'type'            => 'none'
                    )
                ),
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
                    'help'          => __('Choose whether to show or hide this module at different device sizes.'),
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
                    'label'         => __('Top', 'fl-builder'),
                    'default'       => '',
                    'description'   => __('px', 'fl-builder'),
                    'maxlength'     => '4',
                    'size'          => '5',
                    'placeholder'   => $global_settings->module_margins,
                    'preview'         => array(
                        'type'            => 'none'
                    )
                ),
                'margin_bottom' => array(
                    'type'          => 'text',
                    'label'         => __('Bottom', 'fl-builder'),
                    'default'       => '',
                    'description'   => __('px', 'fl-builder'),
                    'maxlength'     => '4',
                    'size'          => '5',
                    'placeholder'   => $global_settings->module_margins,
                    'preview'         => array(
                        'type'            => 'none'
                    )
                ),
                'margin_left'   => array(
                    'type'          => 'text',
                    'label'         => __('Left', 'fl-builder'),
                    'default'       => '',
                    'description'   => __('px', 'fl-builder'),
                    'maxlength'     => '4',
                    'size'          => '5',
                    'placeholder'   => $global_settings->module_margins,
                    'preview'         => array(
                        'type'            => 'none'
                    )
                ),
                'margin_right'  => array(
                    'type'          => 'text',
                    'label'         => __('Right', 'fl-builder'),
                    'default'       => '',
                    'description'   => __('px', 'fl-builder'),
                    'maxlength'     => '4',
                    'size'          => '5',
                    'placeholder'   => $global_settings->module_margins,
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
                        ''              => __('None', 'fl-builder'),
                        'fade-in'       => __('Fade In', 'fl-builder'),
                        'slide-left'    => __('Slide Left', 'fl-builder'),
                        'slide-right'   => __('Slide Right', 'fl-builder'),
                        'slide-up'      => __('Slide Up', 'fl-builder'),
                        'slide-down'    => __('Slide Down', 'fl-builder'),
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
                    'description'   => 'seconds',
                    'help'          => __('The amount of time in seconds before this animation starts.', 'fl-builder'),
                    'preview'         => array(
                        'type'            => 'none'
                    )
                )
            )
        )
    )
));