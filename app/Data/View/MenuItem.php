<?php
/**
 * 2026 DeveMain
 *
 * All rights reserved. For internal use only.
 * Unauthorized copying, modification, or distribution is prohibited.
 *
 * @author    DeveMain <devemain@gmail.com>
 * @copyright 2026 DeveMain
 * @license   PROPRIETARY
 * @link      https://github.com/DeveMain
 */

namespace App\Data\View;

use InvalidArgumentException;

/**
 * Represents a menu item in a navigation system.
 */
class MenuItem
{
    /**
     * Creates a new instance.
     *
     * @param string $uri The URI for the menu item
     * @param string $title The display title of the menu item
     * @param string $icon The icon associated with the menu item
     * @param bool $active Whether the menu item is currently active (default: false)
     * @param array $children Array of child menu items (default: empty array)
     */
    public function __construct(
        public string $uri,
        public string $title,
        public string $icon,
        public bool $active = false,
        public array $children = [],
    ) {
        // Normalize the URI by removing leading slashes
        $this->uri = $this->normalizeUri($uri);
    }

    /**
     * Magic getter method for accessing properties.
     *
     * @param string $name The name of the property to get
     * @return string The value of the property
     * @throws InvalidArgumentException If the property doesn't exist
     */
    public function __get(string $name): string
    {
        return match($name) {
            'url' => $this->getUrl(), // Special handling for 'url' property
            default => throw new InvalidArgumentException(
                'Property ' . $name . ' does not exist in ' . static::class
            )
        };
    }

    /**
     * Magic isset method for checking property existence.
     *
     * @param string $name The name of the property to check
     * @return bool True if 'url' property exists, false otherwise
     */
    public function __isset(string $name): bool
    {
        return $name === 'url';
    }

    /**
     * Static factory method to create a new MenuItem instance.
     *
     * @param string $uri The URI for the menu item
     * @param string $title The display title of the menu item
     * @param string $icon The icon associated with the menu item
     * @param bool $active Whether the menu item is currently active (default: false)
     * @return self A new instance of MenuItem
     */
    public static function make(
        string $uri,
        string $title,
        string $icon,
        bool $active = false
    ): self {
        return new self($uri, $title, $icon, $active);
    }

    /**
     * Adds a child menu item to this menu item.
     *
     * @param self $child The child menu item to add
     * @return self This menu item for method chaining
     */
    public function addChild(self $child): self
    {
        $this->children[] = $child;
        return $this;
    }

    /**
     * Checks if this menu item has any children.
     *
     * @return bool True if the menu item has children, false otherwise
     */
    public function hasChildren(): bool
    {
        return !empty($this->children);
    }

    /**
     * Gets the full URL for this menu item.
     *
     * @return string The fully qualified URL
     */
    public function getUrl(): string
    {
        return url($this->uri);
    }

    /**
     * Normalizes a URI by removing leading slashes.
     *
     * @param string $uri The URI to normalize
     * @return string The normalized URI
     */
    private function normalizeUri(string $uri): string
    {
        return ltrim($uri, '/');
    }
}
