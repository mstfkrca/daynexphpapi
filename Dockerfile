FROM php:8.0-apache

# Apache Rewrite Modülünü aktif et
RUN a2enmod rewrite

# Git ve Zip kütüphanelerini yükle (Composer'ın paketleri indirebilmesi için şarttır)
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip

# Gerekli PHP uzantılarını yükle
RUN docker-php-ext-install pdo pdo_mysql mysqli

# SİHİRLİ DOKUNUŞ: Resmi Composer imajından composer çalıştırıcısını direkt içeri alıyoruz
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Çalışma dizinini ayarla
WORKDIR /var/www/html

# İzinleri ayarla
RUN chown -R www-data:www-data /var/www/html