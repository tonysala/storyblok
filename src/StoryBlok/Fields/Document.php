<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Fields;

use JsonSerializable;

class Document implements FieldInterface, JsonSerializable
{
    use SerializableField;

    public function __construct(
        public readonly array $content = [],
    ) {
    }

    public function serialise(): array
    {
        return [
            'type' => 'doc',
            'content' => $this->content,
        ];
    }

    public static function deserialise(array $data): self
    {
        return new self(
            content: $data['content'],
        );
    }
}
