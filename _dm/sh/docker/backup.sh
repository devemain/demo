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

print_frame "Backup Docker volumes and data"

TIMESTAMP=$(date +%Y%m%d_%H%M%S)
mkdir -p "_docker_backups"
#sudo rm -f "_docker_backups"/*.tar.gz 2>/dev/null || true && exit

# Backup Docker volumes
print_loading_frame "Backing up volumes"
docker run --rm --user $(id -u):$(id -g) \
    -v $(pwd)/_docker_backups:/backup \
    -v app_data:/data \
    alpine \
    tar czf /backup/volumes_$TIMESTAMP.tar.gz -C /data .

# Backup docker-compose files
print_loading_frame "Backing up configuration"
tar czf _docker_backups/config_$TIMESTAMP.tar.gz docker-compose.yml .env* 2>/dev/null || true

print_success_frame "Backup completed: _docker_backups/volumes_$TIMESTAMP.tar.gz"
