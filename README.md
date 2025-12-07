# ③ で PON 🎰

チーム開発の振り返りをゲーム感覚で楽しめる PDCA サイクル管理アプリケーション

![③でPON](assets/img/gacha.png)

## 📖 概要

**③ で PON**は、学生コミュニティやチーム開発における振り返り（PDCA）を、ゲーム感覚で楽しく継続できる Web アプリケーションです。

### 主な特徴

- 🎯 **Plan（計画）**: チームの提案を踏まえて次の目標を立てる
- 🚀 **Do（実行）**: 立てた計画を実際に実行する
- 👀 **Check（評価）**: メンバーをコード面・人格面で 1〜4 の数値で評価
- 💡 **Action（改善）**: 受け取った評価と提案を見て次に活かす
- 🎴 **Gacha（ガチャ）**: コインを使ってメンバーカードを集める
- 👥 **チーム機能**: 横もく・縦もくを切り替えて使える

---

## 🚀 環境構築手順

### 必要な環境

以下のソフトウェアがインストールされていることを確認してください：

- **Docker Desktop** (最新版推奨)
- **Node.js** (v16 以上推奨)
- **Git**

### 初回セットアップ

#### 1. リポジトリをクローン

```bash
git clone git@github.com:taiki-yokoyama/2025winterHackathonB.git
cd 2025winterHackathonB
```

#### 2. Node.js の依存関係をインストール

```bash
npm install --legacy-peer-deps
```

> **Note**: `--legacy-peer-deps` オプションは依存関係の競合を回避するために必要です。

#### 3. Docker コンテナを起動

```bash
docker compose up -d
```

初回起動時は、データベースの初期化に数秒かかる場合があります。

#### 4. アクセス確認

ブラウザで以下の URL にアクセスしてください：

- **Web サイト**: http://localhost:8080
- **PHPMyAdmin**: http://localhost:8081 (データベース管理)
- **MailHog**: http://localhost:8025 (メール確認用)

---

## 🛠️ トラブルシューティング

### 新規登録がうまくいかない（テーブルが作成されない場合）

データベースの初期化に失敗している可能性があります。以下の手順でデータベースをリセットしてください。

#### macOS / Linux の場合

```bash
rm -rf docker/mysql/db
docker compose down
docker compose up -d
```

#### Windows の場合

PowerShell で実行：

```powershell
docker-compose down -v
Remove-Item -Recurse -Force docker/mysql/db
docker-compose up -d --build
```

コマンドプロンプトで実行：

```cmd
docker-compose down -v
rmdir /s /q docker\mysql\db
docker-compose up -d --build
```

### ポートが既に使用されている場合

他のアプリケーションがポート 8080、8081、8025 を使用している場合、Docker コンテナが起動できません。

**解決方法**:

1. 使用中のアプリケーションを停止する
2. または、`docker-compose.yml`のポート番号を変更する

### Docker Desktop が起動しない場合

1. Docker Desktop を再起動
2. システムを再起動
3. Docker Desktop を再インストール

---

## 💻 使い方

### 1. 新規登録・ログイン

1. トップページ（http://localhost:8080）にアクセス
2. 「新規登録」ボタンをクリック
3. メールアドレスとパスワードを入力して登録
4. 自動的にマイページに遷移します

### 2. プロフィール設定

初回ログイン時は、プロフィール登録画面が表示されます：

- **名前**: あなたの名前
- **期生**: 5 期、5.5 期、6 期、6.5 期から選択
- **アイコン**: メンバーカードの画像から選択
- **横もく**: 横もく 5A〜6H
- **縦もく**: 縦もく A〜I

### 3. PDCA サイクルを回す

#### Plan（計画）

- 「Plan」ページで次の目標を立てる
- 自分の計画とチームメンバーの計画を確認できる
- 計画の編集や詳細表示も可能

#### Check（評価）

- 「Check」ページでチームメンバーを評価
- **チームタブ**: チーム全体への意見を共有
- **個人タブ**: メンバーを個別に評価
  - コード面: 1〜4 で評価
  - 人格面: 1〜4 で評価
  - 次の Plan の提案: 改善案を送る

#### Action（改善）

- 「Action」ページで自分への評価と提案を確認
- チームメンバーからの「次の Plan の提案」を見る
- 提案を踏まえて次の計画を立てる

#### Gacha（ガチャ）

- 「Gacha」ページでコインを使ってガチャを引く
- メンバーカードをランダムで獲得
- 同じカードを引くと所持枚数が増える
- カードブックでコレクション率を確認

### 4. チーム切り替え

ヘッダーの「切り替え」ボタンで横もく・縦もくを切り替えられます：

- **横もく**: 同じ期生のチーム
- **縦もく**: 異なる期生が混ざったチーム

---

## 🎮 このサイトの魅力

### 1. 数値評価で簡単

言葉にしにくい評価も、1〜4 の数値で簡単に表現できます。コード面と人格面を分けて評価することで、バランスの取れたフィードバックが可能です。

### 2. ゲーム感覚で楽しい

評価するとコインがもらえ、ガチャでメンバーカードを集められます。振り返りが楽しいゲームに変わります。

### 3. チームの状況が見える

メンバーの計画や評価を一覧で確認できるため、チーム全体の状況を把握しやすくなります。

### 4. 継続しやすい仕組み

- ポップでカラフルなデザイン
- 直感的な操作
- ゲーム要素による動機付け

これらにより、形だけの振り返りにならず、自然と継続できます。

### 5. 横もく・縦もく対応

学生コミュニティ特有の「横もく（同期チーム）」「縦もく（異学年チーム）」の両方に対応。ボタン一つで切り替えられます。

### 6. 次の Plan 提案機能

評価と一緒に「次はこうしてみよう」という具体的な提案を送れるため、建設的なフィードバックループが生まれます。

---

## 🗂️ プロジェクト構成

```
.
├── docker/                 # Docker設定
│   ├── mysql/             # MySQLコンテナ設定
│   ├── nginx/             # Nginxコンテナ設定
│   └── php/               # PHPコンテナ設定
├── src/                   # アプリケーションソースコード
│   ├── action/            # Action（改善）機能
│   ├── assets/            # 画像などの静的ファイル
│   ├── auth/              # 認証機能（ログイン・登録）
│   ├── cardbook/          # カードブック機能
│   ├── check/             # Check（評価）機能
│   ├── components/        # 共通コンポーネント（ヘッダー・フッター）
│   ├── gacha/             # Gacha（ガチャ）機能
│   ├── mypage/            # マイページ機能
│   ├── plan/              # Plan（計画）機能
│   └── index.php          # トップページ
├── docker-compose.yml     # Docker Compose設定
├── package.json           # Node.js依存関係
└── README.md              # このファイル
```

---

## 🔧 開発コマンド

### コンテナの起動

```bash
docker compose up -d
```

### コンテナの停止

```bash
docker compose down
```

### コンテナのログ確認

```bash
docker compose logs -f
```

### データベースのリセット

```bash
# macOS / Linux
rm -rf docker/mysql/db
docker compose down
docker compose up -d

# Windows (PowerShell)
docker-compose down -v
Remove-Item -Recurse -Force docker/mysql/db
docker-compose up -d --build
```

---

## 📝 技術スタック

- **フロントエンド**: HTML, CSS (Tailwind CSS), JavaScript
- **バックエンド**: PHP 8.2
- **データベース**: MySQL 8.0
- **Web サーバー**: Nginx
- **コンテナ**: Docker, Docker Compose
- **ビルドツール**: Webpack, npm

---

**③ で PON** - チーム開発をもっと楽しく！ 🎉

```

```
