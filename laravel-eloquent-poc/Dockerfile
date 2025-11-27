# Usar una imagen oficial de PHP
FROM php:8.2-fpm

# Argumentos para el usuario no-root
ARG user=laradock
ARG uid=1000

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm

# Instalar extensiones de PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crear un usuario no-root
RUN useradd -G www-data,root -u ${uid} -d /home/${user} ${user}
RUN mkdir -p /home/${user}/.composer && \
    chown -R ${user}:${user} /home/${user}

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Cambiar al usuario no-root
USER ${user}

# Exponer el puerto 9000 y ejecutar el servidor PHP-FPM
EXPOSE 9000
CMD ["php-fpm"]
