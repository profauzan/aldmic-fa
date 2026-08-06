#!/bin/sh
set -e

if [ -z "${APP_KEY:-}" ]; then
    php artisan key:generate --force --no-ansi
fi

php artisan storage:link --quiet 2>/dev/null || true
chown -R www-data:www-data storage bootstrap/cache

exec "$@"
