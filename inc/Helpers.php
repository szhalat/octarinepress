<?php
/**
 * Helpers methods
 * List all your static functions you wish to use globally on your theme
 */

//Helpers functions from https://github.com/Alecaddd/awps

if (! function_exists('dd')) {

    // Laravel-style dd() — dump and die, debug only.
    function dd()
    {
        echo '<pre>';
        array_map(function ($x) {
            var_dump($x);
        }, func_get_args());
        echo '</pre>';
        exit;
    }
}

if (! function_exists('assets')) {
    /**
     * Easily point to the assets dist folder.
     *
     * @param  string  $path
     */
    function assets($path): void
    {
        if (! $path) {
            return;
        }

        echo get_template_directory_uri() . '/assets/dist/' . $path;
    }
}

if (! function_exists('octarine_assets')) {
    function octarine_assets(string $path): string
    {
        static $manifest = null;

        if ($manifest === null) {
            $manifestPath = get_theme_file_path('dist/manifest.json');
            $manifest = file_exists($manifestPath)
                ? json_decode(file_get_contents($manifestPath), true)
                : [];
        }

        if (isset($manifest["assets/{$path}"])) {
            return get_theme_file_uri('dist/' . $manifest["assets/{$path}"]['file']);
        }

        return get_theme_file_uri('assets/' . $path);
    }
}

require_once 'static/content.php';
require_once 'static/responsive-images.php';
