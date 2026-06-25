<?php
$image_id = isset($attributes['imageId']) ? (int) $attributes['imageId'] : 0;
$image_url = isset($attributes['imageUrl']) ? esc_url($attributes['imageUrl']) : '';
$image_alt = isset($attributes['imageAlt']) ? sanitize_text_field($attributes['imageAlt']) : '';
$allowed_html = wp_kses_allowed_html('post');
$allowed_html['span'] = [
    'style' => true,
    'class' => true,
];
$caption = isset($attributes['caption']) ? wp_kses($attributes['caption'], $allowed_html) : '';

if (! $image_id && ! $image_url) {
    return;
}
?>
<figure class="octarinepress-image-block">
    <?php if ($image_id) { ?>
        <?php
        octarinepress_responsive_image($image_id, 'content-image', [
            'class' => 'octarinepress-image-block__image w-full h-auto',
            'alt' => $image_alt,
            'sizes' => '(max-width: 1024px) 100vw, 50vw',
            'loading' => 'lazy',
        ]);
        ?>
    <?php } else { ?>
        <img class="octarinepress-image-block__image w-full h-auto" src="<?php echo $image_url; ?>" alt="<?php echo esc_attr($image_alt); ?>" loading="lazy">
    <?php } ?>

    <?php if ($caption) { ?>
        <figcaption class="octarinepress-image-block__caption mt-3"><?php echo $caption; ?></figcaption>
    <?php } ?>
</figure>
