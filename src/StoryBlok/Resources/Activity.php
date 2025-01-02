<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Resources;

use App\Services\StoryBlok\Api\UsesManagementApi;

class Activity
{
    use UsesManagementApi;

    /**
     * @param  array{
     *   id: int,
     *   trackable_id: ?int,
     *   trackable_type: ?object,
     *   owner_id: ?int,
     *   owner_type: ?string,
     *   key: ?object,
     *   parameters: ?object,
     *   recipient_id: ?int,
     *   recipient_type: ?string,
     *   created_at: string,
     *   updated_at: string,
     *   space_id: int,
     * }  $data
     */
    public function __construct(
        public array $data,
    ) {}

    public static function load(int $id): self
    {
        return self::getManagementApi()->getActivity($id);
    }

    public function getId(): int
    {
        return $this->data['id'];
    }

    public function getTrackableId(): ?int
    {
        return $this->data['trackable_id'];
    }

    public function getTrackableType(): ?object
    {
        return $this->data['trackable_type'];
    }

    public function getOwnerId(): ?int
    {
        return $this->data['owner_id'];
    }

    public function getOwnerType(): ?string
    {
        return $this->data['owner_type'];
    }

    public function getKey(): ?object
    {
        return $this->data['key'];
    }

    public function getParameters(): ?object
    {
        return $this->data['parameters'];
    }

    public function getRecipientId(): ?int
    {
        return $this->data['recipient_id'];
    }

    public function getRecipientType(): ?string
    {
        return $this->data['recipient_type'];
    }

    public function getCreatedAt(): string
    {
        return $this->data['created_at'];
    }

    public function getUpdatedAt(): string
    {
        return $this->data['updated_at'];
    }

    public function getSpaceId(): int
    {
        return $this->data['space_id'];
    }

    /**
     * @return array{
     *   id: int,
     *   trackable_id: ?int,
     *   trackable_type: ?object,
     *   owner_id: ?int,
     *   owner_type: ?string,
     *   key: ?object,
     *   parameters: ?object,
     *   recipient_id: ?int,
     *   recipient_type: ?string,
     *   created_at: string,
     *   updated_at: string,
     *   space_id: int,
     * }
     */
    public function getData(): array
    {
        return $this->data;
    }
}