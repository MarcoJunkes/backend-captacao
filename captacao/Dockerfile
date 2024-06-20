# Use uma imagem base do PHP com o Apache
FROM php:8.1-apache

# Instale as dependências necessárias
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

# Instale o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copie o código do Laravel para o diretório de trabalho do Apache
COPY . /var/www/html

# Defina o diretório de trabalho
WORKDIR /var/www/html

# Instale as dependências do Composer
RUN composer install --no-scripts --no-autoloader

# Copie o arquivo de configuração do Apache
COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# Habilite o módulo de reescrita do Apache
RUN a2enmod rewrite

# Defina as permissões corretas
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Execute o autoload do Composer
RUN composer dump-autoload

# Gere a chave da aplicação Laravel
RUN php artisan key:generate

# Exponha a porta 80
EXPOSE 80

# Comando de inicialização do contêiner
CMD ["apache2-foreground"]
