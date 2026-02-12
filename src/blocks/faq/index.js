import { registerBlockType } from '@wordpress/blocks';
import './style.scss';

// 親ブロック
import Edit from './edit';
import save from './save';
import metadata from './block.json';

// 子ブロック
import './faq-item';
import './faq-question';
import './faq-answer';

registerBlockType( metadata.name, {
	edit: Edit,
	save,
} );
