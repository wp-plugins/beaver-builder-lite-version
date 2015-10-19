<?php

/**
 * Helper class for builder updates. 
 *
 * @since 1.2.8
 */
final class FLBuilderUpdate {

	/** 
	 * Checks to see if an update should be run. If it should,
	 * the appropriate update method is run and the version
	 * number is updated in the database.
	 *
	 * @since 1.2.8
	 * @return void
	 */
	static public function init()
	{
		// Make sure the user is logged in.
		if ( ! is_user_logged_in() ) {
			return;
		}
		
		// Don't update for dev copies.
		if ( FL_BUILDER_VERSION == '{FL_BUILDER_VERSION}' ) {
			return;
		}
		
		// Get the saved version. 
		$saved_version = get_site_option( '_fl_builder_version' );
		
		// No saved version number. This must be a fresh install.
		if ( ! $saved_version ) {
			update_site_option( '_fl_builder_version', FL_BUILDER_VERSION );
			return;
		}
		// Only run updates if the version numbers don't match.
		else if ( ! version_compare( $saved_version, FL_BUILDER_VERSION, '=' ) ) {
		
			if ( is_multisite() ) {
				self::run_multisite( $saved_version );
			}
			else {
			   self::run( $saved_version ); 
			}
			
			update_site_option( '_fl_builder_version', FL_BUILDER_VERSION );
		}
	}

	/** 
	 * Runs the update for a specific version.
	 *
	 * @since 1.2.8
	 * @access private
	 * @return void
	 */
	static private function run( $saved_version )
	{
		// Update to 1.2.8 or greater.
		if ( version_compare( $saved_version, '1.2.8', '<' ) ) {
			self::v_1_2_8();
		}
		
		// Update to 1.4.6 or greater.
		if ( version_compare( $saved_version, '1.4.6', '<' ) ) {
			self::v_1_4_6();
		}
		
		// Update to 1.6.3 or greater.
		if ( version_compare( $saved_version, '1.6.3', '<' ) ) {
			self::v_1_6_3();
		}
		
		// Clear all asset cache.
		FLBuilderModel::delete_asset_cache_for_all_posts();
	}

	/** 
	 * Runs the update for all sites on a network install.
	 *
	 * @since 1.2.8
	 * @access private
	 * @return void
	 */
	static private function run_multisite( $saved_version ) 
	{
		global $blog_id;
		global $wpdb;
		
		// Save the original blog id.
		$original_blog_id = $blog_id;
		
		// Get all blog ids.
		$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
		
		// Loop through the blog ids and run the update.
		foreach ( $blog_ids as $id ) {
			switch_to_blog( $id );
			self::run( $saved_version );
		}
		
		// Revert to the original blog.
		switch_to_blog( $original_blog_id );
	}

	/** 
	 * Check for the fl_builder_nodes table that existed before 1.2.8.
	 *
	 * @since 1.2.8
	 * @access private
	 * @return bool
	 */
	static private function pre_1_2_8_table_exists()
	{
		global $wpdb;
		
		$table   = $wpdb->prefix . 'fl_builder_nodes';
		$results = $wpdb->get_results("SHOW TABLES LIKE '{$table}'");
		
		return count($results) > 0;
	}

	/** 
	 * Check to see if the fl_builder_nodes table that existed before 1.2.8
	 * is empty or not.
	 *
	 * @since 1.2.8
	 * @access private
	 * @return bool
	 */
	static private function pre_1_2_8_table_is_empty()
	{
		global $wpdb;
		
		if(self::pre_1_2_8_table_exists()) {
				
			$table = $wpdb->prefix . 'fl_builder_nodes';
			$nodes = $wpdb->get_results("SELECT * FROM {$table}");
			
			return count($nodes) === 0;
		}
		
		return true;
	}

	/** 
	 * Saves a backup of the pre 1.2.8 database table.
	 *
	 * @since 1.2.8
	 * @access private
	 * @return void
	 */
	static private function pre_1_2_8_backup()
	{
		global $wpdb;
		
		if(self::pre_1_2_8_table_exists()) {
		
			$cache_dir = FLBuilderModel::get_cache_dir();
			$table     = $wpdb->prefix . 'fl_builder_nodes';

			// Get the data to backup.            
			$nodes = $wpdb->get_results("SELECT * FROM {$table}");
			$meta  = $wpdb->get_results("SELECT * FROM {$wpdb->postmeta} WHERE meta_key = '_fl_builder_layout'");
		
			// Build the export object.
			$data           = new StdClass();
			$data->version  = FL_BUILDER_VERSION;
			$data->nodes    = $nodes;
			$data->meta     = $meta;
			
			// Save the backup.
			file_put_contents($cache_dir['path'] . 'backup.dat', serialize($data));
		}
	}

	/** 
	 * Restores a site to pre 1.2.8.
	 *
	 * @since 1.2.8
	 * @access private
	 * @return void
	 */
	static private function pre_1_2_8_restore()
	{
		global $wpdb;
		
		if(!self::pre_1_2_8_table_exists() || self::pre_1_2_8_table_is_empty()) {
		
			$cache_dir   = FLBuilderModel::get_cache_dir();
			$backup_path = $cache_dir['path'] . 'backup.dat';
			
			// Install the database.
			FLBuilderModel::install_database();
			
			// Check for the backup file. 
			if(file_exists($backup_path)) {
			
				// Get the backup data.
				$backup = unserialize(file_get_contents($backup_path));
				
				// Check for the correct backup data.
				if(!isset($backup->nodes) || !isset($backup->meta)) {
					return;
				}
				
				// Restore the nodes.
				foreach($backup->nodes as $node) {
					
					$wpdb->insert("{$wpdb->prefix}fl_builder_nodes", 
						array(
							'node'     => $node->node,
							'type'     => $node->type,
							'layout'   => $node->layout,
							'parent'   => $node->parent,
							'position' => $node->position,
							'settings' => $node->settings,
							'status'   => $node->status
						), 
						array('%s', '%s', '%s', '%s', '%d', '%s', '%s')
					);
				}
				
				// Restore the meta.
				foreach($backup->meta as $meta) {
					update_post_meta($meta->post_id, '_fl_builder_layout', $meta->meta_value);
				}
			}
		}
	}

	/** 
	 * Update to version 1.2.8 or later.
	 *
	 * @since 1.2.8
	 * @access private
	 * @return void
	 */
	static private function v_1_2_8()
	{
		global $wpdb;
		
		if(self::pre_1_2_8_table_exists()) {
		
			$table     = $wpdb->prefix . 'fl_builder_nodes';
			$metas     = $wpdb->get_results("SELECT * FROM {$wpdb->postmeta} WHERE meta_key = '_fl_builder_layout'");
			$cache_dir = FLBuilderModel::get_cache_dir();
			
			// Loop through the layout ids for each post.
			foreach($metas as $meta) {
			
				// Get the old layout nodes from the database.
				$published  = $wpdb->get_results("SELECT * FROM {$table} WHERE layout = '{$meta->meta_value}' AND status = 'published'");
				$draft      = $wpdb->get_results("SELECT * FROM {$table} WHERE layout = '{$meta->meta_value}' AND status = 'draft'");
				
				// Convert the old nodes to new ones. 
				$published  = self::v_1_2_8_convert_nodes($published);
				$draft      = self::v_1_2_8_convert_nodes($draft);
				
				// Add the new layout post meta. 
				update_post_meta($meta->post_id, '_fl_builder_data', $published);
				update_post_meta($meta->post_id, '_fl_builder_draft', $draft);
			}
			
			// Backup the old builder table.
			self::pre_1_2_8_backup();
			
			// Drop the old builder table.
			if(file_exists($cache_dir['path'] . 'backup.dat')) {
				$wpdb->query("DROP TABLE {$wpdb->prefix}fl_builder_nodes");
			}
			
			// Delete old post meta.
			delete_post_meta_by_key('_fl_builder_layout');
			delete_post_meta_by_key('_fl_builder_layout_export');
			delete_post_meta_by_key('_fl_builder_css');
			delete_post_meta_by_key('_fl_builder_css-draft');
			delete_post_meta_by_key('_fl_builder_js');
			delete_post_meta_by_key('_fl_builder_js-draft');
			
			// Convert global settings.
			self::v_1_2_8_convert_global_settings();
			
			// Delete all asset cache.
			$css = glob( $cache_dir['path'] . '*.css' );
			$js  = glob( $cache_dir['path'] . '*.js' );
			
			if ( is_array( $css ) ) {
				array_map( 'unlink', $css );
			}
			if ( is_array( $js ) ) {
				array_map( 'unlink', $js );
			}
		}
	}

	/** 
	 * Convert the global settings for 1.2.8 or later.
	 *
	 * @since 1.2.8
	 * @access private
	 * @return void
	 */
	static private function v_1_2_8_convert_global_settings()
	{
		$settings = get_option('_fl_builder_settings');
		
		if($settings && is_string($settings)) {
			update_option('_fl_builder_settings', json_decode($settings));
		}
	}

	/** 
	 * Convert the nodes for 1.2.8 or earlier.
	 *
	 * @since 1.2.8
	 * @access private
	 * @param array $nodes An array of node data.
	 * @return array
	 */
	static private function v_1_2_8_convert_nodes($nodes)
	{
		$new_nodes = array();
		
		foreach($nodes as $node) {    
					
			unset($node->id);
			unset($node->layout);
			unset($node->status);
			
			if($node->type == 'row') {
				$node->parent = null;
			}
			
			$node->settings = self::v_1_2_8_json_decode_settings($node->settings);
			$new_nodes[$node->node] = $node;
		}
		
		return $new_nodes;
	}

	/** 
	 * Convert a JSON encoded settings string for 1.2.8 or earlier.
	 *
	 * @since 1.2.8
	 * @access private
	 * @param object $settings The settings object.
	 * @return object
	 */
	static private function v_1_2_8_json_decode_settings($settings)
	{
		if(!$settings || empty($settings)) {
			return null;    
		}
		
		$settings = json_decode($settings);
		
		foreach($settings as $key => $val) {
		
			if(is_string($val)) {
				
				$decoded = json_decode($val);
				
				if(is_object($decoded) || is_array($decoded)) {
					
					$settings->{$key} = $decoded;
				}
			} 
			else if(is_array($val)) {
			
				foreach($val as $sub_key => $sub_val) {
		
					if(is_string($sub_val)) {
				
						$decoded = json_decode($sub_val);
					
						if(is_object($decoded) || is_array($decoded)) {
						
							$settings->{$key}[$sub_key] = $decoded;
						}
					}
				}
			}
		}
		
		return $settings;
	}
	
	/** 
	 * Update to version 1.4.6 or later.
	 *
	 * @since 1.4.6
	 * @access private
	 * @return void
	 */
	static private function v_1_4_6()
	{
		// Remove the old fl-builder uploads folder.
		$filesystem  = FLBuilderUtils::get_filesystem();
		$upload_dir  = wp_upload_dir();
		$path        = trailingslashit( $upload_dir['basedir'] ) . 'fl-builder';
		
		if ( file_exists( $path ) ) {
			$filesystem->rmdir( $path, true );
		}
	}
	
	/** 
	 * Update to version 1.6.3 or later.
	 *
	 * @since 1.6.3
	 * @access private
	 * @return void
	 */
	static private function v_1_6_3()
	{
		$posts = get_posts( array(
			'post_type' 	 => 'fl-builder-template',
			'posts_per_page' => '-1'
		) );
		
		foreach ( $posts as $post ) {
			
			$type = wp_get_post_terms( $post->ID, 'fl-builder-template-type' );
			
			if ( 0 === count( $type ) ) {
				wp_set_post_terms( $post->ID, 'layout', 'fl-builder-template-type' );
			}
		}
	}
}