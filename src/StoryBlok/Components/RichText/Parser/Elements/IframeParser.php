<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use DOMElement;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class IframeParser
{
    use HasChildNodes;

    public const IGNORED_HOSTS = [];

    public function __construct(
        public readonly DOMElement $element,
    ) {}

    public function parse(): array
    {
        $source = $this->element->getAttribute('src');

        // if there is no src attribute, check for data-lazy attribute
        if (! $source) {
            $source = $this->element->getAttribute('data-lazy');
        }

        $host = parse_url($source, PHP_URL_HOST);
        if (! $host || $this->isIgnored($host)) {
            return [];
        }

        try {
            $parser = match (parse_url($source, PHP_URL_HOST)) {
                'youtu.be', 'www.youtube.com', 'player.vimeo.com' => new VideoEmbedParser($this->element),
                'forms.office.com' => new MicrosoftFormParser($this->element),
                'uea.tfaforms.net' => new TfaFormIframeParser($this->element),
                'v4in1-ti.click4assistance.co.uk' => new ClickForAssistanceParser($this->element),
                'www.google.com' => new GoogleMapsParser($this->element),
                'utv.uea.ac.uk' => new UTVParser($this->element),
                'kuula.co' => new KuulaParser($this->element),
                'w.soundcloud.com' => new SoundCloudParser($this->element),
                'my.matterport.com' => new MatterportParser($this->element),
                'unibuddy.co' => new UniBuddyBlogParser($this->element),
                'app.mobilityways.com' => new MobilityWaysParser($this->element),
                'uea-sylvester.s3.eu-west-2.amazonaws.com' => new SylvesterParser($this->element),
                'www.uea.ac.uk' => new SelfHostedIframeParser($this->element),
                'open.spotify.com' => new SpotifyParser($this->element),
                'echo360.org.uk' => new Echo360Parser($this->element),
                default => throw new InvalidArgumentException(
                    'Iframe not supported: '.parse_url($source, PHP_URL_HOST),
                ),
            };
        } catch (InvalidArgumentException $exception) {
            Log::warning($exception->getMessage());
            throw $exception;
        }

        return $parser->parse();
    }

    protected function isIgnored(string $domain): bool
    {
        /** @phpstan-ignore-next-line */
        foreach (self::IGNORED_HOSTS as $ignoredDomain) {
            if (str_contains($domain, $ignoredDomain)) {
                return true;
            }
        }

        return false;
    }
}
