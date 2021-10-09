<?php

use Eightfold\Markdown\Markdown;

test('Markdown respects configured disallowed raw html', function() {
    expect(
        Markdown::create(<<<md
            <div>Hello, World!</div>
            md
        )->disallowedRawHtml(['div'])->minified()->convertedContent()
    )->toBe(<<<html
        html
    );
});

test('Markdown can use description lists', function() {
    expect(
        Markdown::create(<<<md
            Apple
            :   Pomaceous fruit of plants of the genus Malus in
                the family Rosaceae.
            :   An American computer company.

            Orange
            :   The fruit of an evergreen tree of the genus Citrus.
            md
        )->descriptionLists()->minified()->convertedContent()
    )->toBe(<<<html
        <dl><dt>Apple</dt><dd>Pomaceous fruit of plants of the genus Malus inthe family Rosaceae.</dd><dd>An American computer company.</dd><dt>Orange</dt><dd>The fruit of an evergreen tree of the genus Citrus.</dd></dl>
        html
    );
});

test('Markdown can modify config', function() {
    $default = Markdown::create(<<<md
        <script>alert("Hello XSS!");</script>

        [unsafe link](data:,Hello%2C%20World%21)
        md
    );

    expect(
        $default->minified()->convertedContent()
    )->toBe(<<<html
        <p><a>unsafe link</a></p>
        html
    );

    expect(
        $default->modifyConfig('allow_unsafe_links', true)
            ->minified()->convertedContent()
    )->toBe(<<<html
        <p><a href="data:,Hello%2C%20World%21">unsafe link</a></p>
        html
    );
});

test('Markdown has shorthand extensions', function() {
    expect(
        Markdown::create(<<<md
            Hello, World!{.classy}

            > A nice blockquote
            {: title="Blockquote title"}

            This is *red*{style="color: red"}.
            md
            )->attributes()->convertedContent()
    )->toBe(<<<html
        <p>Hello, World!</p>
        <blockquote title="Blockquote title">
        <p>A nice blockquote</p>
        </blockquote>
        <p>This is <em style="color: red">red</em>.</p>

        html
    );
});

test('Markdown configured with greater security by default', function() {
    // Max nesting level not tested
    expect(
        Markdown::create(<<<md
            <script>alert("Hello XSS!");</script>

            [unsafe link](data:,Hello%2C%20World%21)
            md
            )->minified()->convertedContent()
    )->toBe(<<<html
        <p><a>unsafe link</a></p>
        html
    );
});

test('Markdown configuration can be overridden', function() {
    // Max nesting level not tested
    expect(
        Markdown::create(<<<md
            <script>alert("Hello XSS!");</script>

            [unsafe link](data:,Hello%2C%20World%21)
            md
            )->config([
                'html_input' => 'allow',
                'allow_unsafe_links' => true,
                'max_nesting_level' => PHP_INT_MAX
            ])->minified()->convertedContent()
    )->toBe(<<<html
        <script>alert("Hello XSS!");</script><p><a href="data:,Hello%2C%20World%21">unsafe link</a></p>
        html
    );
});

test('Markdown can clear extensions', function() {
    $content = <<<md
    |col 1 |col 2 |
    |:-----|:-----|
    |1     |      |
    md;
    expect(
        Markdown::create($content)
            ->addExtensions(
                League\CommonMark\Extension\Table\TableExtension::class
            )->overwriteExtensions()->convertedContent()
    )->toBe(<<<html
        <p>|col 1 |col 2 |
        |:-----|:-----|
        |1     |      |</p>

        html
    );
});

test('Markdown output can be minified', function() {
    $content = <<<md
    |col 1 |col 2 |
    |:-----|:-----|
    |1     |      |
    md;
    expect(
        Markdown::create($content)
            ->addExtensions(
                League\CommonMark\Extension\Table\TableExtension::class
            )->minified()->convertedContent()
    )->toBe(<<<html
        <table><thead><tr><th align="left">col 1</th><th align="left">col 2</th></tr></thead><tbody><tr><td align="left">1</td><td align="left"></td></tr></tbody></table>
        html
    );
});

test('Markdown can add and apply extensions', function() {
    $content = <<<md
    |col 1 |col 2 |
    |:-----|:-----|
    |1     |      |
    md;
    expect(
        Markdown::create($content)
            ->addExtensions(
                League\CommonMark\Extension\Table\TableExtension::class
            )->convertedContent()
    )->toBe(<<<html
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

        html
    );
});

test('Markdown can convert to, and immiediately return, HTML', function() {
    expect(
        Markdown::create('# Hello, World!')->convertedContent()
    )->toBe(
        '<h1>Hello, World!</h1>'."\n"
    );
});

test('Markdown can convert to HTML', function() {
    expect(
        Markdown::create('# Hello, World!')->convertToHtml()->getContent()
    )->toBe(
        '<h1>Hello, World!</h1>'."\n"
    );
});

test('Markdown can initialize with string', function() {
    expect(
        Markdown::create('# Hello, World!')->content()
    )->toBe(
        '# Hello, World!'
    );
});

test('Markdown has static initializer', function() {
    expect(
        Markdown::create()
    )->toBeInstanceOf(
        Markdown::class
    );
});

test('Markdown class exists', function() {
    expect(
        class_exists(Markdown::class)
    )->toBeTrue();
});
