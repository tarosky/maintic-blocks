# create-block

新しい WordPress ブロックを作成する Skill です。

## 使い方

`/create-block` を実行すると、対話形式でブロックを作成します。

## 手順

1. **ユーザーから以下の情報を収集する（AskUserQuestion を使用）:**

   - **ブロック名（slug）** (必須): 英小文字とハイフンのみ（例: card, testimonial, hero-banner）
   - **ブロックタイトル** (必須): エディタに表示される名前（例: カード, お客様の声）
   - **説明** (任意): ブロックの説明文
   - **ブロックタイプ** (必須): 静的（static）または 動的（dynamic）
   - **Supports** (任意): align, color, typography, spacing など複数選択可
   - **アイコン** (任意): Dashicon 名（例: admin-post, format-quote）デフォルトは "block-default"

2. **以下のファイル構造を作成する:**

   ```
   src/blocks/{name}/
   ├── block.json
   ├── index.js
   ├── edit.js
   ├── save.js          # 静的ブロックのみ
   ├── render.php       # 動的ブロックのみ
   ├── style.scss
   └── editor.scss
   ```

3. **各ファイルのテンプレート:**

### src/blocks/{name}/block.json

```json
{
	"$schema": "https://schemas.wp.org/trunk/block.json",
	"apiVersion": 3,
	"name": "maintic/{name}",
	"version": "1.0.0",
	"title": "{title}",
	"category": "maintic",
	"icon": "{icon}",
	"description": "{description}",
	"attributes": {},
	"supports": {
		// supports の内容
	},
	"textdomain": "maintic-blocks",
	"editorScript": "file:./index.js",
	"editorStyle": "file:./index.css",
	"style": "file:./style-index.css",
	"render": "file:./render.php"  // 動的ブロックのみ
}
```

### src/blocks/{name}/index.js

```js
import { registerBlockType } from '@wordpress/blocks';
import './style.scss';
import Edit from './edit';
import save from './save';  // 静的ブロックのみ
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit: Edit,
	save,  // 静的ブロック: save, 動的ブロック: () => null
} );
```

### src/blocks/{name}/edit.js（静的ブロック）

```js
import { useBlockProps } from '@wordpress/block-editor';
import './editor.scss';

export default function Edit() {
	return (
		<div { ...useBlockProps() }>
			<p>{ '{title}' } – Edit</p>
		</div>
	);
}
```

### src/blocks/{name}/edit.js（動的ブロック）

```js
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import './editor.scss';

export default function Edit() {
	return (
		<>
			<InspectorControls>
				<PanelBody title="設定">
					{/* 設定項目をここに追加 */}
				</PanelBody>
			</InspectorControls>
			<div { ...useBlockProps() }>
				<p>{ '{title}' } – Edit</p>
			</div>
		</>
	);
}
```

### src/blocks/{name}/save.js（静的ブロックのみ）

```js
import { useBlockProps } from '@wordpress/block-editor';

export default function save() {
	return (
		<div { ...useBlockProps.save() }>
			<p>{ '{title}' } – Saved</p>
		</div>
	);
}
```

### src/blocks/{name}/render.php（動的ブロックのみ）

```php
<?php
/**
 * Render callback for {title} block.
 *
 * @package maintic-blocks
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content.
 * @var WP_Block $block      Block instance.
 */

$wrapper_attributes = get_block_wrapper_attributes();
?>
<div <?php echo $wrapper_attributes; ?>>
	<p><?php esc_html_e( '{title}', 'maintic-blocks' ); ?></p>
</div>
```

### src/blocks/{name}/style.scss

```scss
.wp-block-maintic-{name} {
	// フロントエンドスタイル
}
```

### src/blocks/{name}/editor.scss

```scss
.wp-block-maintic-{name} {
	// エディタ専用スタイル
}
```

4. **ブロック固有のPHP機能がある場合:**
   - `includes/{name}.php` にPHPコードを配置
   - `maintic-blocks.php` に `require_once` を追加

5. **作成後の案内:**
   - `npm run build` でビルド
   - `npm run start` で wp-env 起動
   - WordPress 管理画面でブロックが使えることを確認

## Supports の選択肢

- `align`: 配置（left, center, right, wide, full）
- `color`: 色設定（text, background, link）
- `typography`: タイポグラフィ（fontSize, lineHeight）
- `spacing`: 余白（margin, padding）
- `anchor`: アンカーリンク

## 注意事項

- ブロック名は英小文字とハイフンのみ使用可能
- 既存の同名ブロックがある場合は上書き確認
- 動的ブロックの場合は save.js は作成せず、render.php を作成する
- テキストドメインは `maintic-blocks` で統一
