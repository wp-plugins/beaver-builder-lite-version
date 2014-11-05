<?php if(empty($field['label'])) : ?>
<td colspan="2">
<?php else : ?>
<th>
    <label for="<?php echo $name; ?>">
    <?php 
    
    echo $field['label']; 
    
    if(isset($i)) {
        echo ' <span>' . ($i + 1) . '</span>';
    }
    
    ?>
    </label>
    <?php if(isset($field['help'])) : ?>
	<span class="fl-help-tooltip">
        <i class="fl-help-tooltip-icon fa fa-question-circle"></i>
        <span class="fl-help-tooltip-text"><?php echo $field['help']; ?></span>
	</span>
    <?php endif; ?>
</th>
<td>
<?php endif; ?>
    
    <?php if($field['type'] == 'text') : // TEXT ?>
    <input type="text" name="<?php echo $name; ?>" value="<?php echo htmlspecialchars($value); ?>" class="text<?php if(isset($field['class'])) echo ' '. $field['class']; if(!isset($field['size'])) echo ' text-full'; ?>" <?php if(isset($field['placeholder'])) echo ' placeholder="'. $field['placeholder'] .'"'; if(isset($field['maxlength'])) echo ' maxlength="'. $field['maxlength'] .'"';  if(isset($field['size'])) echo ' size="'. $field['size'] .'"'; ?> />
    <?php endif; ?>
    
    <?php if($field['type'] == 'textarea') : // TEXTAREA ?>
    <textarea name="<?php echo $name; ?>"<?php if(isset($field['class'])) echo ' class="'. $field['class'] .'"'; if(isset($field['placeholder'])) echo ' placeholder="'. $field['placeholder'] .'"'; if(isset($field['rows'])) echo ' rows="'. $field['rows'] .'"'; ?>><?php echo $value; ?></textarea>
    <?php endif; ?>
    
    <?php if($field['type'] == 'select') : // SELECT ?>
    <select name="<?php echo $name; ?>"<?php if(isset($field['class'])) echo ' class="'. $field['class'] .'"'; if(isset($field['toggle'])) echo "data-toggle='". json_encode($field['toggle']) ."'"; if(isset($field['hide'])) echo "data-hide='". json_encode($field['hide']) ."'"; if(isset($field['trigger'])) echo "data-trigger='". json_encode($field['trigger']) ."'"; ?>>
        <?php 
        
        foreach($field['options'] as $key => $val) : 
        
            $label = is_array($val) ? $val['label'] : $val;
        
            if(is_array($val) && isset($val['premium']) && $val['premium'] && FL_BUILDER_LITE === true) {
                continue;
            }
        
        ?>
        <option value="<?php echo $key; ?>" <?php selected($value, $key); ?>><?php echo $label; ?></option>
        <?php endforeach; ?>
    </select>
    <?php endif; ?>
    
    <?php if($field['type'] == 'color') : // COLOR ?>
    <div class="fl-color-picker<?php if(empty($value)) echo ' fl-color-picker-empty'; if(isset($field['class'])) echo ' ' . $field['class']; ?>">
        <div class="fl-color-picker-color fl-picker-<?php echo $name; ?>"></div>
        <?php if(isset($field['show_reset']) && $field['show_reset']) : ?>
        <div class="fl-color-picker-clear"></div>
        <?php endif; ?>
        <input name="<?php echo $name; ?>" type="hidden" value="<?php echo $value; ?>" class="fl-color-picker-value" />
    </div>
    <?php endif; ?>
    
    <?php if($field['type'] == 'photo') : // PHOTO ?>
    <?php $photo = FLBuilderPhoto::get_attachment_data($value); ?>
    <div class="fl-photo-field fl-builder-custom-field<?php if(empty($value) || !$photo) echo ' fl-photo-empty'; if(isset($field['class'])) echo ' ' . $field['class']; ?>">
        <a class="fl-photo-select" href="javascript:void(0);" onclick="return false;"><?php _e('Select Photo', 'fl-builder'); ?></a>
        <div class="fl-photo-preview">
            <div class="fl-photo-preview-img">
                <img src="<?php if($photo) echo FLBuilderPhoto::get_thumb($photo); ?>" />
            </div>
            <select name="<?php echo $name; ?>_src">
                <?php if($photo && isset($settings->{$name . '_src'})) echo FLBuilderPhoto::get_src_options($settings->{$name . '_src'}, $photo); ?>
            </select>
            <br />
            <a class="fl-photo-edit" href="javascript:void(0);" onclick="return false;"><?php _e('Edit', 'fl-builder'); ?></a>
            <a class="fl-photo-replace" href="javascript:void(0);" onclick="return false;"><?php _e('Replace', 'fl-builder'); ?></a>
            <div class="fl-clear"></div>
        </div>
        <input name="<?php echo $name; ?>" type="hidden" value='<?php echo $value; ?>' />
    </div>
    <?php endif; ?>
    
    <?php if($field['type'] == 'multiple-photos') : // MULTIPLE PHOTOS ?>
    <div class="fl-multiple-photos-field fl-builder-custom-field<?php if(empty($value)) echo ' fl-multiple-photos-empty'; if(isset($field['class'])) echo ' ' . $field['class']; ?>">
        <div class="fl-multiple-photos-count">
        <?php
        
        $count = is_array($value) ? count($value) : false;
        
        if($count) {
            if($count == 1) {
                echo $count . __(' Photo Selected', 'fl-builder');
            }
            else {
                echo $count . __(' Photos Selected', 'fl-builder');
            }
        }
            
        ?>
        </div>
        <a class="fl-multiple-photos-select" href="javascript:void(0);" onclick="return false;"><?php _e('Create Gallery', 'fl-builder'); ?></a>
        <a class="fl-multiple-photos-edit" href="javascript:void(0);" onclick="return false;"><?php _e('Edit Gallery', 'fl-builder'); ?></a>
        <a class="fl-multiple-photos-add" href="javascript:void(0);" onclick="return false;"><?php _e('Add Photos', 'fl-builder'); ?></a>
        <input name="<?php echo $name; ?>" type="hidden" value='<?php if(!empty($value)) echo json_encode($value); ?>' />
    </div>
    <?php endif; ?>
    
    <?php if($field['type'] == 'photo-sizes') : // PHOTO SIZES ?>
    <select name="<?php echo $name; ?>">
        <?php 
        
        foreach(FLBuilderPhoto::sizes() as $size => $atts) :
 			
 			$label = ucwords(str_replace(array('_', '-'), ' ', $size)) . ' (' . implode('x', $atts) . ')';
 			        
        ?>
        <option value="<?php echo $size; ?>" <?php selected($value, $size); ?>><?php echo $label; ?></option>
        <?php endforeach; ?>
    </select>
    <?php endif; ?>
    
    <?php if($field['type'] == 'video') : // VIDEO ?>
    <?php $video = FLBuilderPhoto::get_attachment_data($value); ?>
    <div class="fl-video-field fl-builder-custom-field<?php if(empty($value) || !$video) echo ' fl-video-empty'; if(isset($field['class'])) echo ' ' . $field['class']; ?>">
        <a class="fl-video-select" href="javascript:void(0);" onclick="return false;"><?php _e('Select Video', 'fl-builder'); ?></a>
        <div class="fl-video-preview">
            <?php if(!empty($value) && $video) : ?>
            <div class="fl-video-preview-img">
                <img src="<?php echo $video->icon; ?>" />
            </div>
            <span class="fl-video-preview-filename"><?php echo $video->filename; ?></span>
            <?php else : ?>
            <div class="fl-video-preview-img">
                <img src="<?php echo FL_BUILDER_URL; ?>img/spacer.png" />
            </div>
            <span class="fl-video-preview-filename"></span>
            <?php endif; ?>
            <br />
            <a class="fl-video-replace" href="javascript:void(0);" onclick="return false;"><?php _e('Replace Video', 'fl-builder'); ?></a>
            <div class="fl-clear"></div>
        </div>
        <input name="<?php echo $name; ?>" type="hidden" value='<?php echo $value; ?>' />
    </div>
    <?php endif; ?>
    
    <?php if($field['type'] == 'icon') : // ICON ?>
    <div class="fl-icon-field fl-builder-custom-field<?php if(empty($value)) echo ' fl-icon-empty'; if(isset($field['class'])) echo ' ' . $field['class']; ?>">
        <a class="fl-icon-select" href="javascript:void(0);" onclick="return false;"><?php _e('Select Icon', 'fl-builder'); ?></a>
        <div class="fl-icon-preview">
            <i class="<?php echo $value; ?>" data-icon="<?php echo $value; ?>"></i>
            <a class="fl-icon-replace" href="javascript:void(0);" onclick="return false;"><?php _e('Replace', 'fl-builder'); ?></a>
            <?php if(isset($field['show_remove']) && $field['show_remove']) : ?>
            <a class="fl-icon-remove" href="javascript:void(0);" onclick="return false;"><?php _e('Remove', 'fl-builder'); ?></a>
            <?php endif; ?>
        </div>
        <input name="<?php echo $name; ?>" type="hidden" value="<?php echo $value; ?>" />
    </div>
    <?php endif; ?>
    
    <?php if($field['type'] == 'form') : // SETTINGS FORM ?>
    <div class="fl-form-field fl-builder-custom-field"<?php if(isset($field['preview_text'])) echo ' data-preview-text="'. $field['preview_text'] .'"'; ?>>
        <div class="fl-form-field-preview-text">
        <?php 

        if(isset($field['preview_text']) && is_object($value)) {

            if(stristr($value->$field['preview_text'], 'fa fa-')) {
                echo '<i class="' . $value->$field['preview_text'] . '"></i>';
            }
            else if(isset($value->$field['preview_text']) && !empty($value->$field['preview_text'])) {
                echo FLBuilderUtils::snippetwop(strip_tags(str_replace('&#39;', "'", $value->$field['preview_text'])), 35);
            }
        }
        
        ?>
        </div>
        <a class="fl-form-field-edit" href="javascript:void(0);" onclick="return false;" data-type="<?php echo $field['form']; ?>"><?php _e('Edit', 'fl-builder'); echo ' ' . $field['label']; ?></a>
        <input name="<?php echo $name; ?>" type="hidden" value='<?php echo str_replace("'", '&#39;', json_encode($value)); ?>' />
    </div>
    <?php endif; ?>
    
    <?php if($field['type'] == 'editor') : // EDITOR ?>
        <div class="fl-editor-field" id="<?php echo $name; ?>">
            <?php 
        
            // Remove 3rd party editor buttons.
            remove_all_actions('media_buttons', 999999);
            remove_all_actions('media_buttons_context', 999999);
            remove_all_filters('mce_external_plugins', 999999);
            
            global $wp_version;

            $editor_id = 'flrich' . time(); 
            
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
    <?php endif; ?>
                                
	<?php if($field['type'] == 'code') : // CODE ?>
	<div class="fl-code-field">
		<?php $editor_id = 'flcode' . time(); ?>
		<textarea id="<?php echo $editor_id; ?>" name="<?php echo $name; ?>" data-editor="<?php echo $field['editor']; ?>" <?php if(isset($field['class'])) echo ' class="'. $field['class'] .'"'; if(isset($field['rows'])) echo ' rows="'. $field['rows'] .'"'; ?>><?php echo htmlspecialchars($value); ?></textarea>
		<script>
		
		jQuery(function(){
			
			var textarea = jQuery('#<?php echo $editor_id; ?>'), 
	    		mode     = textarea.data('editor'), 
	    		editDiv  = jQuery('<div>', {
	                position: 	'absolute',
	                height: 	parseInt(textarea.attr('rows'), 10) * 20
	            }), 
	    		editor = null;
	
			editDiv.insertBefore(textarea);
	        textarea.css('display', 'none');
	
	        editor = ace.edit(editDiv[0]);
	        editor.getSession().setValue(textarea.val());
	        editor.getSession().setMode('ace/mode/' + mode);
	        
	        editor.getSession().on('change', function(e) {
				textarea.val(editor.getSession().getValue()).trigger('change');
			});
		});
		
		</script>
	</div>
	<?php endif; ?>
    
    <?php if($field['type'] == 'post-type') : // POST TYPE ?>
    <select name="<?php echo $name; ?>"<?php if(isset($field['class'])) echo ' class="'. $field['class'] .'"'; ?>>
        <?php foreach(FLBuilderLoop::post_types() as $slug => $type) : ?>
        <option value="<?php echo $slug; ?>" <?php selected($value, $slug); ?>><?php echo $type->labels->name; ?></option>
        <?php endforeach; ?>
    </select>
    <?php endif; ?>
    
    <?php 
    
    if($field['type'] == 'suggest') : // SUGGEST 
    
        $action = isset($field['action']) ? $field['action'] : '';
        $data   = isset($field['data']) ? $field['data'] : '';
    ?>
    <input type="text" name="<?php echo $name; ?>" data-value='<?php echo FLBuilderAutoSuggest::get_value($action, $value, $data); ?>' data-action="<?php echo $action; ?>" data-action-data="<?php echo $data; ?>" class="text text-full fl-suggest-field<?php if(isset($field['class'])) echo ' '. $field['class']; ?>" placeholder="<?php if(isset($field['placeholder'])) echo $field['placeholder']; else echo _e('Start typing...', 'fl-builder'); ?>" />
    <?php endif; ?>
    
    <?php if($field['type'] == 'layout') : // LAYOUT - Experimental, do not use! ?>
    <div class="fl-layout-field">
        <?php foreach($field['options'] as $key => $img) : ?>
        <div class="fl-layout-field-option <?php if($key == $value) echo 'fl-layout-field-option-selected'; ?>" data-value="<?php echo $key; ?>">
            <img src="<?php echo $img; ?>" />
        </div>
        <?php endforeach; ?>
        <div class="fl-clear"></div>
        <input name="<?php echo $name; ?>" type="hidden" value='<?php echo $value; ?>' />
    </div>
    <?php endif; ?>
    
    <?php if(isset($field['description'])) : ?>
    <span class="fl-field-description"><?php echo $field['description']; ?></span>
    <?php endif; ?>
    
</td>