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

print_frame "Make executable files"

# Make executable function
make_executable() {
    local script_path="$1"

    print_frame_middle "Check executable file: $script_path"
    if [[ -f "$script_path" ]] && [[ ! -x "$script_path" ]]; then
        print_loading_frame "Making executable this file"
        chmod +x "$script_path"
        print_success_frame "Permissions set"
    else
        print_success_frame "OK"
    fi
}
make_executable "docker.sh"
