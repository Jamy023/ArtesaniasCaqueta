FROM php:8.2-fpm-alpine

# Instalar dependencias del sistema
RUN apk add --no-cache \
    nginx \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    lua-resty-http \
    nodejs \
    npm

# Instalar extensiones de PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install pdo pdo_mysql gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /var/www

# Copiar archivos de la aplicación
COPY . .

# Instalar dependencias de PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Instalar dependencias de Node y construir assets
RUN npm ci && npm run build

# Crear directorio de storage y copiar archivos
RUN mkdir -p public/storage && \
    if [ -d "storage/app/public" ]; then cp -r storage/app/public/* public/storage/ || true; fi && \
    php artisan storage:link && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Configurar permisos
RUN chown -R www-data:www-data /var/www && \
    chmod -R 755 /var/www/storage && \
    chmod -R 755 /var/www/bootstrap/cache

# Crear configuración de Nginx
RUN echo 'worker_processes auto;\n\
error_log /var/log/nginx/error.log warn;\n\
pid /var/run/nginx.pid;\n\
\n\
events {\n\
    worker_connections 1024;\n\
}\n\
\n\
http {\n\
    include /etc/nginx/mime.types;\n\
    default_type application/octet-stream;\n\
    \n\
    sendfile on;\n\
    tcp_nopush on;\n\
    tcp_nodelay on;\n\
    keepalive_timeout 65;\n\
    types_hash_max_size 2048;\n\
    \n\
    server {\n\
        listen 8080;\n\
        server_name _;\n\
        root /var/www/public;\n\
        index index.php index.html;\n\
        \n\
        gzip on;\n\
        gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;\n\
        \n\
        location / {\n\
            try_files $uri $uri/ /index.php?$query_string;\n\
        }\n\
        \n\
        location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|webp|woff|woff2|ttf|eot)$ {\n\
            expires 1y;\n\
            add_header Cache-Control "public, immutable";\n\
            access_log off;\n\
        }\n\
        \n\
        location ~ \.php$ {\n\
            fastcgi_pass 127.0.0.1:9000;\n\
            fastcgi_index index.php;\n\
            fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;\n\
            include fastcgi_params;\n\
        }\n\
        \n\
        location ~ /\.ht {\n\
            deny all;\n\
        }\n\
    }\n\
}' > /etc/nginx/nginx.conf

# Crear script de inicio
RUN echo '#!/bin/sh\nphp-fpm -D\nnginx -g "daemon off;"' > /start.sh && \
    chmod +x /start.sh

EXPOSE 8080

CMD ["/start.sh"]