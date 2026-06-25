<?php

function octarinepress_excerpt($num_words = 20, $post_id = false): string
{
    if ($post_id) {
        $excerpt_text = get_the_excerpt($post_id);
    } else {
        $excerpt_text = get_the_excerpt();
    }

    return wp_trim_words($excerpt_text, $num_words);
}

function octarinepress_categories($post_id = false, $max = false): void
{

    if ($post_id) {
        $categories = get_the_category($post_id);
    } else {
        $categories = get_the_category();
    }

    $output = '';
    $separator = '';
    if (! empty($categories)) {

        foreach ($categories as $key => $category) {
            $separator = '';
            $output .= '' . $separator . '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="label mb-2 lg:mb-0">' . esc_html($category->name) . '</a>';

            if ($max && $max === $key + 1) {
                break;
            }
        }

        echo trim($output, $separator);
    }
}
