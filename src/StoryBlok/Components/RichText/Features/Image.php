<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Features;

use App\Services\StoryBlok\Components\SerializableComponent;
use App\Services\StoryBlok\Resources\Asset;
use JsonSerializable;

class Image implements RichTextFeature, JsonSerializable
{
    use SerializableComponent;

    public const NAME = 'image';

    public function __construct(
        public readonly string $src,
        public readonly int|null $id = null,
        public readonly string $alt = '',
        public readonly string $title = '',
        public readonly string $source = '',
        public readonly string $copyright = '',
        public readonly array $metaData = [],
        public readonly array $marks = [],
    ) {}

    public static function fromAsset(Asset $asset, array $marks = []): self
    {
        return new self(
            src: $asset->getUri(),
            id: $asset->getId(),
            alt: $asset->data['alt'] ?? '',
            title: $asset->data['title'] ?? '',
            source: $asset->data['source'] ?? '',
            copyright: $asset->data['copyright'] ?? '',
            metaData: $asset->data['meta_data'] ?? [],
            marks: $marks,
        );
    }

    public function toArray(): array
    {
        return [
            'type' => 'image',
            'attrs' => [
                'alt' => $this->alt,
                'src' => $this->src,
                'title' => $this->title,
                'source' => $this->source,
                'copyright' => $this->copyright,
                'meta_data' => $this->metaData,
                ...($this->id ? ['id' => $this->id] : []),
            ],
            'marks' => $this->marks,
        ];
    }
}
