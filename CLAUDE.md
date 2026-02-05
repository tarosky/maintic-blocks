# CLAUDE.md

このファイルは Claude Code（生成 AI）向けの開発ガイドラインです。

> **Note**: 人間の開発者向けの情報は [README.md](./README.md) を参照してください。

## プロジェクト概要

WordPress ブロックライブラリの monorepo。各ブロックは `plugins/` 配下に独立したプラグインとして配置されます。

## 重要な構造

```
plugins/maintic-{name}-block/
├── src/
│   ├── block.json      # ブロックメタデータ（apiVersion: 3）
│   ├── index.js        # registerBlockType
│   ├── edit.js         # エディタコンポーネント
│   ├── save.js         # 静的ブロックのみ
│   ├── render.php      # 動的ブロックのみ
│   ├── style.scss      # フロントエンド
│   └── editor.scss     # エディタ専用
├── build/              # 生成物（コミット不要）
└── maintic-{name}-block.php
```

## ブロック作成時の注意

1. **命名規則**
   - ディレクトリ: `maintic-{name}-block`
   - ブロック名: `maintic/{name}`
   - 関数名: `maintic_{name_underscore}_block_*`

2. **カテゴリ**
   - すべてのブロックは `maintic` カテゴリを使用
   - `mu-plugins/maintic-block-category.php` で登録済み

3. **静的 vs 動的**
   - 静的: `save.js` でフロントエンド HTML を出力
   - 動的: `render.php` でサーバーサイドレンダリング、`save` は `() => null`

4. **block.json の render**
   - 動的ブロックのみ `"render": "file:./render.php"` を追加

## Skill

`/create-block` でブロックを対話的に作成できます。詳細は `.claude/commands/create-block.md` を参照。

## ビルドシステム

- ルートの `npm run build` で全プラグインを一括ビルド
- 各プラグインで `wp-scripts build` を実行
- ビルド対象は `plugins/*/src/block.json` が存在するディレクトリ

## lint

- JS: `@wordpress/eslint-plugin/recommended-with-formatting`
- CSS: `@wordpress/stylelint-config/scss`
- PHP: WPCS（WordPress Coding Standards）

## コミット

- `git cc-commit "メッセージ"` を使用（Co-authored-by 自動追加）
- `build/` ディレクトリはコミットしない

## 禁止事項

- グローバルな npm パッケージのインストール（`npm install -g`）
- Docker の破壊的コマンド（`docker system prune` など）
- UI 変更後の無断コミット（動作確認をユーザーに依頼すること）
