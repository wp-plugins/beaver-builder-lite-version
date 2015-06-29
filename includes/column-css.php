.fl-node-<?php echo $col->node; ?> {
	width: <?php echo $col->settings->size; ?>%;
}

<?php if(!empty($col->settings->text_color)) : // Text Color ?>
.fl-node-<?php echo $col->node; ?> {
	color: #<?php echo $col->settings->text_color; ?>;
}
<?php endif; ?>

<?php if(!empty($col->settings->link_color)) : // Link Color ?>
.fl-node-<?php echo $col->node; ?> a {
	color: #<?php echo $col->settings->link_color; ?>;
}
<?php elseif(!empty($col->settings->text_color)) : ?>
.fl-node-<?php echo $col->node; ?> a {
	color: #<?php echo $col->settings->text_color; ?>;
}
<?php endif; ?>

<?php if(!empty($col->settings->hover_color)) : // Link Hover Color ?>
.fl-node-<?php echo $col->node; ?> a:hover {
	color: #<?php echo $col->settings->hover_color; ?>;
}
<?php elseif(!empty($col->settings->text_color)) : ?>
.fl-node-<?php echo $col->node; ?> a:hover {
	color: #<?php echo $col->settings->text_color; ?>;
}
<?php endif; ?>

<?php if(!empty($col->settings->heading_color)) : // Heading Color ?>
.fl-node-<?php echo $col->node; ?> h1,
.fl-node-<?php echo $col->node; ?> h2,
.fl-node-<?php echo $col->node; ?> h3,
.fl-node-<?php echo $col->node; ?> h4,
.fl-node-<?php echo $col->node; ?> h5,
.fl-node-<?php echo $col->node; ?> h6,
.fl-node-<?php echo $col->node; ?> h1 a,
.fl-node-<?php echo $col->node; ?> h2 a,
.fl-node-<?php echo $col->node; ?> h3 a,
.fl-node-<?php echo $col->node; ?> h4 a,
.fl-node-<?php echo $col->node; ?> h5 a,
.fl-node-<?php echo $col->node; ?> h6 a {
	color: #<?php echo $col->settings->heading_color; ?>;
}
<?php elseif(!empty($col->settings->text_color)) : ?>
.fl-node-<?php echo $col->node; ?> h1,
.fl-node-<?php echo $col->node; ?> h2,
.fl-node-<?php echo $col->node; ?> h3,
.fl-node-<?php echo $col->node; ?> h4,
.fl-node-<?php echo $col->node; ?> h5,
.fl-node-<?php echo $col->node; ?> h6,
.fl-node-<?php echo $col->node; ?> h1 a,
.fl-node-<?php echo $col->node; ?> h2 a,
.fl-node-<?php echo $col->node; ?> h3 a,
.fl-node-<?php echo $col->node; ?> h4 a,
.fl-node-<?php echo $col->node; ?> h5 a,
.fl-node-<?php echo $col->node; ?> h6 a {
	color: #<?php echo $col->settings->text_color; ?>;
}
<?php endif; ?>

<?php if($col->settings->bg_type == 'color' && !empty($col->settings->bg_color)) : // Background Color ?>
.fl-node-<?php echo $col->node; ?> .fl-col-content {
	background-color: #<?php echo $col->settings->bg_color; ?>;
	background-color: rgba(<?php echo implode(',', FLBuilderColor::hex_to_rgb($col->settings->bg_color)) ?>, <?php echo $col->settings->bg_opacity/100; ?>);
}
<?php endif; ?>

<?php if($col->settings->bg_type == 'photo' && !empty($col->settings->bg_image)) : // Background Image ?>
.fl-node-<?php echo $col->node; ?> .fl-col-content {
	background-image: url(<?php echo $col->settings->bg_image_src; ?>);
	background-repeat: <?php echo $col->settings->bg_repeat; ?>;
	background-position: <?php echo $col->settings->bg_position; ?>;
	background-attachment: <?php echo $col->settings->bg_attachment; ?>;
	background-size: <?php echo $col->settings->bg_size; ?>;
}
<?php endif; ?>

<?php if( in_array( $col->settings->bg_type, array('photo') ) && ! empty( $col->settings->bg_overlay_color ) ) : // Background Overlay Color ?>
.fl-node-<?php echo $col->node; ?> .fl-col-content:after {
	background-color: #<?php echo $col->settings->bg_overlay_color; ?>;
	background-color: rgba(<?php echo implode( ',', FLBuilderColor::hex_to_rgb( $col->settings->bg_overlay_color ) ) ?>, <?php echo $col->settings->bg_overlay_opacity/100; ?>);
}
<?php endif; ?>

<?php if(!empty($col->settings->border_type)) : // Border ?>
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

<?php if($global_settings->responsive_enabled) : // Responsive Sizes ?>

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