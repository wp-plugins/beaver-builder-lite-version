<div class="fl-builder-admin">
    <div class="fl-builder-admin-tabs">
        <a href="javascript:void(0);" onclick="return false;" class="fl-enable-editor<?php if(!$enabled) echo ' fl-active'; ?>"><?php _e('Text Editor', 'fl-builder'); ?></a>
        <a href="javascript:void(0);" onclick="return false;" class="fl-enable-builder<?php if($enabled) echo ' fl-active'; ?>"><?php echo FLBuilderModel::get_branding(); ?></a>
    </div>
    <div class="fl-builder-admin-ui">
        <h3><?php echo FLBuilderModel::get_branding() . ' ' . __('is currently active for this page.', 'fl-builder'); ?></h3>
        <a href="<?php echo FLBuilderModel::get_edit_url(); ?>" class="fl-launch-builder button button-primary button-large"><?php echo __('Launch', 'fl-builder'). ' ' . FLBuilderModel::get_branding(); ?></a>
    </div>
    <div class="fl-builder-loading"></div>
</div>