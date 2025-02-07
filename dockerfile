# Usar uma imagem oficial do PHP CLI
FROM php:8.2-cli

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

# Instalar Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Definir o diretório de trabalho
WORKDIR /var/www/symfony

# Copiar arquivos do projeto para o container
COPY . .

# Instalar dependências do Symfony
RUN composer install --no-dev --optimize-autoloader 

# Ajustar permissões dos diretórios de cache e logs
RUN chmod -R 777 var/cache var/log

# Migração dos dados
# RUN php bin/console doctrine:migrations:diff
# RUN php bin/console doctrine:migrations:migrate

# Configurar variáveis de ambiente
ENV APP_ENV=prod

# Expor a porta usada pelo PHP CLI
EXPOSE 8000

# Comando inicial do container
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
