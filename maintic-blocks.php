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
 */
add_action(
	'init',
	function () {
		$blocks_dir = __DIR__ . '/build/blocks';
		if ( ! is_dir( $blocks_dir ) ) {
			return;
		}
		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $blocks_dir, FilesystemIterator::SKIP_DOTS )
		);
		foreach ( $iterator as $file ) {
			if ( 'block.json' !== $file->getFilename() ) {
				continue;
			}
			register_block_type( $file->getPath() );
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

// Load feature modules.
require_once __DIR__ . '/includes/faq.php';
