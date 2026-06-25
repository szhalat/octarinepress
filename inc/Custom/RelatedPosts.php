<?php

namespace octarinepress\Custom;

use WP_Query;

class RelatedPosts
{
    public function get_related_posts($post_id, $related_count, $args = []): WP_Query
    {
        $terms = get_the_terms($post_id, 'category');

        if (empty($terms)) {
            $terms = [];
        }

        $term_list = wp_list_pluck($terms, 'slug');

        $related_args = [
            'post_type' => 'post',
            'posts_per_page' => $related_count,
            'post_status' => 'publish',
            'post__not_in' => [$post_id],
            'orderby' => 'rand',
            'tax_query' => [
                [
                    'taxonomy' => 'category',
                    'field' => 'slug',
                    'terms' => $term_list,
                ],
            ],
        ];

        return new WP_Query($related_args);
    }
}
