<?php

use Eightfold\Markdown\FluentCommonMark;

use League\CommonMark\Environment\Environment as Environment;
use League\Config\ConfigurationInterface as ConfigurationInterface;

test('Is performant and small', function() {
    $startMs = hrtime(true);

    $startMem = memory_get_usage();

    $html = FluentCommonMark::create()->commonMarkCore()
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

    expect($ms)->toBeLessThan(17.24);

    $used = $endMem - $startMem;
    $kb   = round($used/1024.2);

    expect($kb)->toBeLessThan(1765);
})->group('commonmark');

test('Respects configured disallowed raw html', function() {
    expect(
        FluentCommonMark::create()
            ->commonMarkCore()
            ->disallowedRawHtml([
                'disallowed_tags' => ['div']
            ])->convertToHtml(<<<md
                <div>Hello, World!</div>
                md
            )->getContent()
    )->toBe(<<<html
        &lt;div>Hello, World!&lt;/div>

        html
    );
})->group('commonmark');

test('Can use description lists', function() {
    expect(
        FluentCommonMark::create()->commonMarkCore()
            ->descriptionLists()->convertToHtml(<<<md
                Apple
                :   Pomaceous fruit of plants of the genus Malus in the family Rosaceae.
                :   An American computer company.

                Orange
                :   The fruit of an evergreen tree of the genus Citrus.
                md
            )->getContent()
    )->toBe(<<<html
        <dl>
        <dt>Apple</dt>
        <dd>Pomaceous fruit of plants of the genus Malus in the family Rosaceae.</dd>
        <dd>An American computer company.</dd>
        <dt>Orange</dt>
        <dd>The fruit of an evergreen tree of the genus Citrus.</dd>
        </dl>

        html
    );
})->group('commonmark');

test('Can modify config', function() {
    expect(
        FluentCommonMark::create()
            ->withConfig(['allow_unsafe_links' => false])
            ->commonMarkCore()
            ->convertToHtml(<<<md
                [unsafe link](data:,Hello%2C%20World%21)
                md
            )->getContent()
    )->toBe(<<<html
        <p><a>unsafe link</a></p>

        html
    );

    expect(
        FluentCommonMark::create()
            ->commonMarkCore()
            ->convertToHtml(<<<md
                <script>alert("Hello XSS!");</script>

                [unsafe link](data:,Hello%2C%20World%21)

                md
            )->getContent()
    )->toBe(<<<html
        <script>alert("Hello XSS!");</script>
        <p><a href="data:,Hello%2C%20World%21">unsafe link</a></p>

        html
    );
})->group('commonmark');

test('Can use tables', function() {
    $markdown = <<<md
    |col 1 |col 2 |
    |:-----|:-----|
    |1     |      |
    md;
    expect(
        FluentCommonMark::create()
            ->commonMarkCore()
            ->tables()
            ->convertToHtml(<<<md
                |col 1 |col 2 |
                |:-----|:-----|
                |1     |      |
                md
            )->getContent()
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
})->group('commonmark');

test('Can convert to HTML', function() {
    expect(
        FluentCommonMark::create()
            ->commonMarkCore()
            ->convertToHtml('# Hello, World!')->getContent()
    )->toBe(<<<html
        <h1>Hello, World!</h1>

        html
    );

    expect(
        FluentCommonMark::create()
            ->commonMarkCore()
            ->convertToHtml(<<<md
                ---
                front-matter: Hello
                ---

                World!
                md
            )->getContent()
    )->toBe(<<<html
        <hr />
        <h2>front-matter: Hello</h2>
        <p>World!</p>

        html
    );

    expect(
        FluentCommonMark::create()
            ->commonMarkCore()
            ->frontMatter()
            ->convertToHtml(<<<md
                ---
                front-matter: Hello
                ---

                World!
                md
            )->getContent()
    )->toBe(<<<html
        <p>World!</p>

        html
    );
})->group('commonmark');

test('Can set environment and configuration', function() {
    $sut = FluentCommonMark::create();

    expect(
        $sut->getConfiguration()
    )->toBeInstanceOf(
        ConfigurationInterface::class
    );

    expect(
        $sut->getEnvironment()
    )->toBeInstanceOf(
        Environment::class
    );
})->group('commonmark');

test('Has static initializer', function() {
    expect(
        FluentCommonMark::create()
    )->toBeInstanceOf(
        FluentCommonMark::class
    );
})->group('commonmark');
