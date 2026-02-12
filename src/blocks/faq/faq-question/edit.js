import { useBlockProps, RichText } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

export default function Edit( { attributes, setAttributes } ) {
	const { question } = attributes;

	const blockProps = useBlockProps( {
		className: 'wp-block-maintic-faq-question',
	} );

	return (
		<div { ...blockProps }>
			<RichText
				tagName="span"
				className="wp-block-maintic-faq-question__text"
				value={ question }
				onChange={ ( value ) => setAttributes( { question: value } ) }
				placeholder={ __( '質問を入力…', 'maintic-faq-block' ) }
				allowedFormats={ [ 'core/bold', 'core/italic' ] }
			/>
		</div>
	);
}
