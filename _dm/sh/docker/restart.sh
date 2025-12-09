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

print_frame "Restart Docker containers"

print_loading_frame "Restarting containers"
if $COMPOSE_CMD restart; then
    print_success_frame "Containers restarted successfully!"

    print_loading_frame "Checking container statuses"
    $COMPOSE_CMD ps
else
    print_error_frame "Failed to restart containers!"
    exit 1
fi
