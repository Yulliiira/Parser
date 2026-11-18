# nginx и логи (справочник)

Только факты о текущей настройке. Правила и запреты — в `CLAUDE.md`.

## Конфиг (nginx/default.conf)
- root: /var/www/public, точка входа index.php (Laravel).
- PHP через `fastcgi_pass php:9000` (имя сервиса `php` из docker-compose).
- Порты: см. `stack.md`.
- Конфиг монтируется в /etc/nginx/conf.d/default.conf.
- Блок `location ~ /\.ht { deny all; }` закрывает служебные файлы.

## Лог доступа
- Путь в контейнере: /var/www/storage/logs/nginx-access.log; на хосте: storage/logs/nginx-access.log.
- Формат: стандартный combined.
- Файл создаётся вручную (`touch`) с правами 664 до запуска контейнеров.

## Формат строки (ожидается парсером)
IP - - [дд/Mon/гггг:чч:мм:сс +0000] "METHOD /url HTTP/1.1" код байты "referer" "User-Agent"
- Извлекаются: IP, дата/время, URL, User-Agent (ОС, архитектура, браузер).
- Разбор: `app/Services/LogParser` (`Pattern`, `Format`).
