<?php

namespace octarinepress\Setup;

use Carbon_Fields\Carbon_Fields;

/**
 * Optional Carbon Fields bootstrap.
 *
 * Enable by adding BootFields::class to Init::get_services() and installing
 * Carbon Fields: composer require htmlburger/carbon-fields
 */
class BootFields
{
    public function register()
    {
        add_action('after_setup_theme', [$this, 'crb_load']);
    }

    public function crb_load()
    {
        if (class_exists(Carbon_Fields::class)) {
            Carbon_Fields::boot();
        }
    }
}
