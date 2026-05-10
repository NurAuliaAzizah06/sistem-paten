# Menggunakan base image PHP 8.2 dengan Apache
FROM php:8.2-apache

# BARIS SAKTI: Menambal celah keamanan (Security Patching)
# Ini yang akan menghilangkan temuan HIGH dan CRITICAL dari Trivy
RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# (Opsional) Jika skripsi kamu butuh ekstensi database seperti MySQL, aktifkan ini:
# RUN docker-php-ext-install pdo pdo_mysql mysqli

# Menyalin kode sumber aplikasi ke dalam folder web server
COPY . /var/www/html/

# Memberikan izin akses yang benar agar web bisa dibuka
RUN chown -R www-data:www-data /var/www/html

# Port standar Apache
EXPOSE 80