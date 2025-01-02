<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Embeds;

use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Components\Component;

class UniBuddyCarousel extends Component
{
    public const NAME = 'SB061';

    public function __construct(
        public readonly string $slug = '',
        public readonly string $language = 'en-GB',
        public readonly string $filterKey = '',
        public readonly string $filterValue = '',
        public readonly string $cookieConsent = 'necessary',
    ) {}

    public function toArray(): array
    {
        return [
            'ubLang' => $this->language,
            'filterKey' => $this->filterKey,
            'filterValue' => $this->filterValue,
            'universitySlug' => $this->slug,
            'ubCookieConsent' => $this->cookieConsent,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            slug: $data['universitySlug'],
            language: $data['ubLang'],
            filterKey: $data['filterKey'],
            filterValue: $data['filterValue'],
            cookieConsent: $data['ubCookieConsent'],
        );
    }
}
