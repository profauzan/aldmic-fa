FROM node:20-alpine AS assets

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY resources ./resources
COPY public ./public
COPY tailwind.config.js ./
RUN npm run production

FROM php:7.4-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev libsqlite3-dev libzip-dev unzip \
    && docker-php-ext-install pdo_pgsql pdo_sqlite opcache \
    && rm -rf /var/lib/apt/lists/*

RUN a2dismod mpm_event mpm_worker mpm_prefork || true \
    && a2enmod mpm_prefork rewrite

COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY composer.json composer.lock ./
ARG INSTALL_DEV=false
RUN if [ "$INSTALL_DEV" = "true" ]; then \
        composer install --no-interaction --prefer-dist --no-autoloader --no-scripts; \
    else \
        composer install --no-dev --no-interaction --prefer-dist --no-autoloader --no-scripts; \
    fi
COPY . .
COPY --from=assets /app/public/css/app.css public/css/app.css
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

RUN if [ "$INSTALL_DEV" = "true" ]; then \
        composer dump-autoload --optimize --no-scripts; \
    else \
        composer dump-autoload --no-dev --optimize --no-scripts; \
    fi

RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/film-cabinet-entrypoint
RUN chmod +x /usr/local/bin/film-cabinet-entrypoint

EXPOSE 80

ENTRYPOINT ["film-cabinet-entrypoint"]
CMD ["apache2-foreground"]
