FROM nginx:stable AS web

# copy nginx-config
COPY docker-release/web/iass.conf /etc/nginx/conf.d/iass.conf
COPY docker-release/web/auth.conf /etc/nginx/conf.d/auth.conf

# copy app
COPY in_auth /var/www/in_auth
COPY in_iass/iass_5.0 /var/www/in_iass/iass_5.0

# 不要ファイルの削除
# default config
# all php extension file
RUN rm /etc/nginx/conf.d/default.conf &&\
find /var/www/in_auth -name "*.php" -type f | xargs rm &&\
find /var/www/in_iass -name "*.php" -type f | xargs rm &&\
touch /var/www/in_auth/auth_2.1/index.php &&\
touch /var/www/in_iass/iass_5.0/index.php

