<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Embeds;

use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Fields\Asset;

class PdfEmbed extends Component
{
    public const NAME = 'SB006_19';

    public function __construct(
        public readonly Asset $asset,
        public readonly int $height = 700,
        public readonly bool $showLinkTo = false,
        public readonly string $linkText = 'View PDF',
    ) {}

    public function toArray(): array
    {
        return [
            'PDF' => $this->asset,
            'height' => $this->height,
            'showLinkTo' => $this->showLinkTo,
            'linkText' => $this->linkText,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            asset: Asset::deserialise($data['asset']),
            height: $data['height'] ?? 700,
            showLinkTo: $data['showLinkTo'] ?? false,
            linkText: $data['linkText'] ?? 'View PDF',
        );
    }
}
