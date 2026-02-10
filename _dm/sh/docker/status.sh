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

print_frame "Show Docker container status"

print_loading_frame "Current status"
$COMPOSE_CMD ps

echo
print_loading_frame "Recent logs"
$COMPOSE_CMD logs --tail=10
