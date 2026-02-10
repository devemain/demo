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

print_frame "Start Docker containers"

print_loading_frame "Running containers in the background [detach mode]"
if $COMPOSE_CMD up -d "${@:2}"; then
    print_success_frame "Containers started successfully!"

    print_loading_frame "Checking container statuses"
    $COMPOSE_CMD ps

    print_success_frame "All services are running!"
else
    print_error_frame "Failed to start containers!"
    exit 1
fi
