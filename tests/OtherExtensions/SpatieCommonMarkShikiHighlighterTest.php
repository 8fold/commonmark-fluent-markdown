<?php
declare(strict_types=1);

namespace Eightfold\Markdown\Tests;

use PHPUnit\Framework\TestCase;

use Spatie\CommonMarkShikiHighlighter\HighlightCodeExtension;

use Eightfold\Markdown\Markdown;

use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Environment\Environment as Environment;
use League\CommonMark\MarkdownConverter as MarkdownConverter;

class SpatieCommonMarkShikiHighlighterTest extends TestCase
{
    /**
     * @test
     *
     * Uses CommonMark directly.
     */
    public function confirm_pre(): void
    {
        $theme = 'github-light';

        $md = <<<md
        ```php
        <?php
        class Something {}
        ```
        md;

        $environment = (new Environment())
            ->addExtension(new CommonMarkCoreExtension());

        $markdownConverter = new MarkdownConverter(environment: $environment);

        $result = $markdownConverter->convert($md);

        // Should be false, yeah?
        $this->assertTrue(
            is_a($result, \League\CommonMark\Output\RenderedContent::class)
        );

        // convert() returning RenderedContent instance, convert to string.
        $result = (string) $result;

        // Passes
        $this->assertTrue(
            str_starts_with($result, '<pre')
        );

        // Spatie highlighter
        $environment = (new Environment())
            ->addExtension(new CommonMarkCoreExtension())
            ->addExtension(new HighlightCodeExtension($theme));

        $markdownConverter = new MarkdownConverter(environment: $environment);

        $result = (string) $markdownConverter->convert($md);

        // Fails
        $this->assertTrue(
            str_starts_with($result, '<pre')
        );
    }

    /**
     * @test
     */
    public function has_pre_using_fluent(): void
    {
        $md = <<<md
        ```php
        <?php
        class Something {}
        ```
        md;

        $result = Markdown::create()->addExtension(
            new HighlightCodeExtension('github-light')
        )->convert($md);

        $this->assertTrue(
            str_starts_with($result, '<pre'),
            $result
        );
    }
}
