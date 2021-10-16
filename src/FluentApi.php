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
     * @var array<mixed>
     */
    private array $config = [];

    /**
     * @var array<string>
     */
    private array $extensions = [];

    public function minified(bool $minified = true): Markdown
    {
        $this->minified = $minified;
        return $this;
    }

    /**
     * @param  array<mixed> $config [description]
     */
    public function overwriteConfig(array $config = []): Markdown
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @param  mixed $value
     */
    public function addConfig(string $configKey, $value): Markdown
    {
        $c = $this->theConfig();
        $c[$configKey] = $value;

        $this->overwriteConfig($c);

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

    /**
     * @deprecated Use `overwriteConfig()` instead.
     */
    public function config(array $config = []): Markdown
    {
        return $this->overwriteConfig($config);
    }

    /**
     * @deprecated Use `addConfig()` instead.
     */
    public function modifyConfig(string $configId, $value): Markdown
    {
        return $this->addConfig($configId, $value);
    }
}
