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

namespace App\Services\Logging;

use App\Services\Logging\Contracts\ChannelResolverInterface;

/**
 * Determines logging channels based on class namespace.
 * This follows the Single Responsibility Principle by focusing
 * solely on channel determination logic.
 */
class ChannelResolver implements ChannelResolverInterface
{
    /**
     * Default channel to use when no specific channel is determined.
     */
    protected string $defaultChannel = 'daily';

    /**
     * Mapping of namespace patterns to channel names.
     */
    protected array $channelMap = [
        '\\Api\\Admin\\' => 'api-admin',
        '\\Api\\V1\\' => 'api-v1',
        '\\Api\\' => 'api-v1',
        '\\Services\\' => 'service',
    ];

    /**
     * Determine the appropriate logging channel based on class namespace.
     *
     * @param string $class The fully qualified class name
     * @return string The logging channel name
     */
    public function resolveChannel(string $class): string
    {
        foreach ($this->channelMap as $pattern => $channel) {
            if (str_contains($class, $pattern)) {
                return $channel;
            }
        }

        return $this->defaultChannel;
    }

    /**
     * Set a default channel to use when no specific channel is determined.
     *
     * @param string $channel The default channel name
     * @return self
     */
    public function setDefaultChannel(string $channel): self
    {
        $this->defaultChannel = $channel;
        return $this;
    }
}
