<?php

/**
 * Helper class for building custom WordPress loops.
 *
 * @since 1.2.3
 */
final class FLBuilderLoop {

	/**
	 * Returns a new instance of WP_Query based on 
	 * the provided module settings. 
	 *
	 * @since 1.2.3
	 * @param object $settings Module settings to use for the query.
	 * @return object A WP_Query instance.
	 */
	static public function query($settings) 
	{
		$posts_per_page	 = empty($settings->posts_per_page) ? 10 : $settings->posts_per_page;
		$post_type		 = empty($settings->post_type) ? 'post' : $settings->post_type;
		$order_by		 = empty($settings->order_by) ? 'date' : $settings->order_by;
		$order			 = empty($settings->order) ? 'DESC' : $settings->order;
		$users			 = empty($settings->users) ? '' : $settings->users;
		$paged			 = is_front_page() ? get_query_var('page') : get_query_var('paged');
		$fields			 = empty($settings->fields) ? '' : $settings->fields; 
		
		// Get the offset.
		if ( ! isset( $settings->offset ) || ! is_int( ( int )$settings->offset ) ) {
			$offset = 0;
		}
		else {
			$offset = $settings->offset;
		}
		
		// Get the paged offset. 
		if ( $paged < 2 ) {
			$paged_offset = $offset;
		}
		else {
			$paged_offset = $offset + ( ( $paged - 1 ) * $posts_per_page );
		}
		
		// Build the query args.
		$args = array(
			'paged'					=> $paged,
			'posts_per_page'		=> $posts_per_page,
			'post_type'				=> $post_type,
			'orderby'				=> $order_by,
			'order'					=> $order,
			'author'				=> $users,
			'tax_query'				=> array('relation' => 'AND'),
			'ignore_sticky_posts'	=> true,
			'offset'				=> $paged_offset,
			'fl_original_offset'	=> $offset,
			'fl_builder_loop'		=> true,
			'fields'				=> $fields
		);
		
		// Build the taxonomy query.
		$taxonomies = self::taxonomies($post_type);
		
		foreach($taxonomies as $tax_slug => $tax) {
			
			$tax_value = '';
		
			// New settings slug.
			if(isset($settings->{'tax_' . $post_type . '_' . $tax_slug})) {
				$tax_value = $settings->{'tax_' . $post_type . '_' . $tax_slug};
			}
			// Legacy settings slug.
			else if(isset($settings->{'tax_' . $tax_slug})) {
				$tax_value = $settings->{'tax_' . $tax_slug};
			}
				
			if(!empty($tax_value)) {
			 
				$args['tax_query'][] = array(
					'taxonomy'	=> $tax_slug,
					'field'		=> 'id',
					'terms'		=> explode(',', $tax_value)
				);
			}
		}
		
		// Post in query.
		if(isset($settings->{'posts_' . $post_type})) {
		
			$ids = $settings->{'posts_' . $post_type};
			
			if(!empty($ids)) {
				$args['post__in'] = explode(',', $settings->{'posts_' . $post_type});  
			}
		}
		
		// Build the query.
		$query = new WP_Query($args);
		
		// Return the query.
		return $query;
	}

	/**
	 * Called by the found_posts filter to adjust the number of posts
	 * found based on the user defined offset.
	 *
	 * @since 1.2.3
	 * @param int $found_posts The number of found posts.
	 * @param object $query An instance of WP_Query.
	 * @return int
	 */ 
	static public function found_posts( $found_posts, $query ) 
	{
		if ( isset( $query->query ) && isset( $query->query['fl_builder_loop'] ) ) {
			return $found_posts - $query->query['fl_original_offset'];
		}
		
		return $found_posts;
	}
	
	/**
	 * Builds and renders the pagination for a query.
	 *
	 * @since 1.2.3
	 * @param object $query An instance of WP_Query.
	 * @return void
	 */ 
	static public function pagination($query) 
	{
		$total_pages = $query->max_num_pages;
		$permalink_structure = get_option('permalink_structure');
		$paged = is_front_page() ? get_query_var('page') : get_query_var('paged');
		
		if($total_pages > 1) {
		
			if(!$current_page = $paged) {
				$current_page = 1;
			}
		
			if(empty($permalink_structure)) {
				$format = '&paged=%#%';
			} 
			else {
				$format = 'page/%#%/';
			}
			
			echo paginate_links(array(
				'base'	   => get_pagenum_link(1) . '%_%',
				'format'   => $format,
				'current'  => $current_page,
				'total'	   => $total_pages,
				'type'	   => 'list'
			));
		}
	}

	/**
	 * Returns an array of data for post types supported
	 * by module loop settings.
	 *
	 * @since 1.2.3
	 * @return array
	 */  
	static public function post_types() 
	{
		$post_types = get_post_types(array(
			'public'	=> true,
			'show_ui'	=> true
		), 'objects');
		
		unset($post_types['attachment']);
		unset($post_types['fl-builder-template']);
		
		return $post_types;
	}
	
	/**
	 * Get an array of supported taxonomy data for a post type.
	 *
	 * @since 1.2.3
	 * @param string $post_type The post type to get taxonomies for.
	 * @return array
	 */   
	static public function taxonomies($post_type) 
	{
		$taxonomies = get_object_taxonomies($post_type, 'objects');
		$data		= array();
		
		foreach($taxonomies as $tax_slug => $tax) {
		
			if(!$tax->public || !$tax->show_ui) {
				continue;
			}
			
			$data[$tax_slug] = $tax;
		}
		
		return $data;
	}
}