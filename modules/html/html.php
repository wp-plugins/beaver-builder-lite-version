<?php

/**
 * @class FLHtmlModule
 */
class FLHtmlModule extends FLBuilderModule {

	/** 
	 * @method __construct
	 */  
	public function __construct()
	{
		parent::__construct(array(
			'name'          => __('HTML', 'fl-builder'),
			'description'   => __('Display raw HTML code.', 'fl-builder'),
			'category'      => __('Advanced Modules', 'fl-builder')
		));
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('FLHtmlModule', array(
	'general'       => array(
		'title'         => __('General', 'fl-builder'),
		'sections'      => array(
			'general'       => array(
				'title'         => '',
				'fields'        => array(
					'html'          => array(
						'type'          => 'code',
						'editor'        => 'html',
						'label'         => '',
						'rows'          => '18',
						'preview'           => array(
							'type'              => 'text',
							'selector'          => '.fl-html'
						)
					)
				)
			)
		)
	)
));