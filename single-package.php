<?php
/**
 * Single Package Template
 * Professional layout for Package CPT with ACF flexible modules
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<main id="content" class="site-main single-package">
    <?php
    while ( have_posts() ) :
        the_post();
        
        $package_id = get_the_ID();
        $package_title = get_the_title();
        $package_short_description = function_exists( 'get_field' ) ? get_field( 'short_description', $package_id ) : '';
        $package_featured_image = get_the_post_thumbnail_url( $package_id, 'large' );
        $package_bg_color = function_exists( 'get_field' ) ? get_field( 'package_bg_color', $package_id ) : '#eff5ff';
        $package_bg_color = ! empty( $package_bg_color ) ? $package_bg_color : '#eff5ff';
        
        ?>
        
        <!-- Package Hero Banner (ACF Style) -->
        <section class="hero-section-module package-hero-banner">
            <div class="hero-section-module__inner">
                <!-- Content -->
                <div class="hero-section-module__content">
                    <h1 class="hero-section-module__title">
                        <?php echo wp_kses_post( $package_title ); ?>
                    </h1>
                    
                    <?php if ( $package_short_description ) : ?>
                        <p class="hero-section-module__subtitle">
                            <?php echo wp_kses_post( $package_short_description ); ?>
                        </p>
                    <?php endif; ?>
                    
                    <div class="hero-section-module__button">
                        <a href="#package-sections" class="btn btn--primary">
                            <?php echo esc_html__( 'Explore Package', 'hello-elementor-child' ); ?>
                        </a>
                    </div>
                </div>
                
                <!-- Image -->
                <?php if ( $package_featured_image ) : ?>
                <div class="hero-section-module__image">
                    <img src="<?php echo esc_url( $package_featured_image ); ?>" 
                         alt="<?php echo esc_attr( $package_title ); ?>"
                         loading="lazy">
                </div>
                <?php endif; ?>
            </div>
        </section>
        
        <!-- Package Content (Main Editor) -->
        <?php
        $post_content = trim( get_the_content() );
        if ( $post_content ) :
        ?>
        <section class="package-content" id="package-content">
            <div class="container">
                <div class="package-content__inner">
                    <?php the_content(); ?>
                </div>
            </div>
        </section>
        <?php endif; ?>
        
        <!-- Package Sections (ACF include_sections field) - Accordion Style -->
        <?php
        if ( function_exists( 'have_rows' ) ) {
            $sections = get_field( 'include_sections', $package_id );
            $package_text_color = function_exists( 'get_field' ) ? get_field( 'package_text_color', $package_id ) : '#262f3e';
            $package_text_color = ! empty( $package_text_color ) ? $package_text_color : '#262f3e';
            $package_icon_bg_color = function_exists( 'get_field' ) ? get_field( 'package_icon_bg_color', $package_id ) : '#9a1078';
            $package_icon_bg_color = ! empty( $package_icon_bg_color ) ? $package_icon_bg_color : '#9a1078';
            $package_icon_color = function_exists( 'get_field' ) ? get_field( 'package_icon_color', $package_id ) : '#ffffff';
            $package_icon_color = ! empty( $package_icon_color ) ? $package_icon_color : '#ffffff';
            $package_arrow_color = function_exists( 'get_field' ) ? get_field( 'package_arrow_color', $package_id ) : '#262f3e';
            $package_arrow_color = ! empty( $package_arrow_color ) ? $package_arrow_color : '#262f3e';
            
            if ( $sections && is_array( $sections ) && count( $sections ) > 0 ) :
        ?>
        <section class="package-sections" id="package-sections">
            <div class="container">
                <h2 class="package-sections__heading">What's Included in Your Check-Up</h2>
                <p class="package-sections__subheading">A carefully selected set of essential diagnostic tests designed for clarity, accuracy, and peace of mind.</p>
                
                <div class="package-sections__accordion" style="--pkg-bg-color: #f5f9fb; --pkg-text-color: #262f3f; --pkg-icon-bg-color: #323f53; --pkg-icon-color: #ffffff; --pkg-arrow-color: #323f53; background: #dce6f5; padding: 40px; border-radius: 16px;">
                    <?php
                    foreach ( $sections as $index => $section ) :
                        $section_title_line_1 = trim( (string) ( $section['title_line_1'] ?? '' ) );
                        $section_title_line_2 = trim( (string) ( $section['title_line_2'] ?? '' ) );
                        $section_items = $section['items'] ?? array();
                        
                        if ( empty( $section_title_line_1 ) && empty( $section_title_line_2 ) ) {
                            continue;
                        }
                        
                        // Create unique ID for aria controls
                        $section_id = 'section-' . sanitize_title( $section_title_line_1 . '-' . $index );
                    ?>
                    <details class="package-sections__accordion-item" <?php echo ( $index === 0 || $index === 1 || $index === 2 ) ? 'open' : ''; ?>>
                        <summary class="package-sections__accordion-summary">
                            <?php if ( $section_title_line_1 ) : ?>
                                <span class="package-sections__title-line-1">
                                    <?php echo wp_kses_post( $section_title_line_1 ); ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if ( $section_title_line_2 ) : ?>
                                <span class="package-sections__title-line-2">
                                    <?php echo wp_kses_post( $section_title_line_2 ); ?>
                                </span>
                            <?php endif; ?>
                        </summary>
                        
                        <?php if ( ! empty( $section_items ) && is_array( $section_items ) ) : ?>
                        <div class="package-sections__accordion-content">
                            <ul class="package-sections__list">
                                <?php
                                foreach ( $section_items as $item ) :
                                    $item_text = $item['text'] ?? '';
                                    if ( empty( $item_text ) ) {
                                        continue;
                                    }
                                ?>
                                <li class="package-sections__list-item">
                                    <span class="package-sections__text">
                                        <?php echo wp_kses_post( $item_text ); ?>
                                    </span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                    </details>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php 
            endif;
        }
        ?>
        
        <!-- Package CTA Section -->
        <?php
        $cta_heading = function_exists( 'get_field' ) ? get_field( 'package_cta_heading', $package_id ) : '';
        $cta_subheading = function_exists( 'get_field' ) ? get_field( 'package_cta_subheading', $package_id ) : '';
        $cta_image = function_exists( 'get_field' ) ? get_field( 'package_cta_image', $package_id ) : array();
        $cta_button_text = function_exists( 'get_field' ) ? get_field( 'package_cta_button_text', $package_id ) : '';
        $cta_button_link = function_exists( 'get_field' ) ? get_field( 'package_cta_button_link', $package_id ) : array();
        
        if ( $cta_heading || $cta_subheading || $cta_button_text ) :
        ?>
        <section class="module-cta-package">
            <div class="cta-package__inner">
                <div class="cta-package__block">
                    <div class="cta-package__content">
                        <?php if ( $cta_heading ) : ?>
                            <p class="cta-package__heading"><?php echo esc_html( $cta_heading ); ?></p>
                        <?php endif; ?>

                        <?php if ( $cta_subheading ) : ?>
                            <h2 class="cta-package__subheading"><?php echo esc_html( $cta_subheading ); ?></h2>
                        <?php endif; ?>

                        <?php if ( $cta_button_text && ! empty( $cta_button_link['url'] ) ) : ?>
                            <a href="<?php echo esc_url( $cta_button_link['url'] ); ?>" class="btn btn--primary cta-package__button" <?php echo ! empty( $cta_button_link['target'] ) ? 'target="' . esc_attr( $cta_button_link['target'] ) . '"' : ''; ?>>
                                <span class="btn__text"><?php echo esc_html( $cta_button_text ); ?></span>
                                <span class="btn__icon" aria-hidden="true">
                                    <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/arrow.svg' ); ?>" alt="" />
                                </span>
                            </a>
                        <?php endif; ?>
                    </div>

                    <?php if ( ! empty( $cta_image['url'] ) ) : ?>
                        <div class="cta-package__image-wrap">
                            <img 
                                src="<?php echo esc_url( $cta_image['url'] ); ?>" 
                                alt="<?php echo esc_attr( $cta_image['alt'] ?? '' ); ?>"
                                class="cta-package__image"
                                loading="lazy"
                            >
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>
        
        <!-- Other Packages Section -->
        <?php
        $other_packages_args = array(
            'post_type'      => 'package',
            'posts_per_page' => 3,
            'post__not_in'   => array( $package_id ),
            'orderby'        => 'rand',
            'fields'         => 'ids',
        );
        
        $other_packages_query = new WP_Query( $other_packages_args );
        
        if ( $other_packages_query->have_posts() ) :
        ?>
        <section class="other-packages">
            <div class="other-packages__inner">
                <div class="other-packages__header">
                    <h2 class="other-packages__title"><?php esc_html_e( 'Explore Our Diagnostic Packages', 'hello-elementor-child' ); ?></h2>
                    <p class="other-packages__subtitle"><?php esc_html_e( 'A selection of specialised check-up programs designed to match different medical needs and levels of care.', 'hello-elementor-child' ); ?></p>
                </div>
                <div class="other-packages__grid">
                    <?php
                    while ( $other_packages_query->have_posts() ) :
                        $other_packages_query->the_post();
                        $other_pkg_id = get_the_ID();
                        $other_pkg_title = get_the_title();
                        $other_pkg_price = function_exists( 'get_field' ) ? get_field( 'price', $other_pkg_id ) : '';
                        $other_pkg_description = function_exists( 'get_field' ) ? get_field( 'short_description', $other_pkg_id ) : '';
                        $other_pkg_image = get_the_post_thumbnail_url( $other_pkg_id, 'medium' );
                        $other_pkg_link = get_permalink( $other_pkg_id );
                    ?>
                    <article class="other-packages__card">
                        <a href="<?php echo esc_url( $other_pkg_link ); ?>" class="other-packages__card-link">
                            <?php if ( $other_pkg_image ) : ?>
                                <div class="other-packages__image">
                                    <img src="<?php echo esc_url( $other_pkg_image ); ?>" alt="<?php echo esc_attr( $other_pkg_title ); ?>" loading="lazy">
                                </div>
                            <?php endif; ?>
                            
                            <div class="other-packages__content">
                                <h3 class="other-packages__card-title"><?php echo esc_html( $other_pkg_title ); ?></h3>
                                
                                <?php if ( $other_pkg_description ) : 
                                    $short_desc = wp_strip_all_tags( $other_pkg_description );
                                    $short_desc = mb_strlen( $short_desc ) > 120 ? mb_substr( $short_desc, 0, 120 ) . '...' : $short_desc;
                                ?>
                                    <p class="other-packages__description"><?php echo esc_html( $short_desc ); ?></p>
                                <?php endif; ?>
                                
                                <span class="other-packages__read-more">
                                    <?php esc_html_e( 'Read more', 'hello-elementor-child' ); ?>
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="other-packages__arrow">
                                        <path d="M1 8h14M8 1l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            </div>
                        </a>
                    </article>
                    <?php endwhile; ?>
                </div>
            </div>
        </section>
        <?php 
        endif;
        wp_reset_postdata();
        ?>
        
        <!-- Testimonials Section -->
        <?php
        $testimonials_title = function_exists( 'get_field' ) ? get_field( 'package_testimonials_title', $package_id ) : '';
        $testimonials_shortcode = function_exists( 'get_field' ) ? get_field( 'package_testimonials_shortcode', $package_id ) : '';
        
        if ( $testimonials_title || $testimonials_shortcode ) :
            $arrow_src = get_stylesheet_directory_uri() . '/assets/images/arrow.svg';
        ?>
        <section class="module-testimonials">
            <div class="container">
                <?php if ( $testimonials_title ) : ?>
                    <div class="testimonials__header">
                        <div class="testimonials__header-left">
                            <h2 class="testimonials__title"><?php echo wp_kses_post( $testimonials_title ); ?></h2>
                        </div>

                        <?php if ( $testimonials_shortcode ) : ?>
                            <div class="testimonials__header-right">
                                <div class="testimonials__nav" aria-label="Testimonials navigation">
                                    <button class="testimonials__nav-btn testimonials__nav-btn--prev" type="button" aria-label="Previous testimonial">
                                        <img src="<?php echo esc_url( $arrow_src ); ?>" alt="" />
                                    </button>
                                    <button class="testimonials__nav-btn testimonials__nav-btn--next" type="button" aria-label="Next testimonial">
                                        <img src="<?php echo esc_url( $arrow_src ); ?>" alt="" />
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ( $testimonials_shortcode ) : ?>
                    <div class="testimonials__carousel">
                        <?php echo do_shortcode( $testimonials_shortcode ); ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        <?php endif; ?>
        
        <!-- Final CTA Section -->
        <?php
        $final_cta_heading = function_exists( 'get_field' ) ? get_field( 'package_final_cta_heading', $package_id ) : '';
        $final_cta_features = function_exists( 'get_field' ) ? get_field( 'package_final_cta_features', $package_id ) : array();
        $final_cta_button_text = function_exists( 'get_field' ) ? get_field( 'package_final_cta_button_text', $package_id ) : '';
        $final_cta_button_link = function_exists( 'get_field' ) ? get_field( 'package_final_cta_button_link', $package_id ) : array();
        $final_cta_image = function_exists( 'get_field' ) ? get_field( 'package_final_cta_image', $package_id ) : array();
        
        if ( $final_cta_heading || ! empty( $final_cta_features ) ) :
        ?>
        <section class="module-cta-section package-final-cta">
            <div class="cta-section__panel">
                <div class="cta-section__grid">
                    <div class="cta-section__left">
                        <?php if ( $final_cta_heading ) : ?>
                            <h2 class="cta-section__heading"><?php echo wp_kses_post( $final_cta_heading ); ?></h2>
                        <?php endif; ?>

                        <div class="cta-section__separator" aria-hidden="true"></div>

                        <?php if ( ! empty( $final_cta_features ) ) : ?>
                            <div class="cta-section__items">
                                <?php foreach ( $final_cta_features as $feature ) : 
                                    $icon = $feature['icon'] ?? null;
                                    $title = $feature['title'] ?? '';
                                    $description = $feature['description'] ?? '';
                                ?>
                                    <div class="cta-section__item">
                                        <?php if ( ! empty( $icon['url'] ) ) : ?>
                                            <div class="cta-section__item-icon">
                                                <img src="<?php echo esc_url( $icon['url'] ); ?>" alt="<?php echo esc_attr( $icon['alt'] ?: $title ); ?>">
                                            </div>
                                        <?php endif; ?>

                                        <div class="cta-section__item-body">
                                            <?php if ( $title ) : ?>
                                                <h3 class="cta-section__item-heading"><?php echo esc_html( $title ); ?></h3>
                                            <?php endif; ?>
                                            <?php if ( $description ) : ?>
                                                <p class="cta-section__item-content"><?php echo esc_html( $description ); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ( ! empty( $final_cta_image['url'] ) ) : ?>
                        <div class="cta-section__right">
                            <div class="cta-section__card">
                                <div class="cta-section__image-wrap">
                                    <img class="cta-section__image" src="<?php echo esc_url( $final_cta_image['url'] ); ?>" alt="<?php echo esc_attr( $final_cta_image['alt'] ?? '' ); ?>">
                                </div>
                                
                                <?php if ( $final_cta_button_text && ! empty( $final_cta_button_link['url'] ) ) : ?>
                                    <a href="<?php echo esc_url( $final_cta_button_link['url'] ); ?>" class="btn btn--primary cta-section__button" <?php echo ! empty( $final_cta_button_link['target'] ) ? 'target="' . esc_attr( $final_cta_button_link['target'] ) . '"' : ''; ?>>
                                        <span class="btn__text"><?php echo esc_html( $final_cta_button_text ); ?></span>
                                        <span class="btn__icon" aria-hidden="true">
                                            <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/arrow.svg' ); ?>" alt="" />
                                        </span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>
        
        <!-- Package ACF Flexible Modules -->
        <?php
        if ( function_exists( 'have_rows' ) && have_rows( 'page_modules', $package_id ) ) :
        ?>
        <section class="package-modules">
            <?php
            while ( have_rows( 'page_modules', $package_id ) ) : the_row();
                $layout = get_row();
                hello_child_render_flexible_layout( $layout );
            endwhile;
            ?>
        </section>
        <?php endif; ?>
        
        <?php
    endwhile;
    ?>
</main>

<?php get_footer();
