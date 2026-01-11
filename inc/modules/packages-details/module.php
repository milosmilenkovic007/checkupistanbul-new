<?php
/**
 * Packages Details Module
 * Renders selected Packages (CPT) as a comparison table (desktop)
 * and an accordion (mobile) with package tabs.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$heading     = $data['heading'] ?? '';
$description = $data['description'] ?? '';
$selected    = $data['packages'] ?? array();
$additional_package_raw = $data['additional_package'] ?? array();
$additional_package_id = 0;

// Get style settings
$section_title_bg = $data['section_title_bg'] ?? '#f2ecf2';
$section_title_color = $data['section_title_color'] ?? '#053b3f';
$col0_bg_tab0 = $data['col0_bg_tab0'] ?? '#f2ecf2';
$col0_color_tab0 = $data['col0_color_tab0'] ?? '#35062a';
$col1_bg_tab0 = $data['col1_bg_tab0'] ?? '#053b3f';
$col1_color_tab0 = $data['col1_color_tab0'] ?? '#ffffff';
$col2_bg_tab0 = $data['col2_bg_tab0'] ?? '#fed36f';
$col2_color_tab0 = $data['col2_color_tab0'] ?? '#35062a';
$col1_bg_tab1 = $data['col1_bg_tab1'] ?? '#053b3f';
$col1_color_tab1 = $data['col1_color_tab1'] ?? '#ffffff';
$col2_bg_tab1 = $data['col2_bg_tab1'] ?? '#fed36f';
$col2_color_tab1 = $data['col2_color_tab1'] ?? '#35062a';
$col1_bg_tab2 = $data['col1_bg_tab2'] ?? '#053b3f';
$col1_color_tab2 = $data['col1_color_tab2'] ?? '#ffffff';
$col2_bg_tab2 = $data['col2_bg_tab2'] ?? '#fed36f';
$col2_color_tab2 = $data['col2_color_tab2'] ?? '#35062a';

// Debug style values received in module
if ( current_user_can( 'manage_options' ) ) {
    error_log( 'Module received - section_title_bg: ' . $section_title_bg );
    error_log( 'Module received - col0_bg_tab0: ' . $col0_bg_tab0 );
    error_log( 'Module received - col1_bg_tab0: ' . $col1_bg_tab0 );
    error_log( 'Module received - col2_bg_tab0: ' . $col2_bg_tab0 );
}

// Normalize relationship return values to IDs.
if ( is_array( $selected ) ) {
    $selected = array_values( array_filter( array_map( function( $v ) {
        if ( is_numeric( $v ) ) {
            return intval( $v );
        }
        if ( is_object( $v ) && isset( $v->ID ) ) {
            return intval( $v->ID );
        }
        if ( is_array( $v ) && isset( $v['ID'] ) ) {
            return intval( $v['ID'] );
        }
        return 0;
    }, $selected ) ) );
} else {
    $selected = array();
}

$checkmark_url = HELLO_CHILD_URI . '/assets/images/checkmark-svgrepo-com.svg';
$arrow_up_url  = HELLO_CHILD_URI . '/assets/images/up-svgrepo-com.svg';
$arrow_down_url = HELLO_CHILD_URI . '/assets/images/chevron-down-svgrepo-com.svg';

$arrow_style = sprintf(
    "--pd-arrow-open: url('%s'); --pd-arrow-closed: url('%s');",
    esc_url_raw( $arrow_up_url ),
    esc_url_raw( $arrow_down_url )
);

// Build packages data + section union.
$packages_data = array();
$sections_order = array();
$sections_map = array();

$had_selection = ! empty( $selected );

// Debug: Show selected IDs
if ( current_user_can( 'manage_options' ) && ! empty( $_GET['debug_packages'] ) ) {
    echo '<!-- had_selection: ' . ( $had_selection ? 'YES' : 'NO' ) . ' -->';
    echo '<!-- selected IDs: ' . esc_html( implode( ', ', $selected ) ) . ' -->';
}

if ( ! empty( $selected ) ) {
    // Use get_post() per ID to avoid query filters/caching issues and to allow
    // admins to preview draft packages while building the page.
    $posts = array();
    foreach ( $selected as $maybe_id ) {
        $pkg_id = intval( $maybe_id );
        if ( ! $pkg_id ) {
            continue;
        }

        $p = get_post( $pkg_id );
        if ( ! $p || ( $p instanceof WP_Post ) === false ) {
            continue;
        }

        if ( $p->post_type !== 'package' ) {
            continue;
        }

        // For visitors show only published packages.
        if ( ! is_user_logged_in() && get_post_status( $pkg_id ) !== 'publish' ) {
            continue;
        }

        $posts[] = $p;
    }

    foreach ( $posts as $p ) {
        $pkg_id = intval( $p->ID );
        $pkg_sections = function_exists( 'get_field' ) ? ( get_field( 'include_sections', $pkg_id ) ?: array() ) : array();
        $is_additional_pkg = function_exists( 'get_field' ) ? (bool) get_field( 'is_additional_package', $pkg_id ) : false;

        // If marked as "Additional Package", do not place it in the main comparison grid.
        // Keep only the first such package as the dedicated additional block (unless explicitly set via field).
        if ( $is_additional_pkg ) {
            if ( ! $additional_package_id ) {
                $additional_package_id = $pkg_id;
            }
            continue;
        }

        $packages_data[] = array(
            'id' => $pkg_id,
            'title' => get_the_title( $pkg_id ),
            'sections' => is_array( $pkg_sections ) ? $pkg_sections : array(),
        );

        if ( is_array( $pkg_sections ) ) {
            foreach ( $pkg_sections as $sec ) {
                $line1 = trim( (string) ( $sec['title_line_1'] ?? '' ) );
                $line2 = trim( (string) ( $sec['title_line_2'] ?? '' ) );

                $key_base = trim( $line1 . ' ' . $line2 );
                $key = $key_base !== '' ? sanitize_title( $key_base ) : '';
                if ( $key === '' ) {
                    $key = 'section_' . md5( wp_json_encode( $sec ) );
                }

                if ( ! isset( $sections_map[ $key ] ) ) {
                    $sections_order[] = $key;
                    $sections_map[ $key ] = array(
                        'title_line_1' => $line1,
                        'title_line_2' => $line2,
                        'per_package'  => array(),
                    );
                }

                $items_out = array();
                $items = $sec['items'] ?? array();
                if ( is_array( $items ) ) {
                    foreach ( $items as $it ) {
                        $t = trim( (string) ( $it['text'] ?? '' ) );
                        if ( $t !== '' ) {
                            $items_out[] = $t;
                        }
                    }
                }

                $sections_map[ $key ]['per_package'][ $pkg_id ] = $items_out;
            }
        }
    }
}
?>

<?php if ( empty( $packages_data ) ) : ?>
    <?php return; ?>
<?php endif; ?>

<?php
// If packages exist but they don't yet have include_sections filled,
// render a blank table with default section rows (so the module never disappears).
if ( empty( $sections_order ) ) {
    $defaults = array(
        array( 'Medical', 'Examinations' ),
        array( 'Cardiology', 'Laboratory' ),
        array( 'Radiology & Functional Tests', '' ),
        array( 'Biochemistry', 'Laboratory' ),
    );

    foreach ( $defaults as $d ) {
        $line1 = (string) ( $d[0] ?? '' );
        $line2 = (string) ( $d[1] ?? '' );
        $key = sanitize_title( trim( $line1 . ' ' . $line2 ) );
        if ( $key === '' ) {
            continue;
        }

        $sections_order[] = $key;
        $sections_map[ $key ] = array(
            'title_line_1' => $line1,
            'title_line_2' => $line2,
            'per_package'  => array(),
        );

        foreach ( $packages_data as $pkg ) {
            $pkg_id = intval( $pkg['id'] ?? 0 );
            if ( $pkg_id ) {
                $sections_map[ $key ]['per_package'][ $pkg_id ] = array();
            }
        }
    }
}
?>

<?php 
$pkg_count = count( $packages_data ); 

$root_style = $arrow_style . ' --pkg-cols: ' . (string) $pkg_count . ';';

// Debug: Final counts before render
if ( current_user_can( 'manage_options' ) && ! empty( $_GET['debug_packages'] ) ) {
    echo '<!-- pkg_count: ' . $pkg_count . ' -->';
    echo '<!-- sections_order: ' . count( $sections_order ) . ' -->';
    echo '<!-- packages_data: ' . esc_html( print_r( array_map( function( $p ) {
        return array( 'id' => $p['id'], 'title' => $p['title'], 'sections_count' => count( $p['sections'] ) );
    }, $packages_data ), true ) ) . ' -->';
}

// If no packages, don't render anything
if ( $pkg_count === 0 ) {
    if ( current_user_can( 'manage_options' ) && ! empty( $_GET['debug_packages'] ) ) {
        echo '<!-- EARLY RETURN: pkg_count === 0 -->';
    }
    return;
}

// Generate unique ID for this module instance
$module_id = 'pd-' . uniqid();

// Build dynamic CSS for background and text colors
$dynamic_css = "
<style>
#{$module_id} .packages-details.is-active-0 .packages-details__section-title {
    background: {$section_title_bg};
    color: {$section_title_color};
}
#{$module_id} .packages-details.is-active-0 .packages-details__items[data-col=\"0\"] {
    background: {$col0_bg_tab0};
}
#{$module_id} .packages-details.is-active-0 .packages-details__items[data-col=\"0\"] .packages-details__list-item,
#{$module_id} .packages-details.is-active-0 .packages-details__items[data-col=\"0\"] .packages-details__text {
    color: {$col0_color_tab0};
}
#{$module_id} .packages-details.is-active-0 .packages-details__items[data-col=\"1\"] {
    background: {$col1_bg_tab0};
}
#{$module_id} .packages-details.is-active-0 .packages-details__items[data-col=\"1\"] .packages-details__list-item,
#{$module_id} .packages-details.is-active-0 .packages-details__items[data-col=\"1\"] .packages-details__text {
    color: {$col1_color_tab0};
}
#{$module_id} .packages-details.is-active-0 .packages-details__items[data-col=\"2\"] {
    background: {$col2_bg_tab0};
}
#{$module_id} .packages-details.is-active-0 .packages-details__items[data-col=\"2\"] .packages-details__list-item,
#{$module_id} .packages-details.is-active-0 .packages-details__items[data-col=\"2\"] .packages-details__text {
    color: {$col2_color_tab0};
}
#{$module_id} .packages-details.is-active-1 .packages-details__section-title {
    background: {$section_title_bg};
    color: {$section_title_color};
}
#{$module_id} .packages-details.is-active-1 .packages-details__items[data-col=\"1\"] {
    background: {$col1_bg_tab1};
}
#{$module_id} .packages-details.is-active-1 .packages-details__items[data-col=\"1\"] .packages-details__list-item,
#{$module_id} .packages-details.is-active-1 .packages-details__items[data-col=\"1\"] .packages-details__text {
    color: {$col1_color_tab1};
}
#{$module_id} .packages-details.is-active-1 .packages-details__items[data-col=\"2\"] {
    background: {$col2_bg_tab1};
}
#{$module_id} .packages-details.is-active-1 .packages-details__items[data-col=\"2\"] .packages-details__list-item,
#{$module_id} .packages-details.is-active-1 .packages-details__items[data-col=\"2\"] .packages-details__text {
    color: {$col2_color_tab1};
}
#{$module_id} .packages-details.is-active-2 .packages-details__section-title {
    background: {$section_title_bg};
    color: {$section_title_color};
}
#{$module_id} .packages-details.is-active-2 .packages-details__items[data-col=\"1\"] {
    background: {$col1_bg_tab2};
}
#{$module_id} .packages-details.is-active-2 .packages-details__items[data-col=\"1\"] .packages-details__list-item,
#{$module_id} .packages-details.is-active-2 .packages-details__items[data-col=\"1\"] .packages-details__text {
    color: {$col1_color_tab2};
}
#{$module_id} .packages-details.is-active-2 .packages-details__items[data-col=\"2\"] {
    background: {$col2_bg_tab2};
}
#{$module_id} .packages-details.is-active-2 .packages-details__items[data-col=\"2\"] .packages-details__list-item,
#{$module_id} .packages-details.is-active-2 .packages-details__items[data-col=\"2\"] .packages-details__text {
    color: {$col2_color_tab2};
}
</style>
";
?>

<!-- Packages Details Style Debug -->
<!-- section_title_bg: <?php echo esc_html( $section_title_bg ); ?> -->
<!-- col0_bg_tab0: <?php echo esc_html( $col0_bg_tab0 ); ?> -->
<!-- col1_bg_tab0: <?php echo esc_html( $col1_bg_tab0 ); ?> -->
<!-- col2_bg_tab0: <?php echo esc_html( $col2_bg_tab0 ); ?> -->
<!-- col1_bg_tab1: <?php echo esc_html( $col1_bg_tab1 ); ?> -->
<!-- col2_bg_tab1: <?php echo esc_html( $col2_bg_tab1 ); ?> -->
<!-- col1_bg_tab2: <?php echo esc_html( $col1_bg_tab2 ); ?> -->
<!-- col2_bg_tab2: <?php echo esc_html( $col2_bg_tab2 ); ?> -->

<?php echo $dynamic_css; ?>

<section class="module-packages-details" id="<?php echo esc_attr( $module_id ); ?>">
    <div class="packages-details__inner">
        <?php if ( $heading ) : ?>
            <h2 class="packages-details__heading"><?php echo wp_kses_post( $heading ); ?></h2>
        <?php endif; ?>

        <?php if ( $description ) : ?>
            <p class="packages-details__subheading"><?php echo wp_kses_post( $description ); ?></p>
        <?php endif; ?>

        <div class="packages-details is-active-0" data-packages-details data-count="<?php echo esc_attr( (string) $pkg_count ); ?>" style="<?php echo esc_attr( $root_style ); ?>">
            <div class="packages-details__tabs" role="tablist" aria-label="Packages">
                <?php foreach ( $packages_data as $idx => $pkg ) : ?>
                    <?php $tab_slug = sanitize_title( (string) ( $pkg['title'] ?? '' ) ); ?>
                    <button
                        class="packages-details__tab packages-details__tab--<?php echo esc_attr( (string) $idx ); ?><?php echo $tab_slug ? ' packages-details__tab--' . esc_attr( $tab_slug ) : ''; ?><?php echo $idx === 0 ? ' is-active' : ''; ?>"
                        type="button"
                        role="tab"
                        aria-selected="<?php echo $idx === 0 ? 'true' : 'false'; ?>"
                        data-index="<?php echo esc_attr( (string) $idx ); ?>"
                    >
                        <?php echo esc_html( $pkg['title'] ?? '' ); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Desktop table -->
            <div class="packages-details__table" aria-label="Packages comparison table">
                <div class="packages-details__grid" style="--pkg-cols: <?php echo esc_attr( (string) $pkg_count ); ?>">
                    <?php foreach ( $sections_order as $sec_idx => $sec_key ) : ?>
                        <?php
                            $sec = $sections_map[ $sec_key ] ?? null;
                            if ( ! is_array( $sec ) ) { continue; }
                            $line1 = (string) ( $sec['title_line_1'] ?? '' );
                            $line2 = (string) ( $sec['title_line_2'] ?? '' );
                        ?>
                        <div class="packages-details__grid-row" data-pd-row data-pd-row-index="<?php echo esc_attr( (string) $sec_idx ); ?>" data-pd-section="<?php echo esc_attr( (string) $sec_key ); ?>">
                            <div
                                class="packages-details__cell packages-details__section-title"
                                role="button"
                                tabindex="0"
                                aria-expanded="true"
                                data-pd-row-toggle
                            >
                                <?php if ( $line1 ) : ?><span class="packages-details__section-line"><?php echo esc_html( $line1 ); ?></span><?php endif; ?>
                                <?php if ( $line2 ) : ?><span class="packages-details__section-line"><?php echo esc_html( $line2 ); ?></span><?php endif; ?>
                            </div>

                            <?php foreach ( $packages_data as $idx => $pkg ) : ?>
                                <?php
                                    $pkg_id = intval( $pkg['id'] ?? 0 );
                                    $items = $sec['per_package'][ $pkg_id ] ?? array();
                                ?>
                                <div class="packages-details__cell packages-details__items" data-col="<?php echo esc_attr( (string) $idx ); ?>">
                                    <?php if ( ! empty( $items ) ) : ?>
                                        <ul class="packages-details__list">
                                            <?php foreach ( $items as $it ) : ?>
                                                <li class="packages-details__list-item">
                                                    <img class="packages-details__check" src="<?php echo esc_url( $checkmark_url ); ?>" alt="" />
                                                    <span class="packages-details__text"><?php echo esc_html( $it ); ?></span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Mobile accordion -->
            <div class="packages-details__accordion" aria-label="Packages includes accordion">
                <?php foreach ( $sections_order as $sec_idx => $sec_key ) : ?>
                    <?php
                        $sec = $sections_map[ $sec_key ] ?? null;
                        if ( ! is_array( $sec ) ) { continue; }
                        $line1 = (string) ( $sec['title_line_1'] ?? '' );
                        $line2 = (string) ( $sec['title_line_2'] ?? '' );
                        // Open all sections except the last one by default
                        $total_sections = count( $sections_order );
                        $is_open = $sec_idx < ( $total_sections - 1 );
                    ?>
                    <details class="packages-details__accordion-item" data-pd-section="<?php echo esc_attr( (string) $sec_key ); ?>"<?php echo $is_open ? ' open' : ''; ?>>
                        <summary class="packages-details__accordion-summary">
                            <?php if ( $line1 ) : ?><span class="packages-details__section-line"><?php echo esc_html( $line1 ); ?></span><?php endif; ?>
                            <?php if ( $line2 ) : ?><span class="packages-details__section-line"><?php echo esc_html( $line2 ); ?></span><?php endif; ?>
                        </summary>
                        <div class="packages-details__accordion-content">
                            <?php foreach ( $packages_data as $idx => $pkg ) : ?>
                                <?php
                                    $pkg_id = intval( $pkg['id'] ?? 0 );
                                    $items = $sec['per_package'][ $pkg_id ] ?? array();
                                ?>
                                <div class="packages-details__accordion-panel" data-col="<?php echo esc_attr( (string) $idx ); ?>">
                                    <?php if ( ! empty( $items ) ) : ?>
                                        <ul class="packages-details__list">
                                            <?php foreach ( $items as $it ) : ?>
                                                <li class="packages-details__list-item">
                                                    <img class="packages-details__check" src="<?php echo esc_url( $checkmark_url ); ?>" alt="" />
                                                    <span class="packages-details__text"><?php echo esc_html( $it ); ?></span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </details>
                <?php endforeach; ?>
            </div>

            <div class="packages-details__footer">
                <button type="button" class="packages-details__toggle-all" data-pd-toggle-all>Show all</button>
            </div>
        </div>

        <?php
        // Additional Package: horizontal layout below the main grid
        if ( is_array( $additional_package_raw ) && ! empty( $additional_package_raw ) ) {
            $first = reset( $additional_package_raw );
            if ( is_numeric( $first ) ) {
                $additional_package_id = intval( $first );
            } elseif ( is_object( $first ) && isset( $first->ID ) ) {
                $additional_package_id = intval( $first->ID );
            } elseif ( is_array( $first ) && isset( $first['ID'] ) ) {
                $additional_package_id = intval( $first['ID'] );
            }
        }

        if ( $additional_package_id ) {
            $add_post = get_post( $additional_package_id );
            if ( $add_post && $add_post->post_type === 'package' ) {
                // For visitors only show published
                if ( is_user_logged_in() || get_post_status( $additional_package_id ) === 'publish' ) {
                    $add_title = get_the_title( $additional_package_id );
                    $add_sections = function_exists( 'get_field' ) ? ( get_field( 'include_sections', $additional_package_id ) ?: array() ) : array();
                    
                    if ( ! empty( $add_sections ) && is_array( $add_sections ) ) :
        ?>
        <div class="packages-details__additional">
            <div class="packages-details__additional-inner" style="<?php echo esc_attr( $arrow_style ); ?>">
                <div class="packages-details__additional-header">
                    <h3 
                        class="packages-details__additional-title" 
                        role="button"
                        tabindex="0"
                        aria-expanded="true"
                        data-pd-additional-toggle
                    >
                        <?php echo esc_html( $add_title ); ?>
                    </h3>
                </div>
                <div class="packages-details__additional-content" data-pd-additional-content>
                    <?php foreach ( $add_sections as $sec ) : ?>
                        <?php
                            $line1 = trim( (string) ( $sec['title_line_1'] ?? '' ) );
                            $line2 = trim( (string) ( $sec['title_line_2'] ?? '' ) );
                            $items = is_array( $sec['items'] ?? null ) ? $sec['items'] : array();
                            if ( empty( $items ) ) { continue; }
                        ?>
                        <div class="packages-details__additional-section">
                            <div class="packages-details__additional-section-title">
                                <?php if ( $line1 ) : ?><span class="packages-details__section-line"><?php echo esc_html( $line1 ); ?></span><?php endif; ?>
                                <?php if ( $line2 ) : ?><span class="packages-details__section-line"><?php echo esc_html( $line2 ); ?></span><?php endif; ?>
                            </div>
                            <ul class="packages-details__list packages-details__additional-list">
                                <?php foreach ( $items as $it ) : ?>
                                    <?php $item_text = trim( (string) ( is_array( $it ) ? ( $it['text'] ?? '' ) : $it ) ); ?>
                                    <?php if ( $item_text ) : ?>
                                        <li class="packages-details__list-item">
                                            <img class="packages-details__check" src="<?php echo esc_url( $checkmark_url ); ?>" alt="" />
                                            <span class="packages-details__text"><?php echo esc_html( $item_text ); ?></span>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
                    endif;
                }
            }
        }
        ?>
    </div>
</section>
