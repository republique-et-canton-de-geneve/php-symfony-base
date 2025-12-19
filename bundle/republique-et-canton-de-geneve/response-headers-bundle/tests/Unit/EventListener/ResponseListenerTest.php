<?php

namespace EtatGeneve\ResponseHeadersBundle\Tests\Unit\EventListerner;

use EtatGeneve\ResponseHeadersBundle\EventListener\ResponseListener;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ResponseListenerTest extends TestCase
{
    private function createResponseEvent(bool $mainRequest = true): ResponseEvent
    {
        return new ResponseEvent(
            $this->createMock(HttpKernelInterface::class),
            new Request(),
            $mainRequest ? HttpKernelInterface::MAIN_REQUEST : HttpKernelInterface::SUB_REQUEST,
            new Response()
        );
    }

    /**
     * @return list<array<int,mixed>>
     **/
    public static function headersProvider(): array
    {
        return [
            [['a0' => ['value' => 0]], true, ['a0' => [0]]],
            [['a1' => ['value' => 'v1']], true, ['a1' => ['v1']]],
            [['a2' => ['value' => 'v2'], 'a3' => ['value' => 'v3']], true, ['a2' => ['v2'], 'a3' => ['v3']]],
            [['a3' => ['value' => 'v3', 'condition' => 'true']], true, ['a3' => ['v3']]],
            [['a4' => ['value' => 'v4', 'condition' => 'false']], true, ['a4' => []]],
            [['a5' => ['value' => 'v5', 'condition' => 'true']], false, ['a5' => []]],
            [['a6' => ['value' => 'v6', 'condition' => null]], true, ['a6' => ['v6']]],
            [['a7' => ['value' => ['a7', 'b7', 'c7']]], true, ['a7' => ['a7b7c7']]],
            [['a9' => []], true, ['a9' => [null]]],
            [['a10' => ['condition' => 'true']], true, ['a10' => [null]]],
            [['a11' => ['value' => 'v11', 'condition' => 'response.getStatusCode() == 200']], true, ['a11' => ['v11']]],
            [['a12' => ['value' => 'v12', 'condition' => 'response.getStatusCode() != 200']], true, ['a12' => []]],
            [['a13' => ['value' => 'v13', 'condition' => 'true'], 'a14' => ['value' => ['b1', 'b2', 'b3']]], true, ['a13' => ['v13'], 'a14' => ['b1b2b3']]],
            [['a20' => ['value' => 'v20', 'replace' => false]], true, ['a20' => ['v20']]],
            [['a21' => ['value' => 'v21', 'replace' => true]], true, ['a21' => ['v21']]],
            [['a22' => ['value' => ['a22', 'b22', 'c22'], 'format' => 'multiple']], true, ['a22' => ['a22', 'b22', 'c22']]],
            [['a23' => ['value' => ['a23', 'b23', 'c23'], 'format' => 'line']], true, ['a23' => ['a23b23c23']]],
        ];
    }

    /**
     * @param array<string,array{condition:string}|array{string:string|array<string>}> $headers
     * @param array<string,string>                                                     $expectedValues
     **/
    #[DataProvider('headersProvider')]
    public function testHeaders(array $headers, bool $mainRequest, array $expectedValues): void
    {
        $responseListener = new ResponseListener($headers);
        $responseEvent = $this->createResponseEvent($mainRequest);
        $responseListener->onKernelResponse($responseEvent);
        foreach ($headers as $name => $header) {
            static::assertSame($expectedValues[$name], $responseEvent->getResponse()->headers->all($name));
        }

        if (!$headers) {
            $this->expectNotToPerformAssertions();
        }
    }
}
