# ==========================================
# Stage 1: Build Frontend Assets (Vite / Vue 3)
# ==========================================
FROM node:20-slim AS node_builder

WORKDIR /app

# Ensure devDependencies are installed and memory ceiling is sufficient for large bundle builds
ENV NODE_ENV=development
ENV NODE_OPTIONS="--max-old-space-size=4096"

# Copy package files and install all dependencies (including devDependencies)
COPY package*.json ./
RUN npm install --include=dev

# Copy application files and build frontend
COPY . .
RUN npm run build

# ==========================================
# Stage 2: Production PHP-FPM + Nginx Container
# ==========================================
FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

# Install system dependencies & libraries
RUN apk add --no-cache \
    bash \
    curl \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libpq-dev \
    sqlite-dev \
    icu-dev \
    oniguruma-dev \
    nginx \
    supervisor

# Install PHP extensions
RUN apk add --no-cache --virtual .build-deps \
        autoconf \
        g++ \
        make \
        linux-headers \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        pdo_sqlite \
        bcmath \
        mbstring \
        zip \
        intl \
        gd \
        opcache \
        pcntl \
        exif \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Install Composer from official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy configuration files
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/zz-custom.conf
COPY docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

# Ensure proper permissions and line endings on entrypoint
RUN chmod +x /usr/local/bin/entrypoint.sh && sed -i -e 's/\r$//' /usr/local/bin/entrypoint.sh

# Copy composer files and install PHP dependencies (optimized layer caching)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy application source code
COPY . .

# Copy compiled frontend assets from Stage 1
COPY --from=node_builder /app/public/build /var/www/html/public/build

# Complete composer autoloader optimization
RUN composer dump-autoload --optimize --no-dev

# Set permissions for storage and cache directories
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose HTTP port (Render dynamically routes to $PORT)
EXPOSE 80 10000

# Set entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
