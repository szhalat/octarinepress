<?php

use octarinepress\Custom\RelatedPosts;

$related_posts = new RelatedPosts;

$related = $related_posts->get_related_posts(get_the_ID(), 3);

if ($related->have_posts()) { ?>
    <section class="related-posts container">
        <header class="mb-8">
            <span class="font-semibold font-body text-34"><?php esc_html_e('Related posts:', 'octarinepress'); ?></span>
        </header>
        <div class="posts-cards grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
			<?php while ($related->have_posts()) {
			    $related->the_post(); ?>
				<?php get_template_part('template-parts/blog/post-card'); ?>
			<?php } ?>
        </div>
    </section>
<?php }
wp_reset_postdata();
