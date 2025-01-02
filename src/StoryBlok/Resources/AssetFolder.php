<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Resources;

use App\Services\StoryBlok\Api\ManagementApi;

class AssetFolder
{
    /**
     * @param  array{
     *     id: int,
     *     name: string,
     *     parent_id: int,
     *     uuid: string,
     *     parent_uuid: string
     * }  $data
     */
    public function __construct(
        public array $data,
    ) {
    }

    public static function load(int $id): self
    {
        /** @var ManagementApi $api */
        $api = app(ManagementApi::class);

        return $api->getAssetFolder($id);
    }

    public function getId(): int
    {
        return $this->data['id'];
    }

    public function getName(): string
    {
        return $this->data['name'];
    }

    /**
     * @return array{
     *     id: int,
     *     name: string,
     *     parent_id: int,
     *     uuid: string,
     *     parent_uuid: string
     * }
     */
    public function getData(): array
    {
        return $this->data;
    }
}
