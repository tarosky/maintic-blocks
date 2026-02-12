import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const { variant } = attributes;

	const blockProps = useBlockProps.save();

	if ( variant === 'accordion' ) {
		return (
			<details { ...blockProps }>
				<InnerBlocks.Content />
			</details>
		);
	}

	return (
		<div { ...blockProps }>
			<InnerBlocks.Content />
		</div>
	);
}
