<?php
/**
 * The template for displaying the header
 *
 * Displays all of the head element and everything up until the "container" div.
 */

use octarinepress\Setup\Navigation;

?>
<!doctype html>
<html class="no-js" <?php language_attributes(); ?> >
<head>
    <meta charset="<?php bloginfo('charset'); ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="preload" as="font"
          href="<?php echo octarine_assets('fonts/Inter-VariableFont.woff2'); ?>"
          type="font/woff2" crossorigin="anonymous">
    <?php wp_head(); ?>
    <script>
        document.documentElement.className = document.documentElement.className.replace("no-js", "js");
    </script>

</head>
<body <?php body_class(); ?>>
<div id="page" class="site"> <!-- #content start -->
    <header id="masthead" class="site-header container-grid">
        <nav class="col-start-[content-start] col-end-[content-end] py-8 font-body">
            <div class="flex flex-wrap items-center justify-between">
                <div>
                    <a href="<?php echo esc_url(home_url('/')); ?>" rel="home"
                       class="font-bold text-xl flex items-end site-logo">
                        <span class="sr-only"><?php bloginfo('name'); ?></span>
                    </a>
                </div>

                <div class="lg:hidden px-3 text-center">
                    <button class="mobile-menu-open" id="toggleNav"
                            aria-label="<?php _e('Open mobile menu', 'octarinepress') ?>"
                            aria-controls="mobile-menu"
                            aria-expanded="false">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </div>

                <div class="hidden lg:block grow">
                    <?php Navigation::primary_menu(); ?>
                </div>
            </div>


            <div class="hidden lg:hidden w-full pt-4 mobile-menu relative" id="mobile-menu">
                <?php Navigation::mobile_menu(); ?>
            </div>


        </nav>
    </header>
