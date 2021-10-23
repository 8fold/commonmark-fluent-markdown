<?php

declare(strict_types=1);

namespace Eightfold\Markdown;

use League\CommonMark\Environment\Environment as Environment;
use League\CommonMark\MarkdownConverter as MarkdownConverter;

use League\CommonMark\Environment\EnvironmentInterface as EnvironmentInterface;
use League\Config\ConfigurationInterface as ConfigurationInterface;
use League\Config\Configuration as Configuration;

use League\CommonMark\MarkdownConverterInterface as ConverterInterface;
use League\CommonMark\Output\RenderedContentInterface as RenderedContentInterface;

use League\CommonMark\Extension\ExtensionInterface as ExtensionInterface;

use League\CommonMark\Extension\FrontMatter\FrontMatterExtension as FrontMatter;

use League\CommonMark\Extension\Attributes\AttributesExtension as Attributes;
use League\CommonMark\Extension\Autolink\AutolinkExtension as AutoLink;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension as CoreExtension;
use League\CommonMark\Extension\DefaultAttributes\DefaultAttributesExtension as DefaultAttributes;
use League\CommonMark\Extension\DescriptionList\DescriptionListExtension as DescriptionList;
use League\CommonMark\Extension\DisallowedRawHtml\DisallowedRawHtmlExtension as DisallowedHtml;
use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension as ExternalLinks;
use League\CommonMark\Extension\Footnote\FootnoteExtension as Footnotes;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension as Gfm;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension as HeaddingPermalinks;
use League\CommonMark\Extension\InlinesOnly\InlinesOnlyExtension as InlinesOnly;
use League\CommonMark\Extension\Mention\MentionExtension as Mentions;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension as Strikethroughs;
use League\CommonMark\Extension\Table\TableExtension as Tables;
use League\CommonMark\Extension\TableOfContents\TableOfContentsExtension as TableOfContents;
use League\CommonMark\Extension\TaskList\TaskListExtension as TaskLists;
use League\CommonMark\Extension\SmartPunct\SmartPunctExtension as SmartPunct;

class FluentCommonMark implements ConverterInterface
{
    private $environment;

    private $config;

    public static function create(): FluentCommonMark
    {
        return new FluentCommonMark();
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

    public function withEnvironment(
        EnvironmentInterface $environment
    ): FluentCommonMark {
        $new = clone $this;
        $new->environment = $environment;
        return $new;
    }

    public function getEnvironment(): EnvironmentInterface
    {
        if ($this->environment === null) {
            $this->environment = new Environment();
        }
        return $this->environment;
    }

    public function withConfig(array $config = []): FluentCommonMark
    {
        if ($this->config === null) {
            $this->config = $this->getConfiguration();
        }

        $new = clone $this;
        $new->config = $this->getEnvironment()->mergeConfig($config);
        return $new;
    }

    public function getConfiguration(): ConfigurationInterface
    {
        return $this->getEnvironment()->getConfiguration();
    }

    public function addExtension(
        ExtensionInterface $extension
    ): FluentCommonMark {
        $this->getEnvironment()->addExtension($extension);
        return $this;
    }

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

    private function addExtensionWithConfig(string $key, array $config = [])
    {
        $new = clone $this;
        if (count($config) > 0) {
            $new = $this->withConfig([$key => $config]);
        }
        return $new;
    }

    /*******************************************/
    /**         CommonMark Extensions         **/
    /*******************************************/

    public function frontMatter(): FluentCommonMark
    {
        return $this->addExtension(new FrontMatter());
    }

    public function attributes(): FluentCommonMark
    {
        return $this->addExtension(new Attributes());
    }

    public function autoLink(): FluentCommonMark
    {
        return $this->addExtension(new Autolink());
    }

    public function commonMarkCore(): FluentCommonMark
    {
        return $this->addExtension(new CoreExtension());
    }

    /**
     * @param  array<mixed> $config [description]
     */
    public function defaultAttributes(array $config = []): FluentCommonMark
    {
        if (count($config) === 0) {
            return $this->addExtension(new DefaultAttributes());
        }
        return $this->addExtensionWithConfig('default_attributes', $config)
            ->addExtension(new DefaultAttributes());
    }

    public function descriptionLists(): FluentCommonMark
    {
        return $this->addExtension(new DescriptionList());
    }

    /**
     * @param array<string> $tags [description]
     */
    public function disallowedRawHtml(array $config = []): FluentCommonMark
    {
        if (count($config) === 0) {
            return $this->addExtension(new DisallowedHtml());
        }
        return $this->addExtensionWithConfig(
            'disallowed_raw_html', $config
        )->addExtension(new DisallowedHtml());
    }

    /**
     * @param array<mixed> $config [description]
     */
    public function externalLinks(array $config = []): FluentCommonMark
    {
        if (count($config) === 0) {
            return $this->addExtension(new ExternalLinks());
        }
        return $this->addExtensionWithConfig('external_link', $config)
            ->addExtension(new ExternalLinks());
    }

    /**
     * @param array<mixed> $config [description]
     */
    public function footnotes(array $config = []): FluentCommonMark
    {
        if (count($config) === 0) {
            return $this->addExtension(new Footnotes());
        }
        return $this->addExtensionWithConfig('footnote', $config)
            ->addExtension(new Footnotes());
    }

    public function gitHubFlavoredMarkdown(): FluentCommonMark
    {
        return $this->addExtension(new Gfm());
    }

    /**
     * @param array<mixed> $config [description]
     */
    public function headingPermalinks(array $config = []): FluentCommonMark
    {
        if (count($config) === 0) {
            return $this->addExtension(new headingPermalinks());
        }
        return $this->addExtensionWithConfig('heading_permalink', $config)
            ->addExtension(new headingPermalinks());
    }

    public function inlinesOnly(): FluentCommonMark
    {
        return $this->addExtension(new InlinesOnly());
    }

    /**
     * @param array<mixed> $config [description]
     */
    public function mentions(array $config = []): FluentCommonMark
    {
        if (count($config) === 0) {
            return $this->addExtension(new Mentions());
        }
        return $this->addExtensionWithConfig($config)
            ->addExtension(new Mentions());
    }

    public function strikethroughs(): FluentCommonMark
    {
        return $this->addExtension(new Strikethroughs());
    }

    public function tables(): FluentCommonMark
    {
        return $this->addExtension(new Tables());
    }

    /**
     * @param array<mixed> $config
     * @param array<mixed> $headingPermalinksConfig
     */
    public function tableOfContents(array $config = [])
    {
        if (count($config) === 0) {
            return $this->addExtension(new TableOfContents());
        }
        return $this->addExtensionWithConfig('table_of_contents', $config)
            ->addExtension(new TableOfContents());
    }

    public function taskLists(): FluentCommonMark
    {
        return $this->addExtension(new TaskLists());
    }

    /**
     * @param array<mixed> $config [description]
     */
    public function smartPunctuation(array $config = []): FluentCommonMark
    {
        if (count($config) === 0) {
            return $this->addExtension(new SmartPunct());
        }
        return $this->addExtensionWithConfig($config)
            ->addExtension(new SmartPunct());
    }
}
