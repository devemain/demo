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

namespace App\Data\View;

use InvalidArgumentException;

class MenuItem
{
    public function __construct(
        public string $uri,
        public string $title,
        public string $icon,
        public bool $active = false,
        public array $children = [],
    ) {
        $this->uri = $this->normalizeUri($uri);
    }

    public function __get(string $name)
    {
        return match($name) {
            'url' => $this->getUrl(),
            default => throw new InvalidArgumentException(
                'Property ' . $name . ' does not exist in ' . static::class
            )
        };
    }

    public function __isset(string $name): bool
    {
        return $name === 'url';
    }

    public static function make(
        string $uri,
        string $title,
        string $icon,
        bool $active = false
    ): self {
        return new self($uri, $title, $icon, $active);
    }

    public function addChild(self $child): self
    {
        $this->children[] = $child;
        return $this;
    }

    public function hasChildren(): bool
    {
        return !empty($this->children);
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getUrl(): string
    {
        return url($this->uri);
    }

    private function normalizeUri(string $uri): string
    {
        return ltrim($uri, '/');
    }
}
