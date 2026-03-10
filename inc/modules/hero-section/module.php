<?php
/**
 * Hero Section Module
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$title    = $data['field_hero_title'] ?? $data['title'] ?? '';
$subtitle = $data['field_hero_subtitle'] ?? $data['subtitle'] ?? '';
$image    = $data['field_hero_image'] ?? $data['background_image'] ?? [];
$height   = $data['field_hero_height'] ?? $data['height'] ?? 'large';
$button_text = $data['field_hero_button_text'] ?? $data['button_text'] ?? '';
$button_link = $data['field_hero_button_link'] ?? $data['button_link'] ?? '';
$bg_color = $data['field_hero_bg_color'] ?? $data['bg_color'] ?? '#eff5ff';

$height_class = 'hero--' . $height;
if ( $height === 'full' ) {
    $height_class = 'hero--full-screen';
}

// Get image URL
$image_url = '';
if ( ! empty( $image ) ) {
    if ( is_numeric( $image ) ) {
        // Image is an ID
        $image_url = wp_get_attachment_url( $image );
    } elseif ( is_array( $image ) && isset( $image['url'] ) ) {
        // Image is already an array with URL
        $image_url = $image['url'];
    }
}
?>

<section class="hero-section-module <?php echo esc_attr( $height_class ); ?>">
    <div class="hero-section-module__inner">
        <div class="hero-section-module__content" style="background-color: <?php echo esc_attr( $bg_color ); ?>">
            <?php if ( $title ) : ?>
                <h1 class="hero-section-module__title"><?php echo wp_kses_post( $title ); ?></h1>
            <?php endif; ?>

            <?php if ( $subtitle ) : ?>
                <p class="hero-section-module__subtitle"><?php echo wp_kses_post( $subtitle ); ?></p>
            <?php endif; ?>

            <?php if ( $button_text && $button_link ) : ?>
                <div class="hero-section-module__button">
                    <a href="<?php echo esc_url( $button_link ); ?>" class="btn btn--primary">
                        <span class="btn__text"><?php echo esc_html( $button_text ); ?></span>
                        <span class="btn__icon" aria-hidden="true">
                            <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/arrow.svg' ); ?>" alt="" />
                        </span>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <?php if ( $image_url ) : ?>
            <div class="hero-section-module__image">
                <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" />
            </div>
        <?php endif; ?>
    </div>
</section>
