<?php

/**
 * Helper class for building custom loops.
 *
 * @class FLBuilderLoop
 */

final class FLBuilderLoop {

	/**
     * @method query
     */	 
    static public function query($settings) 
    {
        $posts_per_page  = empty($settings->posts_per_page)  ? 10        : $settings->posts_per_page;
        $post_type       = empty($settings->post_type)       ? 'post'    : $settings->post_type;
        $order_by        = empty($settings->order_by)        ? 'date'    : $settings->order_by;
        $order           = empty($settings->order)           ? 'DESC'    : $settings->order;
        $users           = empty($settings->users)           ? ''        : $settings->users;
        
        $args = array(
            'paged'             => is_front_page() ? get_query_var('page') : get_query_var('paged'),
            'posts_per_page'    => $posts_per_page,
        	'post_type'         => $post_type,
        	'orderby'           => $order_by,
        	'order'             => $order,
        	'author'            => $users,
        	'tax_query'         => array('relation' => 'AND')
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
                    'taxonomy'  => $tax_slug,
                    'field'     => 'id',
                    'terms'     => explode(',', $tax_value)
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
     * @method pagination
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
                'base'     => get_pagenum_link(1) . '%_%',
                'format'   => $format,
                'current'  => $current_page,
                'total'    => $total_pages,
                'type'     => 'list'
            ));
        }
    }

	/**
     * @method post_types
     */	 
    static public function post_types() 
    {
        $post_types = get_post_types(array(
            'public'    => true,
            'show_ui'   => true
        ), 'objects');
        
        unset($post_types['attachment']);
        unset($post_types['fl-builder-template']);
        
        return $post_types;
    }
    
	/**
     * @method taxonomies
     */	 
    static public function taxonomies($post_type) 
    {
        $taxonomies = get_object_taxonomies($post_type, 'objects');
        $data       = array();
        
        foreach($taxonomies as $tax_slug => $tax) {
        
            if(!$tax->public || !$tax->show_ui) {
                continue;
            }
            
            $data[$tax_slug] = $tax;
        }
        
        return $data;
    }
}