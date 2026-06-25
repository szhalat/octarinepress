<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 */
$privacy_policy = get_option('wp_page_for_privacy_policy');

?>
<div class="footer-top">
    <?php dynamic_sidebar('footer-widgets'); ?>
</div>
<footer class="page-footer container-grid py-8">

    <div class="col-start-[content-start] col-end-[content-end] flex justify-between flex-wrap">
        <a href="<?php echo esc_url(home_url('/')); ?>" rel="home"
           class="font-bold text-xl flex items-end site-logo">
            <span class="sr-only"><?php bloginfo('name'); ?></span>

        </a>
	    </div>


</footer>
</div><!-- #content close -->
<?php wp_footer(); ?>

</body>
</html>
