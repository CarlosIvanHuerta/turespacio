# Usar la imagen oficial de PHP 8.3 con Apache
FROM php:8.3-apache

# Establecer variables de entorno para PHP
ENV PHP_INI_DIR=/usr/local/etc/php

# Copiar y reemplazar el archivo php.ini personalizado
COPY php.ini $PHP_INI_DIR/php.ini

# Instalar extensiones adicionales que utilice tu proyecto (si son necesarias)
# Puedes ajustar estas según las necesidades de tu aplicación
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli pdo pdo_mysql \
    && docker-php-ext-enable mysqli pdo_mysql

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite

# Copiar el archivo de Virtual Host a la configuración de Apache
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# Habilitar el Virtual Host
RUN a2ensite 000-default.conf

# Configurar los permisos apropiados para la carpeta HTML que usará Apache
WORKDIR /var/www/html

# Copiar todo el contenido de tu proyecto a la carpeta de Apache
# Asegúrate de que el archivo index.php esté en tu raíz para que funcione como el punto de entrada
COPY /demo /var/www/html/

# Exponer el puerto 80, el puerto por defecto de Apache
EXPOSE 80

# Comando para iniciar Apache en el contenedor
CMD ["apache2-foreground"]