---
name: create-block
description: >
  Maintic ブロックライブラリに新しい WordPress ブロックを追加する。
  ユーザーがブロックの新規作成・スキャフォールディングを依頼した時に使用する。
---

# ブロック作成

## 手順

### 1. ユーザーから情報を収集（AskUserQuestion を使用）

- **ブロック名（slug）** (必須): 英小文字とハイフンのみ（例: card, hero-banner）
- **ブロックタイトル** (必須): エディタに表示される名前（例: カード, お客様の声）
- **説明** (任意): ブロックの説明文
- **ブロックタイプ** (必須): 静的（static）または 動的（dynamic）

### 2. スキャフォールディング

`@wordpress/create-block` で雛形を生成する:

```bash
npx @wordpress/create-block {slug} \
  --no-plugin \
  --namespace maintic \
  --category maintic \
  --textdomain maintic-blocks \
  --target-dir src/blocks/{slug} \
  --title "{title}" \
  --short-description "{description}" \
  --variant {static|dynamic}
```

### 3. 生成ファイルの調整

`block.json` を確認し、以下を保証する:

- `apiVersion` が `3` であること
- `editorStyle: "file:./index.css"` があること
- `style: "file:./style-index.css"` があること
- ユーザーの要望に応じて `supports` を設定（align, color, typography, spacing など）
- `icon` をユーザーの要望に応じて設定（Dashicon 名、デフォルト: `block-default`）

### 4. ビルド確認

```bash
npm run build
```

### 5. PHP オートロードの案内

ブロック固有の PHP 処理が必要な場合:
- トップレベルブロック → `includes/blocks/{slug}.php` を作成
- 子ブロック → 親ブロックの PHP にまとめる

## 注意事項

- 既存の同名ブロックがある場合は上書き確認
- テキストドメインは `maintic-blocks` で統一
- 動的ブロックの `save` は `() => null` を返す
- 静的ブロックには `render.php` は不要
