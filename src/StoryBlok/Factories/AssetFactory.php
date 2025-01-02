<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Factories;

use App\Models\Liferay\LiferayFile;
use App\Models\Liferay\LiferayImage;
use App\Models\Liferay\LiferayJournalArticleImage;
use App\Models\Liferay\LiferayModel;
use App\Services\StoryBlok\Api\ManagementApi;
use App\Services\StoryBlok\Cache\AssetCache;
use App\Services\StoryBlok\Cache\AssetNotFoundException;
use App\Services\StoryBlok\Resources\Asset;
use App\Services\StoryBlok\Spaces\CurrentSpace;
use DOMElement;
use DOMNode;
use DOMXPath;
use InvalidArgumentException;
use Throwable;

class AssetFactory
{
    public static function fromHtmlElement(DOMElement|DOMNode $element): Asset
    {
        $xpath = new DOMXPath($element->ownerDocument);
        $file = LiferayFile::fromPath($xpath->evaluate('string(./@src)', $element));

        return self::fromFile($file, [
            'alt' => $xpath->evaluate('string(./@alt)', $element),
            'name' => basename($xpath->evaluate('string(./@src)', $element)),
            'title' => $xpath->evaluate('string(./@title)', $element),
            'path' => $xpath->evaluate('string(./@src)', $element),
        ]);
    }

    public static function fromLiferayStructure(DOMElement|DOMNode $node): Asset
    {
        $xpath = new DOMXPath($node->ownerDocument);

        $file = LiferayFile::fromPath($xpath->evaluate('string(.)', $node));

        return self::fromFile($file, [
            'alt' => $xpath->evaluate('string(./@alt)', $node),
            'name' => basename($xpath->evaluate('string(./@name)', $node)),
            'title' => $xpath->evaluate('string(./@title)', $node),
            'path' => $xpath->evaluate('string(.)', $node),
        ]);
    }

    public static function fromFile(LiferayModel $file, array $attributes = []): Asset
    {
        try {
            if ($dimensions = $file->getAttribute('dimensions')) {
                $attributes['size'] ??= implode('x', $dimensions);
            }
        } catch (Throwable) {
            // Likely not an image file
        }

        $attributes['meta_data'] = [
            'FID' => (string)$file->getKey(),
            'ID' => (string)$file->getAttribute('assetBankId'),
        ];

        return match (get_class($file)) {
            LiferayFile::class => self::fromLiferayFileEntry($file, $attributes),
            LiferayJournalArticleImage::class => self::fromLiferayJournalArticleImage($file, $attributes),
            LiferayImage::class => self::fromLiferayImage($file, $attributes),
            default => throw new InvalidArgumentException('Unsupported image type: '.get_class($file)),
        };
    }

    protected static function fromLiferayFileEntry(LiferayFile $file, array $attributes): Asset
    {
        $parent = self::getApi()
            ->fromLiferayAssetFolder($file->folder?->hierarchy ?? [])
            ->getId();

        try {
            return AssetCache::get((string)$file->getKey(), $parent);
        } catch (AssetNotFoundException) {
            $default = [
                'name' => $file->getAttribute('fileName'),
                'filename' => $file->getAttribute('fileName'),
                'title' => $file->getAttribute('title'),
                'alt' => $file->getAttribute('description'),
            ];

            $asset = Asset::create([
                'file' => CurrentSpace::get()->getHost().$file->getAttribute('uri'),
                'attributes' => [
                    ...$default,
                    ...$attributes,
                ],
                'parent' => $parent,
            ]);

            return AssetCache::store($asset, $parent);
        }
    }

    protected static function fromLiferayJournalArticleImage(LiferayJournalArticleImage $file, array $attributes): Asset
    {
        $folders = CurrentSpace::get()->getAssetFolders();

        try {
            return AssetCache::get((string)$file->getKey(), $folders['articles']);
        } catch (AssetNotFoundException) {
            $asset = Asset::create([
                'file' => CurrentSpace::get()->getHost().$file->getAttribute('uri'),
                'attributes' => [
                    ...$attributes,
                    'filename' => $attributes['name'],
                ],
                'parent' => $folders['articles'],
            ]);

            return AssetCache::store($asset, $folders['articles']);
        }
    }

    protected static function fromLiferayImage(LiferayImage $file, array $attributes): Asset
    {
        $folders = CurrentSpace::get()->getAssetFolders();

        try {
            return AssetCache::get((string)$file->getKey(), $folders['logos']);
        } catch (AssetNotFoundException) {
            $default = [
                'name' => ($attributes['name'] ?? $file->getAttribute('imageId')).'.'.$file->getAttribute('type_'),
                'filename' => ($attributes['name'] ?? $file->getAttribute('imageId')).'.'.$file->getAttribute('type_'),
            ];

            $asset = Asset::create([
                'file' => CurrentSpace::get()->getHost().$file->getAttribute('uri'),
                'attributes' => [
                    ...$default,
                    ...$attributes,
                ],
                'parent' => $folders['logos'],
            ]);

            return AssetCache::store($asset, $folders['logos']);
        }
    }

    protected static function getApi(): ManagementApi
    {
        return app(ManagementApi::class);
    }
}
