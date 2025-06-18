# Usar la imagen oficial de PHP 8.3 con Apache
FROM php:8.3-apache

# Establecer variables de entorno para PHP
ENV PHP_INI_DIR=/usr/local/etc/php
ENV NVM_DIR=/root/.nvm

# Copiar y reemplazar el archivo php.ini personalizado
COPY php.ini $PHP_INI_DIR/php.ini

# Instalar extensiones adicionales que utilice tu proyecto
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    zip unzip libzip-dev \
    curl wget git build-essential \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli pdo pdo_mysql zip \
    && docker-php-ext-enable mysqli pdo_mysql zip

# Instalar NVM y Node.js
RUN wget -qO- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.5/install.sh | bash \
    && . $NVM_DIR/nvm.sh \
    && nvm install 18 \
    && nvm use 18 \
    && nvm alias default 18 \
    && ln -s "$NVM_DIR/versions/node/$(nvm version)/bin/node" /usr/bin/node \
    && ln -s "$NVM_DIR/versions/node/$(nvm version)/bin/npm" /usr/bin/npm \
    && ln -s "$NVM_DIR/versions/node/$(nvm version)/bin/npx" /usr/bin/npx

# Confirmar las versiones instaladas de Node.js / npm
RUN node -v && npm -v

# Instalar Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    php -r "unlink('composer-setup.php');"

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite

# Copiar el archivo de Virtual Host a la configuración de Apache
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# Habilitar el Virtual Host
RUN a2ensite 000-default.conf

# Configurar permisos para el directorio donde reside el código de la aplicación
WORKDIR /var/www/html

# Copiar los archivos del proyecto
COPY /demo /var/www/html/

# Exponer el puerto 80, predeterminado de Apache
EXPOSE 80

# Comando para iniciar Apache en el contenedor
CMD ["apache2-foreground"]