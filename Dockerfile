FROM node:22-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build


FROM php:8.2-cli-alpine

RUN apk add --no-cache git libpq-dev \
    && docker-php-ext-configure pgsql --with-pgsql=/usr/include/postgresql \
    && docker-php-ext-install pgsql pdo_pgsql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install --no-interaction --optimize-autoloader

COPY --from=assets /app/public/build ./public/build

RUN chmod -R 777 storage bootstrap/cache

EXPOSE 8000

CMD ["sh", "-c", "php artisan migrate --force && php artisan db:seed --force; exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"]
