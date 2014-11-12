<?php

/**
 * @class FLBuilderUpdate
 */
final class FLBuilderUpdate {

    /** 
     * @method init
     */
    static public function init()
    {
        // Make sure the user is logged in.
        if(!is_user_logged_in()) {
            return;
        }
        
        // Get the current version. 
        $version = get_site_option('_fl_builder_version');
        
        // No version number. This must be a fresh install.
        if(!$version) {
            update_site_option('_fl_builder_version', FL_BUILDER_VERSION);
            return;
        }
        
        // Don't update for dev copies.
        else if(FL_BUILDER_VERSION == '{FL_BUILDER_VERSION}') {
            return;
        }
        
        // Only run updates if the version numbers don't match.
        else if(!version_compare($version, FL_BUILDER_VERSION, '=')) {
        
            if(is_multisite()) {
                self::run_multisite();
            }
            else {
               self::run(); 
            }
        }
    }

    /** 
     * @method run
     * @private
     */
    static private function run()
    {
        // Get the current version. 
        $version = get_site_option('_fl_builder_version');
        
        // Update to 1.2.8 or greater.
        if(version_compare($version, '1.2.8', '<')) {
            self::v_1_2_8();
        }
        
        // Clear all asset cache.
        FLBuilderModel::delete_all_asset_cache();
        
        // Update the version number.
        if(!is_multisite()) {
            update_site_option('_fl_builder_version', FL_BUILDER_VERSION);
        }
    }

    /** 
     * @method run_multisite
     * @private
     */
    static private function run_multisite() 
    {
        global $blog_id;
        global $wpdb;
        
        // Save the original blog id.
        $original_blog_id = $blog_id;
        
        // Get all blog ids.
        $blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
        
        // Loop through the blog ids and run the update.
        foreach($blog_ids as $id) {
            switch_to_blog($id);
            self::run();
        }
        
        // Revert to the original blog.
        switch_to_blog($original_blog_id);
        
        // Update the version number.
        update_site_option('_fl_builder_version', FL_BUILDER_VERSION);
    }

    /** 
     * Check for the fl_builder_nodes table that existed before 1.2.8.
     *
     * @method pre_1_2_8_table_exists
     * @private
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
     * @method pre_1_2_8_table_is_empty
     * @private
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
     * @method pre_1_2_8_backup
     * @private
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
     * @method pre_1_2_8_restore
     * @private
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
     * @method v_1_2_8
     * @private
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
            array_map('unlink', glob($cache_dir['path'] . '*.css'));
    	    array_map('unlink', glob($cache_dir['path'] . '*.js'));
        }
    }

    /** 
     * @method v_1_2_8_convert_global_settings
     * @private
     */
    static private function v_1_2_8_convert_global_settings()
    {
        $settings = get_option('_fl_builder_settings');
        
        if($settings && is_string($settings)) {
            update_option('_fl_builder_settings', json_decode($settings));
        }
    }

    /** 
     * @method v_1_2_8_convert_nodes
     * @private
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
     * @method v_1_2_8_json_decode_settings
     * @private
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
}