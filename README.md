# ph1 POSSE 課題 サンプル

## 環境構築手順

### 必要な環境

- Docker Desktop
- Node.js

### セットアップ

1. リポジトリをクローン

```bash
git clone git@github.com:taiki-yokoyama/2025winterHackathonB.git
cd 2025winterHackathonB
```

2. Node.js の依存関係をインストール

```bash
npm install --legacy-peer-deps
```

3. Docker コンテナを起動

```bash
docker compose up -d
```

### アクセス先

- Web サイト: http://localhost:8080
- PHPMyAdmin: http://localhost:8081
- MailHog: http://localhost:8025

### コンテナの停止

```bash
docker compose down
```

### 新規登録がうまくいかない（テーブルが作成されない場合）
【macの場合】
```bash
rm -rf docker/mysql/db
docker compose down
docker compose up -d
```
【windowsの場合】
```bash
docker-compose down -v
Remove-Item -Recurse -Force docker/mysql/db
docker-compose up -d --build
```
---
```
