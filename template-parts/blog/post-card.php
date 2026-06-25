<div class="grid bg-light-gray post-card">
	<?php
    if (! empty($args['post'])) {
        $post = $args['post'];
    }
	?>
    <div class="">
		<?php $img_srcset = wp_get_attachment_image_srcset($post->ID, 'blog-large'); ?>
        <a
                href="<?php the_permalink($post->ID); ?>" class="img-link h-full w-full">
			    <?php echo get_the_post_thumbnail($post->ID, 'blog-aside', ['class' => 'object-cover h-full']); ?>
        </a>
    </div>
    <div class="py-5 px-6 lg:px-10">
        <div class="flex items-center text-xs justify-end">
            <svg width="10" height="11" viewBox="0 0 10 11" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0)">
                    <path d="M8.33331 1.74999H7.91665V0.916656H7.08331V1.74999H2.91665V0.916656H2.08331V1.74999H1.66665C1.20831 1.74999 0.833313 2.12499 0.833313 2.58332V9.24999C0.833313 9.70832 1.20831 10.0833 1.66665 10.0833H8.33331C8.79165 10.0833 9.16665 9.70832 9.16665 9.24999V2.58332C9.16665 2.12499 8.79165 1.74999 8.33331 1.74999ZM8.33331 9.24999H1.66665V3.83332H8.33331V9.24999Z"
                          fill="#434343"/>
                </g>
                <defs>
                    <clipPath id="clip0">
                        <rect width="10" height="10" fill="white"
                              transform="translate(0 0.5)"/>
                    </clipPath>
                </defs>
            </svg>
            <time class="ml-1"
                  datetime="<?php echo get_the_time('c', $post->ID) ?>"><?php echo get_the_date('d.m.Y', $post->ID); ?></time>
        </div>
        <div class="post-card__meta flex flex-wrap items-center text-xs my-2">
           <?php octarinepress_categories($post->ID, 2); ?>
        </div>
        <h3 class="text-lg hover:text-red"><a
                    href="<?php the_permalink($post->ID); ?>"><?php echo get_the_title($post->ID); ?></a>
        </h3>
        <p><?php echo octarinepress_excerpt(15, $post->ID); ?></p>
    </div>
</div>