# Стек (справочник)

Единственное место, где указаны версии. В других файлах не дублировать, ссылаться сюда.
Источник истины: `composer.json`, `docker-compose.yml`.

## Версии
- PHP: ^8.2
- Laravel: ^12.0
- Стиль кода: Laravel Pint ^1.13
- Тесты: PHPUnit ^11.5 (каталога `tests/` пока нет)
- БД: MySQL (образ `mysql`), charset utf8mb4
- Окружение: Docker Compose (сервисы `php`, `nginx`, `db`, `phpmyadmin`)

## Порты
- Приложение: http://localhost:8080
- phpMyAdmin: http://localhost:8081
- MySQL: 3306
- php-fpm: 9001 → 9000 в контейнере

## Команды
```bash
docker compose up --build -d
docker compose exec php php artisan logs:parse
docker compose exec php vendor/bin/pint --test
docker compose exec php php artisan test
```

## Структура
- `app/Console/Commands/ParseLogsCommand.php`: команда `logs:parse`
- `app/Services/LogParser/` (`Pattern`, `Format`): разбор строки лога
- `app/Services/LogParserService.php`, `LogAnalyticsService.php`: бизнес-логика
- `app/Repositories/`: доступ к БД; интерфейсы в `app/Contracts/`, биндинги в `AppServiceProvider`
- `app/Http/Controllers/LogController.php`, `app/Http/Resources/`, `routes/web.php`
- `database/migrations/`: схема
