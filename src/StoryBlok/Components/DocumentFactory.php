<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components;

use App\Services\StoryBlok\Components\RichText\Features\RichTextFeature;
use App\Services\StoryBlok\Fields\Document;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;

final class DocumentFactory
{
    protected static array $components = [];

    /**
     * Loads all PHP files in the current directory,
     * inspects them to identify classes implementing the RichTextFeature interface,
     * and registers those classes in the static components array.
     *
     * @return void
     */
    public static function load(): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(__DIR__, FilesystemIterator::SKIP_DOTS),
        );

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                /** @var class-string<RichTextFeature> $class */
                $class = sprintf('%s\\%s\\%s', __NAMESPACE__, basename($file->getPath()), $file->getBasename('.php'));
                if (class_exists($class) && in_array(RichTextFeature::class, class_implements($class))) {
                    self::$components[$class::NAME] = $class;
                }
            }
        }
    }

    /**
     * Create a new instance of the specified component.
     *
     * @param  string  $name  The name of the component to create.
     *
     * @return ComponentInterface The created component instance.
     * @throws RuntimeException If the specified component is not found.
     */
    public static function new(string $name): ComponentInterface
    {
        if (array_key_exists($name, self::$components)) {
            return new self::$components[$name]();
        }
        throw new RuntimeException('Rich text feature not found.');
    }

    public static function deserialise(array $data): Document
    {
        if (array_key_exists($data['component'], self::$components)) {
            return self::$components[$data['component']]::deserialise($data);
        }
        throw new RuntimeException('Rich text feature not found.');
    }
}
