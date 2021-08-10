<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Test\Service\OpenAm;

use Dvsa\Olcs\Auth\Service\OpenAm\Callback\CallbackInterface;
use Dvsa\Olcs\Auth\Service\OpenAm\Callback\Request;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class RequestTest extends MockeryTestCase
{
    public function testRequest(): void
    {
        $callback1 = m::mock(CallbackInterface::class);
        $callback1->shouldReceive('toArray')->andReturn(['callback1' => 'stuff']);
        $callback2 = m::mock(CallbackInterface::class);
        $callback2->shouldReceive('toArray')->andReturn(['callback2' => 'stuff']);

        $callbacks = [$callback1];

        $sut = new Request('some-auth-token', 'some-stage', $callbacks);
        $sut->addCallback($callback2);

        $result = $sut->toArray();
        $expected = [
            'authId' => 'some-auth-token',
            'stage' => 'some-stage',
            'callbacks' => [
                ['callback1' => 'stuff'],
                ['callback2' => 'stuff']
            ]
        ];

        $this->assertEquals($expected, $result);
    }
}
