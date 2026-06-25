<?php

namespace octarinepress\Setup;

use octarinepress\Core\TailwindMenuWalker;
use octarinepress\Core\TailwindMobileMenuWalker;

class Navigation
{
    public static function primary_menu()
    {
        wp_nav_menu(
            [
                'container' => false,
                'menu_class' => 'menu',
                'items_wrap' => '<ul id="%1$s" class="%2$s desktop-menu">%3$s</ul>',
                'theme_location' => 'primary-menu',
                'depth' => 2,
                'fallback_cb' => false,
                'walker' => new TailwindMenuWalker,
            ]
        );
    }

    public static function mobile_menu()
    {
        wp_nav_menu(
            [
                'container' => false,
                'menu_class' => 'menu mobile-menu__inner',
                'items_wrap' => '<ul id="%1$s" class="%2$s m-0 text-sm font-medium w-full lg:hidden bg-black text-white">%3$s</ul>',
                'theme_location' => 'mobile-menu',
                'depth' => 2,
                'fallback_cb' => false,
                'walker' => new TailwindMobileMenuWalker,
            ]
        );
    }

    public static function footer_menu()
    {
        wp_nav_menu(
            [
                'container' => false,
                'menu_class' => 'menu menu-footer text-center md:flex md:justify-between font-medium m-0',
                'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                'theme_location' => 'footer-menu',
                'depth' => 1,
                'fallback_cb' => false,
            ]
        );
    }

    public function register()
    {
        register_nav_menus(
            [
                'primary-menu' => esc_html__('Desktop', 'octarinepress'),
                'mobile-menu' => esc_html__('Mobile', 'octarinepress'),
                'footer-menu' => esc_html__('Footer', 'octarinepress'),
            ]
        );
    }
}
