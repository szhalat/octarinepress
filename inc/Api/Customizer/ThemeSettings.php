<?php

namespace octarinepress\Api\Customizer;

use WP_Customize_Control;

class ThemeSettings
{
    public function register($wp_customize)
    {
        $wp_customize->add_panel('octarinepress_header_block', [
            'priority' => 500,
            'theme_supports' => '',
            'title' => __('Theme settings', 'octarinepress'),
            'description' => __('Header and footer sections', 'octarinepress'),
        ]);

        $wp_customize->add_section('octarinepress_header_buttons', [
            'title' => __('Menu buttons', 'octarinepress'),
            'panel' => 'octarinepress_header_block',
            'priority' => 10,
        ]);

        $wp_customize->add_setting('octarinepress_header_button_1_text', [
            'sanitize_callback' => 'wp_filter_nohtml_kses',
        ]);

        $wp_customize->add_control(new WP_Customize_Control(
            $wp_customize,
            'octarinepress_header_button_1_text',
            [
                'label' => __('Button text', 'octarinepress'),
                'section' => 'octarinepress_header_buttons',
                'settings' => 'octarinepress_header_button_1_text',
                'type' => 'text',
            ]
        )
        );

        $wp_customize->add_setting('octarinepress_header_button_1_url', [
            'sanitize_callback' => 'esc_url_raw',
        ]);

        $wp_customize->add_control(new WP_Customize_Control(
            $wp_customize,
            'octarinepress_header_button_1_url',
            [
                'label' => __('Button URL', 'octarinepress'),
                'section' => 'octarinepress_header_buttons',
                'settings' => 'octarinepress_header_button_1_url',
                'type' => 'url',
            ]
        )
        );

        $wp_customize->add_section('octarinepress_header_social', [
            'title' => __('Social media', 'octarinepress'),
            'panel' => 'octarinepress_header_block',
            'priority' => 10,
        ]);

        //facebook
        $wp_customize->add_setting('octarinepress_social_facebook', [
            'sanitize_callback' => 'esc_url_raw',
        ]);

        $wp_customize->add_control(new WP_Customize_Control(
            $wp_customize,
            'octarinepress_social_facebook',
            [
                'label' => __('Facebook', 'octarinepress'),
                'section' => 'octarinepress_header_social',
                'settings' => 'octarinepress_social_facebook',
                'type' => 'url',
            ]
        )
        );

        //facebook groups
        $wp_customize->add_setting('octarinepress_social_facebook_group', [
            'sanitize_callback' => 'esc_url_raw',
        ]);

        $wp_customize->add_control(new WP_Customize_Control(
            $wp_customize,
            'octarinepress_social_facebook_group',
            [
                'label' => __('Facebook group', 'octarinepress'),
                'section' => 'octarinepress_header_social',
                'settings' => 'octarinepress_social_facebook_group',
                'type' => 'url',
            ]
        )
        );

        $wp_customize->add_setting('octarinepress_social_instagram', [
            'sanitize_callback' => 'esc_url_raw',
        ]);

        $wp_customize->add_control(new WP_Customize_Control(
            $wp_customize,
            'octarinepress_social_instagram',
            [
                'label' => __('Instagram', 'octarinepress'),
                'section' => 'octarinepress_header_social',
                'settings' => 'octarinepress_social_instagram',
                'type' => 'url',
            ]
        )
        );

        $wp_customize->add_setting('octarinepress_social_linkedin', [
            'sanitize_callback' => 'esc_url_raw',
        ]);

        $wp_customize->add_control(new WP_Customize_Control(
            $wp_customize,
            'octarinepress_social_linkedin',
            [
                'label' => __('LinkedIn', 'octarinepress'),
                'section' => 'octarinepress_header_social',
                'settings' => 'octarinepress_social_linkedin',
                'type' => 'url',
            ]
        )
        );

    }
}
