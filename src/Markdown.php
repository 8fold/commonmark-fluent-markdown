<?php

declare(strict_types=1);

namespace Eightfold\Markdown;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Output\RenderedContentInterface;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\MarkdownConverter;

class Markdown
{
    use FluentApi;
    use ExtensionsCommonMark;

    private string $content = '';

    private bool $minified = false;

    /**
     * @var array<mixed>
     */
    private array $config = [];

    /**
     * @var array<string>
     */
    private array $extensions = [];

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
    public function frontMatter(): array
    {
        $frontMatterExtension = new FrontMatterExtension();
        return $frontMatterExtension->getFrontMatterParser()->parse(
            $this->content
        )->getFrontMatter();
    }

    public function content(): string
    {
        $frontMatterExtension = new FrontMatterExtension();
        return $frontMatterExtension->getFrontMatterParser()->parse(
            $this->content
        )->getContent();
    }

    public function convertToHtml(): RenderedContentInterface
    {
        $environment = new Environment($this->configuration());
        $environment->addExtension(new CommonMarkCoreExtension());

        foreach ($this->extensions() as $extensionClass) {
            $environment->addExtension(new $extensionClass());
        }

        $converter = new MarkdownConverter($environment);
        return $converter->convertToHtml($this->content());
    }

    public function convertedContent(): string
    {
        $html = $this->convertToHtml()->getContent();

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

    /**
     * @return array<mixed> [description]
     */
    private function configuration(): array
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

    private function shouldBeMinified(): bool
    {
        return $this->minified;
    }

    /**
     * @return array<string> [description]
     */
    private function extensions(): array
    {
        return $this->extensions;
    }
}
