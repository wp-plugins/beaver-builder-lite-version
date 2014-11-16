<?php

/**
 * A class for working with auto suggest requests.
 *
 * @class FLBuilderAutoSuggest
 */
final class FLBuilderAutoSuggest {

    /**
     * @method init
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
     * @method init
     */	 
	static public function get_value($action = '', $value = '', $data = '')
	{
    	switch($action) {
    	
    	    case 'fl_as_posts':
    	    $data = self::posts_value($value, $data);
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
     * @method posts
     */	 
	static public function posts()
	{
	    global $wpdb;
	    
	    $data   = array();	    
	    $like   = esc_sql($wpdb->esc_like(urldecode($_REQUEST['fl_as_query'])));
	    $type   = esc_sql($_REQUEST['fl_as_action_data']);
	    
	    $posts  = $wpdb->get_results("
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
     * @method posts_value
     */	 
	static public function posts_value($ids, $type)
	{
	    global $wpdb;
	    
	    $data = array();
	    
	    if(!empty($ids)) {
	    
    	    $posts = $wpdb->get_results("SELECT ID, post_title FROM {$wpdb->posts} WHERE ID IN ({$ids})");
    	    
    	    foreach($posts as $post) {
                $data[] = array('name' => $post->post_title, 'value' => $post->ID);
            }
        }
        
        return $data;
	}
	
    /**
     * @method terms
     */	 
	static public function terms()
	{
	    $data = array();	    
	    $cats = get_categories(array(
	        'hide_empty' => 0, 
	        'taxonomy'   => $_REQUEST['fl_as_action_data']
	    ));
        
        foreach($cats as $cat) {
            $data[] = array('name' => $cat->name, 'value' => $cat->term_id);
        }
        
        return $data;
	}
	
    /**
     * @method terms_value
     */	 
	static public function terms_value($ids, $taxonomy)
	{
	    $data = array();
	    
	    if(!empty($ids)) {

            $cats = get_categories(array(
                'hide_empty' => 0, 
                'taxonomy'   => $taxonomy,
                'include'    => $ids
            ));
        
            foreach($cats as $cat) {
                $data[] = array('name' => $cat->name, 'value' => $cat->term_id);
            }
	    }
	    
        return $data;
	}
	
    /**
     * @method users
     */	 
	static public function users()
	{
	    global $wpdb;
	    
	    $data  = array();
	    $like  = esc_sql($wpdb->esc_like(urldecode($_REQUEST['fl_as_query'])));
	    $users = $wpdb->get_results("SELECT * FROM {$wpdb->users} WHERE user_login LIKE '%{$like}%'");
	    
	    foreach($users as $user) {
            $data[] = array('name' => $user->user_login, 'value' => $user->ID);
        }
	    
	    return $data;
	}
	
    /**
     * @method users_value
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
     * @method links
     */	 
	static public function links()
	{
	    global $wpdb;
	    
	    $data   = array();	    
	    $like   = esc_sql($wpdb->esc_like(urldecode($_REQUEST['fl_as_query'])));
	    $types  = FLBuilderLoop::post_types();
	    $slugs  = array();
	    
	    foreach($types as $slug => $type) {
    	    $slugs[] = esc_sql($slug);
	    }
	    
	    $posts  = $wpdb->get_results("
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