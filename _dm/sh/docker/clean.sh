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

print_frame "Clean Docker containers and volumes"

print_warning_frame "This will remove:"
print_frame_middle "- All stopped containers"
print_frame_middle "- All unused networks"
print_frame_middle "- All unused images"
print_frame_middle "- All unused volumes"

read -p "$(echo -e "${YELLOW}Are you sure? (Y/N): ${RESET_COLOR}")" -n 1 -r
echo

if [[ $REPLY =~ ^[Yy]$ ]]; then
    print_loading_frame "Cleaning containers"
    $COMPOSE_CMD down --rmi all --volumes --remove-orphans

    print_loading_frame "Pruning system"
    docker system prune -f

    print_success_frame "Docker environment cleaned successfully!"
else
    print_info_frame "Clean operation cancelled"
fi
