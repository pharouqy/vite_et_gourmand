FROM php:8.3-apache

# ── 1. Dépendances système ──────────────────────────────────────────────────
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libssl-dev \
    pkg-config \
    && rm -rf /var/lib/apt/lists/*

# ── 2. Extensions PHP ────────────────────────────────────────────────────────
# pdo_mysql : connexion MySQL via PDO
RUN docker-php-ext-install pdo_mysql

# mongodb : driver PECL (NoSQL)
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# ── 3. Composer ─────────────────────────────────────────────────────────────
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ── 4. Configuration Apache ──────────────────────────────────────────────────
# Copie notre vhost (remplace le default)
COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# Active mod_rewrite et mod_headers
RUN a2enmod rewrite headers

# ── 5. Document root ─────────────────────────────────────────────────────────
WORKDIR /var/www/html

# ── 6. Dépendances PHP ───────────────────────────────────────────────────────
# Copie uniquement les fichiers Composer en premier (cache Docker optimisé)
COPY src/composer.json src/composer.lock ./

# Installe les dépendances (sans les packages dev en prod)
RUN composer install --no-dev --optimize-autoloader