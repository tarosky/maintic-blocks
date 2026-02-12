# CLAUDE.md

このファイルは Claude Code（生成 AI）向けの開発ガイドラインです。

> **Note**: 人間の開発者向けの情報は [README.md](./README.md) を参照してください。

## プロジェクト概要

Maintic ブロックライブラリ。単一の WordPress プラグインとして複数のブロックを提供します。

## 重要な構造

```
maintic-blocks/
├── maintic-blocks.php       # メインプラグインファイル
├── includes/
│   └── blocks/              # ブロック固有PHP（オートロード）
│       └── {slug}.php       # トップレベルブロック用（例: faq.php）
├── src/
│   ├── blocks/              # ブロックソース
│   │   └── {name}/
│   │       ├── block.json   # ブロックメタデータ（apiVersion: 3）
│   │       ├── index.js     # registerBlockType
│   │       ├── edit.js      # エディタコンポーネント
│   │       ├── save.js      # 静的ブロックのみ
│   │       ├── render.php   # 動的ブロックのみ
│   │       ├── style.scss   # フロントエンド
│   │       └── editor.scss  # エディタ専用
│   ├── js/                  # ブロック外JS（grab-deps）
│   └── scss/                # ブロック外SCSS（sass）
└── build/                   # 生成物（コミット不要）
```

## ブロック作成時の注意

1. **命名規則**
   - ディレクトリ: `src/blocks/{name}/`
   - ブロック名: `maintic/{name}`
   - テキストドメイン: `maintic-blocks`（統一）

2. **カテゴリ**
   - すべてのブロックは `maintic` カテゴリを使用
   - `maintic-blocks.php` で登録済み

3. **静的 vs 動的**
   - 静的: `save.js` でフロントエンド HTML を出力
   - 動的: `render.php` でサーバーサイドレンダリング、`save` は `() => null`

4. **block.json の render**
   - 動的ブロックのみ `"render": "file:./render.php"` を追加

5. **ブロック登録**
   - `build/blocks/` を再帰スキャンして自動登録
   - 新しいブロックは `src/blocks/{name}/` に配置するだけで認識される
   - フィルター `maintic_blocks_disabled_blocks` で特定ブロックの登録をスキップ可能
   - 親ブロックが無効化されると子ブロックも自動で無効化される

6. **ブロック固有PHPのオートロード**
   - トップレベルブロック（`parent` なし）の登録時に `includes/blocks/{slug}.php` を自動読み込み
   - 子ブロック用のPHPは親ブロックのファイルにまとめる
   - ブロックが無効化されていればPHPも読み込まれない

## Skill

- `create-block`: ブロックの新規作成（`@wordpress/create-block` でスキャフォールド）
  - `/create-block` で明示的に呼び出し可能
  - 「ブロックを作って」等の依頼で Claude が自動的に使用

## ビルドシステム

- `npm run build` で全アセットをビルド
  - `build:blocks`: `wp-scripts build` でブロックをビルド
  - `build:js`: `grab-deps` でブロック外JSをバンドル
  - `build:css`: `sass` + `postcss` でブロック外SCSSをコンパイル
  - `dump`: `grab-deps dump` で `wp-dependencies.json` を生成
- `npm run watch` で全体を監視
- `npm run watch:blocks` でブロックのみ監視

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

## 注意事項

- 新機能については、コードを書く前に実装パターン（静的 vs 動的）を必ず確認すること。
- タスクの基本的なアプローチやアーキテクチャを変更する前には、常に明示的な承認を求めること（例：静的実装から動的実装への切り替え）
- ビルド設定の問題で詰まった場合、複雑な解決策を試みる前に、ユーザーに簡素な代替案を尋ねること。
