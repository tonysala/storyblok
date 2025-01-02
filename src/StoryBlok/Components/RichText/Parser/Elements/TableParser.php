<?php

declare(strict_types=1);

namespace App\Services\StoryBlok\Components\RichText\Parser\Elements;

use App\Services\StoryBlok\Components\RichText\Features\Blok;
use App\Services\StoryBlok\Components\RichText\Parser\ElementFactory;
use App\Services\StoryBlok\Components\Table\Table;
use App\Services\StoryBlok\Components\Table\TableCell;
use App\Services\StoryBlok\Components\Table\TableRow;
use App\Services\StoryBlok\Fields\Document;
use DOMElement;
use DOMText;
use DOMXPath;

class TableParser
{
    use HasChildNodes;

    public function __construct(
        public readonly DOMElement $element,
    ) {}

    public function parse(): array
    {
        $xpath = new DOMXPath($this->element->ownerDocument);

        $showHead = false;

        if ($xpath->query('.//thead/tr/th', $this->element)->count() > 0) {
            $showHead = true;
        }

        $rows = [];
        /** @var DOMElement $row */
        foreach ($xpath->query('.//tr', $this->element) as $row) {
            $cells = [];
            foreach ($row->childNodes as $cell) {
                if ($cell instanceof DOMText) {
                    continue;
                }
                $elements = [];
                foreach ($cell->childNodes as $child) {
                    /** @var DOMElement $child */
                    $elements = [...$elements, ...ElementFactory::make($child)->parse()];
                }
                $cells[] = new TableCell(new Document($elements));
            }
            $rows[] = new TableRow(cells: $cells);
        }

        return [
            new Blok([
                new Table(
                    rows: $rows,
                    showHead: $showHead,
                ),
            ]),
        ];
    }
}
