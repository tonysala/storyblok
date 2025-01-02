<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Embeds;

use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentInterface;

class TfaFormEmbed extends Component
{
    public const NAME = 'SB006_3';

    public function __construct(
        public readonly string $id,
    ) {}

    public function toArray(): array
    {
        return [
            'tfa_id' => $this->id,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            id: $data['tfa_id'],
        );
    }
}
