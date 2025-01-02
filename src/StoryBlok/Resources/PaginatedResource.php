<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Resources;

/**
 * Represents a paginated resource, which can contain various types of items (e.g., Story, Asset).
 *
 * @template T Type of the contained resources.
 */
class PaginatedResource
{
    /**
     * @param T[] $resources Array of resources of type T.
     * @param int $page Current page index.
     * @param int $total Total number of items across all pages.
     */
    public function __construct(
        public readonly array $resources,
        public readonly int $page,
        public readonly int $total
    ) {
    }
}
