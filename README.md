# 8fold Fluent Markdown for CommonMark

A fluent API for use with the highly-extensible [CommonMark parser](https://commonmark.thephpleague.com/2.0/) from the league of extraordinary packages.

We try to put off instantiation and execution until the last possible moment.

## Installation

```bash
composer require 8fold/commonmark-fluent-markdown
```

## Usage

⚠️ Warning: Users of this library are responsible for sanitizing content.

There are two entry classes:

1. Markdown: Does not follow strictly follow conventions established by the [League CommonMark](https://commonmark.thephpleague.com).
2. FluentCommonMark: Tries to mirror the conventions of League CommonMark in a fluent way.

The naming convention for methods that are not part of the League CommonMark implementation follow the convention established by [PSR-7](https://www.php-fig.org/psr/psr-7/).

Methods prefixed by the word `with` will return a new instance to facilitate immunitability.

### Markdown

The Markdown class makes some presumptions the FluentCommonMark class does not:

1. You will be using the CommonMarkCoreExtension.
2. There will always be the potential for front matter; therefore, the FrontMatterExtension will always be used to separate front matter from the body.

The Markdown class uses the the default configuration provided by CommonMark with modifications recommended by the [security](https://commonmark.thephpleague.com/2.0/security/) page of the CommonMark documentation.

Write some markdown:

```markdown
# Markdown

Woohoo!
```

Pass the markdown into the `Markdown` class and ask for the `convertedContent`:

```php
use Eightfold\Markdown\Markdown;

print Markdown::create()->convert($markdown);
```

Output:

```html
<h1>Markdown</h1>
<p>Woohoo!</p>

```

```php
use Eightfold\Markdown\Markdown;

print Markdown::create()->minified()->convert($markdown);
```

```html
<h1>Markdown</h1><p>Woohoo!</p>
```

You can have markdown that is nothing but front matter as well.

```markdown
---
title: The title
---
```

```php
use Eightfold\Markdown\Markdown;

print Markdown::create()->minified()->getFrontMatter($markdown);
```

Output:

```php
[
  'title' => 'The title'
]

```

The Mardkown extends the FluentCommonMark class.

### FluentMarkdown

The FluentMarkdown class is designed to mimic the behavior and feel of the CommonMark library. There are additional methods in place to facilitate the fully fluent nature of this library.

### Extensions

Each internal [CommonMark extension](https://commonmark.thephpleague.com/2.0/extensions/overview/) is available via the fluent API along with
[8fold Abbreviations](https://github.com/8fold/commonmark-abbreviations):

```markdown
---
title: Front matter
---

~~strikethrough from GitHub Flavored Markdown~~

An [.abbr](abbreviation) from 8fold Abbreviations.
```

Setting the extensions and printing the result:

```php
use Eightfold\Markdown\Markdown;
use Eightfold\Markdown\FluentCommonMark;

print Markdown::create()
  ->minified()
  ->gitHubFlavoredMarkdown()
  ->abbreviations()
  ->convert($markdown);

print Markdown::create()
  ->gitHubFlavoredMarkdown()
  ->abbreviations()
  ->convertToHtml($markdown);
```

The result:

```html
<p><del>strikethrough from GitHub Flavored Markdown</del></p><p>An <abbr title="abbreviation">abbr</abbr> from 8fold Abbreviations.</p>

<p><del>strikethrough from GitHub Flavored Markdown</del></p>
<p>An <abbr title="abbreviation">abbr</abbr> from 8fold Abbreviations.</p>
```

If the [extension accepts a configuration](https://commonmark.thephpleague.com/2.0/extensions/disallowed-raw-html/), you can pass it into the method and the primary configuration will be modified accordingly.

```php
use Eightfold\Markdown\Markdown;

print Markdown::create($markdown)
  ->disallowedRawHtml([
    'disallowed_tags' => ['div']
  ]);
```

Not passing in a configuration results in using the default established by the CommonMark library.

## Details

This is actually our third foray into wrapping CommonMark.

CommonMark has been a staple in 8fold web development since inception. As we've progressed and continued to slowly evolve our own XML and HTML generating packages
and used those solutions in an array of websites, CommonMark has been featured front and center, as it were.

Given how much CommonMark is used in our various projects and our desire to be loosely coupled with any solutions we don't write ourselves, I think we've come to a solution that accomplishes both those missions.

Minimal code to start, configure, and render HTML. A consistent API to reduce impact as CommonMark continues to evolve.

## Other

- [Code of Conduct](https://github.com/8fold/commonmark-fluent-markdown/blob/main/.github/CODE_OF_CONDUCT.md)
- [Contributing](https://github.com/8fold/commonmark-fluent-markdown/blob/main/.github/CONTRIBUTING.md)
- [Governance](https://github.com/8fold/commonmark-fluent-markdown/blob/main/.github/GOVERNANCE.md)
- [Versioning](https://github.com/8fold/commonmark-fluent-markdown/blob/main/.github/VERSIONING.md)
- [Security](https://github.com/8fold/commonmark-fluent-markdown/blob/main/.github/SECURITY.md)
- [Coding Standards and Style](https://github.com/8fold/commonmark-fluent-markdown/blob/main/.github/coding-standards-and-styles.md)

