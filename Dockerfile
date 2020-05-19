FROM jbauson/ibm:phpdb2-dsdv11.5

ADD . /var/www/html/

RUN apt-get update && apt-get install -y --no-install-recommends \
  autoconf \
  build-essential \
  apt-utils \
  zlib1g-dev \
  libzip-dev \
  unzip \
  zip \
  libpq-dev \
  libfreetype6-dev \
  libjpeg62-turbo-dev \
  libpng-dev \
  libwebp-dev \ 
  libxpm-dev 

RUN docker-php-ext-configure gd \ 
  --with-jpeg=/usr/include/ \
  --with-freetype=/usr/include/

RUN docker-php-ext-configure zip

RUN docker-php-ext-install gd zip

RUN php -v
RUN php -i
RUN apt-cache search php7.* 

RUN curl -sS https://getcomposer.org/installer | tac | tac | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-interaction

RUN sed -i '$ a auto_prepend_file="php/siteheader.php"' /usr/local/etc/php/conf.d/docker-php-ext-ibm_db2.ini
RUN sed -i '$ a auto_append_file="php/sitefooter.php"' /usr/local/etc/php/conf.d/docker-php-ext-ibm_db2.ini
