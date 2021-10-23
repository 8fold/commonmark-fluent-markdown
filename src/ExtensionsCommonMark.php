<?php

declare(strict_types=1);

namespace Eightfold\Markdown;

use Eightfold\Markdown\Markdown;

use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;

trait ExtensionsCommonMark
{
    public function attributes(): Markdown
    {
        $this->addExtensions(
            '\League\CommonMark\Extension' .
            '\Attributes\AttributesExtension'
        );

        return $this;
    }

    public function autoLinking(): Markdown
    {
        $this->addExtensions(
            '\League\CommonMark\Extension' .
            '\Autolink\AutolinkExtension'
        );

        return $this;
    }

    /**
     * @param  array<mixed> $config [description]
     */
    public function defaultAttributes(array $config = []): Markdown
    {
        if (count($config) > 0) {
            $this->addConfig('external_link', $config);

        }

        $this->addExtensions(
            '\League\CommonMark\Extension' .
            '\DefaultAttributes\DefaultAttributesExtension'
        );

        return $this;
    }

    public function descriptionLists(): Markdown
    {
        $this->addExtensions(
            '\League\CommonMark\Extension' .
            '\DescriptionList\DescriptionListExtension'
        );

        return $this;
    }

    /**
     * @param array<string> $tags [description]
     */
    public function disallowedRawHtml(array $tags = []): Markdown
    {
        if (count($tags) > 0) {
            $this->addConfig(
                'disallowed_raw_html',
                ['disallowed_tags' => $tags]
            );

        }

        $this->addExtensions(
            '\League\CommonMark\Extension' .
            '\DisallowedRawHtml\DisallowedRawHtmlExtension'
        );
        return $this;
    }

    /**
     * @param array<mixed> $config [description]
     */
    public function externalLinks(array $config = []): Markdown
    {
        if (count($config) > 0) {
            $this->addConfig('external_link', $config);

        }

        $this->addExtensions(
            '\League\CommonMark\Extension' .
            '\ExternalLink\ExternalLinkExtension'
        );

        return $this;
    }

    /**
     * @param array<mixed> $config [description]
     */
    public function footnotes(array $config = []): Markdown
    {
        if (count($config) > 0) {
            $this->addConfig('footnote', $config);

        }

        $this->addExtensions(
            '\League\CommonMark\Extension' .
            '\Footnote\FootnoteExtension'
        );

        return $this;
    }

    public function gitHubFlavoredMarkdown(): Markdown
    {
        $this->addExtensions(
            '\League\CommonMark\Extension' .
            '\GithubFlavoredMarkdownExtension'
        );

        return $this;
    }

    public function gfm(): Markdown
    {
        return $this->gitHubFlavoredMarkdown();
    }

    /**
     * @param array<mixed> $config [description]
     */
    public function headingPermalinks(array $config = []): Markdown
    {
        if (count($config) > 0) {
            $this->addConfig('heading_permalink', $config);

        }

        $this->addExtensions(
            '\League\CommonMark\Extension' .
            '\HeadingPermalink\HeadingPermalinkExtension'
        );

        return $this;
    }

    public function inlinesOnly(): Markdown
    {
        $this->addExtensions(
            '\League\CommonMark\Extension' .
            '\InlinesOnly\InlinesOnlyExtension'
        );

        return $this;
    }

    /**
     * @param array<mixed> $config [description]
     */
    public function mentions(array $config = []): Markdown
    {
        if (count($config) > 0) {
            $this->addConfig('mentions', $config);

        }

        $this->addExtensions(
            '\League\CommonMark\Extension' .
            '\Mention\MentionExtension'
        );

        return $this;
    }

    public function strikethroughs(): Markdown
    {
        $this->addExtensions(
            '\League\CommonMark\Extension' .
            '\Strikethrough\StrikethroughExtension'
        );

        return $this;
    }

    public function tables(): Markdown
    {
        $this->addExtensions(
            '\League\CommonMark\Extension' .
            '\Table\TableExtension'
        );

        return $this;
    }

    /**
     * @param array<mixed> $config
     * @param array<mixed> $headingPermalinksConfig
     */
    public function tableOfContents(
        array $config = [],
        array $headingPermalinksConfig = []
    ): Markdown {
        if (count($config) > 0) {
            $this->addConfig('table_of_contents', $config);

        }

        $permalinkClassName = '\League\CommonMark\Extension' .
            '\HeadingPermalink\HeadingPermalinkExtension';
        if (! in_array($permalinkClassName, $this->theExtensions())) {
            $this->headingPermalinks($headingPermalinksConfig);

        }

        $this->addExtensions(
            '\League\CommonMark\Extension' .
            '\TableOfContents\TableOfContentsExtension'
        );

        return $this;
    }

    public function taskLists(): Markdown
    {
        $this->addExtensions(
            '\League\CommonMark\Extension' .
            '\TaskList\TaskListExtension'
        );

        return $this;
    }

    /**
     * @param array<mixed> $config [description]
     */
    public function smartPunctuation(array $config = []): Markdown
    {
        if (count($config) > 0) {
            $this->addConfig('smartpunct', $config);

        }

        $this->addExtensions(
            '\League\CommonMark\Extension' .
            '\SmartPunct\SmartPunctExtension'
        );

        return $this;
    }
}
