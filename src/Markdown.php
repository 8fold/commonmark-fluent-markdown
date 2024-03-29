<?php
declare(strict_types=1);

namespace Eightfold\Markdown;

use Eightfold\Markdown\FluentCommonMark;

use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;

use Eightfold\CommonMarkAbbreviations\AbbreviationExtension as Abbreviations;
use Eightfold\CommonMarkAccessibleHeadingPermalink\HeadingPermalinkExtension
    as HeadingPermalink;
use Eightfold\CommonMarkPartials\PartialsExtension as Partials;

class Markdown extends FluentCommonMark
{
    public static function create(): static
    {
        $instance = new static();
        return $instance->commonMarkCore()->withConfig([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 100
        ]);
    }

    private bool $minified = false;

    public function minified(): Markdown
    {
        $this->minified = true;
        return $this;
    }

    public function getFrontMatter(string $markdown = ''): mixed
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

        if ($this->minified and ! str_contains($html, '</code></pre>')) {
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

    /**
     * @param  array<string, mixed> $config
     */
    public function accessibleHeadingPermalinks(array $config = []): Markdown
    {
        if (count($config) === 0) {
            return $this->addExtension(new HeadingPermalink());
        }
        return $this->addExtensionWithConfig(
            'accessible_heading_permalink',
            $config
        )->addExtension(new HeadingPermalink());
    }

    /**
     * @param  array<string, mixed> $config
     */
    public function partials(array $config = []): Markdown
    {
        if (count($config) === 0) {
            return $this;
        }
        return $this->addExtensionWithConfig(
            'partials',
            $config
        )->addExtension(new Partials());
    }
}
