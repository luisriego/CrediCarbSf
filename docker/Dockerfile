FROM php:8.2-apache

ARG UID

# Create user with same permissions as host and some useful stuff
RUN adduser -u ${UID} --disabled-password --gecos "" appuser
RUN mkdir /home/appuser/.ssh
RUN chown -R appuser:appuser /home/appuser/
RUN echo "StrictHostKeyChecking no" >> /home/appuser/.ssh/config
RUN echo "alias sf=bin/console" >> /home/appuser/.bashrc
RUN echo "alias test='bin/phpunit --colors=always'" >> /home/appuser/.bashrc
RUN echo "alias tf='bin/phpunit --colors=always --filter'" >> /home/appuser/.bashrc
RUN echo "alias fix='composer fix\:standards'" >> /home/appuser/.bashrc
RUN echo "alias analyze='composer analyze\:standards'" >> /home/appuser/.bashrc

# Install packages and PHP extensions
RUN apt update \
    # common libraries and extensions
    && apt install -y git acl openssl openssh-client wget zip \
    && apt install -y libpng-dev zlib1g-dev libzip-dev libxml2-dev libicu-dev \
    && docker-php-ext-install intl pdo zip \
    # for MySQL
    && docker-php-ext-install pdo_mysql \
    # for BCMath
    && docker-php-ext-install bcmath \
    # XDEBUG and APCu
    && pecl install xdebug apcu \
    # enable Docker extensions
    && docker-php-ext-enable --ini-name 05-opcache.ini opcache xdebug apcu

RUN apt update \
    && apt install -y libcurl4-openssl-dev pkg-config libssl-dev libnghttp2-dev \
    && pecl install raphf \
    && docker-php-ext-enable raphf \
    && pecl install pecl_http \
    && docker-php-ext-enable http

# Install and update composer
RUN curl https://getcomposer.org/composer.phar -o /usr/bin/composer && chmod +x /usr/bin/composer
RUN composer self-update

## Install Symfony binary
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash
RUN apt install symfony-cli

RUN mkdir -p /var/www/html

# Config XDEBUG
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Update Apache config
COPY ./default.conf /etc/apache2/sites-available/default.conf
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && a2enmod rewrite \
    && a2dissite 000-default \
    && a2ensite default \
    && service apache2 restart

# Modify upload file size
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Set memory limit
RUN echo "memory_limit=256M" > /usr/local/etc/php/conf.d/memory-limit.ini

WORKDIR /var/www/html
