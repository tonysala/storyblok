<?php

namespace App\Services\StoryBlok\Components\RichText\Features\Marks;

use App\Services\StoryBlok\Fields\Link\Link as LinkField;
use App\Services\StoryBlok\Fields\SerializableField;
use JsonSerializable;

class Link implements JsonSerializable
{
    use SerializableField;

    public function __construct(
        public readonly LinkField $location,
        public readonly string $anchor = '',
    ) {
    }

    public function serialise(): array
    {
        return [
            'type' => 'link',
            'attrs' => [
                'href' => $this->location->getUri(),
                'uuid' => $this->location->id,
                'anchor' => $this->anchor,
                'target' => $this->location->target,
                'linktype' => $this->location->linkType,
            ],
        ];
    }
}
