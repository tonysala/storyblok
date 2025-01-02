<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Commands;

use App\Services\StoryBlok\Api\ManagementApi;
use App\Services\StoryBlok\Resources\ContentStory;
use App\Services\StoryBlok\Resources\ManagementStory;
use Illuminate\Support\Facades\Log;
use Throwable;

class ReplaceReference
{
    public function __invoke(ContentStory $story, string $from, string $to): ManagementStory
    {
        /** @var ManagementApi $managementApi */
        $managementApi = app(ManagementApi::class);

        $replace = function ($data, $from, $to) use (&$replace) {
            if (is_array($data)) {
                foreach ($data as $key => $value) {
                    $data[$key] = $replace($value, $from, $to);
                }
            } elseif (is_string($data)) {
                $data = str_replace($from, $to, $data);
            }

            return $data;
        };

        $updated = $replace($story->data['content'], $from, $to);

        $story = $managementApi->updateStory($story, [
            'content' => $updated,
        ]);
        try {
            $story->getData()['published'] && $story->publish();
        } catch (Throwable $exception) {
            Log::warning('Could not publish story. '.$exception->getMessage());
        }

        return $story;
    }
}
