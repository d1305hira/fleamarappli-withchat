# プロジェクト名：fleamarappli-withchat

# 機能

以前に作成したfreemer-appliに機能追加したものです。
　主な追加機能
　・取引中商品の表示機能
　・購入者とのチャット機能
　・購入完了後、出品者へのメール通知機能

# 環境構築

**Docker ビルド**

1. `git clone https://github.com/d1305hira/fleamarappli-withchat.git`
2. `cd fleamarappli-withchat`

**Laravel 環境構築**

3. `cd src`
4. `composer install`
5. `cp .env.example .env`
6. `.env に以下を設定`

```text
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_FROM_ADDRESS="noreply@example.com"

STRIPE_KEY=pk_test_xxxxxxxxxxxxxxxxxxxxx
STRIPE_SECRET=sk_test_xxxxxxxxxxxxxxxxxxxxx

```

**Dockerの起動**

7. `cd ../`
8. `docker-compose up -d --build`

**Laravelの初期設定**

9. アプリケーションキーの作成

docker compose exec php bash

```bash
php artisan key:generate
```

10. マイグレーションの実行

```bash
php artisan migrate
```

11. シーディングの実行

```bash
php artisan db:seed
```

## 使用技術（実行環境）

- PHP8.1.34
- Laravel8.83.27
- MySQL8.0.26

## ER 図

![alt](er_freemarappli.png)

## URL

- 開発環境：http://localhost/
- phpMyAdmin:：http://localhost:8080/
  test push
