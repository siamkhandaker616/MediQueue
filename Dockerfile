FROM node:22-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build


FROM php:8.2-cli-alpine

RUN docker-php-ext-install pdo_sqlite || true

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN apk add --no-cache git

WORKDIR /app

COPY . .

RUN composer install --no-interaction --optimize-autoloader

COPY --from=assets /app/public/build ./public/build

RUN mkdir -p database \
    && touch database/database.sqlite \
    && chmod -R 777 database storage bootstrap/cache

EXPOSE 8000

CMD ["sh", "-c", "php artisan migrate --force; if [ ! -f storage/app/seeded ]; then php artisan db:seed --force && touch storage/app/seeded; fi; exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"]
