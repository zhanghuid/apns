<?php

/*
 * This file is part of the AppleApnPush package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Apple\ApnPush\Protocol\Http\Visitor;

use Apple\ApnPush\Model\Message;
use Apple\ApnPush\Protocol\Http\Request;

/**
 * Chain visitor for visit for message and request before send request
 */
class HttpProtocolChainVisitor implements HttpProtocolVisitorInterface
{
    /**
     * @var \SplPriorityQueue|HttpProtocolVisitorInterface[]
     */
    private $visitors;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->visitors = new \SplPriorityQueue();
    }

    /**
     * Add visitor to chain
     *
     * @param HttpProtocolVisitorInterface $visitor
     * @param int                          $priority
     */
    public function add(HttpProtocolVisitorInterface $visitor, int $priority = 0)
    {
        $this->visitors->insert($visitor, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function visit(Message $message, Request $request) : Request
    {
        // Clone all visitors because \SplPriorityQueue remove object after iteration
        $visitors = clone $this->visitors;

        foreach ($visitors as $visitor) {
            $request = $visitor->visit($message, $request);
        }

        return $request;
    }
}
