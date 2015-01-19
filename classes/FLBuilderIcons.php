<?php

/**
 * Helper class for working with icons.
 *
 * @class FLBuilderIcons
 */

final class FLBuilderIcons {
    
    /**
     * @property $sets
     * @private
     */
    static private $sets = null;
    
    /**
     * @method get_sets
     */	
    static public function get_sets()
    {
        if ( self::$sets ) {
            return self::$sets;
        }
        
        self::$sets = array(
            'font-awesome'      => array(
                'name'              => 'Font Awesome',
                'prefix'            => 'fa'
            ),
            'foundation-icons'  => array(
                'name'              => 'Foundation Icons',
                'prefix'            => ''
            ),
            'dashicons'         => array(
                'name'              => 'WordPress Dashicons',
                'prefix'            => 'dashicons dashicons-before'
            )
        );
        
        foreach ( self::$sets as $set_key => $set_data ) {
            $icons = json_decode( file_get_contents( FL_BUILDER_DIR . 'json/' . $set_key . '.json' ) );
            self::$sets[ $set_key ]['icons'] = $icons;
        }
        
        return self::$sets;
    }

    /**
     * @method enqueue_styles_for_module
     */	
    static public function enqueue_styles_for_module( $module )
    {
        $fields = FLBuilderModel::get_settings_form_fields( $module->form );
        
        foreach ( $fields as $name => $field ) {
            if ( isset( $field['multiple'] ) && true === $field['multiple'] ) {
                $form = FLBuilderModel::$settings_forms[ $field['form'] ];
                self::enqueue_styles_for_module_multiple( $module, $form['tabs'], $name );
            }
            else if ( $field['type'] == 'icon' && isset( $module->settings->$name ) ) {
                self::enqueue_styles_for_icon( $module->settings->$name );
            }
        }
    }

    /**
     * @method enqueue_styles_for_module_multiple
     * @private
     */	
    static private function enqueue_styles_for_module_multiple( $module, $form, $setting )
    {
        $fields = FLBuilderModel::get_settings_form_fields( $form );
        
        foreach ( $fields as $name => $field ) {
            if ( $field['type'] == 'icon' ) {
                foreach ( $module->settings->$setting as $key => $val ) {
                    if ( isset( $val->$name ) ) {
                        self::enqueue_styles_for_icon( $val->$name );
                    }
                }
            }
        }
    }

    /**
     * @method enqueue_styles_for_icon
     * @private
     */	
    static private function enqueue_styles_for_icon( $icon )
    {
        if ( stristr( $icon, 'fa-' ) ) {
            wp_enqueue_style( 'font-awesome' );
        }
        else if ( stristr( $icon, 'fi-' ) ) {
            wp_enqueue_style( 'foundation-icons' );
        }
        else if ( stristr( $icon, 'dashicon' ) ) {
            wp_enqueue_style( 'dashicons' );
        }
    }
}