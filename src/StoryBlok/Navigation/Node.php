<?php

namespace App\Services\StoryBlok\Navigation;

class Node
{
    /** @var Node[] */
    public array $children = [];

    public function __construct(
        public readonly int $id,
        public readonly ?int $parentId,
        public readonly string $name,
        public readonly string $slug,
        public readonly int $position,
    ) {}

    /**
     * @return Node[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param  int|null  $level
     * @param  int  $currentLevel
     *
     * @return Node[]
     */
    public function getDescendants(?int $level = null, int $currentLevel = 0): array
    {
        $descendants = [];
        if ($level !== null && $currentLevel >= $level) {
            return $descendants;
        }
        foreach ($this->children as $child) {
            $descendants[] = $child;
            $descendants = array_merge($descendants, $child->getDescendants($level, $currentLevel + 1));
        }
        return $descendants;
    }

    public function removeByPath(string $slug): void
    {
        $this->children = array_filter($this->children, function (Node $child) use ($slug) {
            if (! str_starts_with($child->slug, $slug)) {
                return true;
            } elseif ($child->slug === $slug) {
                return false;
            } else {
                $child->removeByPath($slug);
                return true;
            }
        });
    }

    public function toArray(): array
    {
        if (empty($this->children)) {
            return [
                'id' => $this->id,
                'parent_id' => $this->parentId,
                'url' => $this->slug,
                'title' => $this->name,
                'priority' => $this->position,
                'component' => 'navEntry',
            ];
        }

        usort($this->children, fn(Node $a, Node $b) => $a->position <=> $b->position);

        return [
            'id' => $this->id,
            'parent_id' => $this->parentId,
            'priority' => $this->position,
            'url' => '',
            'title' => $this->name,
            'navBlok' => [
                ...array_map(fn(Node $child) => $child->toArray(), $this->getChildren()),
            ],
            'component' => 'navFolder',
        ];
    }
}
