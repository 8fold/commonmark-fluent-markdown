<?php
declare(strict_types=1);

namespace Eightfold\Markdown\Tests;

use PHPUnit\Framework\TestCase;

use Eightfold\Markdown\FluentCommonMark;

use League\CommonMark\Environment\Environment as Environment;
use League\Config\ConfigurationInterface as ConfigurationInterface;

// use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;

class FluentCommonMarkBaselineTest extends TestCase
{
    /**
     * @test
     */
    public function can_use_attributes(): void // phpcs:ignore
    {
        $markdown = <<<md
        ![this is an image](/path/to/image){width="200px", height="auto"}
        md;

        $expected = <<<html
        <p><img src="/path/to/image" alt="this is an image" width="200px" height="auto" /></p>
        html;

        // Define your configuration, if needed
        $config = [];

        // Configure the Environment with all the CommonMark parsers/renderers
        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());

        // Add this extension
        $environment->addExtension(new AttributesExtension());

        // Instantiate the converter engine and start converting some Markdown!
        $converter = new MarkdownConverter($environment);
        $result = $converter->convert($markdown)->getContent();

        // $result = FluentCommonMark::create()->withEnvironment($environment)->commonMarkCore()
            // ->attributes()->convertToHtml($markdown)->getContent();

        $this->assertSame(
            $expected,
            $result
            // '<p><img src="/path/to/image" alt="this is an image" />{width=&quot;200px&quot;, height=&quot;auto&quot;}</p>
        );
    }

    /**
     * @test
     */
    public function is_performant_and_small(): void // phpcs:ignore
    {
        $startMs = hrtime(true);

        $startMem = memory_get_usage();

        $result = FluentCommonMark::create()->commonMarkCore()
            ->descriptionLists()->convertToHtml(<<<md
                Apple
                :   Pomaceous fruit of plants of the genus Malus in the family Rosaceae.
                :   An American computer company.

                Orange
                :   The fruit of an evergreen tree of the genus Citrus.
                md
            )->getContent();

        $endMs = hrtime(true);

        $endMem = memory_get_usage();

        $elapsed = $endMs - $startMs;
        $ms      = $elapsed/1e+6;

        $this->assertLessThan(54, $ms);

        $used = $endMem - $startMem;
        $kb   = round($used/1024.2);

        $this->assertLessThan(1950, $kb);
    }

    /**
     * @test
     */
    public function respects_configured_disallowed_raw_html(): void // phpcs:ignore
    {
        $expected = <<<html
            &lt;div>Hello, World!&lt;/div>

            html;

        $result = FluentCommonMark::create()
            ->commonMarkCore()
            ->disallowedRawHtml([
                'disallowed_tags' => ['div']
            ])->convertToHtml(<<<md
                <div>Hello, World!</div>
                md
            )->getContent();

        $this->assertSame($expected, $result);
    }

    /**
     * @test
     */
    public function can_use_description_lists(): void // phpcs:ignore
    {
        $expected = <<<html
            <dl>
            <dt>Apple</dt>
            <dd>Pomaceous fruit of plants of the genus Malus in the family Rosaceae.</dd>
            <dd>An American computer company.</dd>
            <dt>Orange</dt>
            <dd>The fruit of an evergreen tree of the genus Citrus.</dd>
            </dl>

            html;

        $result = FluentCommonMark::create()->commonMarkCore()
            ->descriptionLists()->convertToHtml(<<<md
                Apple
                :   Pomaceous fruit of plants of the genus Malus in the family Rosaceae.
                :   An American computer company.

                Orange
                :   The fruit of an evergreen tree of the genus Citrus.
                md
            )->getContent();

        $this->assertSame($expected, $result);
    }

    /**
     * @test
     */
    public function can_modify_config(): void // phpcs:ignore
    {
        $expected = <<<html
            <p><a>unsafe link</a></p>

            html;

        $result = FluentCommonMark::create()
            ->withConfig(['allow_unsafe_links' => false])
            ->commonMarkCore()
            ->convertToHtml(<<<md
                [unsafe link](data:,Hello%2C%20World%21)
                md
            )->getContent();

        $this->assertSame($expected, $result);

        $expected = <<<html
            <script>alert("Hello XSS!");</script>
            <p><a href="data:,Hello%2C%20World%21">unsafe link</a></p>

            html;

        $result = FluentCommonMark::create()
            ->commonMarkCore()
            ->convertToHtml(<<<md
                <script>alert("Hello XSS!");</script>

                [unsafe link](data:,Hello%2C%20World%21)

                md
            )->getContent();

        $this->assertSame($expected, $result);
    }

    /**
     * @test
     */
    public function can_use_tables(): void // phpcs:ignore
    {
        $markdown = <<<md
            |col 1 |col 2 |
            |:-----|:-----|
            |1     |      |
            md;

        $expected = <<<html
            <table>
            <thead>
            <tr>
            <th align="left">col 1</th>
            <th align="left">col 2</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            <td align="left">1</td>
            <td align="left"></td>
            </tr>
            </tbody>
            </table>

            html;

        $result = FluentCommonMark::create()
            ->commonMarkCore()
            ->tables()
            ->convertToHtml(<<<md
                |col 1 |col 2 |
                |:-----|:-----|
                |1     |      |
                md
            )->getContent();

        $this->assertSame($expected, $result);
    }

    /**
     * @test
     */
    public function can_convert_to_html(): void // phpcs:ignore
    {
        $expected = <<<html
            <h1>Hello, World!</h1>

            html;

        $result = FluentCommonMark::create()
            ->commonMarkCore()
            ->convertToHtml('# Hello, World!')->getContent();

        $this->assertSame($expected, $result);

         $expected = <<<html
            <hr />
            <h2>front-matter: Hello</h2>
            <p>World!</p>

            html;

        $result = FluentCommonMark::create()
            ->commonMarkCore()
            ->convertToHtml(<<<md
                ---
                front-matter: Hello
                ---

                World!
                md
            )->getContent();

        $this->assertSame($expected, $result);

        $expected = <<<html
            <p>World!</p>

            html;

        $result = FluentCommonMark::create()
            ->commonMarkCore()
            ->frontMatter()
            ->convertToHtml(<<<md
                ---
                front-matter: Hello
                ---

                World!
                md
            )->getContent();

        $this->assertSame($expected, $result);
    }

    /**
     * @test
     */
    public function can_set_environment_and_config(): void // phpcs:ignore
    {
        $sut = FluentCommonMark::create();

        $this->assertInstanceOf(
            ConfigurationInterface::class,
            $sut->getConfiguration()
        );

        $this->assertInstanceOf(
            Environment::class,
            $sut->getEnvironment()
        );
    }

    /**
     * @test
     */
    public function has_static_initializer(): void // phpcs:ignore
    {
        $this->assertInstanceOf(
            FluentCommonMark::class,
            FluentCommonMark::create()
        );
    }
}
