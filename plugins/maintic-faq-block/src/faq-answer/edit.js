import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

const ALLOWED_BLOCKS = [
	'core/paragraph',
	'core/heading',
	'core/list',
	'core/image',
	'core/quote',
];

const TEMPLATE = [ [ 'core/paragraph', { placeholder: '回答を入力…' } ] ];

export default function Edit() {
	const blockProps = useBlockProps( {
		className: 'wp-block-maintic-faq-answer',
	} );

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		allowedBlocks: ALLOWED_BLOCKS,
		template: TEMPLATE,
	} );

	return <div { ...innerBlocksProps } />;
}
