<div class="fl-builder-bar">
	
	<?php if(get_post_type() == 'fl-builder-template') : ?>
	
	<div class="fl-builder-bar-content">
		<?php if(FLBuilderModel::get_branding_icon() == '') : ?>
		<span class="fl-builder-bar-title fl-builder-bar-title-no-icon">
			<?php echo sprintf(__('Template: %s', 'fl-builder'), get_the_title($post_id)); ?>
		</span>
		<?php else : ?>
		<span class="fl-builder-bar-title">
			<img src="<?php echo FLBuilderModel::get_branding_icon(); ?>" />
			<span><?php echo sprintf(__('Template: %s', 'fl-builder'), get_the_title($post_id)); ?></span>
		</span>
		<?php endif; ?>
		<div class="fl-builder-bar-actions">
			<?php if ( $help_button['enabled'] ) : ?>
			<span class="fl-builder-help-button fl-builder-button"><i class="fa fa-question-circle"></i></span>
			<?php endif ?>
			<span class="fl-builder-done-button fl-builder-button fl-builder-button-primary"><?php _e('Done', 'fl-builder'); ?></span>
			<span class="fl-builder-tools-button fl-builder-button"><?php _e('Tools', 'fl-builder'); ?></span>
			<span class="fl-builder-add-content-button fl-builder-button"><?php _e('Add Content', 'fl-builder'); ?></span>
			<div class="fl-clear"></div>
		</div>
		<div class="fl-clear"></div>
	</div>
	
	<?php else : ?>
	
	<div class="fl-builder-bar-content">
		<?php if(stristr(home_url(), 'demo.wpbeaverbuilder.com')) : ?>
		<span class="fl-builder-bar-title">
			<img src="<?php echo FL_BUILDER_URL; ?>/img/beaver.png" />
			<span><?php _e('Page Builder Demo', 'fl-builder'); ?></span>
		</span>
		<?php elseif(FLBuilderModel::get_branding_icon() == '') : ?>
		<span class="fl-builder-bar-title fl-builder-bar-title-no-icon">
			<?php echo FLBuilderModel::get_branding(); ?>
		</span>
		<?php else : ?>
		<span class="fl-builder-bar-title">
			<img src="<?php echo FLBuilderModel::get_branding_icon(); ?>" />
			<span><?php echo FLBuilderModel::get_branding(); ?></span>
		</span>
		<?php endif; ?>
		<div class="fl-builder-bar-actions">
			<?php if ( $help_button['enabled'] ) : ?>
			<span class="fl-builder-help-button fl-builder-button"><i class="fa fa-question-circle"></i></span>
			<?php endif ?>
			<?php if(stristr(home_url(), 'demo.wpbeaverbuilder.com')) : ?>
			<span class="fl-builder-upgrade-button fl-builder-button"><?php _e('Buy Now!', 'fl-builder'); ?></span>
			<?php elseif(FL_BUILDER_LITE === true) : ?>
			<span class="fl-builder-upgrade-button fl-builder-button"><?php _e('Upgrade!', 'fl-builder'); ?></span>
			<?php endif; ?>
			<span class="fl-builder-done-button fl-builder-button fl-builder-button-primary"><?php _e('Done', 'fl-builder'); ?></span>
			<span class="fl-builder-tools-button fl-builder-button"><?php _e('Tools', 'fl-builder'); ?></span>
			<?php if(FL_BUILDER_LITE !== true && $enabled_templates != 'disabled') : ?>
			<span class="fl-builder-templates-button fl-builder-button"><?php _e('Templates', 'fl-builder'); ?></span>
			<?php endif; ?>
			<span class="fl-builder-add-content-button fl-builder-button"><?php _e('Add Content', 'fl-builder'); ?></span>
			<div class="fl-clear"></div>
		</div>
		<div class="fl-clear"></div>
	</div>
	
	<?php endif; ?>
	
</div>
<div class="fl-builder-panel">
	<div class="fl-builder-panel-actions">
		<i class="fl-builder-panel-close fa fa-times"></i>
	</div>
	<div class="fl-builder-panel-content-wrap fl-nanoscroller">
		<div class="fl-builder-panel-content fl-nanoscroller-content">
			<div class="fl-builder-blocks">
				<div id="fl-builder-blocks-rows" class="fl-builder-blocks-section">
					<span class="fl-builder-blocks-section-title">
						<?php _e('Row Layouts', 'fl-builder'); ?>
						<i class="fa fa-chevron-down"></i>
					</span>
					<div class="fl-builder-blocks-section-content fl-builder-rows">
						<span class="fl-builder-block fl-builder-block-row" data-cols="1-col"><?php _e('1 Column', 'fl-builder'); ?></span>
						<span class="fl-builder-block fl-builder-block-row" data-cols="2-cols"><?php _e('2 Columns', 'fl-builder'); ?></span>
						<span class="fl-builder-block fl-builder-block-row" data-cols="3-cols"><?php _e('3 Columns', 'fl-builder'); ?></span>
						<span class="fl-builder-block fl-builder-block-row" data-cols="4-cols"><?php _e('4 Columns', 'fl-builder'); ?></span>
						<span class="fl-builder-block fl-builder-block-row" data-cols="5-cols"><?php _e('5 Columns', 'fl-builder'); ?></span>
						<span class="fl-builder-block fl-builder-block-row" data-cols="6-cols"><?php _e('6 Columns', 'fl-builder'); ?></span>
						<span class="fl-builder-block fl-builder-block-row" data-cols="left-sidebar"><?php _e('Left Sidebar', 'fl-builder'); ?></span>   
						<span class="fl-builder-block fl-builder-block-row" data-cols="right-sidebar"><?php _e('Right Sidebar', 'fl-builder'); ?></span>
						<span class="fl-builder-block fl-builder-block-row" data-cols="left-right-sidebar"><?php _e('Left &amp; Right Sidebar', 'fl-builder'); ?></span>
					</div>
				</div>
				<?php foreach($categories as $title => $modules) : ?>
				<div id="fl-builder-blocks-<?php echo FLBuilderModel::get_module_category_slug( $title ); ?>" class="fl-builder-blocks-section">
					<span class="fl-builder-blocks-section-title">
						<?php echo $title; ?>
						<i class="fa fa-chevron-down"></i>
					</span>
					<?php if($title == __('WordPress Widgets', 'fl-builder')) : ?>
					<div class="fl-builder-blocks-section-content fl-builder-widgets">
						<?php foreach($modules as $module) : ?>
						<span class="fl-builder-block fl-builder-block-module" data-type="widget" data-widget="<?php echo $module->class; ?>"><?php echo $module->name; ?></span>
						<?php endforeach; ?>
					</div>
					<?php else : ?>
					<div class="fl-builder-blocks-section-content fl-builder-modules">
						<?php foreach($modules as $module) : ?>
						<span class="fl-builder-block fl-builder-block-module" data-type="<?php echo $module->slug; ?>"><?php echo $module->name; ?></span>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>
<div class="fl-builder-loading"></div>
<div class="fl-builder-hidden-editor">
	<?php wp_editor(' ', 'flhiddeneditor', array('wpautop' => true)); ?>
</div>
<input type="hidden" id="fl-post-id" value="<?php echo $post_id; ?>" />
<input type="hidden" id="fl-admin-url" value="<?php echo admin_url(); ?>" />