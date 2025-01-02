<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components;

use App\Services\StoryBlok\Attributes\WithComponents;
use JsonSerializable;
use ReflectionClass;
use ReflectionProperty;

abstract class Component implements ComponentInterface, JsonSerializable
{
    use SerializableComponent;

    public const NAME = '';

    public static function getName(): string
    {
        return static::NAME;
    }

    /**
     * @return array<array<\App\Services\StoryBlok\Components\ComponentInterface>>
     */
    public function getNested(): array
    {
        $properties = $this->getNestedProperties($this);

        $groups = [];
        foreach ($properties as $property) {
            $components = [];
            /** @var ComponentInterface $component */
            foreach ($property as $component) {
                $components[] = $component;
            }
            $groups[] = $components;
        }

        return $groups;
    }

    /**
     * @return \App\Services\StoryBlok\Components\ComponentInterface[]
     */
    public function getDescendants(): array
    {
        $search = function (ComponentInterface $component, array $carry = []) use (&$search): array {
            $components = [];
            foreach ($this->getNestedProperties($component) as $property) {
                foreach ($property as $child) {
                    /** @var ComponentInterface $child */
                    $components[] = $child;
                    $components = [...$components, ...$search($child)];
                }
            }
            return [... $carry, ...$components];
        };

        return $search($this);
    }

    /**
     * @return array<\App\Services\StoryBlok\Components\ComponentInterface[]>
     */
    public function getNestedProperties(ComponentInterface $component): array
    {
        $reflector = new ReflectionClass($component);

        $properties = [];
        foreach ($reflector->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->getAttributes(WithComponents::class)) {
                $properties[] = $component->{$property->getName()};
            }
        }

        return $properties;
    }
}
