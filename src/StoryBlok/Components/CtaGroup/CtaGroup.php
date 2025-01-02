<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\CtaGroup;

use App\Services\StoryBlok\Attributes\WithComponents;
use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentFactory;
use App\Services\StoryBlok\Components\ComponentInterface;

class CtaGroup extends Component
{
    public const NAME = 'SB004_3';

    public function __construct(
        #[WithComponents] public readonly array $content,
        public readonly string $headline = '',
    ) {}

    public function toArray(): array
    {
        return [
            'children' => $this->content,
            'headline' => $this->headline,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            content: array_map(fn(array $item) => ComponentFactory::deserialise($item), $data['children']),
        );
    }
}
