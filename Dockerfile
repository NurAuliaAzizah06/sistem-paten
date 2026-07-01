# Menggunakan base image PHP 8.2 dengan Apache
FROM php:8.2-apache

RUN apt-get update && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install mysqli pdo pdo_mysql

# Menyalin kode sumber aplikasi ke dalam folder web server
COPY . /var/www/html/

# Memberikan izin akses yang benar agar web bisa dibuka
RUN chown -R www-data:www-data /var/www/html

# Railway menggunakan $PORT dinamis, bukan selalu 80
# Script startup akan menyesuaikan konfigurasi Apache dengan PORT yang diberikan Railway
EXPOSE 80

CMD PORT=${PORT:-80} && \
    sed -i "s/Listen 80/Listen $PORT/" /etc/apache2/ports.conf && \
    sed -i "s/:80>/:$PORT>/g" /etc/apache2/sites-enabled/000-default.conf && \
    apache2-foreground