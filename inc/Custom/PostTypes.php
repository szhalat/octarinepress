<?php

namespace octarinepress\Custom;

class PostTypes
{
    public function register()
    {
        add_action('init', [$this, 'custom_post_type'], 10, 4);
        add_action('after_switch_theme', [$this, 'rewrite_flush']);
        if (is_admin()) {
            add_filter('enter_title_here', [$this, 'change_post_enter_title']);
        }

    }

    public function custom_post_type()
    {
        /**
         * Add the post types and their details
         */
        $custom_posts = [
            //            array(
            //                'name' => OCTARINEPRESS_SHORT . 'items',
            //                'slug' => 'items',
            //                'singular' => __('Item', 'octarinepress'),
            //                'plural' => __('Items', 'octarinepress'),
            //                'menu_icon' => 'dashicons-admin-post',
            //                'menu_position' => 18,
            //                'text_domain' => 'octarinepress',
            //                'supports' => array('title', 'thumbnail', 'editor', 'excerpt', 'revisions', 'custom-fields', 'page-attributes'),
            //                'description' => '',
            //                'public' => true,
            //                'publicly_queryable' => true,
            //                'exclude_from_search' => true,
            //                'show_ui' => true,
            //                'show_in_menu' => true,
            //                'query_var' => true,
            //                'capability_type' => 'post',
            //                'has_archive' => false,
            //                'hierarchical' => false,
            //                'show_in_rest' => true,
            //                'labels' => array(
            //                    'name' => __('Items', 'octarinepress'),
            //                    'singular_name' => __('Item', 'octarinepress'),
            //                    'menu_name' => __('Items', 'octarinepress'),
            //                    'name_admin_bar' => __('Item', 'octarinepress'),
            //                    'add_new' => __('Add'),
            //                    'add_new_item' => __('Add'),
            //                    'new_item' => __('New'),
            //                    'edit_item' => __('Edit'),
            //                    'view_item' => __('View'),
            //                    'view_items' => __('View'),
            //                    'all_items' => __('All'),
            //                    'search_items' => __('Search'),
            //                    'parent_item_colon' => __('Parent item', 'octarinepress'),
            //                    'not_found' => __('No items found.', 'octarinepress'),
            //                    'not_found_in_trash' => __('No items found in Trash.', 'octarinepress'),
            //                    'featured_image' => __('Featured image', 'octarinepress'),
            //                    'set_featured_image' => __('Set featured image', 'octarinepress'),
            //                )
            //            ),
        ];

        foreach ($custom_posts as $custom_post) {
            $args = [
                'labels' => $custom_post['labels'],
                'description' => __($custom_post['description'], $custom_post['text_domain']),
                'public' => $custom_post['public'],
                'publicly_queryable' => $custom_post['publicly_queryable'],
                'show_ui' => $custom_post['show_ui'],
                'show_in_menu' => $custom_post['show_in_menu'],
                'menu_icon' => $custom_post['menu_icon'],
                'query_var' => $custom_post['publicly_queryable'] ? $custom_post['query_var'] : false,
                'rewrite' => $custom_post['publicly_queryable']
                    ? [
                        'slug' => $custom_post['slug'],
                        'with_front' => false,
                    ]
                    : false,
                'capability_type' => $custom_post['capability_type'],
                'has_archive' => $custom_post['has_archive'],
                'hierarchical' => $custom_post['hierarchical'],
                'menu_position' => $custom_post['menu_position'],
                'supports' => $custom_post['supports'],
                'show_in_rest' => $custom_post['show_in_rest'],
            ];

            register_post_type($custom_post['name'], $args);
        }
    }

    public function rewrite_flush()
    {

        flush_rewrite_rules();
    }

    public function change_post_enter_title($input)
    {
        return $input;
    }
}
