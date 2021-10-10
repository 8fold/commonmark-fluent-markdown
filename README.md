# 8fold Fluent Markdown for CommonMark

A fluent API for use with the highly-extensible [CommonMark parser](https://commonmark.thephpleague.com/2.0/) from the league of extraordinary packages.

We try to put off instantiation and execution until the last possible moment.

## Installation

{how does one install the product}

## Usage

Warning: Users of this library are responsible for sanitizing content.

Write some markdown:

```markdown
# Markdown

Woohoo!
```

Pass the markdown into the `Markdown` class and ask for the `convertedContent`:

```php
use Eightfold\Markdown\Markdown;

print Markdown::create($markdown)->convertedContent();
```

Output:

```html
<h1>Markdown</h1>
<p>Woohoo!</p>

```

You can also go straight to the output:

```php
use Eightfold\Markdown\Markdown;

print Markdown::create($markdown);
```

Same output as before.

### YAML front matter

If you are using YAML front matter, you can access it at any time via the
`frontMatter` method:

```markdown
---
title: Front matter
---

Some content.
```

```php
use Eightfold\Markdown\Markdown;

$frontMatter = Markdown::create($markdown)->frontMatter();

var_dump($frontMatter);

// output:
// array(
//   'title' => 'Front matter'
// )
```

While we use the [CommonMark-native extension](https://commonmark.thephpleague.com/2.0/extensions/front-matter/), for the purposes of Fluent Markdown
we do not classify this as a strictly extension behavior as it returns something
other than rendered HTML, unlike the other extensions.

### Extensions

Each internal [extension of the CommonMark package}https://commonmark.thephpleague.com/2.0/extensions/overview/ is available via the fluent
API alont with [8fold Abbreviations](https://github.com/8fold/commonmark-abbreviations):

```markdown
---
title: Front matter
---

~~strikethrough from GitHub Flavored Markdown~~

An [.abbr](abbreviation) from 8fold Abbreviations.
```

```php
use Eightfold\Markdown\Markdown;

print Markdown::create($markdown)
  ->gitHubFlavoredMarkdown()
  ->abbreviations();
```

```html
<p><del>strikethrough from GitHub Flavored Markdown</del></p>
<p>An <abbr title="abbreviation">abbr</abbr> from 8fold Abbreviations.</p>
```

If the [extension accepts a configuration](https://commonmark.thephpleague.com/2.0/extensions/disallowed-raw-html/), you can pass it into the method and
the configruation will be modified accordingly.

```php
use Eightfold\Markdown\Markdown;

print Markdown::create($markdown)
  ->disallowedRawHtml(['div']);
```

Not passing in a configuration will result in using the default established by
the CommonMark library.

### Fluent API

The non-extension-related fluent API methods can be found in the
[`FluentApi` trait](https://github.com/8fold/commonmark-fluent-markdown/blob/main/src/FluentApi.php).

The primary capabilities afforded by the fluent API are:

- adding or modifying a CommonMark-compliant configuration and
- adding or resetting extensions (if not using the fluen API extension methods).

Note: When add an extension, Fluent Markdown uses the full class name of the
extension, not an instance of the extension. This is part of the waiting for the
last possible moment before executing.

```php
use Eightfold\Markdown\Markdown;

print Markdown::create($markdown)
  ->addExtensions(
  	\League\CommonMark\Extension\Mention\MentionExtension::class,
  	'\Eightfold\CommonMarkAbbreviations\AbbreviationExtension'
  );
```

### Minified output

The output of CommonMark tends to render each block on a new line.

```markdown
Block 1

Block 2
```

Output:

```html
<p>Block 1</p>
<p>Block 2</p>
```

In keeping with 8fold [XML Builder](https://github.com/8fold/php-xml-builder/tree/0.6.0)
and [HTML Builder](https://github.com/8fold/php-html-builder/tree/0.5.1), which
render XML and HTML as a flat string, Fluent Markdown provides the `minified`
method to accomplish the same:

```php
use Eightfold\Markdown\Markdown;

print Markdown::create($markdown)->minified();
```

Output:

```html
<p>Block 1</p><p>Block 2</p>
```

For longer documents the removal of tabs and carriage returns can add up to quite
a bit in the response.

## Details

This is actually our third foray into wrapping CommonMark.

CommonMark has been a staple in 8fold web development since inception. As we've
progressed and continued to slowly evolve our own XML and HTML generating packages
and used those solutions in an array of websites, CommonMark has been featured
front and center, as it were.

Given how much CommonMark is used in our various projects and our desire to be
loosely coupled with any solutions we don't write ourselves, I think we've
come to a solution that accomplishes both those missions.

Minimal code to start, configure, and render HTML. A consistent API to reduce
impact as CommonMark continues to evolve.

## Other

- [Code of Conduct](https://github.com/8fold/commonmark-fluent-markdown/blob/main/.github/CODE_OF_CONDUCT.md)
- [Contributing](https://github.com/8fold/commonmark-fluent-markdown/blob/main/.github/CONTRIBUTING.md)
- [Governance](https://github.com/8fold/commonmark-fluent-markdown/blob/main/.github/GOVERNANCE.md)
- [Versioning](https://github.com/8fold/commonmark-fluent-markdown/blob/main/.github/VERSIONING.md)
- [Security](https://github.com/8fold/commonmark-fluent-markdown/blob/main/.github/SECURITY.md)

