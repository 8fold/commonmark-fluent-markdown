<?php

declare(strict_types=1);

namespace Eightfold\Markdown;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Output\RenderedContentInterface;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

trait FluentApi
{
    /**
     * @param  array<mixed> $config [description]
     */
    public function config(array $config = []): Markdown
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @param  mixed $value
     */
    public function modifyConfig(string $configId, $value): Markdown
    {
        $c = $this->configuration();
        $c[$configId] = $value;
        $this->config = $c;
        return $this;
    }

    public function minified(bool $minified = true): Markdown
    {
        $this->minified = $minified;
        return $this;
    }

    public function overwriteExtensions(string ...$extensionClassNames): Markdown
    {
        $this->extensions = [];
        $this->addExtensions(...$extensionClassNames);
        return $this;
    }

    public function addExtensions(string ...$extensionClassNames): Markdown
    {
        $ext = array_merge($this->extensions(), $extensionClassNames);
        $this->extensions = array_unique($ext);
        return $this;
    }
}
