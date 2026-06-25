import { InspectorControls, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const TEMPLATE = [
    ['octarinepress/column', {}],
    ['octarinepress/column', {}],
];

const GAP_OPTIONS = [
    { label: 'gap-4', value: 'gap-4' },
    { label: 'gap-6', value: 'gap-6' },
    { label: 'gap-8', value: 'gap-8' },
    { label: 'gap-10', value: 'gap-10' },
    { label: 'gap-12', value: 'gap-12' },
    { label: 'gap-16', value: 'gap-16' },
    { label: 'gap-20', value: 'gap-20' },
    { label: 'gap-24', value: 'gap-24' },
    { label: 'gap-32', value: 'gap-32' },
];

export default function Edit({ attributes, setAttributes }) {
    const {
        mobileGapClass = 'gap-8',
        desktopGapClass = 'gap-8',
    } = attributes;
    const blockProps = useBlockProps({ className: 'two-columns-block container-grid' });

    const innerBlocksProps = useInnerBlocksProps(
        {
            className: `col-start-[content-start] col-end-[content-end] grid md:grid-cols-2 ${mobileGapClass} md:${desktopGapClass} mt-16 lg:mt-24`,
        },
        {
            template: TEMPLATE,
            templateLock: 'all',
            allowedBlocks: ['octarinepress/column'],
        }
    );

    return (
        <section {...blockProps}>
            <InspectorControls>
                <PanelBody title={__('Layout', 'octarinepress')} initialOpen={true}>
                    <SelectControl
                        label={__('Mobile gap class', 'octarinepress')}
                        value={mobileGapClass}
                        options={GAP_OPTIONS}
                        onChange={(value) => setAttributes({ mobileGapClass: value })}
                    />
                    <SelectControl
                        label={__('Desktop gap class', 'octarinepress')}
                        value={desktopGapClass}
                        options={GAP_OPTIONS}
                        onChange={(value) => setAttributes({ desktopGapClass: value })}
                    />
                </PanelBody>
            </InspectorControls>
            <div {...innerBlocksProps} />
        </section>
    );
}
