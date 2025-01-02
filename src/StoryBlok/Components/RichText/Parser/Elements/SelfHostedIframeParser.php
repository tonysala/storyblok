<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Models\Liferay\LiferayFile;
use App\Services\StoryBlok\Components\Embeds\PdfEmbed;
use App\Services\StoryBlok\Components\RichText\Features\Blok;
use App\Services\StoryBlok\Factories\AssetFactory;
use App\Services\StoryBlok\Fields\Asset;
use DOMElement;
use DOMXPath;
use Illuminate\Support\Str;

class SelfHostedIframeParser
{
    public function __construct(
        public readonly DOMElement $element,
    ) {}

    public function parse(): array
    {
        $xpath = new DOMXPath($this->element->ownerDocument);
        $source = $xpath->evaluate('string(./@src)', $this->element);

        if (str_contains(parse_url($source, PHP_URL_PATH), '.pdf')) {
            $file = LiferayFile::fromPath($source);
            $asset = AssetFactory::fromFile($file, [
                'name' => Str::snake($file->getAttribute('name')),
                'title' => $file->getAttribute('title'),
                'alt' => $file->getAttribute('description') ?? $file->getAttribute('title'),
            ]);

            return [
                new Blok([
                    new PdfEmbed(asset: Asset::fromResource($asset),),
                ]),
            ];
        }

        return [];
    }
}
