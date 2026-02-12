# Maintic Blocks

Contributors: tarosky, Takahashi_Fumiki  
Tags: blocks, gutenberg, faq  
Tested up to: 6.9  
Stable Tag: nightly  
Requires at least: 6.6  
Requires PHP: 7.4  
License: GPL 3.0 or later  
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Maintic block library for WordPress.

## Description

A single WordPress plugin that provides multiple Gutenberg blocks for the Maintic ecosystem.

### Included Blocks

- **FAQ** — Accordion-style FAQ block with JSON-LD structured data output.

## Installation

1. Download the latest release zip from [GitHub Releases](https://github.com/tarosky/maintic-blocks/releases).
2. Upload the zip from **Plugins > Add New > Upload Plugin** in the WordPress admin.
3. Activate the plugin.

## Frequently Asked Questions

### How can I disable specific blocks?

Use the `maintic_blocks_disabled_blocks` filter:

```php
add_filter( 'maintic_blocks_disabled_blocks', function ( $disabled ) {
    $disabled[] = 'maintic/faq';
    return $disabled;
} );
```

Disabling a parent block automatically disables all its child blocks.

### How can I contribute?

Contributions are welcome at our [GitHub repository](https://github.com/tarosky/maintic-blocks). Please open an [issue](https://github.com/tarosky/maintic-blocks/issues) or [pull request](https://github.com/tarosky/maintic-blocks/pulls).

## Changelog

### 1.0.0

- Initial release with FAQ block.
