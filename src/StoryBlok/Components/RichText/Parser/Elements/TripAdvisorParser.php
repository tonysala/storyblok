<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\Embeds\TripAdvisorEmbed;
use App\Services\StoryBlok\Components\RichText\Features\Blok;
use DOMElement;

class TripAdvisorParser
{
    public function __construct(
        public readonly DOMElement $element
    ) {
    }

    public function parse(): array
    {
        $source = $this->element->getAttribute('src');

        return [
            new Blok([new TripAdvisorEmbed($this->optimiseSource($source))]),
        ];
    }

    protected function optimiseSource(string $source): string
    {
        if (preg_match('#/wejs#', $source)) {
            parse_str(parse_url($source, PHP_URL_QUERY), $query);
            $params = array_filter(
                $query,
                fn (string $key): bool => in_array($key, ['lang', 'locationId', 'display_version', 'uniq']),
                ARRAY_FILTER_USE_KEY,
            );

            return 'https://www.tripadvisor.co.uk/WidgetEmbed-cdswritereviewlg?'.http_build_query($params);
        }

        return $source;
    }
}
