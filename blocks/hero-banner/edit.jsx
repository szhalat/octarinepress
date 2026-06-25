import {
    MediaUpload,
    MediaUploadCheck,
    RichText,
    URLInput,
    useBlockProps,
} from '@wordpress/block-editor';
import {
    Button,
    Card,
    CardBody,
    CardHeader,
    Placeholder,
    TextControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export default function Edit({ attributes, setAttributes }) {
    const {
        topTitle,
        title,
        shortText,
        linkText,
        linkUrl,
        linkText2,
        linkUrl2,
        imageId,
        imageUrl,
        tags = [],
    } = attributes;

    const updateTag = (index, text) => {
        const nextTags = tags.map((tag, tagIndex) => (
            tagIndex === index ? { ...tag, text } : tag
        ));

        setAttributes({ tags: nextTags });
    };

    const moveTag = (index, direction) => {
        const destination = index + direction;

        if (destination < 0 || destination >= tags.length) {
            return;
        }

        const nextTags = [...tags];
        [nextTags[index], nextTags[destination]] = [nextTags[destination], nextTags[index]];
        setAttributes({ tags: nextTags });
    };

    const removeTag = (index) => {
        setAttributes({ tags: tags.filter((tag, tagIndex) => tagIndex !== index) });
    };

    return (
        <div {...useBlockProps()}>
            <Placeholder
                icon="cover-image"
                label={__('Hero Banner', 'octarinepress')}
                instructions={__('Configure the hero banner content.', 'octarinepress')}
            >
                <div style={{ width: '100%' }}>
                    <Card>
                        <CardHeader>
                            <strong>{__('Content', 'octarinepress')}</strong>
                        </CardHeader>
                        <CardBody>
                            <TextControl
                                label={__('Top title', 'octarinepress')}
                                value={topTitle}
                                onChange={(value) => setAttributes({ topTitle: value })}
                            />
                            <RichText
                                tagName="div"
                                value={title}
                                onChange={(value) => setAttributes({ title: value })}
                                placeholder={__('Title', 'octarinepress')}
                                allowedFormats={['core/bold', 'core/italic', 'core/link']}
                            />
                            <RichText
                                tagName="div"
                                value={shortText}
                                onChange={(value) => setAttributes({ shortText: value })}
                                placeholder={__('Short text', 'octarinepress')}
                                allowedFormats={['core/bold', 'core/italic', 'core/link']}
                            />
                        </CardBody>
                    </Card>

                    <Card>
                        <CardHeader>
                            <strong>{__('Links', 'octarinepress')}</strong>
                        </CardHeader>
                        <CardBody>
                            <TextControl
                                label={__('Link text', 'octarinepress')}
                                value={linkText}
                                onChange={(value) => setAttributes({ linkText: value })}
                            />
                            <URLInput
                                value={linkUrl}
                                onChange={(value) => setAttributes({ linkUrl: value })}
                            />
                            <TextControl
                                label={__('Link text 2', 'octarinepress')}
                                value={linkText2}
                                onChange={(value) => setAttributes({ linkText2: value })}
                            />
                            <URLInput
                                value={linkUrl2}
                                onChange={(value) => setAttributes({ linkUrl2: value })}
                            />
                        </CardBody>
                    </Card>

                    <Card>
                        <CardHeader>
                            <strong>{__('Image', 'octarinepress')}</strong>
                        </CardHeader>
                        <CardBody>
                            <MediaUploadCheck>
                                <MediaUpload
                                    allowedTypes={['image']}
                                    value={imageId}
                                    onSelect={(media) => setAttributes({
                                        imageId: media?.id || 0,
                                        imageUrl: media?.url || '',
                                    })}
                                    render={({ open }) => (
                                        <>
                                            {imageUrl ? <img src={imageUrl} alt="" /> : null}
                                            <Button onClick={open} variant="secondary">
                                                {imageUrl
                                                    ? __('Replace image', 'octarinepress')
                                                    : __('Select image', 'octarinepress')}
                                            </Button>
                                            {imageUrl ? (
                                                <Button
                                                    variant="secondary"
                                                    isDestructive
                                                    onClick={() => setAttributes({
                                                        imageId: 0,
                                                        imageUrl: '',
                                                    })}
                                                >
                                                    {__('Remove image', 'octarinepress')}
                                                </Button>
                                            ) : null}
                                        </>
                                    )}
                                />
                            </MediaUploadCheck>
                        </CardBody>
                    </Card>

                    <Card>
                        <CardHeader>
                            <strong>{__('Tags', 'octarinepress')}</strong>
                        </CardHeader>
                        <CardBody>
                            {tags.map((tag, index) => (
                                <div key={index}>
                                    <TextControl
                                        label={`${__('Tag', 'octarinepress')} ${index + 1}`}
                                        value={tag?.text || ''}
                                        onChange={(value) => updateTag(index, value)}
                                    />
                                    <Button
                                        icon="arrow-up-alt2"
                                        label={__('Move up', 'octarinepress')}
                                        disabled={index === 0}
                                        onClick={() => moveTag(index, -1)}
                                    />
                                    <Button
                                        icon="arrow-down-alt2"
                                        label={__('Move down', 'octarinepress')}
                                        disabled={index === tags.length - 1}
                                        onClick={() => moveTag(index, 1)}
                                    />
                                    <Button
                                        icon="trash"
                                        label={__('Remove tag', 'octarinepress')}
                                        isDestructive
                                        onClick={() => removeTag(index)}
                                    />
                                </div>
                            ))}
                            <Button
                                variant="secondary"
                                onClick={() => setAttributes({ tags: [...tags, { text: '' }] })}
                            >
                                {__('Add tag', 'octarinepress')}
                            </Button>
                        </CardBody>
                    </Card>
                </div>
            </Placeholder>
        </div>
    );
}
