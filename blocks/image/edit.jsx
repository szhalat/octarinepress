import { MediaUpload, MediaUploadCheck, RichText, useBlockProps } from '@wordpress/block-editor';
import { Button, Card, CardBody, CardHeader, Placeholder, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export default function Edit({ attributes, setAttributes }) {
    const { imageId, imageUrl, imageAlt, caption } = attributes;
    const blockProps = useBlockProps();

    const onSelectImage = (media) => {
        setAttributes({
            imageId: media?.id || 0,
            imageUrl: media?.url || '',
            imageAlt: media?.alt || '',
            caption: media?.caption?.raw || media?.caption?.rendered || '',
        });
    };

    return (
        <div {...blockProps}>
            <Placeholder
                icon="format-image"
                label={__('Image', 'octarinepress')}
                instructions={__('Select an image rendered with the theme responsive image generator.', 'octarinepress')}
            >
                <div style={{ width: '100%' }}>
                    <Card>
                        <CardHeader>
	                            <strong>{__('Image', 'octarinepress')}</strong>
                        </CardHeader>
                        <CardBody>
                            <MediaUploadCheck>
                                <MediaUpload
                                    onSelect={onSelectImage}
                                    allowedTypes={['image']}
                                    value={imageId}
                                    render={({ open }) => (
                                        <div>
                                            {imageUrl ? (
                                                <div style={{ marginBottom: '12px' }}>
                                                    <img
                                                        src={imageUrl}
                                                        alt=""
                                                        style={{ display: 'block', maxWidth: '100%', height: 'auto' }}
                                                    />
                                                </div>
                                            ) : null}

                                            <div style={{ display: 'flex', gap: '8px', flexWrap: 'wrap' }}>
                                                <Button onClick={open} variant="secondary">
	                                                    {imageUrl ? __('Replace image', 'octarinepress') : __('Select image', 'octarinepress')}
                                                </Button>
                                                {imageUrl ? (
                                                    <Button
                                                        onClick={() => setAttributes({
                                                            imageId: 0,
                                                            imageUrl: '',
                                                            imageAlt: '',
                                                            caption: '',
                                                        })}
                                                        variant="secondary"
                                                        isDestructive
                                                    >
	                                                        {__('Remove image', 'octarinepress')}
                                                    </Button>
                                                ) : null}
                                            </div>
                                        </div>
                                    )}
                                />
                            </MediaUploadCheck>
                        </CardBody>
                    </Card>

                    <Card style={{ marginTop: '16px' }}>
                        <CardHeader>
	                            <strong>{__('Content', 'octarinepress')}</strong>
                        </CardHeader>
                        <CardBody>
                            <TextControl
	                                label={__('Alt text', 'octarinepress')}
                                value={imageAlt}
                                onChange={(value) => setAttributes({ imageAlt: value })}
                            />
                            <div style={{ marginTop: '16px' }}>
	                                <label style={{ display: 'block', marginBottom: '8px', fontWeight: 600 }}>{__('Caption', 'octarinepress')}</label>
                                <RichText
                                    tagName="div"
                                    value={caption}
                                    onChange={(value) => setAttributes({ caption: value })}
	                                    placeholder={__('Add caption...', 'octarinepress')}
                                    allowedFormats={['core/bold', 'core/italic', 'core/link', 'octarinepress/inline-text-color']}
                                    style={{ color: 'var(--wp--preset--color--black, #201F1B)' }}
                                />
                            </div>
                        </CardBody>
                    </Card>
                </div>
            </Placeholder>
        </div>
    );
}
