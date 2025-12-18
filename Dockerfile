# ============================================================================
# 2025 DeveMain
#
# All rights reserved. For internal use only.
# Unauthorized copying, modification, or distribution is prohibited.
#
# @author    DeveMain <devemain@gmail.com>
# @copyright 2025 DeveMain
# @license   PROPRIETARY
# @link      https://github.com/DeveMain
# ============================================================================

FROM php:8.4-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    # Basic packages
    curl libonig-dev libxml2-dev libzip-dev zip unzip \
    # Nginx server
    nginx \
    # SQLite DB
    libsqlite3-dev \
    # Node.js
    nodejs npm \
    # PHP extensions
    && docker-php-ext-install pdo pdo_sqlite mbstring xml zip \
    # Clear cache
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Working directory
WORKDIR /var/www/html

# Copy files
COPY . .

# Script permission
RUN chmod +x docker.sh

# Run deploy script
CMD ["/bin/bash", "docker.sh", "deploy"]
