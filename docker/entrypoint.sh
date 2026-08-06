#!/bin/sh
set -e

# Railway may start the image with an Apache module state different from the
# build layer. PHP's Apache module requires prefork, so normalize MPM at runtime.
a2dismod mpm_event mpm_worker mpm_prefork >/dev/null 2>&1 || true
a2enmod mpm_prefork >/dev/null
apache2ctl -t

if [ -z "${APP_KEY:-}" ]; then
    php artisan key:generate --force --no-ansi
fi

php artisan storage:link --quiet 2>/dev/null || true
chown -R www-data:www-data storage bootstrap/cache

exec "$@"
