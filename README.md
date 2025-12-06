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
npm install
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

---

## サンプルサイト

### ■ トップページ

https://posse-ap.github.io/sample-ph1-website/

```
【参照ソースコード】
/index.html
/assets/styles/common.css
```

### ■ クイズページ

https://posse-ap.github.io/sample-ph1-website/quiz/

```
【参照ソースコード】
/quiz/index.html
/assets/styles/common.css
/assets/scripts/quiz.js
```

#### JavaScript で問題文をループ出力

https://posse-ap.github.io/sample-ph1-website/quiz2/

```
【参照ソースコード】
/quiz2/index.html
/assets/styles/common.css
/assets/scripts/quiz2.js
```

#### JavaScript で問題をランダムに並び替えて出力

https://posse-ap.github.io/sample-ph1-website/quiz3/

```
【参照ソースコード】
/quiz3/index.html
/assets/styles/common.css
/assets/scripts/quiz3.js
```
