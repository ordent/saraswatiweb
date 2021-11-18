FROM php:fpm

# MYSQL Setup
RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable pdo_mysql

# RUN set -ex \
#   && apk --no-cache add \
#     postgresql-dev

# RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \

# RUN docker-php-ext-install pgsql pdo_pgsql \
#     && docker-php-ext-enable pdo_pgsql