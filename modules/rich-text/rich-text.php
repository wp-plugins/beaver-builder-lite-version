<?php

/**
 * @class FLRichTextModule
 */
class FLRichTextModule extends FLBuilderModule {

	/** 
	 * @method __construct
	 */  
	public function __construct()
	{
		parent::__construct(array(
			'name'          => __('Text Editor', 'fl-builder'),
			'description'   => __('A WYSIWYG text editor.', 'fl-builder'),
			'category'      => __('Basic Modules', 'fl-builder')
		));
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('FLRichTextModule', array(
	'general'       => array( // Tab
		'title'         => __('General', 'fl-builder'), // Tab title
		'sections'      => array( // Tab Sections
			'general'       => array( // Section
				'title'         => '', // Section Title
				'fields'        => array( // Section Fields
					'text'          => array(
						'type'          => 'editor',
						'label'         => '',
						'rows'          => 16,
						'preview'         => array(
							'type'             => 'text',
							'selector'         => '.fl-rich-text'  
						)
					)
				)
			)
		)
	)
));