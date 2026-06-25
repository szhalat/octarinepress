<?php if (is_single()) { ?>
    <div class="bg-black">
        <div class="flex flex-wrap justify-between text-white container !lg:px-0 py-8 lg:py-32 text-2xl lg:text-5xl font-body font-semibold">
            <div class="flex items-center"><?php previous_post_link('%link', '<svg width="42" height="44" viewBox="0 0 42 44" class="mr-4" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="41" height="42" transform="translate(0 0.737305)" fill="#BF1F1F"/>
                <path d="M34.959 20.7829C34.959 19.7672 34.1402 18.9394 33.1156 18.9394L17.8354 18.9394L24.3188 12.456C25.0348 11.7401 25.0348 10.5722 24.3188 9.84294L22.5783 8.10236C21.8624 7.38645 20.6946 7.38645 19.9653 8.10236L7.36974 20.6978C7.01179 21.0558 6.82837 21.5256 6.82837 21.9999C6.82837 22.4697 7.01179 22.944 7.36974 23.302L19.9653 35.8975C20.6812 36.6134 21.849 36.6134 22.5783 35.8975L24.3188 34.1569C25.0348 33.441 25.0348 32.2732 24.3188 31.5439L17.8354 25.0828L33.1065 25.0828C34.1223 25.0828 34.9501 24.255 34.9501 23.2393L34.959 20.7829Z" fill="#FDFDFD"/>
	                </svg> ' . esc_html__('Previous', 'octarinepress')); ?></div>
            <div class="flex items-center"><?php next_post_link('%link', esc_html__('Next', 'octarinepress') . ' <svg width="42" height="44" viewBox="0 0 42 44" class="ml-4" fill="none" xmlns="http://www.w3.org/2000/svg">
<rect width="41" height="42" transform="translate(41.7874 43.2627) rotate(-180)" fill="#BF1F1F"/>
<path d="M6.82834 23.2171C6.82834 24.2328 7.64713 25.0606 8.6718 25.0606L23.952 25.0606L17.4685 31.544C16.7526 32.2599 16.7526 33.4278 17.4685 34.1571L19.209 35.8976C19.9249 36.6135 21.0928 36.6135 21.8221 35.8976L34.4176 23.3022C34.7756 22.9442 34.959 22.4744 34.959 22.0001C34.959 21.5303 34.7756 21.056 34.4176 20.698L21.8221 8.10249C21.1062 7.38664 19.9383 7.38664 19.209 8.10249L17.4685 9.84306C16.7526 10.559 16.7526 11.7268 17.4685 12.4561L23.952 18.9172L8.68081 18.9172C7.66508 18.9172 6.83729 19.745 6.83729 20.7607L6.82834 23.2171Z" fill="#FDFDFD"/>
</svg>'); ?></div>
        </div>
    </div>

<?php } ?>
