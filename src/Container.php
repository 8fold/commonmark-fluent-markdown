<?php
declare(strict_types=1);

namespace Eightfold\Markdown;

use Eightfold\Markdown\Markdown as MarkdownConverter;
use Eightfold\Markdown\FluentCommonMark;

class Container
{
    private static Container $instance;

    /**
     * @var array<int|string, MarkdownConverter|FluentCommonMark>
     */
    private array $converters = [];

    public static function instance(): self
    {
        if (isset(self::$instance) === false) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    final private function __construct()
    {
    }

    final public function __clone()
    {
    }

    final public function __wakeup()
    {
    }

    public function addConverter(
        MarkdownConverter|FluentCommonMark $converter,
        int|string|null $key = null
    ): self {
        if ($key === null) {
            $this->converters[] = $converter;

        } else {
            $this->converters[$key] = $converter;

        }
        return $this;
    }

    /**
     * @return array<int|string, MarkdownConverter|FluentCommonMark>
     */
    public function converters(): array
    {
        return $this->converters;
    }

    public function converter(int|string $at): MarkdownConverter|FluentCommonMark
    {
        return $this->converters[$at];
    }

    public function resetConverters(): void
    {
        $this->converters = [];
    }
}
