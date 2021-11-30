<?php

use Eightfold\Markdown\Markdown;

use League\CommonMark\Extension\Attributes\AttributesExtension as Attributes;

test('is performant and small', function() {
    $startMs = hrtime(true);

    $startMem = memory_get_usage();

    $html = Markdown::create()->descriptionLists()->minified()->convert(<<<md
            Apple
            :   Pomaceous fruit of plants of the genus Malus in the family Rosaceae.
            :   An American computer company.

            Orange
            :   The fruit of an evergreen tree of the genus Citrus.
            md
        );

    $endMs = hrtime(true);

    $endMem = memory_get_usage();

    expect(
        $html
    )->toBe(<<<html
        <dl><dt>Apple</dt><dd>Pomaceous fruit of plants of the genus Malus in the family Rosaceae.</dd><dd>An American computer company.</dd><dt>Orange</dt><dd>The fruit of an evergreen tree of the genus Citrus.</dd></dl>
        html
    );

    $elapsed = $endMs - $startMs;
    $ms      = $elapsed/1e+6;

    expect($ms)->toBeLessThan(3.59);

    $used = $endMem - $startMem;
    $kb   = round($used/1024.2);

    expect($kb)->toBeLessThan(1855);
})->group('markdown');

test('Markdown has abbreviations', function() {
    expect(
        Markdown::create()->abbreviations()->minified()->convert(<<<md
            [.8fold](eightfold)
            md
        )
    )->toBe(<<<html
        <p><abbr title="eightfold">8fold</abbr></p>
        html
    );
})->group('markdown');

test('Markdown configured with greater security by default', function() {
    // Max nesting level not tested
    expect(
        Markdown::create()->minified()->convert(<<<md
            <script>alert("Hello XSS!");</script>

            [unsafe link](data:,Hello%2C%20World%21)
            md)
    )->toBe(<<<html
        <p><a>unsafe link</a></p>
        html
    );
})->group('markdown');

test('minify with code block', function() {
    expect(
        Markdown::create()->minified()->convert(<<<md
            ```
            this is a code block

            with multiple lines

            it should not be minified
            ```
            md
        )
    )->toBe(<<<html
        <pre><code>this is a code block

        with multiple lines

        it should not be minified
        </code></pre>

        html
    );
});

it('can use accessible heading permalinks', function() {
    expect(
        Markdown::create()->accessibleHeadingPermalinks([
            'min_heading_level' => 2
        ])->convert(<<<md
            # A word of caution

            Something

            ## Another word of caution
            md
        )
    )->toBe(<<<html
        <h1>A word of caution</h1>
        <p>Something</p><div is="heading-wrapper"><h2 id="another-word-of-caution">Another word of caution</h2><a href="#another-word-of-caution"><span aria-hidden="true">¶</span><span>Section titled Another word of caution</span></a></div>

        html
    );
});

it('can accept late extensions using add extensions', function() {
    $converter = Markdown::create()->minified();

    $converter = $converter->addExtension(new Attributes());

    expect(
        $converter->convert(<<<md
            [Link text]('/test'){#id}
            md
        )
    )->toBe(<<<html
        <p><a id="id" href="'/test'">Link text</a></p>
        html
    );

    $converter = Markdown::create()
        ->minified()
        ->smartPunctuation()
        ->withConfig(['html_input' => 'allow'])
        ->descriptionLists()
        ->attributes()
        ->abbreviations()
        ->externalLinks([
            'open_in_new_window' => true,
            'internal_hosts' => 'joshbruce.com'
        ])->accessibleHeadingPermalinks(
            [
                'min_heading_level' => 2,
                'symbol' => '＃'
            ],
        );

    expect(
        $converter->convert(<<<md
            [.ABBR](Abbreviation)
            md
        )
    )->toBe(<<<html
        <p><abbr title="Abbreviation">ABBR</abbr></p>
        html
    );

    $converter = $converter->addExtension(new Attributes());

    expect(
        $converter->convert(<<<md
            [.ABBR](Abbreviation)

            [Link text]('/test'){#id}
            md
        )
    )->toBe(<<<html
        <p><abbr title="Abbreviation">ABBR</abbr></p><p><a id="id" href="'/test'">Link text</a></p>
        html
    );
});
