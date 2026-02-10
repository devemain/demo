# ============================================================================
# 2026 DeveMain
#
# All rights reserved. For internal use only.
# Unauthorized copying, modification, or distribution is prohibited.
#
# @author    DeveMain <devemain@gmail.com>
# @copyright 2026 DeveMain
# @license   PROPRIETARY
#
# @link      https://github.com/DeveMain
# ============================================================================

print_frame "Deploy Project in production"

# Load env file
load_env

# Install Composer dependencies
print_loading_frame "[prod] Installing Composer dependencies"
if [ "$APP_ENV" != "local" ]; then
    composer install --no-dev --optimize-autoloader --no-interaction
fi

# Install and build npm dependencies
print_loading_frame "[prod] Installing npm dependencies"
if [ "$APP_ENV" != "local" ]; then
    if [ -f "package.json" ]; then
        npm run p

        print_loading_frame "[prod] Building assets with Vite"
        npm run build
    else
        print_warning_frame "No package.json found, skipping npm build"
    fi
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
print_loading_frame "[docker] Setting permissions"
if [ $(pwd) = "/var/www/html" ]; then
    if [ "$APP_ENV" != "local" ]; then
        chown -R www-data:www-data . 2>/dev/null || true
    else
        chown -R "$USER":www-data . 2>/dev/null || true
    fi

    chmod -R 775 storage 2>/dev/null || true
    chmod -R 775 bootstrap/cache 2>/dev/null || true
fi

# Check database connection and run migrations
print_loading_frame "[docker] Testing database connection"
if [ $(pwd) = "/var/www/html" ]; then
    chmod 775 database 2>/dev/null || true

    # Create SQLite database file
    if [ ! -f "database/database.sqlite" ]; then
        touch database/database.sqlite
        print_success_frame "Created SQLite database file"
    fi

    if [ "$APP_ENV" != "local" ]; then
        chown www-data:www-data database/database.sqlite 2>/dev/null || true
        chmod 666 database/database.sqlite 2>/dev/null || true

        if php artisan tinker --execute="echo 'DB connected';" 2>/dev/null || true; then
            print_loading_frame "Database connection successful, running migrations"
            php artisan migrate --force
        else
            print_warning_frame "WARNING: Cannot connect to database, skipping migrations"
            print_warning_frame "Check your DB_HOST, DB_USERNAME, DB_PASSWORD in .env file"
        fi
    else
        chown "$USER":www-data database/database.sqlite 2>/dev/null || true
        chmod 666 database/database.sqlite 2>/dev/null || true
    fi
fi

# For queues
# php artisan queue:restart

# Clear cache
print_loading_frame "[docker] Caching configuration"
if [ $(pwd) = "/var/www/html" ]; then
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
