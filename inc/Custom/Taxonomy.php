<?php

namespace octarinepress\Custom;

class Taxonomy
{
    public function register()
    {
        add_action('init', [$this, 'custom_taxonomy'], 10, 4);
    }

    public function custom_taxonomy()
    {
        /**
         * Add the post types and their details
         */
        $custom_taxonomies = [
            //			array(
            //				'name'                => OCTARINEPRESS_SHORT . 'item_category',
            //				'slug'                => 'item-category',
            //				'singular'            => 'Item Category',
            //				'plural'              => 'Item Categories',
            //				'custom_post_type'    => OCTARINEPRESS_SHORT . 'items',
            //				'text_domain'         => 'octarinepress',
            //				'public'              => true,
            //				'show_in_nav_menus'   => true,
            //				'show_in_quick_edit'  => false,
            //				'publicly_queryable'  => false,
            //				'show_ui'             => true,
            //				'show_in_menu'        => true,
            //				'query_var'           => true,
            //				'hierarchical'        => true,
            //				'show_in_rest'        => true,
            //				'exclude_from_search' => true,
            //				'show_tagcloud'       => false,
            //				'labels'              => array(
            //					'name'          => __( 'Item Categories' ),
            //					'singular_name' => __( 'Item Category' ),
            //					'add_new'       => __( 'Add' ),
            //					'add_new_item'  => __( 'Add' ),
            //					'new_item'      => __( 'New' ),
            //					'edit_item'     => __( 'Edit' ),
            //					'view_item'     => __( 'View' ),
            //					'view_items'    => __( 'View' ),
            //					'all_items'     => __( 'All' ),
            //					'search_items'  => __( 'Search' ),
            //				)
            //			),

        ];

        foreach ($custom_taxonomies as $custom_taxonomy) {
            $args = [
                'public' => $custom_taxonomy['public'],
                'show_in_nav_menus' => $custom_taxonomy['show_in_nav_menus'],
                'publicly_queryable' => $custom_taxonomy['publicly_queryable'],
                'show_ui' => $custom_taxonomy['show_ui'],
                'show_in_menu' => $custom_taxonomy['show_in_menu'],
                'query_var' => $custom_taxonomy['query_var'],
                'rewrite' => ['slug' => $custom_taxonomy['slug']],
                'hierarchical' => $custom_taxonomy['hierarchical'],
                'show_in_rest' => $custom_taxonomy['show_in_rest'],
                'show_tagcloud' => $custom_taxonomy['show_tagcloud'],
            ];

            if (isset($custom_taxonomy['labels'])) {
                $args['labels'] = $custom_taxonomy['labels'];
            }
            if (isset($custom_taxonomy['meta_box_cb'])) {
                $args['meta_box_cb'] = $custom_taxonomy['meta_box_cb'];
            }

            register_taxonomy($custom_taxonomy['name'], $custom_taxonomy['custom_post_type'], $args);
        }
    }
}
