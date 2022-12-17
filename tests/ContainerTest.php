<?php
declare(strict_types=1);

namespace Eightfold\Markdown\Tests;

use PHPUnit\Framework\TestCase;

use Eightfold\Markdown\Container;

use Eightfold\Markdown\FluentCommonMark;
use Eightfold\Markdown\Markdown;

class ContainerTest extends TestCase
{
    public function tearDown(): void
    {
        Container::instance()->resetConverters();

        parent::tearDown();
    }

    /**
     * @test
     */
    public function can_add_multiple_converters(): void
    {
        $expected = 2;

        Container::instance()->addConverter(
            FluentCommonMark::create(),
            'fluent'
        )->addConverter(Markdown::create());

        $converters = Container::instance()->converters();

        $result = count($converters);

        $this->assertSame(
            $expected,
            $result
        );

        $this->assertTrue(
            is_a(
                Container::instance()->converter(at: 'fluent'),
                FluentCommonMark::class
            )
        );
    }

    /**
     * @test
     */
    public function can_add_converter(): void
    {
        $expected = 1;

        Container::instance()->addConverter(
            Markdown::create()
        );

        $converters = Container::instance()->converters();

        $result = count($converters);

        $this->assertSame(
            $expected,
            $result
        );
    }
}
