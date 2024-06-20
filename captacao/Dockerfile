FROM php:8.1-apache

# Instalar dependências necessárias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    locales \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install pdo_mysql

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definir diretório de trabalho
WORKDIR /var/www/html

# Copiar código fonte
COPY . /var/www/html

# Instalar dependências do Composer
RUN composer install --no-scripts --no-autoloader

# Configurar o Apache
COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# Habilitar mod_rewrite do Apache
RUN a2enmod rewrite

# Configurar permissões
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Autoload do Composer
RUN composer dump-autoload

# Gerar chave da aplicação
RUN php artisan key:generate

# Expor a porta 80
EXPOSE 80

# Comando para iniciar o Apache
CMD ["apache2-foreground"]
