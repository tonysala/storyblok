<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\HorizontalCard;

use App\Services\StoryBlok\Attributes\WithComponents;
use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Components\Image\Image;
use App\Services\StoryBlok\Components\RichText\RichText;
use App\Services\StoryBlok\Enums\Colour;

class HorizontalCard extends Component
{
    public const NAME = 'SB028_2';

    public function __construct(
        #[WithComponents] public readonly array $graphic,
        #[WithComponents] public readonly array $body,
        public readonly Colour $colour = Colour::NOT_SET,
        public readonly Alignment $alignment = Alignment::RIGHT,
        public readonly GraphicsWidth $graphicsWidth = GraphicsWidth::FIFTY,
        public readonly ContentPadding $contentPadding = ContentPadding::DEFAULT,
        public readonly bool $stretch = true,
    ) {}

    public function toArray(): array
    {
        return [
            'graphic' => $this->graphic,
            'body' => $this->body,
            'colour' => $this->colour->value,
            'alignment' => $this->alignment->value,
            'graphics_width' => $this->graphicsWidth->value,
            'content_padding' => $this->contentPadding->value,
            'stretch_image' => $this->stretch,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            graphic: array_map(fn(array $item) => Image::deserialise($item), $data['graphic']),
            body: array_map(fn(array $item) => RichText::deserialise($item), $data['body']),
            colour: Colour::from($data['colour']),
            alignment: Alignment::from($data['alignment']),
            graphicsWidth: GraphicsWidth::from($data['graphics_width']),
            contentPadding: ContentPadding::from($data['content_padding']),
            stretch: $data['stretch_image'],
        );
    }
}
