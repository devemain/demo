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

print_frame "Stop Docker containers"

print_loading_frame "Stopping running containers"
if $COMPOSE_CMD down; then
    print_success_frame "Containers stopped successfully!"
else
    print_error_frame "Failed to stop containers!"
    exit 1
fi
