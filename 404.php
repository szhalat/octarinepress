<?php
/**
 * The template for displaying pages
 */
get_header(); ?>


    <main id="primary" class="site-main w-full">

        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="page-header bg-black text-white mb-24 md:mb-32 lg:mb-16">
                <header class="max-w-4xl mx-auto px-4 text-center py-8">
                    <h1 class="entry-title mb-2 uppercase"><?php esc_html_e('Error 404', 'octarinepress'); ?></h1>
                </header>
            </div>
            <div class="container grid lg:grid-cols-2 items-center gap-8 lg:pt-16">
                <div>
                    <h2 class="text-3xl lg:text-8xl leading-none"><?php esc_html_e('Houston, we have a problem.', 'octarinepress'); ?></h2>
                    <p class="text-2xl"><?php esc_html_e('The page you are looking for does not exist. Return to the homepage and try again.', 'octarinepress'); ?></p>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-main px-4"><span><?php esc_html_e('Back to homepage', 'octarinepress'); ?></span></a>
                </div>

            </div>
        </article>

    </main>

<?php get_footer();
