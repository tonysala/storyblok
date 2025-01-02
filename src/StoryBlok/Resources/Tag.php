<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Resources;

use App\Services\StoryBlok\Api\UsesContentApi;
use Throwable;

class Tag
{
    use UsesContentApi;

    public function __construct(
        public readonly array $data,
    ) {}

    public static function create(string $name): self
    {
        try {
            return self::getContentApi()->getTag(self::sanitiseName($name));
        } catch (Throwable) {
            return new self(['name' => self::sanitiseName($name), 'taggings_count' => 0]);
        }
    }

    /**
     * @param  string[]  $names
     *
     * @return Tag[]
     */
    public static function createMany(array $names): array
    {
        if (count($names) === 0) {
            return [];
        }

        $tags = self::getContentApi()->getTags();

        return array_map(function (string $name) use ($tags) {
            $matches = array_filter($tags, fn(Tag $tag) => (string)$tag === self::sanitiseName($name));
            if (count($matches)) {
                return array_values($matches)[0];
            } else {
                return new Tag(['name' => self::sanitiseName($name), 'taggings_count' => 0]);
            }
        }, $names);
    }

    public function getName(): string
    {
        return $this->data['name'];
    }

    public function toFormatted(): array
    {
        return [
            'value' => $this->getName(),
            'label' => $this->getName(),
        ];
    }

    protected static function sanitiseName(string $name): string
    {
        $name = str_replace(',', '', $name);

        return str_replace("\xc2\xa0", ' ', $name);
    }

    public function __toString(): string
    {
        return $this->getName();
    }
}
