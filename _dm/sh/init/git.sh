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

print_frame "Git Setup"

# Check if we're in a Git repository
print_frame_middle "Check Git repository"
if [ -d ".git" ]; then
    print_success_frame "OK"
else
    print_loading_frame "Initializing Git [git init]"
    git init
fi

# Install Git hook for Release Bot [git spush]
setup_git_aliases() {
    print_frame_middle "Setting up Git aliases"

    # Create a Git hook for Release Bot
    local script_path=".git/hooks/smart-push.sh"
    if [ -f "$script_path" ]; then
        print_success_frame "OK [git spush]"
    else
        print_loading_frame "Creating a Git hook for Release Bot"

        cat > "$script_path" << 'EOF'
#!/bin/bash
echo "⟳ Pushing changes..."
git push origin "$@"
if [ $? -eq 0 ]; then
    echo -e "\n⟳ Waiting for Release Bot..."
    sleep 60
    echo "⟳ Checking for updates..."
    git fetch origin
    if git diff HEAD origin/main --name-only 2>/dev/null | grep -q package.json; then
        echo "ℹ Updating CHANGELOG.md, package.json and package-lock.json..."
        git pull origin main
        echo "✔ Files updated"
    else
        echo "✔ No version changes"
    fi
fi
EOF
        # Making it executable
        chmod +x "$script_path"

        # Create an alias that calls the script
        git config alias.spush "!$script_path"

        print_success_frame "Added: git spush"
        print_success_frame "Location: $script_path"
    fi
}
setup_git_aliases
