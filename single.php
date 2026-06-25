<?php
/**
 * The template for displaying all single posts and attachments
 */
get_header(); ?>
    <main class="site-main" id="primary">
        <?php while (have_posts()) {
            the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <?php the_content(); ?>
            </article>
        <?php } ?>
    </main>


<?php get_footer();
