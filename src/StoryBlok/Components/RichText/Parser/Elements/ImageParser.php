<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Models\Liferay\LiferayFile;
use App\Services\StoryBlok\Components\Image\Image as ImageComponent;
use App\Services\StoryBlok\Components\RichText\Features\Blok;
use App\Services\StoryBlok\Components\RichText\Features\Image;
use App\Services\StoryBlok\Factories\AssetFactory;
use App\Services\StoryBlok\Fields\Asset;
use App\Services\StoryBlok\Fields\Link\Link;
use App\Services\StoryBlok\Spaces\CurrentSpace;
use DOMElement;
use Illuminate\Support\Facades\Log;
use Throwable;

class ImageParser
{
    public function __construct(
        public readonly DOMElement $element,
        public readonly array $inherited = [],
    ) {}

    public function parse(): array
    {
        $source = $this->element->getAttribute('src');

        $parts = parse_url($source);
        $path = $parts['path'] ?? '';
        if (Link::isInternal($source) && str_starts_with($path, '/documents/')) {
            if (! Link::isCurrentSpace($source)) {
                CurrentSpace::switch();
                try {
                    $file = LiferayFile::fromPath($parts['path']);
                    $asset = AssetFactory::fromFile($file);
                    $this->element->setAttribute('src', $asset->getUri());
                } finally {
                    CurrentSpace::switch();
                }
            } else {
                try {
                    $asset = AssetFactory::fromHtmlElement($this->element);

                    if ($this->inherited) {
                        return [Image::fromAsset($asset, $this->inherited)];
                    } else {
                        return [
                            new Blok([
                                new ImageComponent(
                                    Asset::fromResource($asset),
                                ),
                            ]),
                        ];
                    }
                } catch (Throwable) {
                    Log::warning('Attempted to load document link, but it could not be found ['.$path.'].');
                }
            }
        }

        return [
            new Image(
                src: $this->element->getAttribute('src'),
                alt: $this->element->getAttribute('alt'),
                title: $this->element->getAttribute('title'),
                marks: $this->inherited,
            ),
        ];
    }
}
