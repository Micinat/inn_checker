# INN Checker API

Backend service on Symfony 7.4 LTS for company lookup by INN through DaData with persistence in MySQL.

## Requirements

- PHP 8.2+
- MySQL 8.0+
- Composer

## Setup

```bash
composer install
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS inn_checker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Set real local credentials in `.env.local`:

```dotenv
DATABASE_URL="mysql://root:your-real-password@localhost:3306/inn_checker?serverVersion=8.0&charset=utf8mb4"
DADATA_API_KEY="your-token"
DADATA_TIMEOUT_SECONDS=5
DADATA_MAX_DURATION_SECONDS=10
```

If your local MySQL is reachable only through the default Unix socket, prefer `localhost` over `127.0.0.1`.

## Migrations

```bash
php bin/console doctrine:migrations:migrate --no-interaction
```

## Run

```bash
symfony server:start
```

or

```bash
php -S 127.0.0.1:8000 -t public public/index.php
```

## Endpoint

```bash
curl -i http://127.0.0.1:8000/api/companies/by-inn/7707083893
```

## Response

```json
{
  "inn": "7707083893",
  "name": "ООО \"Рога и копыта\"",
  "status": "ACTIVE",
  "isActive": true,
  "okvedCode": "62.01",
  "checkedAt": "2026-04-25T12:00:00+00:00"
}
```
