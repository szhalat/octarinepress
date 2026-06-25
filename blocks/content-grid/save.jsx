import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

export default function save() {
    return (
        <section {...useBlockProps.save({ className: 'content-grid-block container-grid' })}>
            <div className="col-start-[content-start] col-end-[content-end] mt-16 lg:mt-24">
                <InnerBlocks.Content />
            </div>
        </section>
    );
}
