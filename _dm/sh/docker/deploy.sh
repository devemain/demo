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

print_frame "Deploy Project in production"

# Load env file
load_env

# Install dependencies
print_loading_frame "[prod] Installing composer dependencies"
if [ "$APP_ENV" != "local" ]; then
    composer install --no-dev --optimize-autoloader --no-interaction
fi

# Copy .env.example to .env
print_loading_frame "[prod] Copying .env.example to .env"
if [ "$APP_ENV" != "local" ] && [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env

    # Replace APP_ENV=local with APP_ENV=production
    sed -i "s/^APP_ENV=.*/APP_ENV=production/" .env
fi

# Generate application key
print_loading_frame "[prod] Generating application key"
if [ "$APP_ENV" != "local" ] && [ -z "$APP_KEY" ]; then
    php artisan key:generate
    grep APP_KEY .env
fi

# Set permissions
print_loading_frame "[prod] Setting permissions"
if [ "$APP_ENV" != "local" ]; then
    chown -R www-data:www-data .
    chmod -R 775 storage
    chmod -R 775 bootstrap/cache
fi

# Check database connection and run migrations
print_loading_frame "[prod] Testing database connection"
if [ "$APP_ENV" != "local" ]; then
    # Create SQLite database file
    if [ ! -f "database/database.sqlite" ]; then
        touch database/database.sqlite
        print_success_frame "Created SQLite database file"
    fi
    chown www-data:www-data database/database.sqlite
    chmod 666 database/database.sqlite

    if php artisan tinker --execute="echo 'DB connected';" 2>/dev/null; then
        print_loading_frame "Database connection successful, running migrations"
        php artisan migrate --force
    else
        print_warning_frame "WARNING: Cannot connect to database, skipping migrations"
        print_warning_frame "Check your DB_HOST, DB_USERNAME, DB_PASSWORD in .env file"
    fi
fi

# For queues
# php artisan queue:restart

# Clear cache
print_loading_frame "[prod] Caching configuration"
if [ "$APP_ENV" != "local" ]; then
    php artisan optimize:clear
fi

# Create nginx config
print_loading_frame "[docker] Creating nginx config"
if [ $(pwd) = "/var/www/html" ]; then
cat > /etc/nginx/sites-available/default << 'EOF'
server {
    listen 80;
    root /var/www/html/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass __PHP_FPM_HOST__:9000;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
EOF

    if [ "$APP_ENV" != "local" ]; then
        HOST="127.0.0.1"
    else
        HOST="app"
    fi
    sed -i "s/__PHP_FPM_HOST__/$HOST/g" /etc/nginx/sites-available/default
fi

# Start services
print_loading_frame "[docker] Starting services"
if [ $(pwd) = "/var/www/html" ]; then
    nginx
    php-fpm
fi
