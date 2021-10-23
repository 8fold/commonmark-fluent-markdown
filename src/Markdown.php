<?php

declare(strict_types=1);

namespace Eightfold\Markdown;

use Eightfold\Markdown\FluentCommonMark;

use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;

use Eightfold\CommonMarkAbbreviations\AbbreviationExtension as Abbreviations;

class Markdown extends FluentCommonMark
{
    public static function create(): Markdown
    {
        $instance = new Markdown();
        $instance = $instance->commonMarkCore();
        $instance = $instance->withConfig([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 100
        ]);
        return $instance;
    }

    private bool $minified = false;

    public function minified(): Markdown
    {
        $this->minified = true;
        return $this;
    }

    /**
     * @return array<mixed> [description]
     */
    public function getFrontMatter(string $markdown = ''): array
    {
        $frontMatterExtension = new FrontMatterExtension();
        $frontMatter = $frontMatterExtension->getFrontMatterParser()->parse(
            $markdown . "\n"
        )->getFrontMatter();

        if ($frontMatter === null) {
            return [];
        }
        return $frontMatter;
    }

    public function getBody(string $markdown = ''): string
    {
        $frontMatterExtension = new FrontMatterExtension();
        return $frontMatterExtension->getFrontMatterParser()->parse($markdown)
            ->getContent();
    }

    public function convert(string $markdown = ''): string
    {
        $markdown = $this->getBody($markdown);
        $html = parent::convertToHtml($markdown)->getContent();

        if ($this->minified) {
            return str_replace([
                "\t",
                "\n",
                "\r",
                "\r\n"
            ], '', $html);

        }
        return $html;
    }

    public function abbreviations(): Markdown
    {
        return $this->addExtension(new Abbreviations());
    }
}
