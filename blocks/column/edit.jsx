import { InnerBlocks, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

const ALLOWED_BLOCKS = [
    'core/paragraph',
    'core/heading',
    'core/list',
    'core/quote',
    'core/buttons',
    'core/separator',
    'octarinepress/custom-list',
    'octarinepress/image'
];

export default function Edit() {
    const innerBlocksProps = useInnerBlocksProps(
        useBlockProps({
            className: 'two-columns-block__column',
            style: {
                minHeight: '140px',
                padding: '16px',
            },
        }),
        {
            allowedBlocks: ALLOWED_BLOCKS,
            templateLock: false,
            renderAppender: InnerBlocks.ButtonBlockAppender,
        }
    );

    return <div {...innerBlocksProps} />;
}
