<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\RichText\Features\Blok;
use App\Services\StoryBlok\Components\Video\Video;
use DOMDocument;
use DOMElement;
use DOMXPath;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class UTVParser
{
    public function __construct(
        public readonly DOMElement $element,
    ) {
    }

    public function parse(): array
    {
        $source = $this->element->getAttribute('src');

        if (str_contains($source, 'Embed.aspx')) {
            try {
                $source = 'https://utv.uea.ac.uk'.$this->getMediaURL($source);
            } catch (RuntimeException $exception) {
                Log::warning($exception->getMessage());
            }
        }

        return [
            new Blok([new Video($source)]),
        ];
    }

    public function getMediaURL(string $source): string
    {
        try {
            $client = new Client();
            $response = $client->get($source);
            $html = $response->getBody()->getContents();
            $document = new DOMDocument();
            $document->loadHTML($html);
            libxml_clear_errors();
            $xpath = new DOMXPath($document);
            $data = json_decode($xpath->evaluate('string(//input[@id="hdn_PlayerData"]/@value)'), associative: true);

            return $data['Playlist'][0]['MediaURL'] ?? throw new RuntimeException();
        } catch (Throwable) {
            throw new RuntimeException('Failed to extract the mp4 from the UTV embed link.');
        }
    }
}
