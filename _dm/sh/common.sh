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

# Exit on error
set -e

# Colors
RED='\e[0;31m'
RED_UNDERLINE='\e[4;31m'
GREEN='\e[0;32m'
GREEN_UNDERLINE='\e[4;32m'
YELLOW='\e[0;33m'
YELLOW_UNDERLINE='\e[4;33m'
BLUE='\e[0;34m'
BLUE_UNDERLINE='\e[4;34m'
MAGENTA='\e[0;35m'
MAGENTA_UNDERLINE='\e[4;35m'
CYAN='\e[0;36m'
CYAN_UNDERLINE='\e[4;36m'
WHITE='\e[0;37m'
WHITE_UNDERLINE='\e[4;37m'
RESET_COLOR='\e[0m'

# Functions for output
print_error() {
    echo -e "${RED}✘ $1${RESET_COLOR}"
}
print_error_frame() {
    printf "${RED}│   %-76s    │${RESET_COLOR}\n" "✘ $1"
}
print_success() {
    echo -e "${GREEN}✔ $1${RESET_COLOR}"
}
print_success_frame() {
    printf "${GREEN}│   %-76s    │${RESET_COLOR}\n" "✔ $1"
}
print_warning() {
    echo -e "${YELLOW}⚠ $1${RESET_COLOR}"
}
print_warning_frame() {
    printf "${YELLOW}│   %-76s    │${RESET_COLOR}\n" "⚠ $1"
}
print_info() {
    echo -e "${BLUE}ℹ $1${RESET_COLOR}"
}
print_info_frame() {
    printf "${BLUE}│   %-76s    │${RESET_COLOR}\n" "ℹ $1"
}
print_loading() {
    echo -e "${MAGENTA}⟳ $1...${RESET_COLOR}"
}
print_loading_frame() {
    printf "${MAGENTA}│   %-76s    │${RESET_COLOR}\n" "⟳ $1..."
}
print_frame() {
    local color="${2:-$CYAN}"
    print_frame_top "$color"
    print_frame_middle "$1" "$color"
    print_frame_bottom "$color"
}
print_frame_top() {
    printf "${1:-$CYAN}┌─────────────────────────────────────────────────────────────────────────────────┐${RESET_COLOR}\n"
}
print_frame_middle() {
    printf "${2:-$WHITE}│   %-74s    │${RESET_COLOR}\n" "$1"
}
print_frame_bottom() {
    printf "${1:-$CYAN}└─────────────────────────────────────────────────────────────────────────────────┘${RESET_COLOR}\n"
}
print_usage() {
    print_frame "Usage: $0 [OPTIONS] COMMAND [ARGUMENTS]"

    print_info_frame "OPTIONS:"
    print_frame_middle "-h, --help       - Show this help"
    print_frame_middle "-q, --quiet      - Disable interactive requests"
    print_frame_bottom
}
print_usage_docker() {
    print_frame "Usage: $0 [OPTIONS] COMMAND [ARGUMENTS]"

    print_info_frame "OPTIONS:"
    print_frame_middle "-h, --help       - Show this help"

    print_info_frame "COMMANDS:"
    print_frame_middle "start            - Start containers"
    print_frame_middle "stop             - Stop containers"
    print_frame_middle "restart          - Restart containers"
    print_frame_middle "clean            - Clean up containers and volumes"
    print_frame_middle "logs             - Show container logs"
    print_frame_middle "status           - Show container status"
    print_frame_bottom
}

# Function to ask yes/no question
ask_yes_no() {
    local question="$1"
    local default="${2:-n}"
    local prompt

    if [[ $default == "y" ]]; then
        prompt="[${YELLOW_UNDERLINE}Y${RESET_COLOR}${YELLOW}/n]"
    else
        prompt="[y/${YELLOW_UNDERLINE}N${RESET_COLOR}${YELLOW}]"
    fi

    read -p "$(echo -e "${YELLOW}${question} ${prompt}:${RESET_COLOR} ")" -n 1 -r
    echo

    case "$REPLY" in
        [Yy]) return 0 ;;
        [Nn]) return 1 ;;
        *)
            print_error "Please answer Y or N."
            ask_yes_no "$question" "$default"
            ;;
    esac
}

# Method for entering text
ask_input() {
    local question="$1"
    local default="${2:-}"
    local prompt

    if [[ -n "$default" ]]; then
        prompt=" [${YELLOW_UNDERLINE}${default}${RESET_COLOR}${YELLOW}]"
    fi

    read -p "$(echo -e "${YELLOW}${question}${prompt}:${RESET_COLOR} ")" -r REPLY

    if [[ -z "$REPLY" && -n "$default" ]]; then
        REPLY="$default"
    fi

    if [[ -n "$REPLY" ]]; then
        return 0
    else
        print_error "Please enter your details."
        ask_input "$question" "$default"
    fi
}

# Function for running PHP scripts
run_php_script() {
    local script="$1"
    local args="$2"

    print_frame "Running: php $script $args" "${MAGENTA}"
    print_loading "Processing"

    if php "$script" $args; then
        return 0
    else
        print_error "Failed with exit code: $?"
        return 1
    fi
}

# Load .env file
load_env() {
    local env_file=".env"
    if [[ -f "$env_file" ]]; then
        set -a
        source "$env_file"
        set +a
    fi
}
