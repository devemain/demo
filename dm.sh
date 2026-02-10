#!/bin/bash
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

# ./dm.sh - Project setup
# chmod +x dm.sh

# Define the working directory
WORK_DIR="$(dirname "$(realpath "${BASH_SOURCE[0]}")")/_dm"
SH_DIR="$WORK_DIR/sh"

# Check if a directory exists
if [[ ! -d "$WORK_DIR" ]]; then
    echo "✘ DeveMain scripts directory not found: $WORK_DIR" && exit 1
fi

# Include common file
source "$SH_DIR/common.sh"

# Check options
OPTION="$1"
[[ "$OPTION" =~ ^(-h|--help)$ ]] && print_usage && exit 0

# Main setup function
main() {
    print_frame "Project Setup Script" "${MAGENTA}"

    # 1. Initialize Git
    source "$SH_DIR/init/git.sh"

    # 2. Dependencies setup
    source "$SH_DIR/init/dependencies.sh"

    # 3. Laravel-specific setup
    source "$SH_DIR/init/laravel.sh"

    # 4. Run PHP initialization script
    if [[ ! "$OPTION" =~ ^(-q|--quiet)$ ]] && ask_yes_no "Run PHP initialization script?" "y"; then
        print_loading_frame "Running initialization"
        run_php_script "_dm/php/_init.php" "$@"

        print_success_frame "Initialization completed"
        print_frame_bottom "${GREEN}"
    fi

    # 5. Make executable files
    source "$SH_DIR/init/executables.sh"

    # 6. Final check
    source "$SH_DIR/init/final_check.sh"

    # 7. Summary
    show_summary
}

# Show summary
show_summary() {
    print_frame "Setup complete!" "${GREEN}"

    print_info_frame "Next steps:"
    if [ -f "artisan" ]; then
        print_frame_middle "1. Edit .env file with your database credentials"
        print_frame_middle "2. Run migrations: php artisan migrate"
        print_frame_middle "3. Start dev server: php artisan serve"
    else
        print_frame_middle "1. Start development server"
    fi

    print_frame "About the Git hook [git spush]"
    print_info_frame "After each commit to main branch, the hook will:"
    print_frame_middle "- Wait 60 seconds for GitHub Actions"
    print_frame_middle "- Check if Release Bot updated package.json"
    print_frame_middle "- Auto-update CHANGELOG.md, package.json and package-lock.json"
    print_frame_middle "- PhpStorm will automatically reload the files"

    print_frame_bottom
}

# Run the main function
main "$@"
