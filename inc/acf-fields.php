<?php
/**
 * ACF Field Groups Registration
 * Clean setup with flexible page modules
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function hello_child_register_acf_field_groups() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    // Template Control - Global
    acf_add_local_field_group( array(
        'key' => 'group_template_control',
        'title' => 'Template Control',
        'fields' => array(
            array(
                'key' => 'field_use_acf_template',
                'label' => 'Use ACF Template',
                'name' => 'use_acf_template',
                'type' => 'true_false',
                'instructions' => 'Enable to use ACF flexible modules instead of Elementor',
                'message' => 'Use ACF flexible modules',
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'page',
                ),
            ),
        ),
        'position' => 'side',
        'style' => 'default',
        'active' => true,
    ) );

    // Page Modules - Flexible Content
    acf_add_local_field_group( array(
        'key' => 'group_page_modules',
        'title' => 'Page Modules',
        'fields' => array(
            array(
                'key' => 'field_page_modules',
                'label' => 'Modules',
                'name' => 'page_modules',
                'type' => 'flexible_content',
                'button_label' => 'Add Module',
                'layouts' => array(
                    // Hero Section
                    array(
                        'key' => 'layout_hero_section',
                        'name' => 'hero-section',
                        'label' => 'Hero Section',
                        'display' => 'block',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_hero_title',
                                'label' => 'Title',
                                'name' => 'title',
                                'type' => 'text',
                                'required' => 1,
                            ),
                            array(
                                'key' => 'field_hero_subtitle',
                                'label' => 'Subtitle',
                                'name' => 'subtitle',
                                'type' => 'textarea',
                                'rows' => 3,
                            ),
                            array(
                                'key' => 'field_hero_image',
                                'label' => 'Background Image',
                                'name' => 'background_image',
                                'type' => 'image',
                                'return_format' => 'array',
                            ),
                            array(
                                'key' => 'field_hero_height',
                                'label' => 'Hero Height',
                                'name' => 'height',
                                'type' => 'select',
                                'choices' => array(
                                    'auto' => 'Auto',
                                    'small' => 'Small (400px)',
                                    'medium' => 'Medium (600px)',
                                    'large' => 'Large (800px)',
                                    'full' => 'Full Screen',
                                ),
                                'default_value' => 'large',
                            ),
                            array(
                                'key' => 'field_hero_button_text',
                                'label' => 'Button Text',
                                'name' => 'button_text',
                                'type' => 'text',
                            ),
                            array(
                                'key' => 'field_hero_button_link',
                                'label' => 'Button Link',
                                'name' => 'button_link',
                                'type' => 'url',
                            ),
                            array(
                                'key' => 'field_hero_bg_color',
                                'label' => 'Content Background Color',
                                'name' => 'bg_color',
                                'type' => 'color_picker',
                                'default_value' => '#eff5ff',
                            ),
                        ),
                    ),
                    // Packages Showcase
                    array(
                        'key' => 'layout_packages_showcase',
                        'name' => 'packages-showcase',
                        'label' => 'Packages Showcase',
                        'display' => 'block',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_showcase_bg_color',
                                'label' => 'Background Color',
                                'name' => 'background_color',
                                'type' => 'color_picker',
                                'instructions' => 'Pick a background color for the whole section',
                                'default_value' => '#FFFFFF',
                            ),
                            array(
                                'key' => 'field_showcase_title',
                                'label' => 'Section Title',
                                'name' => 'title',
                                'type' => 'text',
                            ),
                            array(
                                'key' => 'field_showcase_packages',
                                'label' => 'Packages',
                                'name' => 'packages',
                                'type' => 'repeater',
                                'button_label' => 'Add Package Block',
                                'layout' => 'block',
                                'sub_fields' => array(
                                    array(
                                        'key' => 'field_showcase_pkg_image',
                                        'label' => 'Image',
                                        'name' => 'image',
                                        'type' => 'image',
                                        'return_format' => 'array',
                                        'preview_size' => 'medium',
                                    ),
                                    array(
                                        'key' => 'field_showcase_pkg_image_position',
                                        'label' => 'Image Position',
                                        'name' => 'image_position',
                                        'type' => 'radio',
                                        'choices' => array(
                                            'left' => 'Image Left, Content Right',
                                            'right' => 'Image Right, Content Left',
                                        ),
                                        'default_value' => 'left',
                                        'layout' => 'horizontal',
                                    ),
                                    array(
                                        'key' => 'field_showcase_pkg_heading',
                                        'label' => 'Heading',
                                        'name' => 'heading',
                                        'type' => 'text',
                                        'required' => 1,
                                    ),
                                    array(
                                        'key' => 'field_showcase_pkg_description',
                                        'label' => 'Description',
                                        'name' => 'description',
                                        'type' => 'textarea',
                                        'rows' => 3,
                                    ),
                                    array(
                                        'key' => 'field_showcase_pkg_items',
                                        'label' => 'Items',
                                        'name' => 'items',
                                        'type' => 'repeater',
                                        'button_label' => 'Add Item',
                                        'layout' => 'table',
                                        'sub_fields' => array(
                                            array(
                                                'key' => 'field_showcase_pkg_item_icon',
                                                'label' => 'Icon',
                                                'name' => 'icon',
                                                'type' => 'image',
                                                'return_format' => 'array',
                                                'preview_size' => 'thumbnail',
                                            ),
                                            array(
                                                'key' => 'field_showcase_pkg_item_icon_color',
                                                'label' => 'Icon Color',
                                                'name' => 'icon_color',
                                                'type' => 'color_picker',
                                                'default_value' => '#FED574',
                                                'wrapper' => array(
                                                    'width' => '20',
                                                ),
                                            ),
                                            array(
                                                'key' => 'field_showcase_pkg_item_text',
                                                'label' => 'Text',
                                                'name' => 'text',
                                                'type' => 'text',
                                                'required' => 1,
                                            ),
                                        ),
                                    ),
                                    array(
                                        'key' => 'field_showcase_pkg_button_primary',
                                        'label' => 'Primary Button',
                                        'name' => 'button_primary',
                                        'type' => 'link',
                                        'instructions' => 'Get a free consultation',
                                        'return_format' => 'array',
                                    ),
                                    array(
                                        'key' => 'field_showcase_pkg_button_secondary',
                                        'label' => 'Secondary Button',
                                        'name' => 'button_secondary',
                                        'type' => 'link',
                                        'instructions' => 'Full overview',
                                        'return_format' => 'array',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    // Packages Details
                    array(
                        'key' => 'layout_packages_details',
                        'name' => 'packages-details',
                        'label' => 'Packages Details',
                        'display' => 'block',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_details_heading',
                                'label' => 'Heading',
                                'name' => 'heading',
                                'type' => 'text',
                                'instructions' => 'Center aligned block heading',
                            ),
                            array(
                                'key' => 'field_details_description',
                                'label' => 'Description',
                                'name' => 'description',
                                'type' => 'textarea',
                                'rows' => 3,
                                'instructions' => 'Center aligned subheading/description text',
                            ),
                            array(
                                'key' => 'field_details_packages',
                                'label' => 'Select Packages',
                                'name' => 'packages',
                                'type' => 'relationship',
                                'post_type' => array( 'package' ),
                                'filters' => array( 'search', 'taxonomy' ),
                                'elements' => array( 'featured_image' ),
                                'return_format' => 'id',
                                'min' => 1,
                                'instructions' => 'Select which Packages (CPT) to compare. Drag & drop to reorder.',
                            ),
                            array(
                                'key' => 'field_details_additional_package',
                                'label' => 'Additional Package',
                                'name' => 'additional_package',
                                'type' => 'relationship',
                                'post_type' => array( 'package' ),
                                'filters' => array( 'search', 'taxonomy' ),
                                'elements' => array( 'featured_image' ),
                                'return_format' => 'id',
                                'max' => 1,
                                'instructions' => 'Optional: select one additional package to display horizontally below the comparison grid.',
                            ),
                        ),
                    ),
                    // CTA Package
                    array(
                        'key' => 'layout_cta_package',
                        'name' => 'cta-package',
                        'label' => 'CTA Package',
                        'display' => 'block',
                        'sub_fields' => array(
                            // Content Tab
                            array(
                                'key' => 'field_cta_pkg_content_tab',
                                'label' => 'Content',
                                'name' => '',
                                'type' => 'tab',
                                'placement' => 'top',
                            ),
                            array(
                                'key' => 'field_cta_pkg_heading',
                                'label' => 'Heading',
                                'name' => 'heading',
                                'type' => 'text',
                                'placeholder' => 'Looking for something different?',
                            ),
                            array(
                                'key' => 'field_cta_pkg_subheading',
                                'label' => 'Subheading',
                                'name' => 'subheading',
                                'type' => 'text',
                                'placeholder' => 'Check our targeted check-up',
                            ),
                            array(
                                'key' => 'field_cta_pkg_content',
                                'label' => 'Content',
                                'name' => 'content',
                                'type' => 'textarea',
                                'rows' => 4,
                                'placeholder' => 'Enjoy a personalized check-up according to your needs & wishes!',
                            ),
                            array(
                                'key' => 'field_cta_pkg_button_text',
                                'label' => 'Button Text',
                                'name' => 'button_text',
                                'type' => 'text',
                                'placeholder' => 'Learn more',
                            ),
                            array(
                                'key' => 'field_cta_pkg_button_link',
                                'label' => 'Button Link',
                                'name' => 'button_link',
                                'type' => 'url',
                            ),
                            array(
                                'key' => 'field_cta_pkg_image',
                                'label' => 'Image',
                                'name' => 'image',
                                'type' => 'image',
                                'return_format' => 'array',
                                'preview_size' => 'medium',
                            ),
                            // Style Tab
                            array(
                                'key' => 'field_cta_pkg_style_tab',
                                'label' => 'Style',
                                'name' => '',
                                'type' => 'tab',
                                'placement' => 'top',
                            ),
                            array(
                                'key' => 'field_cta_pkg_bg_block_color',
                                'label' => 'Background Block Color',
                                'name' => 'bg_block_color',
                                'type' => 'color_picker',
                                'default_value' => '#eff5ff',
                                'instructions' => 'Outer container background',
                            ),
                            array(
                                'key' => 'field_cta_pkg_bg_inner_color',
                                'label' => 'Background Inner Color',
                                'name' => 'bg_inner_color',
                                'type' => 'color_picker',
                                'default_value' => '#FFFFFF',
                                'instructions' => 'Inner content background (image side)',
                            ),
                            array(
                                'key' => 'field_cta_pkg_heading_color',
                                'label' => 'Heading Color',
                                'name' => 'heading_color',
                                'type' => 'color_picker',
                                'default_value' => '#323f53',
                            ),
                            array(
                                'key' => 'field_cta_pkg_subheading_color',
                                'label' => 'Subheading Color',
                                'name' => 'subheading_color',
                                'type' => 'color_picker',
                                'default_value' => '#262f3e',
                            ),
                            array(
                                'key' => 'field_cta_pkg_button_text_color',
                                'label' => 'Button Text Color',
                                'name' => 'button_text_color',
                                'type' => 'color_picker',
                                'default_value' => '#FFFFFF',
                            ),
                            array(
                                'key' => 'field_cta_pkg_button_bg_color',
                                'label' => 'Button Background Color',
                                'name' => 'button_bg_color',
                                'type' => 'color_picker',
                                'default_value' => '#323f53',
                            ),
                        ),
                    ),
                    // Testimonials
                    array(
                        'key' => 'layout_testimonials',
                        'name' => 'testimonials',
                        'label' => 'Testimonials',
                        'display' => 'block',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_testimonials_title',
                                'label' => 'Section Title',
                                'name' => 'title',
                                'type' => 'text',
                            ),
                            array(
                                'key' => 'field_testimonials_rating_text',
                                'label' => 'Rating Text',
                                'name' => 'rating_text',
                                'type' => 'text',
                                'default_value' => 'Rated 5/5 Based on 109 reviews',
                            ),
                            array(
                                'key' => 'field_testimonials_shortcode',
                                'label' => 'Shortcode',
                                'name' => 'shortcode',
                                'type' => 'text',
                            ),
                        ),
                    ),
                    // CTA Section
                    array(
                        'key' => 'layout_cta_section',
                        'name' => 'cta-section',
                        'label' => 'CTA Section',
                        'display' => 'block',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_cta_heading',
                                'label' => 'Heading',
                                'name' => 'heading',
                                'type' => 'text',
                                'required' => 1,
                            ),
                            array(
                                'key' => 'field_cta_content',
                                'label' => 'Content',
                                'name' => 'content',
                                'type' => 'wysiwyg',
                                'toolbar' => 'basic',
                                'media_upload' => 0,
                            ),
                            array(
                                'key' => 'field_cta_features',
                                'label' => 'Features',
                                'name' => 'features',
                                'type' => 'repeater',
                                'button_label' => 'Add Feature',
                                'layout' => 'block',
                                'sub_fields' => array(
                                    array(
                                        'key' => 'field_cta_feature_icon',
                                        'label' => 'Icon',
                                        'name' => 'icon',
                                        'type' => 'image',
                                        'return_format' => 'array',
                                    ),
                                    array(
                                        'key' => 'field_cta_feature_title',
                                        'label' => 'Title',
                                        'name' => 'title',
                                        'type' => 'text',
                                        'required' => 1,
                                    ),
                                    array(
                                        'key' => 'field_cta_feature_text',
                                        'label' => 'Description',
                                        'name' => 'description',
                                        'type' => 'textarea',
                                        'rows' => 2,
                                    ),
                                ),
                            ),
                            array(
                                'key' => 'field_cta_image',
                                'label' => 'Image',
                                'name' => 'image',
                                'type' => 'image',
                                'return_format' => 'array',
                                'preview_size' => 'large',
                            ),
                            array(
                                'key' => 'field_cta_button_text',
                                'label' => 'Button Text',
                                'name' => 'button_text',
                                'type' => 'text',
                                'default_value' => 'Get a free consultation',
                            ),
                            array(
                                'key' => 'field_cta_button_link',
                                'label' => 'Button Link',
                                'name' => 'button_link',
                                'type' => 'link',
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'page',
                ),
            ),
        ),
        'position' => 'normal',
        'style' => 'default',
        'active' => true,
        'description' => 'Flexible content page modules',
    ) );

    // Packages CPT Fields
    acf_add_local_field_group( array(
        'key' => 'group_packages_cpt_fields',
        'title' => 'Package Details',
        'fields' => array(
            array(
                'key' => 'field_package_price',
                'label' => 'Price',
                'name' => 'price',
                'type' => 'number',
                'required' => 1,
                'min' => 0,
                'step' => 0.01,
                'prepend' => '€',
            ),
            array(
                'key' => 'field_package_short_description',
                'label' => 'Short Description',
                'name' => 'short_description',
                'type' => 'textarea',
                'rows' => 3,
                'instructions' => 'Optional: short summary used in listings/cards. Full description can be written in the main editor above.',
            ),
            array(
                'key' => 'field_package_bg_color',
                'label' => 'Package Background Color',
                'name' => 'package_bg_color',
                'type' => 'color_picker',
                'instructions' => 'Background color for the package sections accordion on single package page',
                'default_value' => '#eff5ff',
                'wrapper' => array(
                    'width' => '33',
                ),
            ),
            array(
                'key' => 'field_package_text_color',
                'label' => 'Package Text Color',
                'name' => 'package_text_color',
                'type' => 'color_picker',
                'instructions' => 'Text color for the package sections accordion',
                'default_value' => '#262f3e',
                'wrapper' => array(
                    'width' => '33',
                ),
            ),
            array(
                'key' => 'field_package_icon_bg_color',
                'label' => 'Package Icon Background Color',
                'name' => 'package_icon_bg_color',
                'type' => 'color_picker',
                'instructions' => 'Background color for checkmark icons (circle background)',
                'default_value' => '#323f53',
                'wrapper' => array(
                    'width' => '33',
                ),
            ),
            array(
                'key' => 'field_package_icon_color',
                'label' => 'Package Icon Color',
                'name' => 'package_icon_color',
                'type' => 'color_picker',
                'instructions' => 'Color for the checkmark symbol itself',
                'default_value' => '#ffffff',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_package_arrow_color',
                'label' => 'Package Arrow Color',
                'name' => 'package_arrow_color',
                'type' => 'color_picker',
                'instructions' => 'Color for accordion arrow icons',
                'default_value' => '#262f3e',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_package_is_additional',
                'label' => 'Additional Package',
                'name' => 'is_additional_package',
                'type' => 'true_false',
                'message' => 'Mark this as an additional package (displays separately in comparison tables)',
                'ui' => 1,
                'default_value' => 0,
            ),
            array(
                'key' => 'field_package_cta_heading',
                'label' => 'CTA Heading',
                'name' => 'package_cta_heading',
                'type' => 'text',
                'instructions' => 'Heading for the call-to-action section (e.g., "Ready to choose this package?")',
                'default_value' => 'Ready to choose this package?',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_package_cta_subheading',
                'label' => 'CTA Subheading',
                'name' => 'package_cta_subheading',
                'type' => 'text',
                'instructions' => 'Subheading for the call-to-action section',
                'default_value' => 'Contact us today for a free consultation and booking',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_package_cta_image',
                'label' => 'CTA Image',
                'name' => 'package_cta_image',
                'type' => 'image',
                'instructions' => 'Image for the CTA section',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_package_cta_button_text',
                'label' => 'CTA Button Text',
                'name' => 'package_cta_button_text',
                'type' => 'text',
                'instructions' => 'Button text for CTA',
                'default_value' => 'Book Now',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_package_cta_button_link',
                'label' => 'CTA Button Link',
                'name' => 'package_cta_button_link',
                'type' => 'link',
                'instructions' => 'Link for the CTA button',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_package_testimonials_title',
                'label' => 'Testimonials Title',
                'name' => 'package_testimonials_title',
                'type' => 'text',
                'instructions' => 'Title for testimonials section',
                'default_value' => 'What Our Patients Say',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_package_testimonials_shortcode',
                'label' => 'Testimonials Shortcode',
                'name' => 'package_testimonials_shortcode',
                'type' => 'text',
                'instructions' => 'Testimonials slider shortcode (e.g., [testimonials_slider])',
                'default_value' => '',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_package_final_cta_heading',
                'label' => 'Final CTA Heading',
                'name' => 'package_final_cta_heading',
                'type' => 'text',
                'instructions' => 'Main heading for the final CTA section',
                'default_value' => 'Guiding your journey to better health, meet TheMedicorner®',
            ),
            array(
                'key' => 'field_package_final_cta_features',
                'label' => 'Final CTA Features',
                'name' => 'package_final_cta_features',
                'type' => 'repeater',
                'instructions' => 'Add features for the final CTA section',
                'layout' => 'block',
                'button_label' => 'Add Feature',
                'sub_fields' => array(
                    array(
                        'key' => 'field_package_final_cta_feature_icon',
                        'label' => 'Icon',
                        'name' => 'icon',
                        'type' => 'image',
                        'return_format' => 'array',
                        'preview_size' => 'thumbnail',
                    ),
                    array(
                        'key' => 'field_package_final_cta_feature_title',
                        'label' => 'Title',
                        'name' => 'title',
                        'type' => 'text',
                    ),
                    array(
                        'key' => 'field_package_final_cta_feature_description',
                        'label' => 'Description',
                        'name' => 'description',
                        'type' => 'textarea',
                        'rows' => 3,
                    ),
                ),
            ),
            array(
                'key' => 'field_package_final_cta_button_text',
                'label' => 'Final CTA Button Text',
                'name' => 'package_final_cta_button_text',
                'type' => 'text',
                'instructions' => 'Button text for the final CTA',
                'default_value' => 'Schedule Appointment',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_package_final_cta_button_link',
                'label' => 'Final CTA Button Link',
                'name' => 'package_final_cta_button_link',
                'type' => 'link',
                'instructions' => 'Link for the final CTA button',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_package_final_cta_image',
                'label' => 'Final CTA Image',
                'name' => 'package_final_cta_image',
                'type' => 'image',
                'instructions' => 'Image for the right side of the CTA section',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'wrapper' => array(
                    'width' => '50',
                ),
            ),
            array(
                'key' => 'field_package_include_sections',
                'label' => 'Include Sections',
                'name' => 'include_sections',
                'type' => 'repeater',
                'layout' => 'block',
                'button_label' => 'Add Section',
                'collapsed' => 'field_package_section_title_line_1',
                'sub_fields' => array(
                    array(
                        'key' => 'field_package_section_title_line_1',
                        'label' => 'Title (Line 1)',
                        'name' => 'title_line_1',
                        'type' => 'text',
                        'required' => 1,
                        'wrapper' => array( 'width' => '50' ),
                    ),
                    array(
                        'key' => 'field_package_section_title_line_2',
                        'label' => 'Title (Line 2)',
                        'name' => 'title_line_2',
                        'type' => 'text',
                        'required' => 0,
                        'wrapper' => array( 'width' => '50' ),
                    ),
                    array(
                        'key' => 'field_package_section_items',
                        'label' => 'Items',
                        'name' => 'items',
                        'type' => 'repeater',
                        'layout' => 'table',
                        'button_label' => 'Add Item',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_package_section_item_text',
                                'label' => 'Item',
                                'name' => 'text',
                                'type' => 'text',
                                'required' => 1,
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'package',
                ),
            ),
        ),
        'position' => 'normal',
        'style' => 'default',
        'active' => true,
    ) );
}

/**
 * Seed default include sections on new Packages (auto-draft) so the editor starts
 * with the 4 required sections, while still allowing adding more.
 */
add_filter( 'acf/load_value/name=include_sections', function( $value, $post_id, $field ) {
    if ( ! empty( $value ) ) {
        return $value;
    }

    if ( ! is_admin() ) {
        return $value;
    }

    $post_id_int = is_numeric( $post_id ) ? intval( $post_id ) : 0;
    if ( ! $post_id_int ) {
        return $value;
    }

    if ( get_post_type( $post_id_int ) !== 'package' ) {
        return $value;
    }

    // Only seed on the initial auto-draft creation.
    if ( get_post_status( $post_id_int ) !== 'auto-draft' ) {
        return $value;
    }

    return array(
        array(
            'title_line_1' => 'Medical',
            'title_line_2' => 'Examinations',
            'items' => array(),
        ),
        array(
            'title_line_1' => 'Cardiology',
            'title_line_2' => 'Laboratory',
            'items' => array(),
        ),
        array(
            'title_line_1' => 'Radiology & Functional Tests',
            'title_line_2' => '',
            'items' => array(),
        ),
        array(
            'title_line_1' => 'Biochemistry',
            'title_line_2' => 'Laboratory',
            'items' => array(),
        ),
    );
}, 10, 3 );

add_action( 'acf/init', 'hello_child_register_acf_field_groups' );
