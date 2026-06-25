import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const {
        mobileGapClass = 'gap-8',
        desktopGapClass = 'gap-8',
    } = attributes;

    return (
        <section {...useBlockProps.save({ className: 'two-columns-block container-grid' })}>
            <div className={`col-start-[content-start] col-end-[content-end] grid md:grid-cols-2 ${mobileGapClass} md:${desktopGapClass} mt-16 lg:mt-24`}>
                <InnerBlocks.Content />
            </div>
        </section>
    );
}
