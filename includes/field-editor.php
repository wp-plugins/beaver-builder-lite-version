<div class="fl-editor-field" id="<?php echo $name; ?>">
	<?php 

	// Remove 3rd party editor buttons.
	remove_all_actions('media_buttons', 999999);
	remove_all_actions('media_buttons_context', 999999);
	remove_all_filters('mce_external_plugins', 999999);
	
	global $wp_version;

	$editor_id = 'flrich' . time() . '_' . $name;
	
	wp_editor($value, $editor_id, array(
		'media_buttons' => isset($field['media_buttons']) ? $field['media_buttons'] : true,
		'textarea_rows' => isset($field['rows']) ? $field['rows'] : 16,
		'wpautop'       => true
	)); 
	
	?>
	<script type="text/javascript">
	
	<?php if(version_compare($wp_version, '3.8.9', '<=')) : // Pre 3.9 editor init. ?>
	jQuery(function() 
	{
		var editorId = '<?php echo $editor_id; ?>';
		
		quicktags({id : editorId});
		QTags._buttonsInit();
		
		if(typeof tinymce != 'undefined') {
			tinymce.execCommand('mceAddControl', true, editorId);
		}
		
		FLBuilder.initEditorField(editorId);
	});
	<?php else : // 3.9 and above editor init. ?>
	jQuery(function() 
	{
		var editorId     = '<?php echo $editor_id; ?>',
			hiddenEditor = tinyMCEPreInit.mceInit['flhiddeneditor'],
			editorProps  = null;
		
		if(typeof tinymce != 'undefined') {
			editorProps = tinymce.extend({}, hiddenEditor);
			editorProps.selector = '#' + editorId;
			editorProps.body_class = editorProps.body_class.replace('flhiddeneditor', editorId);
			tinyMCEPreInit.mceInit[editorId] = editorProps;
			tinymce.init(editorProps);
		}
		if(typeof quicktags != 'undefined') {                
			quicktags({id : editorId});
			QTags._buttonsInit();
		}
		
		window.wpActiveEditor = editorId;
	});
	<?php endif; ?>
	
	</script>
</div>