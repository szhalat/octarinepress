<?php
/**
 * This theme uses PSR-4 and OOP logic instead of procedural coding
 */
$octarine_autoload = dirname(__FILE__) . '/vendor/autoload.php';

if (! file_exists($octarine_autoload)) {
    add_action('admin_notices', function () {
        echo '<div class="notice notice-error"><p><strong>OctarinePress:</strong> '
            . 'Theme dependencies are missing. Run <code>composer install &amp;&amp; npm run build</code> '
            . 'in the theme directory to finish setup.</p></div>';
    });

    return;
}

require_once $octarine_autoload;

if (class_exists('octarinepress\\Init')) {
    octarinepress\Init::register_services();
}
