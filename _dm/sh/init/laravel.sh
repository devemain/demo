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

print_frame "Laravel Setup"

# Laravel-specific setup
setup_laravel() {
    # .env setup
    print_frame_middle "Check .env file"
    if [ ! -f ".env" ] && [ -f ".env.example" ]; then
        print_loading_frame "Creating .env file from .env.example"
        cp .env.example .env
        print_success_frame ".env file created"
    elif [ ! -f ".env" ]; then
        print_error_frame ".env.example not found"
    else
        print_success_frame "OK"
    fi

    # Load .env file
    load_env

    # Generate application key
    print_frame_middle "Check application key"
    if [ -z "$APP_KEY" ]; then
        print_loading_frame "Generating Laravel application key"
        php artisan key:generate
        print_success_frame "Application key generated"
    else
        print_success_frame "OK"
    fi

    # Ask about migrations
    if ask_yes_no "Run database migrations?" "n"; then
        print_loading_frame "Running migrations"
        php artisan migrate --force
        print_success_frame "Migrations completed"
    fi

    # Setup permissions
    setup_permissions

    # Clear caches
    print_loading_frame "Clearing Laravel caches"
    php artisan optimize:clear 2>/dev/null || true
    print_success_frame "Caches cleared"
}

# Setup permissions
setup_permissions() {
    # Check cache permissions
    print_frame_middle "Check permissions for project"
    if [[ ! "$OPTION" =~ ^(-q|--quiet)$ ]]; then
        sudo chown -R "$USER":www-data .
    fi
    print_success_frame "Permissions set"

    # Check SQLite database permissions
    print_frame_middle "Check SQLite database permissions"
    if [ -f "database/database.sqlite" ]; then
        print_loading_frame "Setting up SQLite database permissions"
        chmod 666 database/database.sqlite 2>/dev/null || true
        print_success_frame "Permissions set"
    fi

    # Check cache permissions
    print_frame_middle "Check cache permissions"
    if [ -d "bootstrap/cache" ]; then
        print_loading_frame "Setting up cache directory permissions"
        chmod -R 775 bootstrap/cache 2>/dev/null || true
        print_success_frame "Permissions set"
    fi

    # Check storage permissions
    print_frame_middle "Check storage permissions"
    if [ -d "storage" ] && [ -d "bootstrap/cache" ]; then
        print_loading_frame "Setting up storage directory permissions"
        chmod -R 775 storage 2>/dev/null || true
        print_success_frame "Permissions set"
    fi
}

# Check that this is a Laravel project
print_frame_middle "Check Laravel project"
if [ -f "artisan" ]; then
    print_success_frame "OK"
    setup_laravel
else
    print_info_frame "Not a Laravel project (artisan not found)"
fi
