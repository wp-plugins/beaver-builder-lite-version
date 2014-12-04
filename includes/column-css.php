<?php if(!empty($col->settings->text_color)) : ?>
.fl-node-<?php echo $col->node; ?>,
.fl-node-<?php echo $col->node; ?> * {
    color: #<?php echo $col->settings->text_color; ?>;
}
<?php endif; ?>

<?php if(!empty($col->settings->bg_color)) : ?>
.fl-node-<?php echo $col->node; ?> .fl-col-content {
    background-color: #<?php echo $col->settings->bg_color; ?>;
    background-color: rgba(<?php echo implode(',', FLBuilderColor::hex_to_rgb($col->settings->bg_color)) ?>, <?php echo $col->settings->bg_opacity/100; ?>);
}
<?php endif; ?>

<?php if(!empty($col->settings->border_type)) : ?>
.fl-builder-content .fl-node-<?php echo $col->node; ?> .fl-col-content {
    border-style: <?php echo $col->settings->border_type; ?>;
    border-color: #<?php echo $col->settings->border_color; ?>;
    border-color: rgba(<?php echo implode(',', FLBuilderColor::hex_to_rgb($col->settings->border_color)) ?>, <?php echo $col->settings->border_opacity/100; ?>);
    border-top-width: <?php echo is_numeric($col->settings->border_top) ? $col->settings->border_top : '0'; ?>px;
    border-bottom-width: <?php echo is_numeric($col->settings->border_bottom) ? $col->settings->border_bottom : '0'; ?>px;
    border-left-width: <?php echo is_numeric($col->settings->border_left) ? $col->settings->border_left : '0'; ?>px;
    border-right-width: <?php echo is_numeric($col->settings->border_right) ? $col->settings->border_right : '0'; ?>px;
}
<?php endif; ?>

<?php if($global_settings->responsive_enabled) : ?>

    <?php if($col->settings->medium_size == 'custom') : ?>
    @media(max-width: <?php echo $global_settings->medium_breakpoint; ?>px) {
        .fl-builder-content .fl-node-<?php echo $col->node; ?> {
            width: <?php echo $col->settings->custom_medium_size; ?>% !important;
        }
    }
    <?php endif; ?>
    
    <?php if($col->settings->responsive_size == 'custom') : ?>
    @media(max-width: <?php echo $global_settings->responsive_breakpoint; ?>px) {
        .fl-builder-content .fl-node-<?php echo $col->node; ?> {
            clear: none;
            float: left;
            width: <?php echo $col->settings->custom_responsive_size; ?>% !important;
        }
    }
    <?php endif; ?>
    
<?php endif; ?>