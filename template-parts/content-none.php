<?php
/**
 * Template part for displaying a message when no posts are found.
 */
?>

<section class="no-results not-found">
    <header class="page-header">
        <h1 class="page-title"><?php esc_html_e('Nothing here', 'octarinepress'); ?></h1>
    </header>

    <div class="page-content">
		<?php if (is_home() && current_user_can('publish_posts')) { ?>

            <p>
				<?php
                printf(
                    wp_kses(
                        /* translators: %s: URL to the WordPress editor for a new post. */
                        __('Ready to publish your first post? <a href="%s">Get started here</a>.', 'octarinepress'),
                        ['a' => ['href' => []]]
                    ),
                    esc_url(admin_url('post-new.php'))
                );
		    ?>
            </p>

		<?php } elseif (is_search()) { ?>

            <p><?php esc_html_e('Sorry, nothing matched your search terms. Please try again with some different keywords.', 'octarinepress'); ?></p>
			<?php get_search_form(); ?>

		<?php } else { ?>

            <p><?php esc_html_e('It seems we cannot find what you are looking for. Perhaps searching can help.', 'octarinepress'); ?></p>
			<?php get_search_form(); ?>

		<?php } ?>
    </div>
</section>