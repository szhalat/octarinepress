<?php

namespace octarinepress\Api;

class Gutenberg
{
    public function register(): void
    {

        if (! function_exists('register_block_type')) {
            return;
        }

        add_action('init', [$this, 'setup']);

        add_filter('block_categories_all', [$this, 'theme_block_category']);

        add_action('init', [$this, 'register_all_blocks']);

    }

    public function setup(): void
    {
        // add css style for editor admin
        add_theme_support('editor-styles');
        add_editor_style('style-editor.css');

        // add default block style
        add_theme_support('wp-block-styles');

        // responsive embed
        add_theme_support('responsive-embeds');

        // remove template support
        remove_theme_support('block-templates');

        /** PATTERNS SECTION */

        // add category for theme patterns
        register_block_pattern_category('octarinepress/octarinepress-patterns', ['label' => __('OctarinePress patterns', 'octarinepress')]);
        // add theme support for the core-block-patterns
        // add_theme_support('core-block-patterns');

        // or remove the theme support for the core-block-patterns
        remove_theme_support('core-block-patterns');

        // remove remote patterns
        add_filter('should_load_remote_block_patterns', '__return_false');

        // unregister_block_pattern_category('buttons');
        // unregister_block_pattern_category('query');
        // unregister_block_pattern_category('header');
        // unregister_block_pattern_category('footer');
    }

    public function theme_block_category(array $categories): array
    {
        $custom_block = [
            'slug' => OCTARINEPRESS_THEME_CATEGORY,
            'title' => __('Theme sections', 'octarinepress'),
        ];

        return array_merge([$custom_block], $categories);
    }

    public function register_all_blocks(): void
    {
        $blocks = [
            'hero-banner',
            'two-columns',
            'content-grid',
            'column',
            'image',

        ];

        foreach ($blocks as $block_name) {
            $block_dir = get_template_directory() . '/blocks/' . $block_name;
            $block_json = $block_dir . '/block.json';
            $render_file = $block_dir . '/render.php';

            if (! file_exists($block_json)) {
                continue;
            }

            $args = [
                'editor_script' => 'octarinepress-blocks-js',
            ];

            if (file_exists($render_file)) {
                $args['render_callback'] = function ($attributes, $content) use ($render_file) {
                    ob_start();
                    include $render_file;

                    return ob_get_clean();
                };
            }

            register_block_type($block_json, $args);
        }
    }
}
