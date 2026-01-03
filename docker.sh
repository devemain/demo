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
# @link      https://github.com/DeveMain
# ============================================================================

# ./docker.sh - Unified Docker management script
# chmod +x docker.sh

# Define the working directory
WORK_DIR="$(dirname "$(realpath "${BASH_SOURCE[0]}")")/_dm"
SH_DIR="$WORK_DIR/sh"

# Check if a directory exists
if [[ ! -d "$SH_DIR/docker" ]]; then
    echo "✘ Docker scripts directory not found: $SH_DIR/docker" && exit 1
fi

# Include common file
source "$SH_DIR/common.sh"

# Check commands
COMMAND="$1"
[[ "$COMMAND" =~ ^(-h|--help)$ ]] && print_usage_docker && exit 0

# Check file existence
SCRIPT_PATH="$SH_DIR/docker/$COMMAND.sh"
if [[ ! -f "$SCRIPT_PATH" ]]; then
  print_error "Unknown command: $COMMAND" && print_usage_docker && exit 1
fi

# Docker helpers
check_docker() {
    if ! command -v docker &>/dev/null; then
        print_error "Docker not found! Please install Docker first."
        exit 1
    fi
}
check_docker_compose() {
    if ! command -v docker-compose &>/dev/null && ! command -v docker compose &>/dev/null; then
        print_error "Docker Compose not found!"
        exit 1
    fi
}
get_compose_command() {
    if command -v docker-compose &>/dev/null; then
        echo "docker-compose"
    else
        echo "docker compose"
    fi
}
COMPOSE_CMD=$(get_compose_command)

# Check if Docker is installed
print_loading_frame "Checking Docker installation"
if [[ $(pwd) != "/var/www/html" ]]; then
    check_docker
    check_docker_compose
    print_success_frame "OK"
fi

# Include the script
source "$SCRIPT_PATH"

# Check environment port
if [ $COMMAND = "start" ]; then
    PORT=$($COMPOSE_CMD port app 80 2>/dev/null | cut -d: -f2)

    print_info_frame "App running at: http://localhost:$PORT"
fi
