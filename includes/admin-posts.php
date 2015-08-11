<div class="fl-builder-admin">
	<div class="fl-builder-admin-tabs">
		<a href="javascript:void(0);" onclick="return false;" class="fl-enable-editor<?php if(!$enabled) echo ' fl-active'; ?>"><?php _e('Text Editor', 'fl-builder'); ?></a>
		<a href="javascript:void(0);" onclick="return false;" class="fl-enable-builder<?php if($enabled) echo ' fl-active'; ?>"><?php echo FLBuilderModel::get_branding(); ?></a>
	</div>
	<div class="fl-builder-admin-ui">
		<h3><?php printf( _x( '%s is currently active for this %s.', 'The first %s stands for custom branded "Page Builder" name. The second %s stands for the post type name.', 'fl-builder' ), FLBuilderModel::get_branding(), $post_type_name ); ?></h3>
		<a href="<?php echo FLBuilderModel::get_edit_url(); ?>" class="fl-launch-builder button button-primary button-large"><?php printf( _x( 'Launch %s', '%s stands for custom branded "Page Builder" name.', 'fl-builder' ), FLBuilderModel::get_branding() ); ?></a>
	</div>
	<div class="fl-builder-loading"></div>
</div>
<script type="text/javascript">

FLBuilderAdminPostsStrings = {
	switchToEditor: '<?php printf( esc_attr_x( 'Switching to Text Editor mode will disable your %s layout until it is enabled again. Any edits made while in Text Editor mode will not be made on your %s layout. Do you want to continue?', '%s stands for custom branded "Page Builder" name.', 'fl-builder' ), FLBuilderModel::get_branding(), FLBuilderModel::get_branding() ); ?>'
};

</script>