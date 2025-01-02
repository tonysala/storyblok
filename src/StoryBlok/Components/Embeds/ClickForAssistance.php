<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\Embeds;

use App\Services\StoryBlok\Components\Component;
use App\Services\StoryBlok\Components\ComponentInterface;
use App\Services\StoryBlok\Components\SerializableComponent;

class ClickForAssistance extends Component
{
    public const NAME = 'SB006-2';

    public function __construct(
        public readonly string $chatId,
        public readonly ClickForAssistanceTool $tool = ClickForAssistanceTool::ONE,
    ) {}

    public function toArray(): array
    {
        return [
            'chatId' => $this->chatId,
            'tool' => $this->tool,
            'component' => self::NAME,
        ];
    }

    public static function deserialise(array $data): ComponentInterface
    {
        return new self(
            chatId: $data['chatId'],
            tool: ClickForAssistanceTool::from($data['tool']),
        );
    }
}
