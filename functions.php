<?php
/**
 * Hello Elementor Child Theme Functions
 *
 * @package Hello_Elementor_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Define constants
 */
define( 'HELLO_CHILD_VERSION', '1.0.0' );
define( 'HELLO_CHILD_DIR', get_stylesheet_directory() );
define( 'HELLO_CHILD_URI', get_stylesheet_directory_uri() );

/**
 * Enqueue parent and child theme styles
 */
function hello_elementor_child_enqueue_styles() {
    // Enqueue parent theme stylesheet
    wp_enqueue_style(
        'hello-elementor',
        get_template_directory_uri() . '/style.css',
        [],
        wp_get_theme()->parent()->get('Version')
    );

    // Enqueue main compiled CSS
    wp_enqueue_style(
        'hello-elementor-child-main',
        get_stylesheet_directory_uri() . '/dist/main.css',
        ['hello-elementor'],
        HELLO_CHILD_VERSION
    );

    // Enqueue child theme stylesheet (fallback)
    wp_enqueue_style(
        'hello-elementor-child',
        get_stylesheet_directory_uri() . '/style.css',
        ['hello-elementor-child-main'],
        HELLO_CHILD_VERSION
    );

    // Enqueue main compiled JS
    wp_enqueue_script(
        'hello-elementor-child-main',
        get_stylesheet_directory_uri() . '/dist/main.js',
        ['jquery'],
        HELLO_CHILD_VERSION,
        true
    );

    // Enqueue page-specific assets on the programs landing page if needed.
    if ( is_page( 'our-programs' ) || is_page( 'our-packages' ) ) {
        // CSS already included in main.css, but can add page-specific if needed
    }
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_enqueue_styles', 20 );

/**
 * Programs CPT + taxonomies
 */
function hello_child_register_packages_cpt() {
    $labels = array(
        'name'                  => __( 'Programs', 'hello-elementor-child' ),
        'singular_name'         => __( 'Program', 'hello-elementor-child' ),
        'menu_name'             => __( 'Programs', 'hello-elementor-child' ),
        'name_admin_bar'        => __( 'Program', 'hello-elementor-child' ),
        'add_new'               => __( 'Add New', 'hello-elementor-child' ),
        'add_new_item'          => __( 'Add New Program', 'hello-elementor-child' ),
        'new_item'              => __( 'New Program', 'hello-elementor-child' ),
        'edit_item'             => __( 'Edit Program', 'hello-elementor-child' ),
        'view_item'             => __( 'View Program', 'hello-elementor-child' ),
        'all_items'             => __( 'All Programs', 'hello-elementor-child' ),
        'search_items'          => __( 'Search Programs', 'hello-elementor-child' ),
        'not_found'             => __( 'No programs found.', 'hello-elementor-child' ),
        'not_found_in_trash'    => __( 'No programs found in Trash.', 'hello-elementor-child' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'show_in_rest'       => true,
        'has_archive'        => 'programs',
        'rewrite'            => array(
            'slug'       => 'program',
            'with_front' => false,
        ),
        'menu_position'      => 20,
        'menu_icon'          => 'dashicons-clipboard',
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
        'capability_type'    => 'post',
    );

    register_post_type( 'package', $args );
}
add_action( 'init', 'hello_child_register_packages_cpt' );

function hello_child_register_packages_taxonomies() {
    // Custom category (hierarchical)
    register_taxonomy(
        'package_category',
        array( 'package' ),
        array(
            'labels' => array(
                'name'          => __( 'Program Categories', 'hello-elementor-child' ),
                'singular_name' => __( 'Program Category', 'hello-elementor-child' ),
            ),
            'public'            => true,
            'show_in_rest'      => true,
            'hierarchical'      => true,
            'show_admin_column' => true,
            'rewrite'           => array(
                'slug'       => 'program-category',
                'with_front' => false,
            ),
        )
    );

    // Custom tags (non-hierarchical)
    register_taxonomy(
        'package_tag',
        array( 'package' ),
        array(
            'labels' => array(
                'name'          => __( 'Program Tags', 'hello-elementor-child' ),
                'singular_name' => __( 'Program Tag', 'hello-elementor-child' ),
            ),
            'public'            => true,
            'show_in_rest'      => true,
            'hierarchical'      => false,
            'show_admin_column' => true,
            'rewrite'           => array(
                'slug'       => 'program-tag',
                'with_front' => false,
            ),
        )
    );
}
add_action( 'init', 'hello_child_register_packages_taxonomies' );

/**
 * Flush rewrite rules once after permalink structure changes.
 */
function hello_child_maybe_flush_rewrite_rules() {
    if ( ! is_admin() ) {
        return;
    }

    $rewrite_version = 'program-permalinks-v2';
    if ( get_option( 'hello_child_rewrite_version' ) === $rewrite_version ) {
        return;
    }

    flush_rewrite_rules( false );
    update_option( 'hello_child_rewrite_version', $rewrite_version );
}
add_action( 'admin_init', 'hello_child_maybe_flush_rewrite_rules' );

/**
 * Rename the curated landing page slug from our-packages to our-programs once.
 */
function hello_child_maybe_rename_programs_landing_page() {
    if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $migration_version = 'our-programs-page-v1';
    if ( get_option( 'hello_child_programs_page_version' ) === $migration_version ) {
        return;
    }

    $page = get_page_by_path( 'our-programs' );
    if ( $page instanceof WP_Post ) {
        update_option( 'hello_child_programs_page_version', $migration_version );
        return;
    }

    $legacy_page = get_page_by_path( 'our-packages' );
    if ( $legacy_page instanceof WP_Post ) {
        wp_update_post(
            array(
                'ID'        => $legacy_page->ID,
                'post_name' => 'our-programs',
                'post_title'=> 'Our Programs',
            )
        );
    }

    update_option( 'hello_child_programs_page_version', $migration_version );
}
add_action( 'admin_init', 'hello_child_maybe_rename_programs_landing_page', 20 );

/**
 * Redirect legacy package URLs to the current program permalinks.
 */
function hello_child_redirect_legacy_package_urls() {
    if ( is_admin() || is_feed() || is_preview() ) {
        return;
    }

    if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
        return;
    }

    $request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
    $request_path = trim( (string) wp_parse_url( $request_uri, PHP_URL_PATH ), '/' );
    if ( '' === $request_path ) {
        return;
    }

    if ( preg_match( '#^(?:package|packages)/([^/]+)/?$#', $request_path, $matches ) ) {
        $program = get_page_by_path( sanitize_title( $matches[1] ), OBJECT, 'package' );
        if ( $program instanceof WP_Post ) {
            wp_safe_redirect( get_permalink( $program ), 301 );
            exit;
        }
    }

    if ( preg_match( '#^(?:packages|package)/?$#', $request_path ) ) {
        wp_safe_redirect( home_url( '/our-programs/' ), 301 );
        exit;
    }

    if ( preg_match( '#^package-category(?:/.*)?$#', $request_path ) || preg_match( '#^package-tag(?:/.*)?$#', $request_path ) ) {
        wp_safe_redirect( home_url( '/our-programs/' ), 301 );
        exit;
    }

    if ( preg_match( '#^our-packages/?$#', $request_path ) ) {
        wp_safe_redirect( home_url( '/our-programs/' ), 301 );
        exit;
    }
}
add_action( 'template_redirect', 'hello_child_redirect_legacy_package_urls', 5 );

/**
 * Redirect Programs archive (and related taxonomies) to the curated /our-programs/ page on frontend.
 */
function hello_child_redirect_package_archive() {
    if ( is_admin() || is_feed() || is_preview() ) {
        return;
    }

    // Avoid interfering with REST/AJAX.
    if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
        return;
    }

    $should_redirect = is_post_type_archive( 'package' ) || is_tax( array( 'package_category', 'package_tag' ) );
    if ( ! $should_redirect ) {
        return;
    }

    $target = home_url( '/our-programs/' );
    wp_safe_redirect( $target, 301 );
    exit;
}
add_action( 'template_redirect', 'hello_child_redirect_package_archive' );

/**
 * Admin UI tweaks for ACF fields
 */
add_action( 'admin_head', function() {
        // Make Packages Showcase bullet icons preview smaller inside repeater.
        echo '<style>
            .acf-field[data-key="field_showcase_pkg_item_icon"] .acf-image-uploader .image-wrap img,
            .acf-field[data-key="field_showcase_pkg_item_icon"] img {
                max-width: 28px !important;
                max-height: 28px !important;
                width: 28px !important;
                height: 28px !important;
                object-fit: contain;
            }
            .acf-field[data-key="field_showcase_pkg_item_icon"] .acf-image-uploader .image-wrap {
                max-width: 34px;
            }
        </style>';
} );

/**
 * ACF debug helper (opt-in)
 * Visit any wp-admin page with ?hello_acf_debug=1 to see status.
 */
add_action( 'admin_notices', function() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    if ( empty( $_GET['hello_acf_debug'] ) ) {
        return;
    }

    $acf_loaded = function_exists( 'acf_add_local_field_group' );
    $groups = $acf_loaded && function_exists( 'acf_get_local_field_groups' ) ? acf_get_local_field_groups() : array();
    $group_count = is_array( $groups ) ? count( $groups ) : 0;

    echo '<div class="notice notice-info"><p>';
    echo '<strong>Hello Child ACF Debug</strong><br>';
    echo 'ACF loaded: ' . ( $acf_loaded ? 'YES' : 'NO' ) . '<br>';
    echo 'Local field groups registered: ' . intval( $group_count ) . '<br>';
    if ( $acf_loaded && $group_count ) {
        $keys = array();
        foreach ( $groups as $g ) {
            if ( is_array( $g ) && ! empty( $g['key'] ) ) {
                $keys[] = sanitize_text_field( $g['key'] );
            }
        }
        echo 'Keys: ' . esc_html( implode( ', ', $keys ) );
    }
    echo '</p></div>';
} );

/**
 * ACF Options Page
 * Uncomment this when ACF Pro is installed
 */
/*
if( function_exists('acf_add_options_page') ) {
    acf_add_options_page(array(
        'page_title'    => 'Theme General Settings',
        'menu_title'    => 'Theme Settings',
        'menu_slug'     => 'theme-general-settings',
        'capability'    => 'edit_posts',
        'redirect'      => false
    ));
}
*/

/**
 * Register ACF Blocks
 * Uncomment and modify this when ready to create custom blocks
 */
/*
function hello_child_register_acf_blocks() {
    if ( function_exists( 'acf_register_block_type' ) ) {
        // Register a packages block
        acf_register_block_type(array(
            'name'              => 'packages',
            'title'             => __('Packages', 'hello-elementor-child'),
            'description'       => __('A custom packages block', 'hello-elementor-child'),
            'render_template'   => 'template-parts/blocks/packages/packages.php',
            'category'          => 'formatting',
            'icon'              => 'grid-view',
            'keywords'          => array( 'packages', 'pricing' ),
            'mode'              => 'edit',
            'supports'          => array(
                'align' => array( 'wide', 'full' ),
                'mode' => false,
            ),
        ));
    }
}
add_action('acf/init', 'hello_child_register_acf_blocks');
*/

/**
 * Include additional files
 */
require_once HELLO_CHILD_DIR . '/inc/acf-fields.php';
require_once HELLO_CHILD_DIR . '/inc/acf-flexible-layouts.php';
// require_once HELLO_CHILD_DIR . '/inc/migrate-packages.php';
// require_once HELLO_CHILD_DIR . '/inc/custom-functions.php';

/**
 * ACF JSON Sync - Save Point
 * This allows ACF to save field groups as JSON in /acf-json folder
 */
add_filter( 'acf/settings/save_json', function( $path ) {
    return HELLO_CHILD_DIR . '/acf-json';
} );

/**
 * ACF JSON Sync - Load Point
 * This allows ACF to load field groups from /acf-json folder
 */
add_filter( 'acf/settings/load_json', function( $paths ) {
    $paths[] = HELLO_CHILD_DIR . '/acf-json';
    return $paths;
} );
