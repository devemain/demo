<?php
/**
 * 2025 DeveMain
 *
 * All rights reserved. For internal use only.
 * Unauthorized copying, modification, or distribution is prohibited.
 *
 * @author    DeveMain <devemain@gmail.com>
 * @copyright 2025 DeveMain
 * @license   PROPRIETARY
 * @link      https://github.com/DeveMain
 */

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AppVersionBadge extends Component
{
    public string $version;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->version = $this->getAppVersion();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.app-version-badge');
    }

    protected function getAppVersion(): string
    {
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
