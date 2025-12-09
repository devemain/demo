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

print_frame "Build Docker containers"

print_loading_frame "Building containers"
if $COMPOSE_CMD build --no-cache "${@:2}"; then
    print_success_frame "Containers built successfully!"

    print_loading_frame "Checking container statuses"
    $COMPOSE_CMD ps

    print_success_frame "All services are running!"
else
    print_error_frame "Failed to build containers!"
    exit 1
fi
