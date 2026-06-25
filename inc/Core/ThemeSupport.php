<?php

namespace octarinepress\Core;

class ThemeSupport
{
    public function register()
    {
        add_action('after_setup_theme', [$this, 'theme_support']);
        add_filter('next_post_link', [$this, 'posts_link_next']);
        add_filter('previous_post_link', [$this, 'posts_link_previous']);
        add_filter('xmlrpc_enabled', '__return_false');

    }

    public function theme_support()
    {

        load_theme_textdomain('octarinepress', get_template_directory() . '/languages');

        // Switch default core markup for search form, comment form, and comments to output valid HTML5
        add_theme_support(
            'html5', [
                'search-form',
                'comment-form',
                'comment-list',
                'gallery',
                'caption',
            ]
        );

        // Add custom logo support
        add_theme_support('custom-logo');

        // Add menu support
        add_theme_support('menus');

        // Let WordPress manage the document title
        add_theme_support('title-tag');

        // Add post thumbnail support: http://codex.wordpress.org/Post_Thumbnails
        add_theme_support('post-thumbnails');

        // RSS thingy
        add_theme_support('automatic-feed-links');

    }

    public function posts_link_previous($html)
    {
        return str_replace('<a', '<a class="btn btn-yellow btn-previous"', $html);
    }

    public function posts_link_next($html)
    {
        return str_replace('<a', '<a class="btn btn-yellow btn-next"', $html);
    }
}
