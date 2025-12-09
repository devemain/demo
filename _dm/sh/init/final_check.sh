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

print_frame "Final check"

# Check project name
check_project_name() {
    print_frame_middle "Check project name"
    if ! grep -q '"name"' "package.json"; then
        if ask_input "Please enter the project name"; then
            print_loading_frame "Saving the project name"
            sed -i '1s/{/{\n    "name": "'"$REPLY"'",/' "package.json"
#            sed -i 's/^  "/    "/g' "package.json" # 2 spaces -> 4 spaces
            print_success_frame "Project name saved"
        fi
    else
        print_success_frame "OK"
    fi
}
check_project_name
