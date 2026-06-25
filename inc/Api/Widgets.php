<?php

namespace octarinepress\Api;

class Widgets
{
    public function register()
    {
        //		add_action( 'widgets_init', array( $this, 'sidebar_widgets' ) );
        add_action('widgets_init', [$this, 'footer_widgets']);
        //		add_action( 'widgets_init', array( $this, 'register_blog_widget' ) );
    }

    public function register_blog_widget() {}

    public function sidebar_widgets()
    {

        register_sidebar(
            [
                'id' => 'sidebar-widgets',
                'name' => __('Blog', 'octarinepress'),

                'before_widget' => '<section id="%1$s" class="widget %2$s">',
                'after_widget' => '</section>',
                'before_title' => '<h6>',
                'after_title' => '</h6>',
            ]
        );

    }

    public function footer_widgets()
    {

        register_sidebar(
            [
                'id' => 'footer-widgets',
                'name' => __('Footer - full width', 'octarinepress'),

                'before_widget' => '<section id="%1$s" class="widget w-full %2$s">',
                'after_widget' => '</section>',
                'before_title' => '<p class="mb-5">',
                'after_title' => '</p>',
            ]);

    }
}
