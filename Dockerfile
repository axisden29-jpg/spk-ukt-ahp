FROM php:8.3-apache

# 1. Install utilitas dasar Linux yang ringan via apt
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# 2. Trik Jitu: Salin ekstensi GD dan PDO_MYSQL yang sudah jadi (PRE-COMPILED) 
# Tanpa install-php-extensions, tanpa kompilasi C, langsung pasang dalam 1 detik!
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions gd pdo_mysql

# 3. Aktifkan rewrite untuk .htaccess Laravel
RUN a2enmod rewrite

# 4. Ubah port Apache ke 7860 (Wajib untuk Hugging Face)
RUN sed -i 's/Listen 80/Listen 7860/' /etc/apache2/ports.conf
RUN sed -i 's/<VirtualHost \*:80>/<VirtualHost \*:7860>/' /etc/apache2/sites-available/000-default.conf

# 5. Set DocumentRoot ke folder public Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 6. Set Working Directory
WORKDIR /var/www/html

# 7. Ambil Composer yang sudah jadi dari image resmi Composer (Instan tanpa download skrip)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 8. Copy seluruh source code project Laravel
COPY . .

# 9. Jalankan installasi dependency Laravel & atur permission folder
RUN composer install --no-dev --optimize-autoloader
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 7860
CMD ["apache2-foreground"]