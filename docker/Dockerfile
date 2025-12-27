# =========================
# 🌱 Base Laravel (avec PHP & Composer)
# =========================
FROM php:8.3-fpm-alpine AS base

WORKDIR /var/www

# Installation des dépendances système et extensions PHP en une seule layer optimisée
RUN apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    postgresql-dev \
    icu-dev \
    imagemagick-dev \
    && apk add --no-cache \
    bash \
    libzip \
    libpng \
    libjpeg-turbo \
    freetype \
    libpq \
    icu-libs \
    imagemagick \
    # Configuration et installation des extensions PHP
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    zip \
    bcmath \
    opcache \
    pcntl \
    intl \
    exif \
    gd \
    # Installation de Redis extensions
    && pecl install redis \
    && docker-php-ext-enable redis \
    # Nettoyage complet des dépendances de build et cache
    && apk del .build-deps \
    && rm -rf /tmp/* /var/cache/apk/* /usr/share/man /usr/share/doc

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuration PHP pour production
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && { \
    echo 'opcache.enable=1'; \
    echo 'opcache.memory_consumption=256'; \
    echo 'opcache.max_accelerated_files=20000'; \
    echo 'opcache.validate_timestamps=0'; \
    } > "$PHP_INI_DIR/conf.d/opcache.ini"


# =========================
# 🧩 Backend — installation Composer
# =========================
FROM base AS backend

WORKDIR /var/www

# Copier uniquement les fichiers de dépendances pour optimiser le cache
COPY composer.json composer.lock ./

# Installer les dépendances sans scripts ni dev
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-scripts \
    && rm -rf ~/.composer/cache

# Copier le reste du code source
COPY . .

# Finaliser le chargement
RUN composer run-script post-autoload-dump \
    && composer clear-cache

# =========================
# 🚀 Image finale de production
# =========================
FROM base AS production

WORKDIR /var/www

# Copier le code backend (incluant vendor)
COPY --from=backend /var/www /var/www

# Ajuster les permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Ajouter l’entrypoint
COPY ./docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

USER www-data

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]

EXPOSE 9000