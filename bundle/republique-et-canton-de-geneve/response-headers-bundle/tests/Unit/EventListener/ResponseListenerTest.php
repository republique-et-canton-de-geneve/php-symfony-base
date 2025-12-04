<?php

namespace EtatGeneve\ResponseHeadersBundle\Tests\Unit\EventListener;

use EtatGeneve\ResponseHeadersBundle\EventListener\ResponseListener;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ResponseListenerTest extends TestCase
{


    private static function createResponseListener(array $headers): ResponseListener
    {
        return new ResponseListener($headers);
    }

    private function createResponseEvent(        bool $mainRequest = true    ): ResponseEvent {
        return new ResponseEvent(
            $this->createMock(HttpKernelInterface::class),
            new Request(),
            $mainRequest ? HttpKernelInterface::MAIN_REQUEST : HttpKernelInterface::SUB_REQUEST,
            new Response()
        );
    }


    public static function headersProvider(): array
    {
        return [
            [['a1' => ['value' => 'v1']]],
            [['a2' => ['value' => 'v2'], 'a3' => ['value' => 'v3']]],

        ];
    }

    #[DataProvider('headersProvider')]
    public function testHeaders(array $headers): void
    {
        $responseListener = new ResponseListener($headers);
        $responseEvent = $this->createResponseEvent();
        $responseListener->onKernelResponse($responseEvent);
        foreach ($headers as $name => $header) {
            $expectedValue = $header['value'];
            static::assertSame($expectedValue, $responseEvent->getResponse()->headers->get($name));
        }
    }

    public static function fullHeaderProvider(): array
    {
        return [
            [['a1' => ['value' => 'v1', 'condition' => 'true']], true,'v1'],
            [['a2' => ['value' => 'v2', 'condition' => 'false']], true,null]
        ];
    }

    #[DataProvider('fullHeaderProvider')]
    public function testFullHeaders(array $header, $mainRequest, $expectedValue): void
    {
        $name = key($header);
        $responseListener = new ResponseListener($header);
        $responseEvent = $this->createResponseEvent();
        $responseListener->onKernelResponse($responseEvent);
        static::assertSame($expectedValue, $responseEvent->getResponse()->headers->get($name));
    }
}
