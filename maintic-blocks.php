<?php
/**
 * Plugin Name: Maintic Blocks
 * Plugin URI: https://github.com/tarosky/maintic-blocks
 * Description: Maintic ブロックライブラリ
 * Author: Tarosky INC.
 * Version: nightly
 * Requires at least: 6.6
 * Requires PHP: 7.4
 * Author URI: https://tarosky.co.jp/
 * License: GPL-3.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: maintic-blocks
 * Domain Path: /languages
 *
 * @package maintic-blocks
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register all blocks from build/blocks/.
 *
 * Supports:
 * - Filter `maintic_blocks_disabled_blocks` to skip specific blocks.
 * - Cascading disable: child blocks whose parent is disabled are also removed.
 * - Auto-loading: includes/blocks/{slug}.php is loaded for top-level blocks.
 */
add_action(
	'init',
	function () {
		$blocks_dir = __DIR__ . '/build/blocks';
		if ( ! is_dir( $blocks_dir ) ) {
			return;
		}

		// 1. Discover all block.json files.
		$blocks   = array();
		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $blocks_dir, FilesystemIterator::SKIP_DOTS )
		);
		foreach ( $iterator as $file ) {
			if ( 'block.json' !== $file->getFilename() ) {
				continue;
			}
			$metadata = wp_json_file_decode( $file->getPathname(), array( 'associative' => true ) );
			if ( ! $metadata || empty( $metadata['name'] ) ) {
				continue;
			}
			$blocks[ $metadata['name'] ] = array(
				'path'     => $file->getPath(),
				'metadata' => $metadata,
			);
		}

		// 2. Apply disabled blocks filter.
		$disabled = apply_filters( 'maintic_blocks_disabled_blocks', array() );
		foreach ( $disabled as $name ) {
			unset( $blocks[ $name ] );
		}

		// 3. Remove orphan blocks (parent not in $blocks).
		do {
			$removed = false;
			foreach ( $blocks as $name => $block ) {
				if ( empty( $block['metadata']['parent'] ) ) {
					continue;
				}
				foreach ( $block['metadata']['parent'] as $parent ) {
					if ( isset( $blocks[ $parent ] ) ) {
						continue 2; // At least one parent exists.
					}
				}
				// No valid parent found.
				unset( $blocks[ $name ] );
				$removed = true;
			}
		} while ( $removed );

		// 4. Register surviving blocks.
		foreach ( $blocks as $block ) {
			register_block_type( $block['path'] );
		}

		// 5. Auto-load PHP for top-level blocks.
		foreach ( $blocks as $name => $block ) {
			if ( ! empty( $block['metadata']['parent'] ) ) {
				continue;
			}
			$slug     = str_replace( 'maintic/', '', $name );
			$php_file = __DIR__ . '/includes/blocks/' . $slug . '.php';
			if ( file_exists( $php_file ) ) {
				require_once $php_file;
			}
		}
	}
);

/**
 * Register "Maintic" block category.
 */
add_filter(
	'block_categories_all',
	function ( $categories ) {
		array_unshift(
			$categories,
			array(
				'slug'  => 'maintic',
				'title' => __( 'Maintic', 'maintic-blocks' ),
				'icon'  => null,
			)
		);
		return $categories;
	}
);

/**
 * Register non-block assets from wp-dependencies.json.
 */
add_action(
	'init',
	function () {
		$json = __DIR__ . '/wp-dependencies.json';
		if ( ! file_exists( $json ) ) {
			return;
		}
		$deps = json_decode( file_get_contents( $json ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		if ( ! $deps ) {
			return;
		}
		foreach ( $deps as $dep ) {
			if ( empty( $dep['path'] ) ) {
				continue;
			}
			$url = plugin_dir_url( __FILE__ ) . $dep['path'];
			switch ( $dep['ext'] ) {
				case 'js':
					$footer = array(
						'in_footer' => $dep['footer'],
						'strategy'  => $dep['strategy'],
					);
					wp_register_script( $dep['handle'], $url, $dep['deps'], $dep['hash'], $footer );
					break;
				case 'css':
					wp_register_style( $dep['handle'], $url, $dep['deps'], $dep['hash'], $dep['media'] );
					break;
			}
		}
	}
);
