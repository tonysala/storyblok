<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\DynamicList;

use App\Services\StoryBlok\Api\UsesContentApi;
use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Fields\Categories;

class DataSourceDynamicList extends Component
{
    use UsesContentApi;

    public const NAME = 'SB008_3';

    public function __construct(
        public readonly ?Categories $datasource,
        public readonly Order $order = Order::RELEVANCE,
    ) {}

    public function toArray(): array
    {
        return [
            'Datasource' => $this->datasource,
            'order' => $this->order,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            datasource: $data['Datasource'] ? Categories::deserialise($data['Datasource']) : null,
            order: Order::from($data['order']),
        );
    }
}
