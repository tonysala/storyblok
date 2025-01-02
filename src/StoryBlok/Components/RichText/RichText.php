<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText;

use App\Services\StoryBlok\Fields\Document;
use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Components\Component;

class RichText extends Component
{
    public readonly Document $content;

    public const NAME = 'richText';

    public function __construct(
        array|Document $content = new Document(),
        public readonly bool $paddingTop = false,
        public readonly bool $paddingBottom = false,
        public readonly TextAlignment $textAlignment = TextAlignment::NOT_SET,
    ) {
        $this->content = $content instanceof Document ? $content : new Document($content);
    }

    public function toArray(): array
    {
        return [
            'paddingTop' => false,
            'paddingBottom' => false,
            'textAlignment' => $this->textAlignment->value,
            'richText' => $this->content->serialise(),
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): self
    {
        return new self(
            content: Document::deserialise($data['richText']),
            paddingTop: $data['paddingTop'] ?? false,
            paddingBottom: $data['paddingBottom'] ?? false,
            textAlignment: TextAlignment::from($data['textAlignment'] ?? ''),
        );
    }
}
