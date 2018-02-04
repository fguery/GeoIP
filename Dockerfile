FROM php:7.2

RUN apt-get update \
  && apt-get -y install libssl-dev ${PHPIZE_DEPS} \
  && pecl install xdebug \
  && docker-php-ext-enable xdebug \
  && echo " xdebug.remote_enable=on \n\
            xdebug.remote_host=docker.for.mac.localhost\n\
            xdebug.remote_handler=\"dbgp\" \n\
            xdebug.remote_mode=\"req\" \n\
            xdebug.remote_port=9000 \n\
            xdebug.remote_connect_back=0\n\
            xdebug.idekey=\"phpstorm\" \n\
            xdebug.remote_autostart=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
  && pecl install mongodb \
  && docker-php-ext-enable mongodb \
  && apt-get -y remove libssl-dev ${PHPIZE_DEPS}