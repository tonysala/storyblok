<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Fields\Options;

use App\Services\StoryBlok\Api\ContentApi;
use App\Services\StoryBlok\Fields\FieldInterface;
use App\Services\StoryBlok\Fields\SerializableField;
use JsonSerializable;

class DataSourceOption implements FieldInterface, JsonSerializable
{
    use SerializableField;

    public function __construct(
        public readonly string $name,
    ) {}

    public function getOptions(): array
    {
        /** @var ContentApi $api */
        $api = app(ContentApi::class);

        return $api->getDataSourceEntriesAsArray($this->name);
    }

    public function serialise(): array
    {
        return [];
    }
}
