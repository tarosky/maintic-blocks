import { useEffect } from '@wordpress/element';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import { PanelBody, RadioControl } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import './editor.scss';

const TEMPLATE = [ [ 'maintic/faq-item' ] ];

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { variant } = attributes;

	const { updateBlockAttributes } = useDispatch( blockEditorStore );

	// 子ブロック（faq-item）を取得
	const childBlocks = useSelect(
		( select ) => {
			return select( blockEditorStore ).getBlocks( clientId );
		},
		[ clientId ]
	);

	// variant が変わったら子ブロックの variant も更新
	useEffect( () => {
		childBlocks.forEach( ( block ) => {
			if ( block.name === 'maintic/faq-item' ) {
				updateBlockAttributes( block.clientId, { variant } );
			}
		} );
	}, [ variant, childBlocks, updateBlockAttributes ] );

	const blockProps = useBlockProps( {
		className: `is-variant-${ variant }`,
	} );

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		allowedBlocks: [ 'maintic/faq-item' ],
		template: TEMPLATE,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( '表示形式', 'maintic-faq-block' ) }>
					<RadioControl
						label={ __( 'バリエーション', 'maintic-faq-block' ) }
						selected={ variant }
						options={ [
							{
								label: __(
									'アコーディオン（details/summary）',
									'maintic-faq-block'
								),
								value: 'accordion',
							},
							{
								label: __( '常時展開', 'maintic-faq-block' ),
								value: 'expanded',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { variant: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...innerBlocksProps } />
		</>
	);
}
