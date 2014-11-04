<?php 

FLBuilder::register_settings_form('global', array(
    'title' => FLBuilderModel::get_branding() . ' ' . __('Settings', 'fl-builder'),
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
                            'label'                => __('Show', 'fl-builder'),
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
                            'description'       => __('px', 'fl-builder')
                        ),
                    	'row_padding'       => array(
                            'type'              => 'text',
                            'label'             => __('Padding', 'fl-builder'),
                            'default'           => '20',
                            'maxlength'         => '3',
                            'size'              => '5',
                            'description'       => __('px', 'fl-builder')
                        ),
                        'row_width'         => array(
                            'type'              => 'text',
                            'label'             => __('Max Width', 'fl-builder'),
                            'default'           => '1100',
                            'maxlength'         => '4',
                            'size'              => '5',
                            'description'       => __('px', 'fl-builder'),
                            'help'                     => __('All rows will default to this width. You can override this and make a row full width in the settings for each row.', 'fl-builder')
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
                            'description'       => __('px', 'fl-builder')
                        )
                    )
                ),
                'mobile'        => array(
                    'title'         => __('Mobile Layout', 'fl-builder'),
                    'fields'        => array(
                        'responsive_enabled'   => array(
                            'type'                 => 'select',
                            'label'                => __('Enabled', 'fl-builder'),
                            'default'              => '1',
                            'options'              => array(
                                '0'                     => __('No', 'fl-builder'),
                                '1'                     => __('Yes', 'fl-builder')
                            ),
                            'toggle'               => array(
                                '1'                    => array(
                                    'fields'               => array('responsive_breakpoint')
                                )
                            )
                        ),
                        'responsive_breakpoint' => array(
                            'type'              => 'text',
                            'label'             => __('Breakpoint', 'fl-builder'),
                            'default'           => '768',
                            'maxlength'         => '4',
                            'size'              => '5',
                            'description'       => __('px', 'fl-builder')
                        )
                    )
                )
            )
        )
    )
));