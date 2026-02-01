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

namespace App\Services\Logging\Contracts;

/**
 * Interface for determining logging channels based on context.
 * This follows the Single Responsibility Principle by separating
 * channel determination logic from logging operations.
 */
interface ChannelResolverInterface
{
    /**
     * Determine the appropriate logging channel based on class namespace.
     *
     * @param string $class The fully qualified class name
     * @return string The logging channel name
     */
    public function resolveChannel(string $class): string;

    /**
     * Set a default channel to use when no specific channel is determined.
     *
     * @param string $channel The default channel name
     * @return self
     */
    public function setDefaultChannel(string $channel): self;
}
