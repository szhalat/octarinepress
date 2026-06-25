<?php

namespace octarinepress\Setup;

class Cleanup
{
    public function register()
    {
        add_action('after_setup_theme', [$this, 'cleanup_functions']);
    }

    public function cleanup_functions()
    {
        add_action('init', [$this, 'remove_emoji']);
        add_action('init', [$this, 'remove_head_scripts']);
    }

    public function remove_emoji()
    {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_styles', 'print_emoji_styles');
    }

    public function remove_head_scripts()
    {

        // EditURI link.
        remove_action('wp_head', 'rsd_link');

        // Category feed links.
        remove_action('wp_head', 'feed_links_extra', 3);

        // Post and comment feed links.
        remove_action('wp_head', 'feed_links', 2);

        // Shortlink.
        remove_action('wp_head', 'wp_shortlink_wp_head');

        // Windows Live Writer.
        remove_action('wp_head', 'wlwmanifest_link');

        // WP version.
        remove_action('wp_head', 'wp_generator');

        // JSON api
        remove_action('wp_head', 'rest_output_link_wp_head');

        // Index link.
        remove_action('wp_head', 'index_rel_link');

        // Previous link.
        remove_action('wp_head', 'parent_post_rel_link', 10);

        // Start link.
        remove_action('wp_head', 'start_post_rel_link', 10);

        // Canonical.
        remove_action('wp_head', 'rel_canonical', 10);

        // Links for adjacent posts.
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
    }
}
