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

print_frame "Show Docker container logs"

print_loading_frame "Showing logs for all services"
$COMPOSE_CMD logs -f "${@:2}"
