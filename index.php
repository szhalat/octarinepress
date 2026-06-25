<?php
/**
 * The main template file.
 *
 * This is the most generic template in the WordPress template hierarchy and the
 * final fallback used when no more specific template matches the current query
 * (home, archive, search, and so on). Keep it design-neutral; put opinionated
 * layouts in the more specific templates such as archive.php or home.php.
 */

use octarinepress\Setup\Pagination;

get_header();
?>
    <div class="container">
        <main id="primary" class="site-main">

			<?php if (have_posts()) { ?>

				<?php if (is_home() && ! is_front_page()) { ?>
                    <header class="page-header">
                        <h1 class="page-title"><?php single_post_title(); ?></h1>
                    </header>
				<?php } ?>

				<?php
                while (have_posts()) {
                    the_post();

                    /*
                     * Loads template-parts/content-{post-type}.php and falls back to
                     * template-parts/content.php when a type-specific part is absent.
                     */
                    get_template_part('template-parts/content', get_post_type());
                }
			    ?>

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
