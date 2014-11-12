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