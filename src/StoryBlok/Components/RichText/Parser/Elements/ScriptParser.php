<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Liferay\Parsers\Exceptions\LayoutContainsScriptsException;
use DOMElement;
use Illuminate\Support\Facades\Log;

class ScriptParser
{
    public const IGNORED_HOSTS = [
        'player.vimeo.com',
        'www.scoop.it',
        'cdn.unibuddy.co',
        'code.jquery.com',
    ];

    public function __construct(
        public readonly DOMElement $element,
    ) {
    }

    public function parse(): array
    {
        $source = $this->element->getAttribute('src');

        if ($source === '') {
            Log::debug('Inline script tag found');

            return [];
        }

        if (str_contains($source, 'www.instagram.com/embed.js')) {
            Log::debug('Instagram embed found');

            return [];
        }

        $host = parse_url($source, PHP_URL_HOST);
        if (! $host || $this->isIgnored($host)) {
            return [];
        }

        $parser = match ($host) {
            'app.geckoform.com' => new GeckoFormParser($this->element),
            'v4in1-si.click4assistance.co.uk' => new ClickForAssistanceParser($this->element),
            'public.flourish.studio' => new FlourishParser($this->element),
            'widget.siteminder.com' => new DirectBookParser($this->element),
            'www.jscache.com' => $this->guessScript($source),
            default => throw new LayoutContainsScriptsException(
                'Script not supported: '.parse_url($source, PHP_URL_HOST)
            ),
        };

        return $parser->parse();
    }

    protected function guessScript(string $source): object
    {
        return match (1) {
            preg_match('#cdswritereviewlg#', $source) => new TripAdvisorParser($this->element),
            default => throw new LayoutContainsScriptsException(
                'Script not supported: '.parse_url($source, PHP_URL_HOST)
            ),
        };
    }

    protected function isIgnored(string $domain): bool
    {
        foreach (self::IGNORED_HOSTS as $ignoredDomain) {
            if (str_contains($domain, $ignoredDomain)) {
                return true;
            }
        }

        return false;
    }
}
