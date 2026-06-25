import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

export default function save() {
    return (
        <div {...useBlockProps.save({ className: 'two-columns-block__column' })}>
            <InnerBlocks.Content />
        </div>
    );
}
