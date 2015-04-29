<div id="fl-builder-settings-section-general" class="fl-builder-settings-section">           
	<table class="fl-form-table">
		<tr id="fl-field-sidebar" class="fl-field" data-type="select" data-preview='{"type":"refresh"}'>
			<th>
				<label for="sidebar"><?php _e('Sidebar', 'fl-builder'); ?></label>
			</th>
			<td>
				<select name="sidebar">
					<?php foreach (FLBuilderModel::get_wp_sidebars() as $sidebar) : ?>
					<option value="<?php echo $sidebar['id']; ?>"<?php if(isset($settings->sidebar) && $settings->sidebar ==  $sidebar['id']) echo ' selected="selected"'; ?>><?php echo $sidebar['name']; ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
	</table>
</div>