<?php
/**
 *  Plugin Name: Test plugin for Maintic Blocks local-dev
 *  Description: Maintic ブロックライブラリのローカル開発用
 *  Author: Tarosky INC.
 *  Version: 0.0.0
 *  Requires at least: 6.6
 *  Requires PHP: 7.4
 *  Author URI: https://tarosky.co.jp/
 *  License: GPL-3.0-or-later
 *  License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *  Domain Path: /languages
 */

add_filter( 'maintic_blocks_disabled_blocks', function ( $disabled ) {
//	$disabled[] = 'maintic/faq';
	return $disabled;
} );
