FROM aro.jfrog.io/performance-app/php:8

RUN apt-get update -y && apt -y upgrade && apt-get install -y openssl zip unzip git \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libfreetype6 \
    libc6 \
    libgd3 \
    libjpeg62-turbo \
    libpng16-16 \
    libwebp6 \
    libx11-6 \
    libxpm4 \
    ucf \
    zlib1g \
    sudo \
    wget  

RUN apt install ca-certificates apt-transport-https wget gnupg -y
RUN wget -q https://packages.sury.org/php/apt.gpg -O- | apt-key add -
RUN echo "deb https://packages.sury.org/php/ buster main" | tee /etc/apt/sources.list.d/php.list

RUN sudo apt-get update
RUN apt list|grep php7.3-gd
#RUN apt-get install php7.3-gd/stable -y
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install pdo pdo_mysql mbstring 
WORKDIR /app
COPY . /app

RUN composer update --ignore-platform-reqs
RUN php artisan config:clear

EXPOSE 8000

RUN chgrp -R 0 /app && \
    chmod -R g=u /app
USER 1001


CMD php artisan serve --host=0.0.0.0 --port=8000