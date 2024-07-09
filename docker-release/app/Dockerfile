FROM php:8.2.21-fpm AS app

# pdo-mysqlの追加
RUN docker-php-ext-install -j$(nproc) pdo pdo_mysql

# php.iniの追加
COPY docker-release/app/z-app-php.ini /usr/local/etc/php/conf.d/z-app-php.ini

# appを追加
COPY in_auth /var/www/in_auth
COPY in_iass /var/www/in_iass
