<?php

declare(strict_types=1);

namespace Eightfold\Markdown;

use Eightfold\Markdown\Markdown;

trait ExtensionsEightfold
{
    public function abbreviations(): Markdown
    {
        $this->addExtensions(
            '\Eightfold\CommonMarkAbbreviations' .
            '\AbbreviationExtension'
        );

        return $this;
    }
}
