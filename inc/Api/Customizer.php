<?php

namespace octarinepress\Api;

use octarinepress\Api\Customizer\ThemeSettings;

class Customizer
{
    /**
     * This will generate text for use inin selective refresh. If the setting
     * ($mod_name) has no defined value, the text will not be output.
     *
     *
     * @return string Returns a single line of text.
     *
     * @uses get_theme_mod()
     */
    public static function text($theme_mod)
    {
        $theme_mod = get_theme_mod($theme_mod);

        if (! empty($theme_mod)) {
            return $theme_mod;
        }
    }

    /**
     * register default hooks and actions for WordPress
     *
     * @return void
     */
    public function register()
    {
        add_action('customize_register', [$this, 'setup']);
    }

    /**
     * Store all the classes inside an array
     *
     * @return array Full list of classes
     */
    public function get_classes()
    {
        return [
            ThemeSettings::class,
        ];
    }

    /**
     * Add postMessage support for site title and description for the Theme Customizer.
     */
    public function setup($wp_customize)
    {
        foreach ($this->get_classes() as $class) {
            $service = new $class;
            if (method_exists($class, 'register')) {
                $service->register($wp_customize);
            }
        }
    }
}
