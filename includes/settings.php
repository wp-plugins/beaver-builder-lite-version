<form class="fl-builder-settings <?php echo $form['class']; ?>" <?php echo $form['attrs']; ?> onsubmit="return false;">
	<div class="fl-lightbox-header">
		<h1>
			<?php echo $form['title']; ?>
			<?php foreach ( $form['badges'] as $form_badge_slug => $form_badge_title ) : ?>
			<span class="fl-builder-badge fl-builder-badge-<?php echo $form_badge_slug; ?>"><?php echo $form_badge_title; ?></span>
			<?php endforeach; ?>
		</h1>
	</div>
	<?php if(count($form['tabs']) > 1) : ?>
	<div class="fl-builder-settings-tabs">
		<?php  $i = 0; foreach($form['tabs'] as $id => $tab) : ?>
		<a href="#fl-builder-settings-tab-<?php echo $id; ?>"<?php if($i == 0) echo ' class="fl-active"'; ?>><?php echo $tab['title']; ?></a>
		<?php $i++; endforeach; ?>
	</div>
	<?php endif; ?>
	<div class="fl-builder-settings-fields fl-nanoscroller">
		<div class="fl-nanoscroller-content">
			<?php $i = 0; foreach($form['tabs'] as $id => $tab) : // Tabs ?>
			<div id="fl-builder-settings-tab-<?php echo $id; ?>" class="fl-builder-settings-tab <?php if($i == 0) echo 'fl-active'; ?>">

				<?php if(isset($tab['file']) && file_exists($tab['file'])) : // Tab File ?>

					<?php include $tab['file']; ?>

				<?php else : ?>

					<?php if(!empty($tab['description'])) : // Tab Description ?>
					<p class="fl-builder-settings-tab-description"><?php echo $tab['description']; ?></p>
					<?php endif; ?>

					<?php foreach($tab['sections'] as $id => $section) : // Tab Sections ?>
					<div id="fl-builder-settings-section-<?php echo $id; ?>" class="fl-builder-settings-section">

						<?php if(isset($section['file']) && file_exists($section['file'])) : // Section File ?>

							<?php include $section['file']; ?>

						<?php else : ?>

							<?php if(!empty($section['title'])) : // Section Title ?>
							<h3 class="fl-builder-settings-title"><?php echo $section['title']; ?></h3>
							<?php endif; ?>

							<table class="fl-form-table">
								<?php

								foreach($section['fields'] as $name => $field) {  // Fields
									FLBuilder::render_settings_field($name, $field, $settings);
								}

								?>
							</table>

						<?php endif; ?>

					</div>
					<?php endforeach; ?>

				<?php endif; ?>

			</div>
			<?php $i++; endforeach; ?>
		</div>
	</div>
	<div class="fl-lightbox-footer">
		<span class="fl-builder-settings-save fl-builder-button fl-builder-button-large fl-builder-button-primary" href="javascript:void(0);" onclick="return false;"><?php _e('Save', 'fl-builder'); ?></span>
		<?php if ( in_array( 'save-as', $form['buttons'] ) ) : ?>
		<span class="fl-builder-settings-save-as fl-builder-button fl-builder-button-large" href="javascript:void(0);" onclick="return false;"><?php _e('Save As...', 'fl-builder'); ?></span>
		<?php endif; ?>
		<span class="fl-builder-settings-cancel fl-builder-button fl-builder-button-large" href="javascript:void(0);" onclick="return false;"><?php _e('Cancel', 'fl-builder'); ?></span>
	</div>
</form>
