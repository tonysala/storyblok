<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Fields\Options;

use App\Services\StoryBlok\Api\ContentApi;
use App\Services\StoryBlok\Fields\FieldInterface;
use App\Services\StoryBlok\Fields\SerializableField;
use App\Services\StoryBlok\Resources\Story;
use JsonSerializable;

abstract class StoryOption implements FieldInterface, JsonSerializable
{
    use SerializableField;

    public string $path;

    public array $types;

    public function __construct(
        public readonly Story $story,
    ) {
    }

    public function getOptions(): array
    {
        /** @var ContentApi $api */
        $api = app(ContentApi::class);
        $aggregate = [];
        while (true) {
            $response = $api->searchStories([
                'page' => $page ??= 1,
                'starts_with' => $this->path,
                ...($this->types ? ['filter_query[component][in]' => implode(',', $this->types)] : []),
            ]);
            $aggregate = array_merge($aggregate, $response->resources);
            if (ceil($response->total / 100) > $page) {
                $page += 1;
            } else {
                break;
            }
        }

        return $aggregate;
    }

    public function serialise(): string
    {
        return $this->story->getData()['uuid'];
    }
}
