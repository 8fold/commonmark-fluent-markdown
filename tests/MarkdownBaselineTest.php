<?php
declare(strict_types=1);

namespace Eightfold\Markdown\Tests;

use PHPUnit\Framework\TestCase;

use Eightfold\Markdown\Markdown;

class MarkdownBaselineTest extends TestCase
{
    /**
     * @test
     */
    public function is_performant_and_small(): void // phpcs:ignore
    {
        $startMs = hrtime(true);

        $startMem = memory_get_usage();

        $expected = <<<html
            <dl><dt>Apple</dt><dd>Pomaceous fruit of plants of the genus Malus in the family Rosaceae.</dd><dd>An American computer company.</dd><dt>Orange</dt><dd>The fruit of an evergreen tree of the genus Citrus.</dd></dl>
            html;

        $result = Markdown::create()->descriptionLists()->minified()->convert(<<<md
            Apple
            :   Pomaceous fruit of plants of the genus Malus in the family Rosaceae.
            :   An American computer company.

            Orange
            :   The fruit of an evergreen tree of the genus Citrus.
            md
        );

        $endMs = hrtime(true);

        $endMem = memory_get_usage();

        $this->assertSame($expected, $result);

        $elapsed = $endMs - $startMs;
        $ms      = $elapsed/1e+6;

        $this->assertLessThan(3.59, $ms);

        $used = $endMem - $startMem;
        $kb   = round($used/1024.2);

        $this->assertLessThan(1855, $kb);
    }

    /**
     * @test
     */
    public function markdown_has_abbreviations(): void // phpcs:ignore
    {
        $expected = <<<html
            <p><abbr title="eightfold">8fold</abbr></p>
            html;

        $result = Markdown::create()->abbreviations()->minified()->convert(<<<md
            [.8fold](eightfold)
            md
        );

        $this->assertSame($expected, $result);
    }

    /**
     * @test
     */
    public function markdown_configured_with_greater_security_by_default(): void // phpcs:ignore
    {
        $expected = <<<html
            <p><a>unsafe link</a></p>
            html;

        $result = Markdown::create()->minified()->convert(<<<md
            <script>alert("Hello XSS!");</script>

            [unsafe link](data:,Hello%2C%20World%21)
            md
        );

        $this->assertSame($expected, $result);
    }

    /**
     * @test
     */
    public function minify_handles_code_block(): void // phpcs:ignore
    {
        $expected = <<<html
            <pre><code>this is a code block

            with multiple lines

            it should not be minified
            </code></pre>

            html;

        $result = Markdown::create()->minified()->convert(<<<md
            ```
            this is a code block

            with multiple lines

            it should not be minified
            ```
            md
        );

        $this->assertSame($expected, $result);

        $expected = file_get_contents(__DIR__ . '/code-block-test.html');

        $result = Markdown::create()->withConfig(['html_input' => 'allow'])
            ->minified()->convert(
                file_get_contents(__DIR__ . '/code-block-test.md')
            );

        $this->assertSame($expected, $result);
    }

    /**
     * @test
     */
    public function can_use_accessible_heading_permalinks(): void // phpcs:ignore
    {
        $expected = <<<html
            <h1>A word of caution</h1>
            <p>Something</p><div is="heading-wrapper"><h2 id="another-word-of-caution">Another word of caution</h2><a href="#another-word-of-caution"><span aria-hidden="true">¶</span><span>Section titled Another word of caution</span></a></div>

            html;

        $result = Markdown::create()->accessibleHeadingPermalinks([
                'min_heading_level' => 2
            ])->convert(<<<md
                # A word of caution

                Something

                ## Another word of caution
                md
            );

        $this->assertSame($expected, $result);
    }
}
