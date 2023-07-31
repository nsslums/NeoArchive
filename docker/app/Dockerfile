FROM php:8.2-fpm AS app

#COPY in_auth /var/www/in_auth
#COPY in_iass /var/www/in_iass

# pdo-mysqlの追加
RUN docker-php-ext-install -j$(nproc) pdo pdo_mysql &&\
mkdir -p /mnt/persistent-volume/iass/data &&\
mkdir -p /mnt/persistent-volume/iass/dustbox &&\
mkdir -p /mnt/persistent-volume/iass/uploadtmp &&\
chown -R www-data:www-data /mnt/persistent-volume/iass

# php.iniの追加
COPY docker/app/z-app-php.ini /usr/local/etc/php/conf.d/z-app-php.ini
