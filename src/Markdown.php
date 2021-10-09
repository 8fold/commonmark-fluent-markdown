<?php

declare(strict_types=1);

namespace Eightfold\Markdown;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Output\RenderedContentInterface;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
// use League\CommonMark\Extension\InlinesOnly\InlinesOnlyExtension;
// use League\CommonMark\Extension\SmartPunct\SmartPunctExtension;
// use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\MarkdownConverter;

class Markdown
{
    private string $content = '';

    public static function create(string $content = ''): Markdown
    {
        return new Markdown($content);
    }

    public function __construct(string $content = '')
    {
        $this->content = $content;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function convertToHtml(): RenderedContentInterface
    {
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());

        $converter   = new MarkdownConverter($environment);
        return $converter->convertToHtml($this->content());
    }

    public function convertedContent(): string
    {
        return $this->convertToHtml()->getContent();
    }
}
