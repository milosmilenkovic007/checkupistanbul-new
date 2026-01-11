<?php
/**
 * ACF Flexible Content Layouts
 * Sve flexible module u sistemu
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Registruj sve ACF flexible content layouts
 */
function hello_child_register_flexible_layouts() {
    // Pronađi sve module i registruj ih
    $modules_dir = get_stylesheet_directory() . '/inc/modules';
    
    if ( is_dir( $modules_dir ) ) {
        $modules = array_diff( scandir( $modules_dir ), array( '.', '..' ) );
        
        foreach ( $modules as $module ) {
            $module_path = $modules_dir . '/' . $module . '/config.php';
            
            if ( file_exists( $module_path ) ) {
                require_once $module_path;
            }
        }
    }
}
add_action( 'acf/init', 'hello_child_register_flexible_layouts' );

/**
 * Render flexible content layout
 */
function hello_child_render_flexible_layout( $layout ) {
    if ( empty( $layout ) || ! is_array( $layout ) ) {
        return;
    }

    $layout_type = $layout['acf_fc_layout'] ?? '';
    $module_path = get_stylesheet_directory() . '/inc/modules/' . $layout_type . '/module.php';

    // Debug
    if ( current_user_can( 'manage_options' ) && ! empty( $_GET['debug_packages'] ) ) {
        echo '<!-- hello_child_render_flexible_layout: layout_type=' . esc_html( $layout_type ) . ' -->';
        echo '<!-- module_path=' . esc_html( $module_path ) . ' -->';
        echo '<!-- file_exists=' . ( file_exists( $module_path ) ? 'YES' : 'NO' ) . ' -->';
    }

    if ( file_exists( $module_path ) ) {
        // Transform field names
        $data = array();
        
        // Special handling for packages-details layout with its unique field key naming
        if ( $layout_type === 'packages-details' ) {
            // Debug: Log all available keys in layout
            if ( current_user_can( 'manage_options' ) ) {
                error_log( 'Packages Details Layout Keys: ' . print_r( array_keys( $layout ), true ) );
            }
            
            // Map ACF field keys to field names
            $data['heading']     = $layout['field_details_heading'] ?? '';
            $data['description'] = $layout['field_details_description'] ?? '';
            $data['packages']    = $layout['field_details_packages'] ?? array();
            $data['additional_package'] = $layout['field_details_additional_package'] ?? array();
            
            // Style fields - use field_details_* keys like other fields
            $data['section_title_bg'] = $layout['field_details_section_title_bg'] ?? '#f2ecf2';
            $data['section_title_color'] = $layout['field_details_section_title_color'] ?? '#053b3f';
            $data['col0_bg_tab0'] = $layout['field_details_col0_bg_tab0'] ?? '#f2ecf2';
            $data['col0_color_tab0'] = $layout['field_details_col0_color_tab0'] ?? '#35062a';
            $data['col1_bg_tab0'] = $layout['field_details_col1_bg_tab0'] ?? '#053b3f';
            $data['col1_color_tab0'] = $layout['field_details_col1_color_tab0'] ?? '#ffffff';
            $data['col2_bg_tab0'] = $layout['field_details_col2_bg_tab0'] ?? '#fed36f';
            $data['col2_color_tab0'] = $layout['field_details_col2_color_tab0'] ?? '#35062a';
            $data['col1_bg_tab1'] = $layout['field_details_col1_bg_tab1'] ?? '#053b3f';
            $data['col1_color_tab1'] = $layout['field_details_col1_color_tab1'] ?? '#ffffff';
            $data['col2_bg_tab1'] = $layout['field_details_col2_bg_tab1'] ?? '#fed36f';
            $data['col2_color_tab1'] = $layout['field_details_col2_color_tab1'] ?? '#35062a';
            $data['col1_bg_tab2'] = $layout['field_details_col1_bg_tab2'] ?? '#053b3f';
            $data['col1_color_tab2'] = $layout['field_details_col1_color_tab2'] ?? '#ffffff';
            $data['col2_bg_tab2'] = $layout['field_details_col2_bg_tab2'] ?? '#fed36f';
            $data['col2_color_tab2'] = $layout['field_details_col2_color_tab2'] ?? '#35062a';
            
            // Debug: Log style values
            if ( current_user_can( 'manage_options' ) ) {
                error_log( 'Section Title BG: ' . $data['section_title_bg'] );
                error_log( 'Col0 Tab0 BG: ' . $data['col0_bg_tab0'] );
            }
            
            $data['acf_fc_layout'] = $layout['acf_fc_layout'];
        } elseif ( $layout_type === 'cta-section' ) {
            // Map ACF field keys (and nested repeater field keys) to field names
            $data['heading'] = $layout['field_cta_heading'] ?? ( $layout['heading'] ?? '' );

            $raw_features = $layout['field_cta_features'] ?? ( $layout['features'] ?? array() );
            $features = array();
            if ( is_array( $raw_features ) ) {
                foreach ( $raw_features as $feature ) {
                    if ( ! is_array( $feature ) ) {
                        continue;
                    }
                    $features[] = array(
                        'icon'        => $feature['field_cta_feature_icon'] ?? ( $feature['icon'] ?? null ),
                        'title'       => $feature['field_cta_feature_title'] ?? ( $feature['title'] ?? '' ),
                        'description' => $feature['field_cta_feature_text'] ?? ( $feature['description'] ?? '' ),
                    );
                }
            }

            $data['features'] = $features;
            $data['image'] = $layout['field_cta_image'] ?? ( $layout['image'] ?? null );
            $data['button_text'] = $layout['field_cta_button_text'] ?? ( $layout['button_text'] ?? '' );
            $data['button_link'] = $layout['field_cta_button_link'] ?? ( $layout['button_link'] ?? null );
            $data['acf_fc_layout'] = $layout['acf_fc_layout'];
        } elseif ( $layout_type === 'cta-package' ) {
            // Map ACF field keys to field names for cta-package
            $data['heading']              = $layout['field_cta_pkg_heading'] ?? '';
            $data['subheading']           = $layout['field_cta_pkg_subheading'] ?? '';
            $data['content']              = $layout['field_cta_pkg_content'] ?? '';
            $data['button_text']          = $layout['field_cta_pkg_button_text'] ?? '';
            $data['button_link']          = $layout['field_cta_pkg_button_link'] ?? '';
            $data['image']                = $layout['field_cta_pkg_image'] ?? array();
            $data['bg_block_color']       = $layout['field_cta_pkg_bg_block_color'] ?? '#f2ecf2';
            $data['bg_inner_color']       = $layout['field_cta_pkg_bg_inner_color'] ?? '#FFFFFF';
            $data['heading_color']        = $layout['field_cta_pkg_heading_color'] ?? '#9a1078';
            $data['subheading_color']     = $layout['field_cta_pkg_subheading_color'] ?? '#053b3f';
            $data['button_text_color']    = $layout['field_cta_pkg_button_text_color'] ?? '#FFFFFF';
            $data['button_bg_color']      = $layout['field_cta_pkg_button_bg_color'] ?? '#9a1078';
            $data['acf_fc_layout'] = $layout['acf_fc_layout'];
        } elseif ( $layout_type === 'testimonials' ) {
            // Map ACF field keys to field names for testimonials
            $data['title'] = $layout['field_testimonials_title'] ?? ( $layout['title'] ?? '' );
            $data['rating_text'] = $layout['field_testimonials_rating_text'] ?? ( $layout['rating_text'] ?? '' );
            $data['shortcode'] = $layout['field_testimonials_shortcode'] ?? ( $layout['shortcode'] ?? '' );
            $data['acf_fc_layout'] = $layout['acf_fc_layout'];
        } else {
            // Default: keep all data as-is
            $data = $layout;
        }
        
        include $module_path;
    }
}

/**
 * Get module config
 */
function hello_child_get_module_config( $module_name ) {
    $config_path = get_stylesheet_directory() . '/inc/modules/' . $module_name . '/config.php';
    
    if ( file_exists( $config_path ) ) {
        return require $config_path;
    }
    
    return [];
}
