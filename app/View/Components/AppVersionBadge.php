<?php

declare(strict_types=1);

/**
 * 2026 DeveMain
 *
 * All rights reserved. For internal use only.
 * Unauthorized copying, modification, or distribution is prohibited.
 *
 * @author    DeveMain <devemain@gmail.com>
 * @copyright 2026 DeveMain
 * @license   PROPRIETARY
 *
 * @link      https://github.com/DeveMain
 */

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Displays the application version.
 * It reads the version from the package.json file and caches it for performance.
 */
class AppVersionBadge extends Component
{
    /**
     * The application version that will be displayed in the badge.
     */
    public string $version;

    /**
     * Creates a new instance.
     */
    public function __construct()
    {
        $this->version = $this->getAppVersion();
    }

    /**
     * Get the view / contents that represent the component.
     * Returns the view for rendering the app version badge component.
     *
     * @return View|Closure|string The view instance or a closure/string that represents the component view.
     */
    public function render(): View|Closure|string
    {
        return view('components.app-version-badge');
    }

    /**
     * Get the current application version from package.json.
     * Implements a static cache to avoid reading the file on every request.
     * Falls back to '1.0.0' if the file doesn't exist or version is not specified.
     *
     * @return string The application version number.
     */
    protected function getAppVersion(): string
    {
        // Static cache variable to store version across multiple calls
        static $cachedVersion = null;
        if ($cachedVersion !== null) {
            return $cachedVersion;
        }

        $packageJsonPath = base_path('package.json');
        if (!file_exists($packageJsonPath)) {
            return '1.0.0';
        }

        $packageJson = json_decode(file_get_contents($packageJsonPath), true);

        return $cachedVersion = $packageJson['version'] ?? '1.0.0';
    }
}
