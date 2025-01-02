<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;

final class ComponentFactory
{
    protected static array $components = [];

    public static function load(): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(__DIR__, FilesystemIterator::SKIP_DOTS),
        );

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                /** @var class-string<ComponentInterface> $class */
                $class = sprintf('%s\\%s\\%s', __NAMESPACE__, basename($file->getPath()), $file->getBasename('.php'));
                if (class_exists($class) && in_array(ComponentInterface::class, class_implements($class))) {
                    self::$components[$class::NAME] = $class;
                }
            }
        }
    }

    public static function new(string $name): ComponentInterface
    {
        if (array_key_exists($name, self::$components)) {
            return new self::$components[$name]();
        }
        throw new RuntimeException('Component not found.');
    }

    public static function deserialise(array $data): ComponentInterface
    {
        if (array_key_exists($data['component'], self::$components)) {
            return self::$components[$data['component']]::deserialise($data);
        }
        throw new RuntimeException('Component not found.');
    }
}
