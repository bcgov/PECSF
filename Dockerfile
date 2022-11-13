FROM aro.jfrog.io/performance-app/php:8

RUN apt-get update -y && apt -y upgrade && apt-get install -y openssl zip unzip git \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
	libxrender-dev \
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
    wget \
    vim 

RUN apt-get update && apt-get install -y procps
RUN apt install ca-certificates apt-transport-https wget gnupg -y
RUN wget -q https://packages.sury.org/php/apt.gpg -O- | apt-key add -
RUN echo "deb https://packages.sury.org/php/ buster main" | tee /etc/apt/sources.list.d/php.list

RUN sudo apt-get update
#RUN apt list|grep php7.3-gd
#RUN apt-get install php7.3-gd/stable -y
RUN docker-php-ext-install gd
RUN apt-get install -y \
        libzip-dev \
        zip \
    && docker-php-ext-install zip
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install pdo pdo_mysql mbstring 
WORKDIR /app
COPY . /app

# JP add 2022-06-22 -- copy the start script and additional php setting file from repo to container
COPY ./php-memory-limits.ini /usr/local/etc/php/conf.d/php-memory-limits.ini
COPY ./start.sh /usr/local/bin/start

RUN php artisan cache:clear

# RUN composer update --ignore-platform-reqs


# # Create cache and session storage structure
# RUN bash -c 'mkdir -p /var/www/html/storage{app,framework,logs}'
# RUN chmod -R 755 /var/www/html/storage


# EXPOSE 8000

# RUN chgrp -R 0 /app && \
#     chmod +x /usr/local/bin/start && \
#     chmod -R g=u /app

# #CMD php artisan serve --host=0.0.0.0 --port=8000
# CMD ["/usr/local/bin/start"]