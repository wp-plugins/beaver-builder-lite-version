<div class="fl-form-field fl-builder-custom-field"<?php if(isset($field['preview_text'])) echo ' data-preview-text="'. $field['preview_text'] .'"'; ?>>
    <div class="fl-form-field-preview-text">
    <?php

    if(isset($field['preview_text']) && is_object($value)) {

        if('icon' == $field['preview_text']) {
            echo '<i class="' . $value->$field['preview_text'] . '"></i>';
        }
        else if(isset($value->$field['preview_text']) && !empty($value->$field['preview_text'])) {
            echo FLBuilderUtils::snippetwop(strip_tags(str_replace('&#39;', "'", $value->$field['preview_text'])), 35);
        }
    }

    ?>
    </div>
	<a class="fl-form-field-edit" href="javascript:void(0);" onclick="return false;" data-type="<?php echo $field['form']; ?>"><?php printf( _x( 'Edit %s', '%s stands for form field label.', 'fl-builder' ), $field['label'] ); ?></a>
    <input name="<?php echo $name; ?>" type="hidden" value='<?php echo str_replace("'", '&#39;', json_encode($value)); ?>' />
</div>