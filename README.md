# Maintic Blocks

Maintic のための WordPress ブロックライブラリ。各ブロックは独立したプラグインとして `plugins/` に配置される monorepo 構成です。

## 必要環境

- Node.js 22 以上（Volta で管理）
- Docker Desktop（wp-env 用）
- Composer（PHP lint 用）

## セットアップ

```bash
# 依存関係のインストール
npm install
composer install

# 開発環境の起動
npm run start
```

WordPress は http://localhost:8888 で起動します（管理者: admin / password）。

## ディレクトリ構造

```
maintic-blocks/
├── .claude/commands/     # Claude Code Skills
├── .github/workflows/    # CI/CD
├── mu-plugins/           # Must-use プラグイン（共通機能）
├── plugins/              # 各ブロックプラグイン
│   └── maintic-{name}-block/
│       ├── src/          # ソースコード
│       ├── build/        # ビルド成果物（生成）
│       └── maintic-{name}-block.php
├── scripts/              # ビルドスクリプト
├── .wp-env.json          # wp-env 設定
└── package.json
```

## 開発コマンド

| コマンド | 説明 |
|---------|------|
| `npm run start` | wp-env 起動 |
| `npm run stop` | wp-env 停止 |
| `npm run build` | 全プラグインをビルド |
| `npm run watch` | 全プラグインを watch モードでビルド |
| `npm run lint` | JS/CSS lint |
| `npm run lint:js` | JavaScript lint |
| `npm run lint:css` | SCSS lint |
| `npm run format:js` | JavaScript 自動修正 |
| `npm run format:css` | SCSS 自動修正 |
| `composer lint` | PHP lint（PHPCS） |
| `composer fix` | PHP 自動修正（PHPCBF） |

## 新しいブロックの作成

### Claude Code を使う場合

```
/create-block
```

対話形式でブロック名、タイプ（静的/動的）、サポート機能などを指定できます。

### 手動で作成する場合

1. `plugins/maintic-{name}-block/` ディレクトリを作成
2. 以下のファイルを作成:
   - `maintic-{name}-block.php` - メインプラグインファイル
   - `src/block.json` - ブロックメタデータ
   - `src/index.js` - ブロック登録
   - `src/edit.js` - エディタコンポーネント
   - `src/save.js` - 保存コンポーネント（静的ブロック）
   - `src/render.php` - サーバーサイドレンダリング（動的ブロック）
   - `src/style.scss` - フロントエンドスタイル
   - `src/editor.scss` - エディタ専用スタイル

### ブロックの命名規則

- プラグインディレクトリ: `maintic-{機能名}-block`
- ブロック名（block.json）: `maintic/{機能名}`
- カテゴリ: `maintic`（mu-plugins で自動登録済み）

## ビルド

```bash
# 全プラグインをビルド
npm run build

# 開発時は watch モード
npm run watch
```

ビルド成果物は各プラグインの `build/` ディレクトリに出力されます。

## CI/CD

GitHub Actions で以下を自動実行:

- **lint.yml**: JS/CSS/PHP の lint チェック
- **build.yml**: 全プラグインのビルド検証

PR 作成時および main ブランチへの push 時に実行されます。

## ライセンス

GPL-3.0-or-later
