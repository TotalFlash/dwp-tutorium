FROM debian:latest
RUN apt update && apt upgrade -y

# APACHE install
RUN apt install apache2 -y

# Mount Apache (without container will shut down)
CMD apachectl -D FOREGROUND

# PHP 8.1 isntall
RUN apt install apt-transport-https lsb-release ca-certificates wget -y
RUN wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
RUN sh -c 'echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list'
RUN apt update
RUN apt upgrade -y
RUN apt-get install php8.1 -y

# PHP extensions
RUN apt-get install php8.1-mysql php-pear php8.1-gd php8.1-mbstring -y
RUN service apache2 restart

# Copy sourec code
COPY ./src/ /var/www/html/
WORKDIR /var/www/html/

RUN rm index.html

RUN chown -R www-data:www-data /var/www/html/