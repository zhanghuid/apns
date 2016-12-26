<?php

/*
 * This file is part of the AppleApnPush package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Apple\ApnPush\Encoder;

use Apple\ApnPush\Model\Alert;
use Apple\ApnPush\Model\Aps;
use Apple\ApnPush\Model\Payload;

/**
 * The encoder for encode notification message to string for next send to Apple Push Notification Service
 */
class PayloadEncoder implements PayloadEncoderInterface
{
    /**
     * {@inheritdoc}
     */
    public function encode(Payload $payload) : string
    {
        $data = [
            'aps' => $this->convertApsToArray($payload->getAps()),
        ];

        $data = array_merge($payload->getCustomData(), $data);

        return json_encode($data);
    }

    /**
     * Convert APS data to array
     *
     * @param Aps $aps
     *
     * @return array
     */
    private function convertApsToArray(Aps $aps) : array
    {
        $data = [
            'alert' => $this->convertAlertToArray($aps->getAlert()),
        ];

        if ($aps->getSound()) {
            $data['sound'] = $aps->getSound();
        }

        if ($aps->getBadge()) {
            $data['badge'] = $aps->getBadge();
        }

        if ($aps->getCategory()) {
            $data['category'] = $aps->getCategory();
        }

        if ($aps->isContentAvailable()) {
            $data['content-available'] = 1;
        }

        if ($aps->getThreadId()) {
            $data['thread-id'] = $aps->getThreadId();
        }

        return $data;
    }

    /**
     * Convert alert object to array
     *
     * @param Alert $alert
     *
     * @return array
     */
    private function convertAlertToArray(Alert $alert) : array
    {
        $data = [];

        if ($alert->getBodyLocalized()->getKey()) {
            $data['loc-key'] = $alert->getBodyLocalized()->getKey();
            $data['loc-args'] = $alert->getBodyLocalized()->getArgs();
        } else {
            $data['body'] = $alert->getBody();
        }

        if ($alert->getTitleLocalized()->getKey()) {
            $data['title-loc-key'] = $alert->getTitleLocalized()->getKey();
            $data['title-loc-args'] = $alert->getTitleLocalized()->getArgs();
        } elseif ($alert->getTitle()) {
            $data['title'] = $alert->getTitle();
        }

        if ($alert->getLocalizedAction()->getKey()) {
            $data['action-loc-key'] = $alert->getLocalizedAction()->getKey();
        }

        if ($alert->getLaunchImage()) {
            $data['launch-image'] = $alert->getLaunchImage();
        }

        return $data;
    }
}
