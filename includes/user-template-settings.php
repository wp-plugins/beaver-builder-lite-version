<?php 

FLBuilder::register_settings_form('user_template', array(
    'title' => __('Save Template', 'fl-builder'),
    'tabs' => array(
        'general'  => array(
            'title'         => __('General', 'fl-builder'),
            'description'   => __('Save the current layout as a template that can be reused under Templates > Your Templates.', 'fl-builder'),
            'sections'      => array(
                'general'       => array(
                    'title'         => '',
                    'fields'        => array(
                        'name'          => array(
                            'type'          => 'text',
                            'label'         => __('Name', 'fl-builder')
                        )
                    )
                )
            )
        )
    )
));