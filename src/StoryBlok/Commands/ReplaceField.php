<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Commands;

use App\Services\StoryBlok\Api\UsesManagementApi;
use App\Services\StoryBlok\Components\ComponentFactory;
use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Components\RichText\Features\RichTextFeature;
use App\Services\StoryBlok\Fields\FieldInterface;
use App\Services\StoryBlok\Resources\Story;
use ReflectionClass;

class ReplaceField
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

    /**
     * @param  ComponentInterface|FieldInterface  $component
     * @param  callable  $search
     * @param  callable  $replace
     *
     * @return ComponentInterface|FieldInterface
     * @throws \ReflectionException
     */
    protected function parse(mixed $component, callable $search, callable $replace): mixed
    {
        /** @var iterable $component */
        // Loop through the properties of the component
        foreach ($component as $property => $value) {
            if (is_array($value)) {
                $new = [];
                foreach ($value as $item) {
                    if ($item instanceof ComponentInterface || $item instanceof FieldInterface || $item instanceof RichTextFeature) {
                        $new[] = $this->parse($item, $search, $replace);
                    } else {
                        $new[] = $item;
                    }
                }
                // Replace the property of the component with the new values
                $component = $this->patch($component, $property, $new);
            }
            if ($value instanceof FieldInterface) {
                if ($search($value)) {
                    // Replace the property of the component with the new values
                    $component = $this->patch($component, $property, $replace($value));
                } else {
                    $value = $this->parse($value, $search, $replace);
                    $component = $this->patch($component, $property, $value);
                }
            }
        }

        return $component;
    }

    /**
     * @param  ComponentInterface|FieldInterface  $component
     * @param  string  $property
     * @param  mixed  $value
     *
     * @return ComponentInterface|FieldInterface
     * @throws \ReflectionException
     */
    public function patch(mixed $component, string $property, mixed $value): mixed
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
