<?php

/**
 * A class for working with auto suggest AJAX requests.
 *
 * @since 1.2.3
 */
final class FLBuilderAutoSuggest {

	/**
	 * Checks for an auto suggest request. If one is found
	 * the data will be echoed as a JSON response.
	 *
	 * @since 1.2.3
	 * @return void
	 */	 
	static public function init()
	{
		if(isset($_REQUEST['fl_as_action']) && isset($_REQUEST['fl_as_query'])) {
		
			switch($_REQUEST['fl_as_action']) {
				
				case 'fl_as_posts':
				$data = self::posts();
				break; 
				
				case 'fl_as_terms':
				$data = self::terms();
				break; 
				
				case 'fl_as_users':
				$data = self::users();
				break; 
				
				case 'fl_as_links':
				$data = self::links();
				break; 
			}
		
			if(isset($data)) {
				echo json_encode($data);
				die();
			}
		}
	}
	
	/**
	 * Returns a JSON encoded value for a suggest field.
	 *
	 * @since 1.2.3
	 * @param string $action The type of auto suggest action.
	 * @param string $value The current value.
	 * @param string $data Additional auto suggest data.
	 * @return string The JSON encoded value.
	 */	 
	static public function get_value($action = '', $value = '', $data = '')
	{
		switch($action) {
		
			case 'fl_as_posts':
			$data = self::posts_value($value);
			break; 
			
			case 'fl_as_terms':
			$data = self::terms_value($value, $data);
			break; 
			
			case 'fl_as_users':
			$data = self::users_value($value);
			break; 
			
			default : 
			
			if(function_exists($action . '_value')) {
				$data = call_user_func_array($action . '_value', array($value, $data));
			}
			
			break;
		}
		
		return isset($data) ? json_encode($data) : '';
	}
	
	/**
	 * Returns the SQL escaped like value for auto suggest queries.
	 *
	 * @since 1.2.3
	 * @return string
	 */	 
	static public function get_like()
	{
		global $wpdb;
		
		$like = stripslashes( urldecode( $_REQUEST['fl_as_query'] ) );
		
		if ( method_exists( $wpdb, 'esc_like' ) ) {
			$like = esc_sql( $wpdb->esc_like( $like ) );
		}
		else {
			$like = like_escape( esc_sql( $like ) );
		}
		
		return $like;
	}
	
	/**
	 * Returns data for post auto suggest queries.
	 *
	 * @since 1.2.3
	 * @return array
	 */	 
	static public function posts()
	{
		global $wpdb;
		
		$data	= array();		
		$like	= self::get_like();
		$type	= esc_sql($_REQUEST['fl_as_action_data']);
		
		$posts	= $wpdb->get_results("
			SELECT ID, post_title FROM {$wpdb->posts} 
			WHERE post_title LIKE '%{$like}%'
			AND post_type = '{$type}'
			AND post_status = 'publish'
		");
		
		foreach($posts as $post) {
			$data[] = array('name' => $post->post_title, 'value' => $post->ID);
		}
		
		return $data;
	}
	
	/**
	 * Returns data for selected posts.
	 *
	 * @since 1.2.3
	 * @param string $ids The selected post ids.
	 * @return array An array of post data.
	 */	 
	static public function posts_value($ids)
	{
		global $wpdb;
		
		$data = array();
		
		if(!empty($ids)) {
		
			$posts = $wpdb->get_results("SELECT ID, post_title FROM {$wpdb->posts} WHERE ID IN ({$ids})");
			
			foreach($posts as $post) {
				$data[] = array('name' => esc_attr( $post->post_title ), 'value' => $post->ID);
			}
		}
		
		return $data;
	}
	
	/**
	 * Returns data for term auto suggest queries.
	 *
	 * @since 1.2.3
	 * @return array
	 */	 
	static public function terms()
	{
		$data = array();		
		$cats = get_categories(array(
			'hide_empty' => 0, 
			'taxonomy'	 => $_REQUEST['fl_as_action_data']
		));
		
		foreach($cats as $cat) {
			$data[] = array('name' => $cat->name, 'value' => $cat->term_id);
		}
		
		return $data;
	}
	
	/**
	 * Returns data for selected terms.
	 *
	 * @since 1.2.3
	 * @param string $ids The selected term ids.
	 * @param string $taxonomy The taxonomy to look in.
	 * @return array An array of term data.
	 */	
	static public function terms_value($ids, $taxonomy)
	{
		$data = array();
		
		if(!empty($ids)) {

			$cats = get_categories(array(
				'hide_empty' => 0, 
				'taxonomy'	 => $taxonomy,
				'include'	 => $ids
			));
		
			foreach($cats as $cat) {
				$data[] = array('name' => esc_attr( $cat->name ), 'value' => $cat->term_id);
			}
		}
		
		return $data;
	}
	
	/**
	 * Returns data for user auto suggest queries.
	 *
	 * @since 1.2.3
	 * @return array
	 */	 
	static public function users()
	{
		global $wpdb;
		
		$data  = array();
		$like  = self::get_like();
		$users = $wpdb->get_results("SELECT * FROM {$wpdb->users} WHERE user_login LIKE '%{$like}%'");
		
		foreach($users as $user) {
			$data[] = array('name' => $user->user_login, 'value' => $user->ID);
		}
		
		return $data;
	}
	
	/**
	 * Returns data for selected users.
	 *
	 * @since 1.2.3
	 * @param string $ids The selected user ids.
	 * @return array An array of user data.
	 */	 
	static public function users_value($ids)
	{
		global $wpdb;
		
		$data = array();
		
		if(!empty($ids)) {
		
			$users = $wpdb->get_results("SELECT * FROM {$wpdb->users} WHERE ID IN ({$ids})");
			
			foreach($users as $user) {
				$data[] = array('name' => $user->user_login, 'value' => $user->ID);
			}
		}
		
		return $data;
	}
	
	/**
	 * Returns data for link auto suggest queries.
	 *
	 * @since 1.3.9
	 * @return array
	 */	  
	static public function links()
	{
		global $wpdb;
		
		$data	= array();		
		$like	= self::get_like();
		$types	= FLBuilderLoop::post_types();
		$slugs	= array();

		foreach($types as $slug => $type) {
			$slugs[] = esc_sql($slug);
		}
		
		$posts	= $wpdb->get_results("
			SELECT ID, post_title FROM {$wpdb->posts} 
			WHERE post_title LIKE '%{$like}%'
			AND post_type IN ('" . implode("','", $slugs) . "')
			AND post_status = 'publish'
		");
		
		foreach($posts as $post) {
			$data[] = array('name' => $post->post_title, 'value' => get_permalink($post->ID));
		}
		
		return $data;
	}
}