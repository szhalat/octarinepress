import { useState } from '@wordpress/element';
import { RichTextToolbarButton, ColorPalette } from '@wordpress/block-editor';
import { registerFormatType, applyFormat, removeFormat } from '@wordpress/rich-text';
import { Popover } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const FORMAT_TYPE = 'octarinepress/inline-text-color';

registerFormatType(FORMAT_TYPE, {
    title: __('Text color', 'octarinepress'),
    tagName: 'span',
    className: 'hal-text-color',
    attributes: {
        style: 'style',
    },
    edit({ isActive, value, onChange, activeAttributes }) {
        const [isOpen, setIsOpen] = useState(false);

        const currentColor = isActive && activeAttributes?.style
            ? activeAttributes.style.replace('color:', '').replace(';', '').trim()
            : undefined;

        return (
            <>
                <RichTextToolbarButton
                    icon="art"
                    title={__('Text color', 'octarinepress')}
                    onClick={() => setIsOpen((prev) => !prev)}
                    isActive={isActive}
                />
                {isOpen && (
                    <Popover onClose={() => setIsOpen(false)} placement="bottom-start">
                        <div style={{ padding: '16px', minWidth: '200px' }}>
                            <ColorPalette
                                value={currentColor}
                                onChange={(color) => {
                                    if (!color) {
                                        onChange(removeFormat(value, FORMAT_TYPE));
                                        return;
                                    }
                                    onChange(
                                        applyFormat(value, {
                                            type: FORMAT_TYPE,
                                            attributes: { style: `color: ${color};` },
                                        })
                                    );
                                }}
                                clearable={isActive}
                            />
                        </div>
                    </Popover>
                )}
            </>
        );
    },
});
