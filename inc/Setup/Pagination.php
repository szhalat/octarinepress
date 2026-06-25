<?php

namespace octarinepress\Setup;

class Pagination
{
    public static function blog_pagination()
    {
        global $wp_query;

        $big = 999999999; // This needs to be an unlikely integer

        $paginate_links = paginate_links(
            [
                'base' => str_replace($big, '%#%', html_entity_decode(get_pagenum_link($big))),
                'current' => max(1, get_query_var('paged')),
                'total' => $wp_query->max_num_pages,
                'mid_size' => 5,
                'prev_next' => true,
                'prev_text' => '<svg width="27" height="13" viewBox="0 0 27 13" fill="none"
                                          xmlns="http://www.w3.org/2000/svg">
                                         <path d="M17.9127 7.79289C14.7806 7.79289 10.7774 6.76008 7.74804 7.66379C8.6632 8.94447 10.934 9.6726 11.1248 11.5368C11.2961 13.2616 9.93072 13.2565 8.87363 12.5696C7.17544 11.4232 5.68768 9.95662 3.91607 8.84635C2.69259 8.07691 -0.0578025 7.01828 0.000924837 5.20053C0.0547579 3.27434 2.62897 3.16073 3.91607 2.65466C5.96174 1.86456 7.86548 0.769779 9.98455 0.150093C11.9764 -0.42828 12.2113 0.780107 10.4739 1.81292C10.1363 2.00915 5.15914 4.45691 5.25212 4.63765C5.43809 4.99914 8.06124 4.91135 8.61426 4.95782C9.9503 5.06627 11.2912 5.04045 12.6273 5.16955C15.5637 5.44841 18.5 5.46907 21.4364 5.68596C22.1362 5.74276 28.4494 5.22636 26.6925 7.17837C26.0709 7.87035 23.575 7.576 22.6892 7.60698C21.1134 7.65862 19.5179 7.66379 17.9421 7.79805C16.9193 7.79289 18.8524 7.71026 17.9127 7.79289Z"
                                               fill="#434343"/>
                                     </svg>',
                'next_text' => '<svg width="26" height="13" viewBox="0 0 26 13" fill="none"
                                          xmlns="http://www.w3.org/2000/svg">
                                         <path d="M18.4759 7.67146C16.9654 7.67146 -0.432537 6.55914 0.00822999 5.02969C0.315283 3.97199 8.63044 5.23825 9.82893 5.27798C11.6811 5.3475 13.5334 5.47661 15.3905 5.50144C16.0195 5.50144 20.4866 5.20349 20.6005 5.35743C19.5159 3.90247 17.0595 3.18741 16.2473 1.53879C15.2816 -0.447504 17.5102 -0.18432 18.5601 0.520813C20.0954 1.55368 21.5316 2.81498 22.9876 3.95213C23.8939 4.66223 26.3453 5.80435 25.9591 7.33876C25.7709 8.11341 25.2756 7.96444 24.7754 8.25742C24.0285 8.66067 23.3103 9.1153 22.6261 9.61803C21.2542 10.6708 19.7586 12.1357 18.1689 12.811C16.5791 13.4863 14.7715 12.2697 15.8709 10.4672C16.5147 9.40947 19.1346 8.19783 20.2737 7.77078C19.6849 7.63199 19.0763 7.59837 18.4759 7.67146Z"
                                               fill="#434343"/>
                                     </svg>',
                'type' => 'list',
            ]
        );

        $paginate_links = str_replace("<ul class='page-numbers'>", "<ul class='pagination flex flex-wrap text-2xl items-center justify-end font-bold' role='navigation' aria-label='Paginacja'>", $paginate_links);
        $paginate_links = str_replace('<li><span class="page-numbers dots">', "<li><a href='#'>", $paginate_links);
        $paginate_links = str_replace('</span>', '</a>', $paginate_links);
        $paginate_links = str_replace("<li><span class='page-numbers text-yellow current'>", "<li class='current text-yellow'>", $paginate_links);
        $paginate_links = str_replace("<li><a href='#'>&hellip;</a></li>", "<li><span class='dots'>&hellip;</span></li>", $paginate_links);
        $paginate_links = preg_replace('/\s*page-numbers/', '', $paginate_links);

        // Display the pagination if more than one page is found.
        if ($paginate_links) {
            echo $paginate_links;
        }
    }
}
