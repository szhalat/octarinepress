<?php

namespace octarinepress\Setup;

class Enqueue
{
    private bool $jqueryEnabled;
    private bool $isLocal;
    private string $theme_folder;
    private ?array $manifest;

    public function __construct()
    {
        $raw = $_ENV['JQUERY_ENABLED'] ?? '';
        $parsed = filter_var($raw, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        $this->jqueryEnabled = $parsed ?? false;
        $this->isLocal = ($_ENV['APP_ENV'] ?? 'production') === 'local';
        $this->theme_folder = $_ENV['VITE_THEME_FOLDER'] ?? basename(get_template_directory());

        $manifestPath = get_theme_file_path('dist/manifest.json');
        $this->manifest = file_exists($manifestPath)
            ? json_decode(file_get_contents($manifestPath), true)
            : null;
    }

    public function register(): void
    {

        add_action('wp_enqueue_scripts', [$this, 'main_scripts']);
        add_action('wp_default_scripts', [$this, 'remove_jquery_migrate']);
        add_action('enqueue_block_editor_assets', [$this, 'editor_assets']);

        add_filter('script_loader_tag', function (string $tag, string $handle, string $src) {
            if (in_array($handle, ['vite', 'main-js', 'octarinepress-blocks-js'])) {
                return '<script type="module" src="' . esc_url($src) . '" defer></script>';
            }

            return $tag;
        }, 10, 3);

    }

    public function main_scripts(): void
    {

        $dependencies = ['jquery'];

        if (! $this->jqueryEnabled) {
            wp_deregister_script('jquery');
            $dependencies = array_diff($dependencies, ['jquery']);
        }

        if ($this->isLocal) {

            wp_enqueue_script('vite', 'http://localhost:5173/wp-content/themes/' . $this->theme_folder . '/@vite/client', [], null);
            wp_enqueue_script('main-js', 'http://localhost:5173/wp-content/themes/' . $this->theme_folder . '/assets/js/main.js', $dependencies, null, true);

            wp_enqueue_style('style-css', 'http://localhost:5173/wp-content/themes/' . $this->theme_folder . '/assets/css/styles.css', [], null);

        } elseif ($this->manifest) {

            wp_enqueue_script('main-js', get_theme_file_uri('dist/' . $this->manifest['assets/js/main.js']['file']), $dependencies, null, true);

            wp_enqueue_style('style-css', get_theme_file_uri('dist/' . $this->manifest['assets/css/styles.css']['file']), [], null);
        }

    }

    public function remove_jquery_migrate($scripts): void
    {
        if (! is_admin() && isset($scripts->registered['jquery'])) {
            $script = $scripts->registered['jquery'];
            if ($script->deps) {

                $script->deps = array_diff($script->deps, ['jquery-migrate']);
            }
        }
    }

    public function editor_assets(): void
    {

        $dependencies = [
            'wp-blocks',
            'wp-element',
            'wp-editor',
            'wp-block-editor',
            'wp-components',
            'wp-i18n',
            'wp-rich-text',
        ];

        if ($this->isLocal) {

            wp_enqueue_style(OCTARINEPRESS_SHORT . 'editor-css', 'http://localhost:5173/wp-content/themes/' . $this->theme_folder . '/assets/css/editor.css', [], null);

            wp_enqueue_script('octarinepress-blocks-js', 'http://localhost:5173/wp-content/themes/' . $this->theme_folder . '/assets/js/octarinepress-blocks.js', $dependencies, null, true);

        } elseif ($this->manifest) {

            wp_enqueue_script('octarinepress-blocks-js', get_theme_file_uri('dist/' . $this->manifest['assets/js/octarinepress-blocks.js']['file']), $dependencies, null, true);

            wp_enqueue_style(OCTARINEPRESS_SHORT . 'editor-css', get_theme_file_uri('dist/' . $this->manifest['assets/css/editor.css']['file']), ['wp-edit-blocks'], null);

        }

        wp_set_script_translations('octarinepress-blocks-js', 'octarinepress', get_template_directory() . '/languages');

    }
}
