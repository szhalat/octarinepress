import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

export default function Edit() {
    const blockProps = useBlockProps({ className: 'content-grid-block container-grid' });
    const innerBlocksProps = useInnerBlocksProps(
        {
            className: 'col-start-[content-start] col-end-[content-end]',
        }
    );

    return (
        <section {...blockProps}>
            <div {...innerBlocksProps} />
        </section>
    );
}
