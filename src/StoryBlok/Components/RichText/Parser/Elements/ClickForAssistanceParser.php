<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\Embeds\ClickForAssistance;
use App\Services\StoryBlok\Components\Embeds\ClickForAssistanceTool;
use App\Services\StoryBlok\Components\RichText\Features\Blok;
use DOMElement;
use DOMXPath;

class ClickForAssistanceParser
{
    public const CHATS = [
        'it' => [
            'id' => '476e5bdc-a6de-473a-8757-1b182b08ca19',
            'tool' => '1',
        ],
        'admissions' => [
            'id' => 'cb37598c-2a30-41b4-b070-3d8057f9d8ec',
            'tool' => '9',
        ],
        'hr' => [
            'id' => '03b59961-8016-47dd-8621-1e5616037cc1',
            'tool' => '1',
        ],
        'library' => [
            'id' => 'd6a3c096-0b8a-435e-ad4d-c074ef040f24',
            'tool' => '1',
        ],
        'accommodations' => [
            'id' => 'b2e41bfe-20a7-46e6-a78f-28c7cb812c3a',
            'tool' => '1',
        ],
    ];

    public function __construct(
        public readonly DOMElement $element,
    ) {
    }

    public function parse(): array
    {
        $xpath = new DOMXPath($this->element->ownerDocument);

        if ($this->element->tagName === 'script') {
            foreach ($xpath->query('//script') as $script) {
                if (preg_match('#C4A.Run\([\'"](?<uuid>.*?)[\'"]\)#', $script->textContent, $matches)) {
                    return [
                        new Blok([
                            new ClickForAssistance(
                                chatId: $matches['uuid'],
                                tool: ClickForAssistanceTool::from('1')
                            ),
                        ]),
                    ];
                }
            }
        } elseif ($this->element->tagName === 'iframe') {
            $source = $this->element->getAttribute('src');
            $query = [];
            parse_str(parse_url($source, PHP_URL_QUERY), $query);

            // Return admissions chat by default
            return [
                new Blok([
                    new ClickForAssistance(
                        chatId: self::CHATS['admissions']['id'],
                        tool: ClickForAssistanceTool::from('1'),
                    ),
                ]),
            ];
        }

        return [];
    }
}
