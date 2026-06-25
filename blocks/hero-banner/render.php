<?php
$top_title = isset($attributes['topTitle']) ? sanitize_text_field($attributes['topTitle']) : '';
$title = isset($attributes['title']) ? wp_kses_post($attributes['title']) : '';
$short_text = isset($attributes['shortText']) ? wp_kses_post($attributes['shortText']) : '';
$link_text = isset($attributes['linkText']) ? sanitize_text_field($attributes['linkText']) : '';
$link_url = isset($attributes['linkUrl']) ? esc_url($attributes['linkUrl']) : '';
$link_text_2 = isset($attributes['linkText2']) ? sanitize_text_field($attributes['linkText2']) : '';
$link_url_2 = isset($attributes['linkUrl2']) ? esc_url($attributes['linkUrl2']) : '';
$image_id = isset($attributes['imageId']) ? (int) $attributes['imageId'] : 0;
$image_url = isset($attributes['imageUrl']) ? esc_url($attributes['imageUrl']) : '';
$tags = isset($attributes['tags']) && is_array($attributes['tags']) ? $attributes['tags'] : [];

$tags = array_values(array_filter(array_map(
    static function ($tag) {
        return is_array($tag) && isset($tag['text'])
            ? sanitize_text_field($tag['text'])
            : '';
    },
    $tags
)));

if (
    ! $top_title
    && ! $title
    && ! $short_text
    && ! ($link_text && $link_url)
    && ! ($link_text_2 && $link_url_2)
    && ! $image_id
    && ! $image_url
    && ! $tags
) {
    return;
}
?>
<section <?php echo get_block_wrapper_attributes(['class' => 'hero-banner-block']); ?>>
    <div class="hero-banner-block__content">
        <?php if ($top_title) { ?>
            <p class="hero-banner-block__top-title"><?php echo esc_html($top_title); ?></p>
        <?php } ?>

        <?php if ($title) { ?>
            <h1 class="hero-banner-block__title"><?php echo $title; ?></h1>
        <?php } ?>

        <?php if ($short_text) { ?>
            <div class="hero-banner-block__short-text"><?php echo $short_text; ?></div>
        <?php } ?>

        <?php if (($link_text && $link_url) || ($link_text_2 && $link_url_2)) { ?>
            <div class="hero-banner-block__links">
                <?php if ($link_text && $link_url) { ?>
                    <a class="hero-banner-block__link" href="<?php echo esc_url($link_url); ?>">
                        <?php echo esc_html($link_text); ?>
                    </a>
                <?php } ?>

                <?php if ($link_text_2 && $link_url_2) { ?>
                    <a class="hero-banner-block__link hero-banner-block__link--secondary" href="<?php echo esc_url($link_url_2); ?>">
                        <?php echo esc_html($link_text_2); ?>
                    </a>
                <?php } ?>
            </div>
        <?php } ?>

        <?php if ($tags) { ?>
            <ul class="hero-banner-block__tags">
                <?php foreach ($tags as $tag) { ?>
                    <li class="hero-banner-block__tag"><?php echo esc_html($tag); ?></li>
                <?php } ?>
            </ul>
        <?php } ?>
    </div>

    <?php if ($image_id || $image_url) { ?>
        <div class="hero-banner-block__media">
            <?php if ($image_id) { ?>
                <?php echo wp_get_attachment_image($image_id, 'full', false, [
                    'class' => 'hero-banner-block__image',
                ]); ?>
            <?php } else { ?>
                <img class="hero-banner-block__image" src="<?php echo esc_url($image_url); ?>" alt="">
            <?php } ?>
        </div>
    <?php } ?>
</section>
