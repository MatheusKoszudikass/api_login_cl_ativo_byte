# Usar uma imagem oficial do PHP
FROM php:8.3-fpm

# Instalar dependências do sistema e extensões do PHP
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpq-dev \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-install \
    intl \
    opcache \
    pdo \
    pdo_mysql \
    zip

RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony
# Limpeza para reduzir o tamanho da imagem
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Definir o diretório de trabalho
WORKDIR /var/www/symfony

# Copiar arquivos do projeto para o container
COPY . .

# Instalar dependências do Symfony
RUN composer install --no-dev --optimize-autoloader 

# Alterar permissões
RUN chown -R www-data:www-data /var/www/symfony

# Configurar variáveis de ambiente
ENV APP_ENV=prod

# Expor a porta usada pelo PHP-FPM
EXPOSE 9000

# Comando inicial do container
CMD ["php-fpm"]
