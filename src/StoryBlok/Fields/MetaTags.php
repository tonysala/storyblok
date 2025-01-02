<?php

namespace App\Services\StoryBlok\Fields;

use App\Services\StoryBlok\Api\UsesManagementApi;
use JsonSerializable;
use RuntimeException;

class MetaTags implements FieldInterface, JsonSerializable
{
    use SerializableField;
    use UsesManagementApi;

    public const NAME = 'seo_metatags';

    public function __construct(
        public readonly string $title = '',
        public readonly string $description = '',
        public readonly string $twitterTitle = '',
        public readonly ?Asset $twitterImage = null,
        public readonly string $twitterDescription = '',
        public readonly string $ogTitle = '',
        public readonly ?Asset $ogImage = null,
        public readonly string $ogDescription = '',
    ) {
    }

    public function serialise(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'og_title' => $this->ogTitle,
            'og_image' => $this->ogImage?->getUri(),
            'og_description' => $this->ogDescription,
            'twitter_title' => $this->twitterTitle,
            'twitter_image' => $this->twitterImage?->getUri(),
            'twitter_description' => $this->twitterDescription,
            'plugin' => self::NAME,
        ];
    }

    public static function deserialise(array $data): self
    {
        return new self(
            title: $data['title'],
            description: $data['description'],
            twitterTitle: $data['twitter_title'],
            twitterImage: $data['twitter_image'] ? self::loadAssetFromUri($data['twitter_image']) : null,
            twitterDescription: $data['twitter_description'],
            ogTitle: $data['og_title'],
            ogImage: $data['og_image'] ? self::loadAssetFromUri($data['og_image']) : null,
            ogDescription: $data['og_description'],
        );
    }

    protected static function loadAssetFromUri(string $uri): Asset
    {
        $response = self::getManagementApi()->searchAssets([
            'search' => parse_url($uri, PHP_URL_PATH),
        ]);

        if (count($response->resources) > 0) {
            return Asset::fromResource($response->resources[0]);
        }

        throw new RuntimeException('Image not found: '.$uri);
    }
}
