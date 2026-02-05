import { useBlockProps, RichText } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const { question } = attributes;

	const blockProps = useBlockProps.save( {
		className: 'wp-block-maintic-faq-question',
	} );

	// 親（faq-item）の variant に応じて summary または div を出力
	// 静的ブロックでは親の属性にアクセスできないため、
	// faq-item の save.js 側で details/summary 構造を制御する
	// ここでは常に summary タグを出力し、faq-item が div の場合は CSS で調整
	return (
		<summary { ...blockProps }>
			<RichText.Content
				tagName="span"
				className="wp-block-maintic-faq-question__text"
				value={ question }
			/>
		</summary>
	);
}
