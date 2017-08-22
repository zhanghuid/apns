<?php

declare(strict_types = 1);

/*
 * This file is part of the AppleApnPush package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Apple\ApnPush\Exception\SendNotification;

/**
 * Too many requests were made consecutively to the same device token.
 */
class TooManyRequestsException extends SendNotificationException
{
    /**
     * Constructor.
     *
     * @param string $message
     */
    public function __construct(string $message = 'Too many requests.')
    {
        parent::__construct($message);
    }
}
