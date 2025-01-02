<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Table;

use App\Services\StoryBlok\Fields\Document;
use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Components\Component;

class TableCell extends Component
{
    public const NAME = 'table_cell';

    public function __construct(
        public readonly Document $text = new Document(),
    ) {}

    public function toArray(): array
    {
        return [
            'text' => $this->text->serialise(),
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            text: Document::deserialise($data['text']),
        );
    }
}
