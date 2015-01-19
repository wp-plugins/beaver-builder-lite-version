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
    <?php
        
    $field_file = FL_BUILDER_DIR . 'includes/field-' . $field['type'] . '.php';
    
    if(file_exists($field_file)) {
        include $field_file;
    }
    else {
        do_action('fl_builder_control_' . $field['type'], $name, $value, $field);
    }
        
    ?>
    <?php if(isset($field['description'])) : ?>
    <span class="fl-field-description"><?php echo $field['description']; ?></span>
    <?php endif; ?>
</td>