<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\Embeds\UniBuddyCarousel;
use App\Services\StoryBlok\Components\RichText\Features\Blok;
use DOMElement;
use DOMXPath;

class UniBuddyCarouselParser
{
    public function __construct(
        public readonly DOMElement $element,
        public readonly DOMXPath $document
    ) {
    }

    public function parse(): array
    {
        // find the script tags
        $settings = $this->document->query("//script[contains(text(), 'window.unibuddySettings')]")->item(0);

        $settingsContent = $settings->nodeValue;
        preg_match_all('/\b(\w+)\s*:\s*["\']?(.*?)["\']?\s*,/', $settingsContent, $matches, PREG_SET_ORDER);

        // Initialize an array to store key-value pairs
        $settings = [];
        foreach ($matches as $match) {
            // Store key-value pairs in the array
            $settings[$match[1]] = $match[2];
        }

        return [
            new Blok([
                new UniBuddyCarousel(
                    slug: $settings['universitySlug'] ?? '',
                    language: $settings['ubLang'] ?? 'en-GB',
                    filterKey: $settings['filterKey'] ?? '',
                    filterValue: $settings['filterValue'] ?? '',
                    cookieConsent: $settings['cookieConsent'] ?? 'necessary',
                ),
            ]),
        ];
    }
}
