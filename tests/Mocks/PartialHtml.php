<?php
declare(strict_types=1);

namespace Eightfold\Markdown\Tests\Mocks;

use Eightfold\CommonMarkPartials\PartialInterface;

use Eightfold\CommonMarkPartials\PartialInput;

class PartialHtml implements PartialInterface
{
    public function __invoke(PartialInput $input, array $extras = []): string
    {
        return '<h1>This was created by a partial</h1>';
    }
}
