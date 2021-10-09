<?php

use Eightfold\Markdown\Markdown;

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
