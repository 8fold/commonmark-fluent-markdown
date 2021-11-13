<?php

declare(strict_types=1);

namespace Eightfold\Markdown;

use League\CommonMark\Environment\Environment as Environment;
use League\CommonMark\MarkdownConverter as MarkdownConverter;

use League\CommonMark\Environment\EnvironmentInterface as EnvironmentInterface;
use League\Config\ConfigurationInterface as ConfigurationInterface;
use League\Config\Configuration as Configuration;

use League\CommonMark\MarkdownConverterInterface as ConverterInterface;
use League\CommonMark\Output\RenderedContentInterface
    as RenderedContentInterface;

use League\CommonMark\Extension\ExtensionInterface as ExtensionInterface;

use League\CommonMark\Extension\FrontMatter\FrontMatterExtension as FrontMatter;

use League\CommonMark\Extension\Attributes\AttributesExtension as Attributes;
use League\CommonMark\Extension\Autolink\AutolinkExtension as AutoLink;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension
    as CoreExtension;
use League\CommonMark\Extension\DefaultAttributes\DefaultAttributesExtension
    as DefaultAttributes;
use League\CommonMark\Extension\DescriptionList\DescriptionListExtension
    as DescriptionList;
use League\CommonMark\Extension\DisallowedRawHtml\DisallowedRawHtmlExtension
    as DisallowedHtml;
use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension
    as ExternalLinks;
use League\CommonMark\Extension\Footnote\FootnoteExtension as Footnotes;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension as Gfm;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension
    as HeaddingPermalinks;
use League\CommonMark\Extension\InlinesOnly\InlinesOnlyExtension as InlinesOnly;
use League\CommonMark\Extension\Mention\MentionExtension as Mentions;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension
    as Strikethroughs;
use League\CommonMark\Extension\Table\TableExtension as Tables;
use League\CommonMark\Extension\TableOfContents\TableOfContentsExtension
    as TableOfContents;
use League\CommonMark\Extension\TaskList\TaskListExtension as TaskLists;
use League\CommonMark\Extension\SmartPunct\SmartPunctExtension as SmartPunct;

class FluentCommonMark implements ConverterInterface
{
    /**
     * @var Environment
     */
    private $environment;

    public static function create(): static
    {
        return new static();
    }

    final public function __construct()
    {
    }

    /**
     * Disable magic setter to ensure immutability
     *
     * @param string $name  The property name
     * @param mixed  $value The property value
     *
     * @return void
     */
    public function __set($name, $value): void
    {
        // Do nothing
    }

    public function withEnvironment(Environment $environment): static
    {
        $this->environment = $environment;
        return $this;
    }

    public function getEnvironment(): Environment
    {
        if ($this->environment === null) {
            $this->environment = new Environment();
        }
        return $this->environment;
    }

    /**
     * @param  array<string, mixed> $config [description]
     */
    public function withConfig(array $config = []): static
    {
        $this->getEnvironment()->mergeConfig($config);
        return $this;
    }

    public function getConfiguration(): ConfigurationInterface
    {
        return $this->getEnvironment()->getConfiguration();
    }

    public function addExtension(ExtensionInterface $extension): static
    {
        $this->getEnvironment()->addExtension($extension);
        return $this;
    }

    /**
     * @return ExtensionInterface[] [description]
     */
    public function getExtensions(): iterable
    {
        return $this->getEnvironment()->getExtensions();
    }

    public function convertToHtml(string $markdown): RenderedContentInterface
    {
        $converter = new MarkdownConverter($this->getEnvironment());

        return $converter->convertToHtml($markdown);
    }

    public function __invoke(string $markdown): RenderedContentInterface
    {
        return $this->convertToHtml($markdown);
    }

    /**
     * @param string $key    [description]
     * @param  array<string, mixed> $config [description]
     */
    protected function addExtensionWithConfig(
        string $key,
        array $config = []
    ): static {
        return $this->withConfig([$key => $config]);
    }

    /*******************************************/
    /**         CommonMark Extensions         **/
    /*******************************************/

    public function frontMatter(): static
    {
        return $this->addExtension(new FrontMatter());
    }

    public function attributes(): static
    {
        return $this->addExtension(new Attributes());
    }

    public function autoLink(): static
    {
        return $this->addExtension(new Autolink());
    }

    public function commonMarkCore(): static
    {
        return $this->addExtension(new CoreExtension());
    }

    /**
     * @param  array<string, mixed> $config [description]
     */
    public function defaultAttributes(array $config = []): static
    {
        if (count($config) === 0) {
            return $this->addExtension(new DefaultAttributes());
        }
        return $this->addExtensionWithConfig('default_attributes', $config)
            ->addExtension(new DefaultAttributes());
    }

    public function descriptionLists(): static
    {
        return $this->addExtension(new DescriptionList());
    }

    /**
     * @param  array<string, mixed> $config [description]
     */
    public function disallowedRawHtml(array $config = []): static
    {
        if (count($config) === 0) {
            return $this->addExtension(new DisallowedHtml());
        }
        return $this->addExtensionWithConfig(
            'disallowed_raw_html',
            $config
        )->addExtension(new DisallowedHtml());
    }

    /**
     * @param  array<string, mixed> $config [description]
     */
    public function externalLinks(array $config = []): static
    {
        if (count($config) === 0) {
            return $this->addExtension(new ExternalLinks());
        }
        return $this->addExtensionWithConfig('external_link', $config)
            ->addExtension(new ExternalLinks());
    }

    /**
     * @param  array<string, mixed> $config [description]
     */
    public function footnotes(array $config = []): static
    {
        if (count($config) === 0) {
            return $this->addExtension(new Footnotes());
        }
        return $this->addExtensionWithConfig('footnote', $config)
            ->addExtension(new Footnotes());
    }

    public function gitHubFlavoredMarkdown(): static
    {
        return $this->addExtension(new Gfm());
    }

    /**
     * @param  array<string, mixed> $config [description]
     */
    public function headingPermalinks(array $config = []): static
    {
        if (count($config) === 0) {
            return $this->addExtension(new HeaddingPermalinks());
        }
        return $this->addExtensionWithConfig('heading_permalink', $config)
            ->addExtension(new HeaddingPermalinks());
    }

    public function inlinesOnly(): static
    {
        return $this->addExtension(new InlinesOnly());
    }

    /**
     * @param  array<string, mixed> $config [description]
     */
    public function mentions(array $config = []): static
    {
        if (count($config) === 0) {
            return $this->addExtension(new Mentions());
        }
        return $this->addExtensionWithConfig('mentions', $config)
            ->addExtension(new Mentions());
    }

    public function strikethroughs(): static
    {
        return $this->addExtension(new Strikethroughs());
    }

    public function tables(): static
    {
        return $this->addExtension(new Tables());
    }

    /**
     * @param  array<string, mixed> $config [description]
     */
    public function tableOfContents(array $config = []): static
    {
        if (count($config) === 0) {
            return $this->addExtension(new TableOfContents());
        }
        return $this->addExtensionWithConfig('table_of_contents', $config)
            ->addExtension(new TableOfContents());
    }

    public function taskLists(): static
    {
        return $this->addExtension(new TaskLists());
    }

    /**
     * @param  array<string, mixed> $config [description]
     */
    public function smartPunctuation(array $config = []): static
    {
        if (count($config) === 0) {
            return $this->addExtension(new SmartPunct());
        }
        return $this->addExtensionWithConfig('smartpunct', $config)
            ->addExtension(new SmartPunct());
    }
}
