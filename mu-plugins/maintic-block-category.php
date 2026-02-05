<?php
/**
 * Register Maintic block category.
 *
 * @package maintic-blocks
 */

add_filter(
	'block_categories_all',
	function ( $categories ) {
		return array_merge(
			array(
				array(
					'slug'  => 'maintic',
					'title' => __( 'Maintic', 'maintic-blocks' ),
					'icon'  => null,
				),
			),
			$categories
		);
	}
);
