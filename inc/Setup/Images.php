<?php

namespace octarinepress\Setup;

use Imagick;
use WP_CLI;

class Images
{
    private array $custom_sizes = [
        'feature-image' => [700, 620, false],
        'content-image' => [1600, 1600, false],
    ];

    private array $responsive_breakpoints = [
        'feature-image' => [
            'desktop' => [700, 620, false],
            'medium' => [780, 692, false],
            'mobile' => [520, 462, false],
        ],
        'content-image' => [
            'desktop' => [1600, 1600, false],
            'medium' => [1200, 1200, false],
            'mobile' => [800, 800, false],
        ],
    ];

    private bool $enable_webp = true;
    private int $webp_quality;

    public function register()
    {
        // Allow filtering WebP quality (default: 80)
        $this->webp_quality = apply_filters('octarinepress_webp_quality', 80);

        $this->image_sizes();
        $this->cleanup_images();
        $this->dynamic_generation();
        $this->register_admin_bar_button();
        $this->register_cli_commands();
        $this->disable_wp_auto_sizes();

    }

    /**
     * Dynamically generate and return image size information
     *
     * @param  false|array  $out  Default value (false)
     * @param  int  $id  Attachment ID
     * @param  string|array  $size  Image size name or array of dimensions
     * @return false|array Array with image URL, width, height, and is_intermediate flag, or false
     */
    public function dynamic_downsize($out, $id, $size)
    {
        // Only handle registered custom sizes.
        if (! is_string($size) || ! isset($this->custom_sizes[$size])) {
            return false;
        }

        [$target_w, $target_h, $crop] = $this->custom_sizes[$size];

        $upload_dir = wp_get_upload_dir();
        $original = get_attached_file($id);

        if (! $original || ! file_exists($original)) {
            return false;
        }

        $ext = pathinfo($original, PATHINFO_EXTENSION);
        $cache_dir = apply_filters('octarinepress_cache_directory', trailingslashit($upload_dir['basedir']) . 'cache');

        if (! $this->ensure_cache_directory($cache_dir)) {
            error_log("Failed to create cache directory: {$cache_dir}");

            return false;
        }

        // Generate all responsive versions
        $this->generate_responsive_versions($id, $size, $original, $cache_dir, $ext);

        // Return standard version info for WordPress
        $cache_filename = sprintf('%d-%s.%s', $id, $size, $ext);
        $cache_path = trailingslashit($cache_dir) . $cache_filename;
        $cache_url = trailingslashit($upload_dir['baseurl']) . 'cache/' . $cache_filename;

        if (file_exists($cache_path)) {
            $image_info = @getimagesize($cache_path);
            if (! $image_info) {
                error_log("Corrupted cache file detected, regenerating: {$cache_path}");
                @unlink($cache_path);

                // Retry generation
                $this->generate_responsive_versions($id, $size, $original, $cache_dir, $ext);

                // Re-check after regeneration
                if (file_exists($cache_path)) {
                    $image_info = @getimagesize($cache_path);
                    if ($image_info) {
                        $result = [
                            $cache_url,
                            (int) $image_info[0],
                            (int) $image_info[1],
                            true,
                        ];

                        return apply_filters('octarinepress_dynamic_downsize', $result, $id, $size);
                    }
                }

                error_log("Failed to regenerate corrupted cache file: {$cache_path}");

                return false;
            }

            $result = [
                $cache_url,
                (int) $image_info[0],
                (int) $image_info[1],
                true,
            ];

            return apply_filters('octarinepress_dynamic_downsize', $result, $id, $size);
        }

        return false;
    }

    /**
     * Clear cached images for a specific attachment
     *
     * @param  int|array  $attachment_id  Attachment ID or array containing ID
     * @return void
     */
    public function clear_image_cache($attachment_id)
    {
        if (is_array($attachment_id)) {
            $attachment_id = $attachment_id[0] ?? 0;
        }

        $upload_dir = wp_get_upload_dir();
        $cache_dir = trailingslashit($upload_dir['basedir']) . 'cache';

        foreach (array_keys($this->custom_sizes) as $size) {
            // Clear all versions
            $pattern = sprintf('%d-%s*.*', $attachment_id, $size);
            $files = glob($cache_dir . '/' . $pattern);
            if ($files) {
                array_map('unlink', $files);
            }

            // Clear transient cache
            delete_transient("srcset_{$attachment_id}_{$size}_v1");
        }
    }

    /**
     * Clear image cache when attachment metadata is updated
     *
     * @param  array  $metadata  Attachment metadata
     * @param  int  $attachment_id  Attachment ID
     * @return array Unmodified metadata
     */
    public function clear_image_cache_by_id($metadata, $attachment_id)
    {
        $this->clear_image_cache($attachment_id);

        return $metadata;
    }

    /**
     * Check if a custom size is registered
     *
     * @param  string  $size  Size name
     */
    public function has_custom_size(string $size): bool
    {
        return isset($this->custom_sizes[$size]);
    }

    /**
     * Get all available srcset entries for a given image and size
     *
     * @param  int  $attachment_id
     * @param  string  $size
     * @return array
     */
    public function get_srcset_data($attachment_id, $size)
    {
        if (! isset($this->custom_sizes[$size])) {
            return [];
        }

        // Check transient cache first
        $cache_key = "srcset_{$attachment_id}_{$size}_v1";
        $cached = get_transient($cache_key);
        if ($cached !== false) {
            return $cached;
        }

        $upload_dir = wp_get_upload_dir();
        $cache_base_url = trailingslashit($upload_dir['baseurl']) . 'cache/';
        $cache_base_path = trailingslashit($upload_dir['basedir']) . 'cache/';

        $srcset = [];
        $original = get_attached_file($attachment_id);

        if (! $original || ! file_exists($original)) {
            return [];
        }

        $ext = pathinfo($original, PATHINFO_EXTENSION);

        $breakpoints = $this->get_breakpoints_for_size($size);

        foreach ($breakpoints as $breakpoint_name => $dimensions) {
            [$width, $height, $crop] = $dimensions;
            $suffix = $breakpoint_name !== 'default' ? "-{$breakpoint_name}" : '';

            // Standard format
            $filename = sprintf('%d-%s%s.%s', $attachment_id, $size, $suffix, $ext);
            $file_path = $cache_base_path . $filename;
            $file_url = $cache_base_url . $filename;

            if (file_exists($file_path)) {
                $image_info = @getimagesize($file_path);
                if ($image_info) {
                    $srcset['standard'][$breakpoint_name] = [
                        'url' => $file_url,
                        'width' => (int) $image_info[0],
                        'height' => (int) $image_info[1],
                    ];
                }
            }

            // WebP format
            if ($this->enable_webp) {
                $webp_filename = sprintf('%d-%s%s.webp', $attachment_id, $size, $suffix);
                $webp_path = $cache_base_path . $webp_filename;
                $webp_url = $cache_base_url . $webp_filename;

                if (file_exists($webp_path)) {
                    $image_info = @getimagesize($webp_path);
                    if ($image_info) {
                        $srcset['webp'][$breakpoint_name] = [
                            'url' => $webp_url,
                            'width' => (int) $image_info[0],
                            'height' => (int) $image_info[1],
                        ];
                    }
                }
            }
        }

        // Cache for 1 hour if we have data
        if (! empty($srcset)) {
            set_transient($cache_key, $srcset, HOUR_IN_SECONDS);
        }

        return apply_filters('octarinepress_srcset_data', $srcset, $attachment_id, $size);
    }

    /**
     * Get the sizes attribute for a given image size based on its breakpoints
     * Uses actual image widths for accurate browser selection
     *
     * @param  string  $size
     * @return string
     */
    public function get_sizes_attribute($size)
    {
        $breakpoints = $this->get_breakpoints_for_size($size);

        // Build sizes attribute from breakpoints using actual image widths
        $sizes_parts = [];
        $breakpoint_config = [
            'mobile' => ['max_width' => 640],
            'medium' => ['max_width' => 1024],
            'tablet' => ['max_width' => 1024],
            'desktop' => ['max_width' => 1920],
            'retina' => ['max_width' => 3840],
        ];

        foreach ($breakpoints as $breakpoint_name => $dimensions) {
            if (isset($breakpoint_config[$breakpoint_name])) {
                $config = $breakpoint_config[$breakpoint_name];
                $image_width = $dimensions[0]; // Use actual image width
                $sizes_parts[] = sprintf('(max-width: %dpx) %dpx', $config['max_width'], $image_width);
            }
        }

        // Add fallback (largest breakpoint width)
        if (! empty($breakpoints)) {
            $last_breakpoint = end($breakpoints);
            $fallback_width = $last_breakpoint[0]; // Image width
            $sizes_parts[] = $fallback_width . 'px';
        }

        $sizes_string = ! empty($sizes_parts) ? implode(', ', $sizes_parts) : '100vw';

        return apply_filters('octarinepress_sizes_attribute', $sizes_string, $size, $breakpoints);
    }

    /**
     * Add clear cache button to WordPress admin bar
     *
     * @param  WP_Admin_Bar  $wp_admin_bar  WordPress admin bar instance
     * @return void
     */
    public function add_clear_cache_admin_bar_button($wp_admin_bar)
    {
        // Only show to users who can manage options
        if (! current_user_can('manage_options')) {
            return;
        }

        $clear_url = add_query_arg(
            'action',
            'clear_image_cache',
            admin_url('admin.php')
        );

        $clear_url = wp_nonce_url($clear_url, 'clear_image_cache_nonce');

        $wp_admin_bar->add_node([
            'id' => 'clear-image-cache',
            'title' => __('Clear image cache', 'octarinepress'),
            'href' => $clear_url,
            'meta' => [
                'title' => __('Clear image cache', 'octarinepress'),
            ],
        ]);
    }

    /**
     * Handle clear cache request from admin bar button
     *
     * @return void
     */
    public function handle_clear_cache_request()
    {
        // Check if this is the clear cache action
        if (! isset($_GET['action']) || $_GET['action'] !== 'clear_image_cache') {
            return;
        }

        // Verify nonce
        if (! isset($_GET['_wpnonce']) || ! wp_verify_nonce($_GET['_wpnonce'], 'clear_image_cache_nonce')) {
            wp_die('Security check failed');
        }

        // Check user capabilities
        if (! current_user_can('manage_options')) {
            wp_die('You do not have permission to perform this action');
        }

        // Clear all image cache
        $this->clear_all_image_cache();

        // Get the referrer URL, fallback to home if not available
        $redirect_url = isset($_SERVER['HTTP_REFERER']) ? esc_url_raw($_SERVER['HTTP_REFERER']) : home_url();

        // Redirect back to the page where the button was clicked
        wp_redirect($redirect_url);
        exit;
    }

    private function disable_wp_auto_sizes()
    {
        // Disable WordPress 6.3+ auto sizes feature that adds "auto," prefix
        // This conflicts with our explicit pixel-based sizes attribute
        add_filter('wp_img_tag_add_auto_sizes', '__return_false');
    }

    private function image_sizes()
    {
        foreach ($this->custom_sizes as $name => [$width, $height, $crop]) {
            add_image_size($name, $width, $height, $crop);
        }
    }

    private function cleanup_images()
    {
        remove_image_size('1536x1536');
        remove_image_size('medium_large');
        remove_image_size('2048x2048');

        // Disable WordPress "-scaled" image generation.
        add_filter('big_image_size_threshold', '__return_false');
    }

    private function dynamic_generation()
    {
        add_filter('intermediate_image_sizes_advanced', function ($sizes, $metadata, $attachment_id) {
            foreach (array_keys($this->custom_sizes) as $size) {
                unset($sizes[$size]);
            }

            return $sizes;
        }, 10, 3);

        add_filter('image_downsize', [$this, 'dynamic_downsize'], 10, 3);

        // Clear cache when attachment is updated
        add_action('delete_attachment', [$this, 'clear_image_cache']);
        add_action('wp_update_attachment_metadata', [$this, 'clear_image_cache_by_id'], 10, 2);
    }

    private function generate_responsive_versions($id, $size, $original, $cache_dir, $ext)
    {
        $breakpoints = $this->get_breakpoints_for_size($size);

        // Allow filtering breakpoints
        $breakpoints = apply_filters('octarinepress_responsive_breakpoints', $breakpoints, $size);

        foreach ($breakpoints as $breakpoint_name => $dimensions) {
            [$target_w, $target_h, $crop] = $dimensions;
            $this->generate_single_version($id, $size, $original, $cache_dir, $ext, $target_w, $target_h, $crop, $breakpoint_name);
        }
    }

    private function can_process_image($original): bool
    {
        $image_info = @getimagesize($original);
        if (! $image_info) {
            return false;
        }

        // Estimate memory needed: width × height × channels × 1.8 (overhead)
        $memory_needed = $image_info[0] * $image_info[1] * 4 * 1.8;
        $memory_limit = wp_convert_hr_to_bytes(ini_get('memory_limit'));
        $memory_available = $memory_limit - memory_get_usage(true);

        if ($memory_available <= $memory_needed) {
            error_log(sprintf(
                'Insufficient memory to process image %s. Needed: %s, Available: %s',
                $original,
                size_format($memory_needed),
                size_format($memory_available)
            ));

            return false;
        }

        return true;
    }

    private function generate_single_version($id, $size, $original, $cache_dir, $ext, $target_w, $target_h, $crop, $breakpoint_name)
    {
        $suffix = $breakpoint_name !== 'default' ? "-{$breakpoint_name}" : '';

        // Standard format
        $cache_filename = sprintf('%d-%s%s.%s', $id, $size, $suffix, $ext);
        $cache_path = trailingslashit($cache_dir) . $cache_filename;
        $lock_file = $cache_path . '.lock';

        // WebP format
        $webp_filename = sprintf('%d-%s%s.webp', $id, $size, $suffix);
        $webp_path = trailingslashit($cache_dir) . $webp_filename;

        // Acquire lock to prevent race conditions
        $lock_handle = @fopen($lock_file, 'c');
        if (! $lock_handle || ! flock($lock_handle, LOCK_EX)) {
            error_log("Failed to acquire lock for image generation: {$cache_path}");

            return false;
        }

        try {
            // Re-check if regeneration is needed after acquiring lock
            $needs_generation = ! file_exists($cache_path)
                || (file_exists($original) && filemtime($cache_path) < filemtime($original));

            $needs_webp_generation = $this->enable_webp
                && (! file_exists($webp_path) || (file_exists($original) && filemtime($webp_path) < filemtime($original)));

            // Skip if both exist and are fresh
            if (! $needs_generation && (! $this->enable_webp || ! $needs_webp_generation)) {
                return true;
            }

            // Check memory availability before processing
            if ($needs_generation && ! $this->can_process_image($original)) {
                return false;
            }

            // Generate if needed
            if ($needs_generation) {
                $editor = wp_get_image_editor($original);
                if (is_wp_error($editor)) {
                    error_log("Image editor error for attachment {$id}: " . $editor->get_error_message());

                    return false;
                }

                // Get original dimensions
                $original_size = $editor->get_size();
                $original_w = $original_size['width'];
                $original_h = $original_size['height'];

                // Don't upscale - limit to original dimensions
                $final_w = min($target_w, $original_w);
                $final_h = $target_h > 0 ? min($target_h, $original_h) : 0;

                // Skip if requested size is larger than original
                if ($target_w > $original_w && $target_h > $original_h) {
                    return false;
                }

                // Resize
                if ($final_h === 0) {
                    $editor->resize($final_w, null, false);
                } else {
                    $editor->resize($final_w, $final_h, $crop);
                }

                $saved = $editor->save($cache_path);
                if (is_wp_error($saved)) {
                    error_log("Failed to save image {$cache_path}: " . $saved->get_error_message());

                    return false;
                }
            }

            // Generate WebP version
            if ($this->enable_webp && $needs_webp_generation && file_exists($cache_path)) {
                $this->generate_webp_version($cache_path, $webp_path);
            }

            return true;
        } finally {
            // Always release lock and clean up
            flock($lock_handle, LOCK_UN);
            fclose($lock_handle);
            @unlink($lock_file);
        }
    }

    private function generate_webp_version(string $source_path, string $webp_path): bool
    {
        // Validate MIME type - ensure source is an image
        $mime_type = wp_check_filetype($source_path)['type'];
        if (! in_array($mime_type, ['image/jpeg', 'image/png', 'image/gif'], true)) {
            error_log("Invalid MIME type for WebP conversion: {$source_path} ({$mime_type})");

            return false;
        }

        // Check if GD or Imagick supports WebP
        if (! $this->webp_supported()) {
            return false;
        }

        $editor = wp_get_image_editor($source_path);
        if (is_wp_error($editor)) {
            return false;
        }

        // Set WebP quality (configurable via filter)
        $editor->set_quality($this->webp_quality);

        // Save as WebP
        $saved = $editor->save($webp_path, 'image/webp');

        if (is_wp_error($saved)) {
            error_log("Failed to generate WebP: {$webp_path} - " . $saved->get_error_message());

            return false;
        }

        return true;
    }

    private function webp_supported(): bool
    {
        // Check GD support
        if (function_exists('imagewebp')) {
            return true;
        }

        // Check Imagick support
        if (extension_loaded('imagick')) {
            $imagick = new Imagick;

            return in_array('WEBP', $imagick->queryFormats());
        }

        return false;
    }

    private function ensure_cache_directory(string $cache_dir): bool
    {
        if (is_dir($cache_dir)) {
            return true;
        }

        if (! wp_mkdir_p($cache_dir)) {
            return false;
        }

        // Add .htaccess for security
        $htaccess_content = "Options -Indexes\n<FilesMatch \"\.(php|php\\.|phtml|phps)$\">\nOrder Allow,Deny\nDeny from all\n</FilesMatch>";
        $htaccess_path = $cache_dir . '/.htaccess';
        $result = @file_put_contents($htaccess_path, $htaccess_content);

        if ($result === false) {
            error_log("Failed to create .htaccess file: {$htaccess_path}");
        }

        return true;
    }

    private function get_breakpoints_for_size(string $size): array
    {
        if (! isset($this->custom_sizes[$size])) {
            return [];
        }

        $configured_breakpoints = $this->responsive_breakpoints[$size] ?? [];
        $normalized_breakpoints = [];

        foreach ($configured_breakpoints as $breakpoint_name => $dimensions) {
            if (! is_array($dimensions) || count($dimensions) < 3) {
                continue;
            }

            $normalized_breakpoints[$breakpoint_name] = [
                (int) $dimensions[0],
                (int) $dimensions[1],
                (bool) $dimensions[2],
            ];
        }

        if (empty($normalized_breakpoints)) {
            return ['default' => $this->custom_sizes[$size]];
        }

        return $normalized_breakpoints;
    }

    private function register_admin_bar_button(): void
    {
        add_action('admin_bar_menu', [$this, 'add_clear_cache_admin_bar_button'], 999);
        add_action('admin_init', [$this, 'handle_clear_cache_request']);
    }

    private function clear_all_image_cache()
    {
        $upload_dir = wp_get_upload_dir();
        $cache_dir = trailingslashit($upload_dir['basedir']) . 'cache';

        if (! is_dir($cache_dir)) {
            return;
        }

        $files = glob($cache_dir . '/*');

        if ($files) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    $result = @unlink($file);
                    if ($result === false) {
                        error_log("Failed to delete cache file: {$file}");
                    }
                }
            }
        }

        // Clear all transient caches
        global $wpdb;
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_srcset_%' OR option_name LIKE '_transient_timeout_srcset_%'");

        // Show success message on frontend using a transient
        set_transient('image_cache_cleared_' . get_current_user_id(), true, 30);
    }

    /**
     * Enforce cache size limit by deleting oldest files
     *
     * @param  int  $max_size_mb  Maximum cache size in megabytes
     */
    private function enforce_cache_size_limit(int $max_size_mb = 500): void
    {
        $upload_dir = wp_get_upload_dir();
        $cache_dir = trailingslashit($upload_dir['basedir']) . 'cache';

        if (! is_dir($cache_dir)) {
            return;
        }

        $total_size = 0;
        $files = [];

        foreach (glob($cache_dir . '/*') as $file) {
            if (is_file($file) && ! str_ends_with($file, '.htaccess')) {
                $size = filesize($file);
                $total_size += $size;
                $files[] = [
                    'path' => $file,
                    'time' => filemtime($file),
                    'size' => $size,
                ];
            }
        }

        // If over limit, delete oldest files
        if ($total_size > $max_size_mb * 1024 * 1024) {
            // Sort by modification time (oldest first)
            usort($files, fn ($a, $b) => $a['time'] <=> $b['time']);

            foreach ($files as $file) {
                $result = @unlink($file['path']);
                if ($result) {
                    $total_size -= $file['size'];
                } else {
                    error_log("Failed to delete cache file during size enforcement: {$file['path']}");
                }

                // Stop when we're at 80% of limit
                if ($total_size <= $max_size_mb * 1024 * 1024 * 0.8) {
                    break;
                }
            }

            error_log(sprintf(
                'Cache size limit enforced. Reduced from %s to %s',
                size_format($total_size + array_sum(array_column($files, 'size'))),
                size_format($total_size)
            ));
        }
    }

    /**
     * Register WP-CLI commands for image management
     */
    private function register_cli_commands(): void
    {
        if (! class_exists('WP_CLI')) {
            return;
        }

        // Regenerate all images command
        WP_CLI::add_command('octarinepress images regenerate', function ($args, $assoc_args) {
            $attachments = get_posts([
                'post_type' => 'attachment',
                'post_mime_type' => 'image',
                'posts_per_page' => -1,
                'fields' => 'ids',
            ]);

            $progress = \WP_CLI\Utils\make_progress_bar('Regenerating images', count($attachments));

            foreach ($attachments as $id) {
                $this->clear_image_cache($id);
                foreach (array_keys($this->custom_sizes) as $size) {
                    wp_get_attachment_image_src($id, $size); // Trigger generation
                }
                $progress->tick();
            }

            $progress->finish();
            WP_CLI::success('All images regenerated.');
        });

        // Clear cache command
        WP_CLI::add_command('octarinepress images clear-cache', function () {
            $this->clear_all_image_cache();
            WP_CLI::success('Cache cleared.');
        });

        // Cache statistics command
        WP_CLI::add_command('octarinepress images stats', function () {
            $upload_dir = wp_get_upload_dir();
            $cache_dir = trailingslashit($upload_dir['basedir']) . 'cache';

            if (! is_dir($cache_dir)) {
                WP_CLI::warning('Cache directory does not exist.');

                return;
            }

            $files = glob($cache_dir . '/*');
            $file_count = 0;
            $total_size = 0;

            foreach ($files as $file) {
                if (is_file($file) && ! str_ends_with($file, '.htaccess')) {
                    $file_count++;
                    $total_size += filesize($file);
                }
            }

            WP_CLI::line('Cache Statistics:');
            WP_CLI::line('Files: ' . number_format($file_count));
            WP_CLI::line('Size: ' . size_format($total_size));
            WP_CLI::line('Directory: ' . $cache_dir);
        });
    }
}
