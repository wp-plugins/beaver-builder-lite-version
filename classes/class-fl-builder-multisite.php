<?php

/**
 * Multisite helper for the page builder.
 *
 * @since 1.0
 */
final class FLBuilderMultisite {

	/**
	 * Initializes builder multisite support.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function init()
	{
		add_action('wpmu_new_blog', 'FLBuilderMultisite::install_for_new_blog', 10, 6);
		add_filter('wpmu_drop_tables', 'FLBuilderMultisite::uninstall_on_delete_blog');
	}

	/**
	 * Runs the install method for each site on the network.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function install() 
	{
		global $blog_id;
		global $wpdb;
		
		$original_blog_id   = $blog_id;
		$blog_ids           = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
		
		foreach($blog_ids as $id) {
			switch_to_blog($id);
			FLBuilderAdmin::install();
		}
		
		switch_to_blog($original_blog_id);
	}

	/**
	 * Runs the install for a newly created site.
	 *
	 * @since 1.0
	 * @param int $blog_id
	 * @param int $user_id
	 * @param string $domain
	 * @param string $path
	 * @param int $site_id
	 * @param array $meta
	 * @return void
	 */
	static public function install_for_new_blog($blog_id, $user_id, $domain, $path, $site_id, $meta) 
	{
		global $wpdb;
		
		if(is_plugin_active_for_network(FLBuilderModel::plugin_basename())) {
			switch_to_blog($blog_id);
			FLBuilderAdmin::install();
			restore_current_blog();
		}
	}

	/**
	 * Runs the uninstall method for each site on the network.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function uninstall() 
	{
		global $blog_id;
		global $wpdb;
		
		$original_blog_id   = $blog_id;
		$blog_ids           = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
		
		foreach($blog_ids as $id) {
			switch_to_blog($id);
			FLBuilderAdmin::uninstall();
		}
		
		switch_to_blog($original_blog_id);
	}

	/**
	 * Runs the uninstall method when a site is deleted.
	 *
	 * @since 1.0
	 * @return array
	 */
	static public function uninstall_on_delete_blog($tables) 
	{
		return $tables;
	}
	
	/** 
	 * Checks if a blog on the network exists.
	 *
	 * @since 1.5.7
	 * @param $blog_id The blog ID to check.
	 * @return bool
	 */
	static public function blog_exists( $blog_id )
	{
		global $wpdb;
		
		if ( method_exists( $wpdb, 'esc_like' ) ) {
			$like = esc_sql( $wpdb->esc_like( $blog_id ) );
		}
		else {
			$like = like_escape( esc_sql( $blog_id ) );
		}
		
		return $wpdb->get_row( "SELECT blog_id FROM $wpdb->blogs WHERE blog_id = '$like'" );
	}
}