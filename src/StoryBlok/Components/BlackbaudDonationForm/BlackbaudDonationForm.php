<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\BlackbaudDonationForm;

use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentInterface;

class BlackbaudDonationForm extends Component
{
    public const NAME = 'BlackbaudDonationForm';

    public function __construct(
        public readonly string $uuid,
    ) {}

    public function toArray(): array
    {
        return [
            'UUID' => $this->uuid,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            uuid: $data['UUID'],
        );
    }
}
