<?php

declare(strict_types=1);

namespace Eightfold\Markdown;

use Eightfold\Markdown\Markdown;

trait ExtensionsCommonMark
{
    public function attributes(): Markdown
    {
        $this->addExtensions(
            '\League\CommonMark\Extension\Attributes' .
            '\AttributesExtension'
        );
        return $this;
    }

    public function autoLinking(): Markdown
    {
        $this->addExtensions(
            '\League\CommonMark\Extension\Autolink' .
            '\AutolinkExtension'
        );
        return $this;
    }

    /**
     * @param  array<mixed> $config [description]
     */
    public function defaultAttributes(array $config): Markdown
    {
        $this->modifyConfig('default_attributes', $config);

        $this->addExtensions(
            '\League\CommonMark\Extension\DefaultAttributes' .
            '\DefaultAttributesExtension'
        );
        return $this;
    }

    public function descriptionLists(): Markdown
    {
        $this->addExtensions(
            '\League\CommonMark\Extension\DescriptionList' .
            '\DescriptionListExtension'
        );
        return $this;
    }
}
