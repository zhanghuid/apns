<?php

/*
 * This file is part of the AppleApnPush package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Tests\Apple\ApnPush\Sender\Builder;

use Apple\ApnPush\Encoder\PayloadEncoderInterface;
use Apple\ApnPush\Protocol\Http\Authenticator\AuthenticatorInterface;
use Apple\ApnPush\Protocol\Http\ExceptionFactory\ExceptionFactoryInterface;
use Apple\ApnPush\Protocol\Http\Sender\HttpSenderInterface;
use Apple\ApnPush\Protocol\Http\UriFactory\UriFactoryInterface;
use Apple\ApnPush\Protocol\Http\Visitor\AddApnIdHeaderVisitor;
use Apple\ApnPush\Protocol\Http\Visitor\AddExpirationHeaderVisitor;
use Apple\ApnPush\Protocol\Http\Visitor\AddPriorityHeaderVisitor;
use Apple\ApnPush\Protocol\Http\Visitor\HttpProtocolChainVisitor;
use Apple\ApnPush\Protocol\Http\Visitor\HttpProtocolVisitorInterface;
use Apple\ApnPush\Protocol\HttpProtocol;
use Apple\ApnPush\Sender\Builder\Http20Builder;
use Apple\ApnPush\Sender\Sender;
use PHPUnit\Framework\TestCase;

class Http20BuilderTest extends TestCase
{
    /**
     * @test
     */
    public function shouldSuccessBuild()
    {
        $authenticator = self::createMock(AuthenticatorInterface::class);
        $builder = new Http20Builder($authenticator);

        $exceptionFactory = self::createMock(ExceptionFactoryInterface::class);
        $httpSender = self::createMock(HttpSenderInterface::class);
        $messageEncoder = self::createMock(PayloadEncoderInterface::class);
        $uriFactory = self::createMock(UriFactoryInterface::class);
        $visitor = self::createMock(HttpProtocolVisitorInterface::class);

        $chainVisitor = new HttpProtocolChainVisitor();
        $chainVisitor->add(new AddExpirationHeaderVisitor(), 1);
        $chainVisitor->add(new AddPriorityHeaderVisitor(), 2);
        $chainVisitor->add(new AddApnIdHeaderVisitor(), 3);
        $chainVisitor->add($visitor, 4);

        $builder
            ->setAuthenticator($authenticator)
            ->setExceptionFactory($exceptionFactory)
            ->setHttpSender($httpSender)
            ->setPayloadEncoder($messageEncoder)
            ->setUriFactory($uriFactory)
            ->addDefaultVisitors()
            ->addVisitor($visitor);

        $sender = $builder->build();

        $expectedProtocol = new HttpProtocol(
            $authenticator,
            $httpSender,
            $messageEncoder,
            $uriFactory,
            $chainVisitor,
            $exceptionFactory
        );

        $expectedSender = new Sender($expectedProtocol);

        self::assertInstanceOf(Sender::class, $sender);
        self::assertEquals($expectedSender, $sender);
    }
}
