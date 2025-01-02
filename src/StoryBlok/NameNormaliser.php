<?php

declare(strict_types=1);

namespace App\Services\StoryBlok;

class NameNormaliser
{
    protected array $minorWords = [
        'a',
        'an',
        'the',
        'and',
        'but',
        'or',
        'nor',
        'for',
        'yet',
        'so',
        'at',
        'by',
        'for',
        'in',
        'of',
        'on',
        'to',
        'up',
        'with',
        'as',
        'if',
        'than',
        'that',
        'after',
        'although',
        'as',
        'because',
        'before',
        'if',
        'once',
        'since',
        'though',
        'till',
        'unless',
        'until',
        'when',
        'whenever',
        'where',
        'whereas',
        'wherever',
        'while',
        'from',
    ];

    protected array $uppercaseWords = [
        'uk',
        'eu',
        'uea',
    ];

    public function capitalise(string $path): string
    {
        $words = explode('-', $path);

        $words = array_map(function (string $word) {
            $lowerWord = strtolower($word);
            if (in_array($lowerWord, $this->minorWords)) {
                return $word;
            } elseif (in_array($lowerWord, $this->uppercaseWords)) {
                return strtoupper($word);
            } else {
                return ucfirst($lowerWord);
            }
        }, $words);

        return ucfirst(implode(' ', $words));
    }
}
