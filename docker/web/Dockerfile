FROM nginx:stable AS web

# copy nginx-config
RUN rm /etc/nginx/conf.d/default.conf
COPY docker/web/iass.conf /etc/nginx/conf.d/iass.conf
COPY docker/web/auth.conf /etc/nginx/conf.d/auth.conf
