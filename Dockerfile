FROM php:8.2-cli

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json ./
RUN composer install --no-dev --no-interaction

COPY src/ src/
COPY example.php ./

CMD ["php", "example.php"]
