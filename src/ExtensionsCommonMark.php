<?php

declare(strict_types=1);

namespace Eightfold\Markdown;

use Eightfold\Markdown\Markdown;

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
            $this->modifyConfig('external_link', $config);

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
            $this->modifyConfig(
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
            $this->modifyConfig('external_link', $config);

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
            $this->modifyConfig('footnote', $config);

        }

        $this->addExtensions(
            '\League\CommonMark\Extension' .
            '\Footnote\FootnoteExtension'
        );

        return $this;
    }
}
