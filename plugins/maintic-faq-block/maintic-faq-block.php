<?php
/**
 * Plugin Name: Maintic FAQ Block
 * Description: よくある質問（FAQ）を表示するブロック。JSON-LD構造化データ出力対応。
 * Version: 1.0.0
 * Author: Tarosky INC
 * License: GPL-3.0-or-later
 * Text Domain: maintic-faq-block
 *
 * @package maintic-faq-block
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register FAQ blocks.
 *
 * @return void
 */
function maintic_faq_block_init() {
	// 親ブロック
	register_block_type( __DIR__ . '/build' );

	// 子ブロック
	register_block_type( __DIR__ . '/build/faq-item' );
	register_block_type( __DIR__ . '/build/faq-question' );
	register_block_type( __DIR__ . '/build/faq-answer' );
}
add_action( 'init', 'maintic_faq_block_init' );

/**
 * Output FAQ JSON-LD structured data.
 *
 * @return void
 */
function maintic_faq_block_output_json_ld() {
	if ( ! is_singular() ) {
		return;
	}

	$post = get_queried_object();
	if ( ! $post instanceof WP_Post ) {
		return;
	}

	// FAQブロックからQ&Aデータを抽出
	$faq_data = maintic_faq_block_extract_faq_data( $post );

	if ( empty( $faq_data ) ) {
		return;
	}

	$json_ld = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => array(),
	);

	foreach ( $faq_data as $item ) {
		$json_ld['mainEntity'][] = array(
			'@type'          => 'Question',
			'name'           => $item['question'],
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => $item['answer'],
			),
		);
	}

	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		wp_json_encode( $json_ld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES )
	);
}
add_action( 'wp_head', 'maintic_faq_block_output_json_ld' );

/**
 * Extract FAQ data from post content (static blocks).
 *
 * @param null|int|WP_Post $post Post object.
 * @return array Array of FAQ items with 'question' and 'answer' keys.
 */
function maintic_faq_block_extract_faq_data( $post ) {
	$post = get_post( $post );
	if ( ! $post || ! has_blocks( $post ) ) {
		return [];
	}

	$blocks   = parse_blocks( $post->post_content );
	$faq_data = [];

	maintic_faq_block_find_faq_blocks( $blocks, $faq_data );

	return $faq_data;
}

/**
 * Recursively find FAQ blocks and extract Q&A data.
 *
 * @param array $blocks    Parsed blocks.
 * @param array $faq_data  Reference to FAQ data array.
 * @return void
 */
function maintic_faq_block_find_faq_blocks( $blocks, &$faq_data ) {
	foreach ( $blocks as $block ) {
		if ( 'maintic/faq' === $block['blockName'] && ! empty( $block['innerBlocks'] ) ) {
			// faq-item を探す
			foreach ( $block['innerBlocks'] as $item_block ) {
				if ( 'maintic/faq-item' !== $item_block['blockName'] ) {
					continue;
				}

				$question = '';
				$answer   = '';

				foreach ( $item_block['innerBlocks'] ?? [] as $child_block ) {
					if ( 'maintic/faq-question' === $child_block['blockName'] ) {
						// 静的ブロックの場合、innerHTML から取得
						$question = wp_strip_all_tags( $child_block['innerHTML'] ?? '' );
						$question = trim( $question );
					} elseif ( 'maintic/faq-answer' === $child_block['blockName'] ) {
						// 回答の InnerBlocks をレンダリング
						$answer_html = '';
						foreach ( $child_block['innerBlocks'] ?? [] as $answer_inner ) {
							$answer_html .= render_block( $answer_inner );
						}
						$answer = wp_strip_all_tags( $answer_html );
						$answer = trim( preg_replace( '/\s+/', ' ', $answer ) );
					}
				}

				if ( ! empty( $question ) && ! empty( $answer ) ) {
					$faq_data[] = [
						'question' => $question,
						'answer'   => $answer,
					];
				}
			}
		}

		// 再帰的にネストされたブロックを検索
		if ( ! empty( $block['innerBlocks'] ) ) {
			maintic_faq_block_find_faq_blocks( $block['innerBlocks'], $faq_data );
		}
	}
}
