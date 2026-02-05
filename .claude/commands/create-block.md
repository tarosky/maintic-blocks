# create-block

新しい WordPress ブロックプラグインを作成する Skill です。

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
   plugins/maintic-{name}-block/
   ├── src/
   │   ├── block.json
   │   ├── index.js
   │   ├── edit.js
   │   ├── save.js          # 静的ブロックのみ
   │   ├── render.php       # 動的ブロックのみ
   │   ├── style.scss
   │   └── editor.scss
   └── maintic-{name}-block.php
   ```

3. **各ファイルのテンプレート:**

### maintic-{name}-block.php

```php
<?php
/**
 * Plugin Name: Maintic {Title} Block
 * Description: {description}
 * Version: 1.0.0
 * Author: Tarosky INC
 * License: GPL-3.0-or-later
 * Text Domain: maintic-{name}-block
 *
 * @package maintic-{name}-block
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function maintic_{name_underscore}_block_init() {
	register_block_type( __DIR__ . '/build' );
}
add_action( 'init', 'maintic_{name_underscore}_block_init' );
```

### src/block.json

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
	"textdomain": "maintic-{name}-block",
	"editorScript": "file:./index.js",
	"editorStyle": "file:./index.css",
	"style": "file:./style-index.css",
	"render": "file:./render.php"  // 動的ブロックのみ
}
```

### src/index.js

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

### src/edit.js（静的ブロック）

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

### src/edit.js（動的ブロック）

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

### src/save.js（静的ブロックのみ）

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

### src/render.php（動的ブロックのみ）

```php
<?php
/**
 * Render callback for {title} block.
 *
 * @package maintic-{name}-block
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content.
 * @var WP_Block $block      Block instance.
 */

$wrapper_attributes = get_block_wrapper_attributes();
?>
<div <?php echo $wrapper_attributes; ?>>
	<p><?php esc_html_e( '{title}', 'maintic-{name}-block' ); ?></p>
</div>
```

### src/style.scss

```scss
.wp-block-maintic-{name} {
	// フロントエンドスタイル
}
```

### src/editor.scss

```scss
.wp-block-maintic-{name} {
	// エディタ専用スタイル
}
```

4. **作成後の案内:**
   - `npm install` で依存関係をインストール（未実行の場合）
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
- 既存の同名プラグインがある場合は上書き確認
- 動的ブロックの場合は save.js は作成せず、render.php を作成する
