<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Commands;

use App\Services\StoryBlok\Api\UsesManagementApi;
use App\Services\StoryBlok\Components\ComponentFactory;
use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Resources\Story;
use ReflectionClass;

class ReplaceComponent
{
    use UsesManagementApi;

    public function __invoke(Story $story, callable $search, callable $replace): Story
    {
        // Deserialise the content of the story
        $content = ComponentFactory::deserialise($story->getData()['content']);

        // Traverse the tree, patching each leaf
        $content = $this->parse($content, $search, $replace);

        // Update the story
        return self::getManagementApi()
            ->updateStory(
                $story,
                [
                    'content' => $content,
                ]
            );
    }

    protected function parse(ComponentInterface $component, callable $search, callable $replace): ComponentInterface
    {
        /** @var iterable $component */
        // Loop through the properties of the component
        foreach ($component as $property => $value) {
            if (is_array($value)) {
                $new = [];
                foreach ($value as $item) {
                    if ($item instanceof ComponentInterface) {
                        $new[] = $this->parse($item, $search, $replace);
                    }
                }
                // Replace the property of the component with the new values
                $component = $this->patch($component, $property, $new);
            }
        }
        if ($search($component)) {
            $component = $replace($component);
        }

        return $component;
    }

    public function patch(ComponentInterface $component, string $property, array $value): ComponentInterface
    {
        // We need to recreate the class by re-instantiating it
        $class = new ReflectionClass($component);
        $params = $class->getConstructor()->getParameters();
        $args = [];

        // Loop through the property and replace the required one
        foreach ($params as $param) {
            $name = $param->getName();
            if ($name === $property) {
                $args[] = $value;
            } else {
                $args[] = $component->{$name};
            }
        }

        return $class->newInstanceArgs($args);
    }
}
