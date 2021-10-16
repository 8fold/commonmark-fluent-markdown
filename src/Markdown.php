<?php

declare(strict_types=1);

namespace Eightfold\Markdown;

use League\CommonMark\MarkdownConverterInterface;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Output\RenderedContentInterface;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\MarkdownConverter;

class Markdown implements MarkdownConverterInterface
{
    use FluentApi;
    use ExtensionsCommonMark;
    use ExtensionsEightfold;

    private string $content = '';

    private bool $minified = false;

    public static function create(string $content = ''): Markdown
    {
        return new Markdown($content);
    }

    public function __construct(string $content = '')
    {
        $this->content = $content;
    }

    /**
     * @return array<mixed> [description]
     */
    public function theConfig(): array
    {
        if (empty($this->config)) {
            $this->config = [
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
                'max_nesting_level' => 50
            ];
        }
        return $this->config;
    }

    /**
     * @return array<mixed> [description]
     */
    public function theFrontMatter(string $content = ''): array
    {
        if (strlen($content) === 0) {
            $content = $this->content;
        }

        $frontMatterExtension = new FrontMatterExtension();
        $frontMatter = $frontMatterExtension->getFrontMatterParser()->parse(
            $content . "\n"
        )->getFrontMatter();

        if ($frontMatter === null) {
            return [];
        }
        return $frontMatter;
    }

    public function theContent(string $content = ''): string
    {
        $frontMatterExtension = new FrontMatterExtension();
        return $frontMatterExtension->getFrontMatterParser()->parse(
            (strlen($content) === 0) ? $this->content : $content
        )->getContent();
    }

    public function convertToHtml(string $content = ''): RenderedContentInterface
    {
        $environment = new Environment($this->theConfig());
        $environment->addExtension(new CommonMarkCoreExtension());

        foreach ($this->theExtensions() as $extensionClass) {
            $environment->addExtension(new $extensionClass());
        }

        $converter = new MarkdownConverter($environment);

        // if (strlen($content) > 0) {
        //     return $converter->convertToHtml($content);
        // }
        return $converter->convertToHtml($this->theContent($content));
    }

    public function convert(string $content = ''): string
    {
        $html = $this->convertToHtml($this->theContent($content))->getContent();

        if ($this->shouldBeMinified()) {
            return str_replace([
                "\t",
                "\n",
                "\r",
                "\r\n"
            ], '', $html);

        }
        return $html;
    }

    public function __toString(): string
    {
        return $this->convert();
    }

    private function shouldBeMinified(): bool
    {
        return $this->minified;
    }

    /**
     * @return array<string> [description]
     */
    private function theExtensions(): array
    {
        return $this->extensions;
    }

    /**
     * @deprecated use `theFrontMatter()` instead.
     * @return array<mixed>
     */
    public function frontMatter(string $content = ''): array
    {
        return $this->theFrontMatter($content);
    }

    /**
     * @deprecated Use `theContent()` instead.
     */
    public function content(string $content = ''): string
    {
        return $this->theContent($content);
    }

    /**
     * @deprecated Use `convert()` instead.
     */
    public function convertedContent(string $content = ''): string
    {
        return $this->convert($content);
    }

    /**
     * @deprecated Use `theConfig()` instead.
     * @return array<mixed>
     */
    private function configuration(): array
    {
        return $this->theConfig();
    }

    /**
     * @deprecated Use `theExtensions()` instead.
     * @return array<string>
     */
    private function extensions(): array
    {
        return $this->theExtensions();
    }
}
