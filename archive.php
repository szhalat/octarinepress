<?php
/**
 * The template for displaying archive pages.
 *
 * Used for category, tag, author, custom taxonomy, and date-based archives.
 */

use octarinepress\Setup\Pagination;

get_header();
?>

        <div class="max-w-4xl mx-auto px-4 text-center pb-8">
            <h1 class="entry-title">
				<?php
                if (is_category()) {
                    single_cat_title();
                } elseif (is_tag()) {
                    single_tag_title();
                } elseif (is_author()) {
                    the_post();
                    echo esc_html(get_the_author());
                    rewind_posts();
                } elseif (is_day()) {
                    echo esc_html(get_the_date());
                } elseif (is_month()) {
                    echo esc_html(get_the_date('F Y'));
                } elseif (is_year()) {
                    echo esc_html(get_the_date('Y'));
                } else {
                    esc_html_e('Archives', 'octarinepress');
                }
?>
            </h1>
        </div>


    <div class="container">
        <main id="primary" class="site-main">

			<?php if (have_posts()) { ?>

                <section class="grid md:grid-cols-2 lg:grid-cols-4 2xl:grid-cols-5 gap-8">
					<?php
    while (have_posts()) {
        the_post();
        get_template_part('template-parts/blog/post-card');
    }
			    ?>
                </section>

                <div class="mt-8 mb-20">
					<?php Pagination::blog_pagination(); ?>
                </div>

			<?php } else { ?>

				<?php get_template_part('template-parts/content', 'none'); ?>

			<?php } ?>

        </main>
    </div>
<?php
get_footer();
