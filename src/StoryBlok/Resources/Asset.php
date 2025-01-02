<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Resources;

use App\Services\StoryBlok\Api\ManagementApi;
use App\Services\StoryBlok\Spaces\CurrentSpace;
use App\Services\StoryBlok\Spaces\Space;
use Illuminate\Support\Facades\Log;

class Asset implements Creatable, Deletable
{
    /**
     * @param  array{
     *     id: int,
     *     filename: string,
     *     space_id: int,
     *     created_at: string,
     *     updated_at: string,
     *     file: object,
     *     asset_folder_id: int,
     *     deleted_at: string,
     *     short_filename: string,
     *     content_type: string,
     *     content_length: int,
     *     alt: string,
     *     copyright: string,
     *     title: string,
     *     source: string,
     *     expire_at: string,
     *     focus: string,
     *     internal_tag_ids: string[],
     *     internal_tags_list: array{
     *         tag_id: int,
     *         tag_name: string
     *     }[],
     *     locked: bool,
     *     publish_at: string,
     *     is_private: bool,
     *     meta_data: array
     * }  $data
     */
    public function __construct(
        public readonly array $data,
    ) {}

    public function getId(): int
    {
        return $this->data['id'];
    }

    public function getUri(): string
    {
        return str_replace('s3.amazonaws.com/', '', $this->data['filename']);
    }

    public static function load(int $id): self
    {
        /** @var ManagementApi $api */
        $api = app(ManagementApi::class);

        return $api->getAsset($id);
    }

    public static function create(array $data): self
    {
        /** @var ManagementApi $api */
        $api = app(ManagementApi::class);
        $asset = $api->createAsset($data['file'], $data['attributes'], $data['parent']);
        Log::debug('Created new asset ['.$asset->getId().']');

        return $asset;
    }

    public function update(array $updates): self
    {
        /** @var ManagementApi $api */
        $api = app(ManagementApi::class);

        return $api->updateAsset($this->getId(), $updates);
    }

    public function delete(): bool
    {
        /** @var ManagementApi $api */
        $api = app(ManagementApi::class);

        return $api->deleteAsset($this);
    }

    public function refresh(): self
    {
        /** @var ManagementApi $api */
        $api = app(ManagementApi::class);

        return $api->getAsset($this->data['id']);
    }

    public function __serialize(): array
    {
        return [$this->data['id'], CurrentSpace::get()->value];
    }

    public function __unserialize(array $data): void
    {
        [$id, $space] = $data;
        $from = CurrentSpace::get();
        CurrentSpace::change(Space::from($space));
        $this->__construct($this->load($id)->data);
        CurrentSpace::change($from);
    }
}
