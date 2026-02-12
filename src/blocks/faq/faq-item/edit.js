import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

const TEMPLATE = [
	[ 'maintic/faq-question' ],
	[ 'maintic/faq-answer' ],
];

export default function Edit( { attributes } ) {
	const { variant } = attributes;

	const blockProps = useBlockProps( {
		className: `wp-block-maintic-faq-item is-variant-${ variant }`,
	} );

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		allowedBlocks: [ 'maintic/faq-question', 'maintic/faq-answer' ],
		template: TEMPLATE,
		templateLock: 'all',
	} );

	// エディタでは常に div で表示（details/summary はエディタで扱いにくい）
	return <div { ...innerBlocksProps } />;
}
