<?php

namespace App\Services\StoryBlok\Navigation;

use Illuminate\Support\Facades\Log;

class Tree
{
    /** @var array<int, Node> */
    protected array $nodes = [];

    /** @var Node[] */
    protected array $roots = [];

    /** @var array<string> */
    protected array $ignoredPaths = [];

    /**
     * @param  array<int, array{
     *     id: int,
     *     parent_id?: int,
     *     position: int,
     *     is_folder: bool,
     *     name: string,
     *     slug: string
     * }>  $links
     */
    public function __construct(array $links, array $nodes)
    {
        foreach ($links as $data) {
            if (! $data['is_folder']) {
                if (array_key_exists($data['id'], $nodes)) {
                    if ($nodes[$data['id']]['exclude'] === true) {
                        continue;
                    }
                } else {
                    Log::warning('Link not found in content nodes: '.$data['slug']);
                    continue;
                }
            }
            $this->nodes[$data['id']] = new Node(
                $data['id'],
                $data['parent_id'],
                $data['name'],
                $data['slug'],
                $data['position'],
            );
        }

        // Build tree structure
        foreach ($this->nodes as $node) {
            if (! $node->parentId) {
                $this->roots[] = $node;
            } elseif (isset($this->nodes[$node->parentId])) {
                $this->nodes[$node->parentId]->children[] = $node;
            } else {
                Log::warning('Orphan node: #'.$node->id.' '.$node->slug);
                // Handle orphan nodes (nodes with non-existent parents)
                // For now, we'll ignore orphans, but we could log them or handle differently if needed
            }
        }
    }

    public function setRootNode(int $id): void
    {
        $this->roots = $this->nodes[$id]->children;
    }

    public function findNodeById(int $id): ?Node
    {
        return $this->nodes[$id] ?? null;
    }

    /**
     * @return Node[]
     */
    public function getRootNodes(): array
    {
        return $this->roots;
    }

    /**
     * @param  int  $id
     *
     * @return Node[]
     */
    public function getPathToNode(int $id): array
    {
        $path = [];
        $node = $this->findNodeById($id);
        while ($node) {
            array_unshift($path, $node);
            $node = $this->findNodeById($node->parentId);
        }
        return $path;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getTreeAsArray(): array
    {
        return array_map(fn(Node $node) => $node->toArray(), $this->roots);
    }

    public function removeByPaths(...$paths): void
    {
        foreach ($paths as $slug) {
            $this->roots = array_filter($this->roots, function (Node $node) use ($slug) {
                if (! str_starts_with($node->slug, $slug)) {
                    return true;
                }
                if ($node->slug === $slug) {
                    return false;
                }
                $node->removeByPath($slug);
                return true;
            });
        }
    }
}
