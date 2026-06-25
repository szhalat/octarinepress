<?php

if (! defined('ABSPATH')) {
    exit;
}

// Define responsive breakpoint constants
const OCTARINEPRESS_BREAKPOINT_DESKTOP = '(min-width: 1025px)';
const OCTARINEPRESS_BREAKPOINT_TABLET = '(min-width: 641px)';
const OCTARINEPRESS_BREAKPOINT_MOBILE_MAX = '(max-width: 640px)';
const OCTARINEPRESS_BREAKPOINT_TABLET_MAX = '(max-width: 1024px)';

/**
 * Build srcset strings from srcset data array
 *
 * @param  array  $data  Srcset data from Images::get_srcset_data()
 * @return array ['webp' => string, 'standard' => string]
 */
function octarinepress_build_srcset_strings(array $data): array
{
    $out = ['webp' => '', 'standard' => ''];

    if (! empty($data['webp'])) {
        $parts = [];
        foreach ($data['webp'] as $bp => $d) {
            $parts[] = sprintf('%s %dw', esc_url($d['url']), (int) $d['width']);
        }
        $out['webp'] = implode(', ', $parts);
    }

    if (! empty($data['standard'])) {
        $parts = [];
        foreach ($data['standard'] as $bp => $d) {
            $parts[] = sprintf('%s %dw', esc_url($d['url']), (int) $d['width']);
        }
        $out['standard'] = implode(', ', $parts);
    }

    return $out;
}

/**
 * Choose the best base image from srcset data
 * Prefers standard format, falls back to WebP, selects largest width
 *
 * @param  array  $data  Srcset data
 * @return array|null ['url', 'width', 'height'] or null
 */
function octarinepress_choose_base_image(array $data): ?array
{
    $base = null;

    // Try standard format first (better browser compatibility)
    if (! empty($data['standard'])) {
        // Prefer desktop, otherwise use largest
        if (isset($data['standard']['desktop'])) {
            $base = $data['standard']['desktop'];
        } else {
            foreach ($data['standard'] as $bp => $d) {
                if ($base === null || (int) $d['width'] > (int) $base['width']) {
                    $base = $d;
                }
            }
        }
    }

    // Fallback to WebP if no standard format available
    if (! $base && ! empty($data['webp'])) {
        if (isset($data['webp']['desktop'])) {
            $base = $data['webp']['desktop'];
        } else {
            foreach ($data['webp'] as $bp => $d) {
                if ($base === null || (int) $d['width'] > (int) $base['width']) {
                    $base = $d;
                }
            }
        }
    }

    return $base;
}

/**
 * Build HTML attribute string from attributes array
 *
 * @param  array  $attributes  Attributes to build
 * @param  array  $exclude  Keys to exclude from output
 * @param  bool  $add_loading  Whether to add loading="lazy" if not set
 * @return string Attribute string with leading space
 */
function octarinepress_build_attribute_string(array $attributes, array $exclude = [], bool $add_loading = true): string
{
    $attr_string = '';

    foreach ($attributes as $key => $value) {
        if (! in_array($key, $exclude, true)) {
            $attr_string .= sprintf(' %s="%s"', esc_attr($key), esc_attr($value));
        }
    }

    if ($add_loading && ! isset($attributes['loading'])) {
        // Allow filter to control default loading behavior
        $default_loading = apply_filters('octarinepress_default_loading', 'lazy');
        $attr_string .= sprintf(' loading="%s"', esc_attr($default_loading));
    }

    // Add fetchpriority if specified (for LCP optimization)
    if (isset($attributes['fetchpriority']) && ! in_array('fetchpriority', $exclude, true)) {
        $attr_string .= sprintf(' fetchpriority="%s"', esc_attr($attributes['fetchpriority']));
    }

    return $attr_string;
}

/**
 * Output responsive image with WebP support and width-based srcset
 *
 * @param  array  $attributes  Additional img attributes
 *                             - 'enable_pswp' (bool): Enable PhotoSwipe attributes
 *                             - 'webp_only' (bool): Only output WebP in picture element, no srcset
 */
function octarinepress_get_responsive_image(int $attachment_id, string $size = 'thumbnail', array $attributes = []): string
{
    if (! $attachment_id) {
        return '';
    }

    static $images_instance = null;
    if ($images_instance === null) {
        $images_instance = new \octarinepress\Setup\Images;
    }

    // Trigger generation if needed (ensures all breakpoints exist for the first call)
    wp_get_attachment_image_src($attachment_id, $size);

    // If not our custom size, fall back to WordPress core
    if (! $images_instance->has_custom_size($size)) {
        return wp_get_attachment_image($attachment_id, $size, false, $attributes);
    }

    $srcset_data = $images_instance->get_srcset_data($attachment_id, $size);

    // If nothing is generated yet, fall back to WP (so you still get *something*)
    if (empty($srcset_data) || (empty($srcset_data['webp']) && empty($srcset_data['standard']))) {
        $fallback = wp_get_attachment_image_src($attachment_id, $size);
        if (! $fallback) {
            return '';
        }

        $alt = array_key_exists('alt', $attributes)
            ? (string) $attributes['alt']
            : get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
        $alt_attr = $alt ? sprintf('alt="%s"', esc_attr($alt)) : 'alt=""';

        $attr_string = octarinepress_build_attribute_string($attributes, ['alt', 'sizes', 'enable_pswp', 'webp_only']);

        return sprintf(
            '<img src="%s" width="%d" height="%d" %s decoding="async"%s>',
            esc_url($fallback[0]),
            (int) $fallback[1],
            (int) $fallback[2],
            $alt_attr,
            $attr_string
        );
    }

    $webp_only = ! empty($attributes['webp_only']);

    // Build srcset strings using helper
    $srcsets = octarinepress_build_srcset_strings($srcset_data);
    $webp_srcset = $srcsets['webp'];
    $standard_srcset = $webp_only ? '' : $srcsets['standard'];

    // Choose base <img> variant using helper
    $base = octarinepress_choose_base_image($srcset_data);

    if (! $base || empty($base['url']) || empty($base['width']) || empty($base['height'])) {
        return '';
    }

    $img_url = $base['url'];
    $img_w = (int) $base['width'];
    $img_h = (int) $base['height'];

    // Alt
    $alt = array_key_exists('alt', $attributes)
        ? (string) $attributes['alt']
        : get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
    $alt_attr = $alt ? sprintf('alt="%s"', esc_attr($alt)) : 'alt=""';

    // Sizes attribute (from Images class unless overridden)
    $sizes_attr = isset($attributes['sizes']) ? (string) $attributes['sizes'] : $images_instance->get_sizes_attribute($size);

    // Build other attributes using helper (do NOT output enable_pswp/webp_only as HTML attributes)
    $attr_string = octarinepress_build_attribute_string($attributes, ['alt', 'sizes', 'enable_pswp', 'webp_only']);

    // PhotoSwipe attrs (opt-in) - merge by width, prefer WebP
    if (! empty($attributes['enable_pswp'])) {
        $full_image = wp_get_attachment_image_src($attachment_id, 'full');
        $full_width = $full_image[1] ?? 0;
        $full_height = $full_image[2] ?? 0;

        // Merge srcsets by width, prefer WebP
        $pswp_by_width = [];

        if (! empty($srcset_data['webp'])) {
            foreach ($srcset_data['webp'] as $bp => $data) {
                $pswp_by_width[(int) $data['width']] = $data['url'];
            }
        }

        if (! empty($srcset_data['standard'])) {
            foreach ($srcset_data['standard'] as $bp => $data) {
                $width = (int) $data['width'];
                // Only add if width doesn't exist (WebP takes priority)
                if (! isset($pswp_by_width[$width])) {
                    $pswp_by_width[$width] = $data['url'];
                }
            }
        }

        if (! empty($pswp_by_width) && $full_width && $full_height) {
            ksort($pswp_by_width); // Sort by width ascending
            $pswp_parts = [];
            foreach ($pswp_by_width as $width => $url) {
                $pswp_parts[] = sprintf('%s %dw', esc_url($url), $width);
            }

            $attr_string .= sprintf(
                ' data-pswp-srcset="%s" data-pswp-width="%d" data-pswp-height="%d"',
                esc_attr(implode(', ', $pswp_parts)),
                (int) $full_width,
                (int) $full_height
            );
        }
    }

    // Build picture
    $html = '<picture>';

    $breakpoint_media = [
        'mobile' => OCTARINEPRESS_BREAKPOINT_MOBILE_MAX,
        'medium' => '(min-width: 641px) and (max-width: 1024px)',
        'tablet' => '(min-width: 641px) and (max-width: 1024px)',
        'desktop' => OCTARINEPRESS_BREAKPOINT_DESKTOP,
    ];

    if (! empty($srcset_data['webp'])) {
        foreach ($srcset_data['webp'] as $bp => $d) {
            $media = $breakpoint_media[$bp] ?? '';
            $html .= sprintf(
                '<source srcset="%s" type="image/webp" %s sizes="%s">',
                esc_url($d['url']),
                $media ? 'media="' . esc_attr($media) . '"' : '',
                esc_attr($sizes_attr)
            );
        }
    }

    if (! empty($srcset_data['standard']) && ! $webp_only) {
        foreach ($srcset_data['standard'] as $bp => $d) {
            $media = $breakpoint_media[$bp] ?? '';
            $mime_type = pathinfo($d['url'], PATHINFO_EXTENSION) === 'png' ? 'image/png' : 'image/jpeg';
            $html .= sprintf(
                '<source srcset="%s" %s sizes="%s" type="%s">',
                esc_url($d['url']),
                $media ? 'media="' . esc_attr($media) . '"' : '',
                esc_attr($sizes_attr),
                $mime_type
            );
        }
    }

    // Important: <img> src/width/height must match the chosen base cached file
    $html .= sprintf(
        '<img src="%s" width="%d" height="%d" %s sizes="%s" decoding="async"%s>',
        esc_url($img_url),
        $img_w,
        $img_h,
        $alt_attr,
        esc_attr($sizes_attr),
        $attr_string
    );

    $html .= '</picture>';

    return $html;
}

/**
 * Echo version of octarinepress_get_responsive_image
 */
function octarinepress_responsive_image(int $attachment_id, string $size = 'thumbnail', array $attributes = []): void
{
    echo octarinepress_get_responsive_image($attachment_id, $size, $attributes);
}

/**
 * Output the post thumbnail with responsive srcset
 * Drop-in replacement for the_post_thumbnail()
 */
function octarinepress_the_post_thumbnail(string $size = 'post-thumbnail', array $attributes = []): void
{
    if (! has_post_thumbnail()) {
        return;
    }

    octarinepress_responsive_image(get_post_thumbnail_id(), $size, $attributes);
}

/**
 * Get the post thumbnail with responsive srcset
 * Drop-in replacement for get_the_post_thumbnail()
 */
function octarinepress_get_the_post_thumbnail(?int $post_id = null, string $size = 'post-thumbnail', array $attributes = []): string
{
    $post_id = $post_id ?: get_the_ID();
    $thumbnail_id = get_post_thumbnail_id($post_id);

    if (! $thumbnail_id) {
        return '';
    }

    return octarinepress_get_responsive_image($thumbnail_id, $size, $attributes);
}

/**
 * Build a <picture> element using two different sources (desktop/mobile)
 * while leveraging the cached responsive images + WebP from Images class.
 *
 * @param  int  $desktop_id  Attachment ID for desktop image
 * @param  string  $desktop_size  Custom size key for desktop (e.g. 'banner-large')
 * @param  int  $mobile_id  Attachment ID for mobile image
 * @param  string  $mobile_size  Custom size key for mobile (e.g. 'banner-medium')
 * @param  array  $attributes  Additional attributes for <img> (alt, class, etc.)
 * @param  string  $desktop_media  Media query for desktop source
 * @param  string  $mobile_media  Media query for mobile source (e.g. tablet)
 */
function octarinepress_get_responsive_picture_dual(
    int $desktop_id,
    string $desktop_size,
    int $mobile_id,
    string $mobile_size,
    array $attributes = [],
    string $desktop_media = OCTARINEPRESS_BREAKPOINT_DESKTOP,
    string $mobile_media = OCTARINEPRESS_BREAKPOINT_TABLET
): string {
    if (! $desktop_id && ! $mobile_id) {
        return '';
    }

    // Validate attachment IDs
    if ($desktop_id && get_post_type($desktop_id) !== 'attachment') {
        error_log("Invalid desktop attachment ID: {$desktop_id}");
        $desktop_id = 0;
    }
    if ($mobile_id && get_post_type($mobile_id) !== 'attachment') {
        error_log("Invalid mobile attachment ID: {$mobile_id}");
        $mobile_id = 0;
    }

    // Re-check after validation
    if (! $desktop_id && ! $mobile_id) {
        return '';
    }

    static $images_instance = null;
    if ($images_instance === null) {
        $images_instance = new \octarinepress\Setup\Images;
    }

    // Trigger generation if needed
    if ($desktop_id) {
        wp_get_attachment_image_src($desktop_id, $desktop_size);
    }
    if ($mobile_id) {
        wp_get_attachment_image_src($mobile_id, $mobile_size);
    }

    $sizes_attr = isset($attributes['sizes']) ? (string) $attributes['sizes'] : '100vw';

    // Collect srcset data (get_srcset_data triggers generation via image_downsize filter)
    $desktop_srcset_data = $desktop_id ? $images_instance->get_srcset_data($desktop_id, $desktop_size) : [];
    $mobile_srcset_data = $mobile_id ? $images_instance->get_srcset_data($mobile_id, $mobile_size) : [];

    // Build srcset strings using helper
    $desktop_srcsets = octarinepress_build_srcset_strings($desktop_srcset_data);
    $mobile_srcsets = octarinepress_build_srcset_strings($mobile_srcset_data);

    // Choose <img> base from mobile set (fallback for < 768px) using helper
    $img_base = octarinepress_choose_base_image($mobile_srcset_data);
    if (! $img_base) {
        // Fallback to desktop if mobile missing
        $img_base = octarinepress_choose_base_image($desktop_srcset_data);
    }

    // If still nothing, fall back to WP core
    if (! $img_base) {
        $fallback_id = $mobile_id ?: $desktop_id;
        $fallback_size = $mobile_id ? $mobile_size : $desktop_size;
        $fallback = wp_get_attachment_image_src($fallback_id, $fallback_size);
        if (! $fallback) {
            return '';
        }

        $alt = $attributes['alt'] ?? get_post_meta($fallback_id, '_wp_attachment_image_alt', true);
        $alt_attr = $alt ? sprintf('alt="%s"', esc_attr($alt)) : 'alt=""';

        $attr_string = octarinepress_build_attribute_string($attributes, ['alt', 'sizes']);

        return sprintf(
            '<picture><img src="%s" width="%d" height="%d" %s sizes="%s" decoding="async"%s></picture>',
            esc_url($fallback[0]),
            (int) $fallback[1],
            (int) $fallback[2],
            $alt_attr,
            esc_attr($sizes_attr),
            $attr_string
        );
    }

    // Alt text - try all IDs in priority order
    $alt_val = $attributes['alt'] ?? null;
    if (! $alt_val) {
        foreach ([$mobile_id, $desktop_id] as $id) {
            if ($id) {
                $alt_val = get_post_meta($id, '_wp_attachment_image_alt', true);
                if ($alt_val) {
                    break;
                }
            }
        }
    }
    $alt_attr = $alt_val ? sprintf('alt="%s"', esc_attr($alt_val)) : 'alt=""';

    // Other attributes using helper
    $attr_string = octarinepress_build_attribute_string($attributes, ['alt', 'sizes']);

    // Begin picture
    $html = '<picture>';

    // Desktop WebP
    if (! empty($desktop_srcsets['webp'])) {
        $html .= sprintf(
            '<source srcset="%s" type="image/webp" media="%s" sizes="%s">',
            $desktop_srcsets['webp'],
            esc_attr($desktop_media),
            esc_attr($sizes_attr)
        );
    }
    // Mobile WebP (e.g., tablet breakpoint)
    if (! empty($mobile_srcsets['webp'])) {
        $html .= sprintf(
            '<source srcset="%s" type="image/webp" media="%s" sizes="%s">',
            $mobile_srcsets['webp'],
            esc_attr($mobile_media),
            esc_attr($sizes_attr)
        );
    }

    // Desktop standard
    if (! empty($desktop_srcsets['standard'])) {
        $mime_type = pathinfo($img_base['url'] ?? '', PATHINFO_EXTENSION) === 'png' ? 'image/png' : 'image/jpeg';
        $html .= sprintf(
            '<source srcset="%s" media="%s" sizes="%s" type="%s">',
            $desktop_srcsets['standard'],
            esc_attr($desktop_media),
            esc_attr($sizes_attr),
            $mime_type
        );
    }
    // Mobile standard
    if (! empty($mobile_srcsets['standard'])) {
        $mime_type = pathinfo($img_base['url'] ?? '', PATHINFO_EXTENSION) === 'png' ? 'image/png' : 'image/jpeg';
        $html .= sprintf(
            '<source srcset="%s" media="%s" sizes="%s" type="%s">',
            $mobile_srcsets['standard'],
            esc_attr($mobile_media),
            esc_attr($sizes_attr),
            $mime_type
        );
    }

    // <img> fallback for < 768px (or when sources not supported)
    $img_url = esc_url($img_base['url']);
    $img_w = (int) $img_base['width'];
    $img_h = (int) $img_base['height'];
    $img_srcset_attr = ! empty($mobile_srcsets['standard']) ? ' srcset="' . esc_attr($mobile_srcsets['standard']) . '"' : '';

    $html .= sprintf(
        '<img src="%s"%s width="%d" height="%d" %s sizes="%s" decoding="async"%s>',
        $img_url,
        $img_srcset_attr,
        $img_w,
        $img_h,
        $alt_attr,
        esc_attr($sizes_attr),
        $attr_string
    );

    $html .= '</picture>';

    return $html;
}

/**
 * Echo version of octarinepress_get_responsive_picture_dual
 */
function octarinepress_responsive_picture_dual(
    int $desktop_id,
    string $desktop_size,
    int $mobile_id,
    string $mobile_size,
    array $attributes = [],
    string $desktop_media = OCTARINEPRESS_BREAKPOINT_DESKTOP,
    string $mobile_media = OCTARINEPRESS_BREAKPOINT_TABLET
): void {
    echo octarinepress_get_responsive_picture_dual(
        $desktop_id,
        $desktop_size,
        $mobile_id,
        $mobile_size,
        $attributes,
        $desktop_media,
        $mobile_media
    );
}

/**
 * Build a <picture> element using THREE different sources (desktop/tablet/mobile)
 *
 * @param  int  $desktop_id  Attachment ID for desktop image
 * @param  string  $desktop_size  Custom size key for desktop (e.g. 'banner-large')
 * @param  int  $tablet_id  Attachment ID for tablet image
 * @param  string  $tablet_size  Custom size key for tablet
 * @param  int  $mobile_id  Attachment ID for mobile image
 * @param  string  $mobile_size  Custom size key for mobile
 * @param  array  $attributes  Additional attributes for <img>
 * @param  string  $desktop_media  Media query for desktop source (default: 1200px+)
 * @param  string  $tablet_media  Media query for tablet source (default: 768px+)
 */
function octarinepress_get_responsive_picture_triple(
    int $desktop_id,
    string $desktop_size,
    int $tablet_id,
    string $tablet_size,
    int $mobile_id,
    string $mobile_size,
    array $attributes = [],
    string $desktop_media = OCTARINEPRESS_BREAKPOINT_DESKTOP,
    string $tablet_media = OCTARINEPRESS_BREAKPOINT_TABLET
): string {
    if (! $desktop_id && ! $tablet_id && ! $mobile_id) {
        return '';
    }

    // Validate attachment IDs
    if ($desktop_id && get_post_type($desktop_id) !== 'attachment') {
        error_log("Invalid desktop attachment ID: {$desktop_id}");
        $desktop_id = 0;
    }
    if ($tablet_id && get_post_type($tablet_id) !== 'attachment') {
        error_log("Invalid tablet attachment ID: {$tablet_id}");
        $tablet_id = 0;
    }
    if ($mobile_id && get_post_type($mobile_id) !== 'attachment') {
        error_log("Invalid mobile attachment ID: {$mobile_id}");
        $mobile_id = 0;
    }

    // Re-check after validation
    if (! $desktop_id && ! $tablet_id && ! $mobile_id) {
        return '';
    }

    static $images_instance = null;
    if ($images_instance === null) {
        $images_instance = new \octarinepress\Setup\Images;
    }

    // Trigger generation if needed
    if ($desktop_id) {
        wp_get_attachment_image_src($desktop_id, $desktop_size);
    }
    if ($tablet_id) {
        wp_get_attachment_image_src($tablet_id, $tablet_size);
    }
    if ($mobile_id) {
        wp_get_attachment_image_src($mobile_id, $mobile_size);
    }

    $sizes_attr = isset($attributes['sizes']) ? (string) $attributes['sizes'] : '100vw';

    // Collect srcset data for all three (get_srcset_data triggers generation)
    $desktop_srcset_data = $desktop_id ? $images_instance->get_srcset_data($desktop_id, $desktop_size) : [];
    $tablet_srcset_data = $tablet_id ? $images_instance->get_srcset_data($tablet_id, $tablet_size) : [];
    $mobile_srcset_data = $mobile_id ? $images_instance->get_srcset_data($mobile_id, $mobile_size) : [];

    // Build srcset strings using helper
    $desktop_srcsets = octarinepress_build_srcset_strings($desktop_srcset_data);
    $tablet_srcsets = octarinepress_build_srcset_strings($tablet_srcset_data);
    $mobile_srcsets = octarinepress_build_srcset_strings($mobile_srcset_data);

    // Choose <img> base from mobile set (fallback for < 768px) using helper
    $img_base = octarinepress_choose_base_image($mobile_srcset_data);
    if (! $img_base) {
        $img_base = octarinepress_choose_base_image($tablet_srcset_data);
    }
    if (! $img_base) {
        $img_base = octarinepress_choose_base_image($desktop_srcset_data);
    }

    // If still nothing, fall back to WP core (consistent with dual function)
    if (! $img_base) {
        $fallback_id = $mobile_id ?: $tablet_id ?: $desktop_id;
        $fallback_size = $mobile_id ? $mobile_size : ($tablet_id ? $tablet_size : $desktop_size);
        $fallback = wp_get_attachment_image_src($fallback_id, $fallback_size);
        if (! $fallback) {
            return '';
        }

        $alt = $attributes['alt'] ?? get_post_meta($fallback_id, '_wp_attachment_image_alt', true);
        $alt_attr = $alt ? sprintf('alt="%s"', esc_attr($alt)) : 'alt=""';

        $attr_string = octarinepress_build_attribute_string($attributes, ['alt', 'sizes']);

        return sprintf(
            '<picture><img src="%s" width="%d" height="%d" %s sizes="%s" decoding="async"%s></picture>',
            esc_url($fallback[0]),
            (int) $fallback[1],
            (int) $fallback[2],
            $alt_attr,
            esc_attr($sizes_attr),
            $attr_string
        );
    }

    // Alt text - try all IDs in priority order
    $alt_val = $attributes['alt'] ?? null;
    if (! $alt_val) {
        foreach ([$mobile_id, $tablet_id, $desktop_id] as $id) {
            if ($id) {
                $alt_val = get_post_meta($id, '_wp_attachment_image_alt', true);
                if ($alt_val) {
                    break;
                }
            }
        }
    }
    $alt_attr = $alt_val ? sprintf('alt="%s"', esc_attr($alt_val)) : 'alt=""';

    // Other attributes using helper
    $attr_string = octarinepress_build_attribute_string($attributes, ['alt', 'sizes']);

    // Begin picture
    $html = '<picture>';

    // Desktop WebP (1200px+)
    if (! empty($desktop_srcsets['webp'])) {
        $html .= sprintf(
            '<source srcset="%s" type="image/webp" media="%s" sizes="%s">',
            $desktop_srcsets['webp'],
            esc_attr($desktop_media),
            esc_attr($sizes_attr)
        );
    }

    // Tablet WebP (768px+)
    if (! empty($tablet_srcsets['webp'])) {
        $html .= sprintf(
            '<source srcset="%s" type="image/webp" media="%s" sizes="%s">',
            $tablet_srcsets['webp'],
            esc_attr($tablet_media),
            esc_attr($sizes_attr)
        );
    }

    // Mobile WebP (< 768px)
    if (! empty($mobile_srcsets['webp'])) {
        $html .= sprintf(
            '<source srcset="%s" type="image/webp" sizes="%s">',
            $mobile_srcsets['webp'],
            esc_attr($sizes_attr)
        );
    }

    // <img> fallback
    $img_url_raw = $img_base['url'];
    $img_url = esc_url($img_url_raw);
    $img_w = (int) $img_base['width'];
    $img_h = (int) $img_base['height'];
    $img_srcset_attr = ! empty($mobile_srcsets['standard']) ? ' srcset="' . esc_attr($mobile_srcsets['standard']) . '"' : '';

    $mime_type = pathinfo($img_url_raw, PATHINFO_EXTENSION) === 'png' ? 'image/png' : 'image/jpeg';

    // Desktop Standard (1200px+)
    if (! empty($desktop_srcsets['standard'])) {
        $html .= sprintf(
            '<source srcset="%s" media="%s" sizes="%s" type="%s">',
            $desktop_srcsets['standard'],
            esc_attr($desktop_media),
            esc_attr($sizes_attr),
            $mime_type
        );
    }

    // Tablet Standard (768px+)
    if (! empty($tablet_srcsets['standard'])) {
        $html .= sprintf(
            '<source srcset="%s" media="%s" sizes="%s" type="%s">',
            $tablet_srcsets['standard'],
            esc_attr($tablet_media),
            esc_attr($sizes_attr),
            $mime_type
        );
    }

    // Mobile Standard (< 768px)
    if (! empty($mobile_srcsets['standard'])) {
        $html .= sprintf(
            '<source srcset="%s" sizes="%s" type="%s">',
            $mobile_srcsets['standard'],
            esc_attr($sizes_attr),
            $mime_type
        );
    }

    $html .= sprintf(
        '<img src="%s"%s width="%d" height="%d" %s sizes="%s" decoding="async"%s>',
        $img_url,
        $img_srcset_attr,
        $img_w,
        $img_h,
        $alt_attr,
        esc_attr($sizes_attr),
        $attr_string
    );

    $html .= '</picture>';

    return $html;
}
