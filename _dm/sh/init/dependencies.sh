# ============================================================================
# 2026 DeveMain
#
# All rights reserved. For internal use only.
# Unauthorized copying, modification, or distribution is prohibited.
#
# @author    DeveMain <devemain@gmail.com>
# @copyright 2026 DeveMain
# @license   PROPRIETARY
# @link      https://github.com/DeveMain
# ============================================================================

print_frame "Dependencies Setup"

# Setup dependencies
setup_dependencies() {
    # Composer
    print_frame_middle "Check composer.json file"
    if [ -f "composer.json" ]; then
        if command -v composer &> /dev/null; then
            print_success_frame "OK"
            if [[ ! "$OPTION" =~ ^(-q|--quiet)$ ]] && ask_yes_no "Install Composer dependencies?" "y"; then
                print_loading_frame "Installing Composer dependencies"
                composer install
                print_success_frame "Composer dependencies installed"
            fi
        else
            print_error_frame "Composer not found, skipping"
        fi
    fi

    # npm/yarn
    print_frame_middle "Check package.json file"
    if [ -f "package.json" ]; then
        if command -v npm &> /dev/null; then
            print_success_frame "OK"
            if [[ ! "$OPTION" =~ ^(-q|--quiet)$ ]] && ask_yes_no "Install npm dependencies?" "y"; then
                print_loading_frame "Installing npm dependencies"
                npm install
                print_success_frame "npm dependencies installed"
            fi

            if [[ ! "$OPTION" =~ ^(-q|--quiet)$ ]] && ask_yes_no "Build assets?" "y"; then
                print_loading_frame "Building assets with Vite"
                npm run build
                print_success_frame "Assets created"
            fi
        else
            print_warning_frame "npm not found, skipping"
        fi
    fi
}
setup_dependencies
