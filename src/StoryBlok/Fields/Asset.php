<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Fields;

use App\Services\StoryBlok\Resources\Asset as ResourceAsset;
use JsonSerializable;

class Asset implements FieldInterface, JsonSerializable
{
    use SerializableField;

    public function __construct(
        public ?int $id = null,
        public string $filename = '',
        public array $metaData = [],
        public ?string $alt = '',
        public ?string $focus = '',
        public ?string $title = '',
        public ?string $source = '',
        public ?string $copyright = '',
        public bool $isExternal = false,
    ) {}

    public static function fromResource(ResourceAsset $resource): self
    {
        return new self(
            id: $resource->data['id'],
            filename: $resource->data['filename'],
            metaData: $resource->data['meta_data'],
            alt: $resource->data['alt'] ?? '',
            focus: $resource->data['focus'] ?? '',
            title: $resource->data['title'] ?? '',
            source: $resource->data['source'] ?? '',
            copyright: $resource->data['copyright'] ?? '',
            isExternal: false,
        );
    }

    public function getUri(): string
    {
        return str_replace('s3.amazonaws.com/', '', $this->filename);
    }

    public function serialise(): array
    {
        return [
            'id' => $this->id,
            'alt' => $this->alt,
            'focus' => $this->focus,
            'title' => $this->title,
            'source' => $this->source,
            'filename' => $this->getUri(),
            'fieldtype' => 'asset',
            'copyright' => $this->copyright,
            'meta_data' => $this->metaData,
            'is_external_url' => $this->isExternal,
        ];
    }

    public static function deserialise(array $data): self
    {
        return new self(
            id: $data['id'],
            filename: $data['filename'],
            metaData: $data['meta_data'],
            alt: $data['alt'],
            focus: $data['focus'],
            title: $data['title'],
            source: $data['source'],
            copyright: $data['copyright'],
            isExternal: $data['is_external_url'] ?? false,
        );
    }
}
