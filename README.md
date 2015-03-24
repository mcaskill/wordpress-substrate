# Substrate

A WordPress plugin which contains a collection of modules and utilities to improve the front-end and administration area.

Inspired by [Soil](https://github.com/roots/soil). Best used together.

## Installation

If you're using Composer to manage WordPress, add Substrate to your project's dependencies. Run:

```sh
composer require mcaskill/wordpress-substrate
```

## Features

### Utility-belts

Provides additional helpers and template tags.

To enable the specific "belts" (see supported collections below), use:

```php
add_theme_support( 'substrate-utilities', [ 'formatting', 'l10n', 'post', 'link', 'media' ] );
```

Various:

* `remove_autosave_script()`: Disable _revision auto-save_ by setting `AUTOSAVE_INTERVAL` to `false`.

Formatting:

* `sanitize_zero_chars()`
* ~~`ascii_filename()`~~
* ~~`lowercase_filename()`~~

* [Sanitize Accented Uploads](https://github.com/devgeniem/wp-sanitize-accented-uploads) for filename cleanup.

Localization:

* `get_locale_language()`
* `get_locale_territory()`

Link tags:

* `get_adjacent_post()`

Post tags:

* `the_title_url()`
* `the_title_url_raw()`

Media:

* `get_image_size()`
* `get_image_sizes()`

### Media

Extend media support in WordPress.

```php
add_theme_support( 'substrate-media', [ 'svg' ] );
```

**SVG**: Adds SVG media support to WordPress.

### Outdated Navigator Notice

Enable Substrate's front-end warning to visitors using outdated browsers:

```php
add_theme_support('substrate-outdated-navigator');
```

Filters:

* `substrate/outdated_navigator/conditional_statement`
* `substrate/outdated_navigator/link`
* `substrate/outdated_navigator/message`,
* `substrate/outdated_navigator/classes`

### Page Template Column

Enable Substrate's custom "Page Template" column for Pages:

```php
add_theme_support( 'substrate-page', [ 'template-column' ] );
```

Filters:

* `substrate/page_template_column/value`
