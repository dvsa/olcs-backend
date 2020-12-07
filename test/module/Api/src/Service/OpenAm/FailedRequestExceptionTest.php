<?php

namespace Dvsa\OlcsTest\Api\Service;

use Dvsa\Olcs\Api\Service\OpenAm\FailedRequestException;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * @covers Dvsa\Olcs\Api\Service\OpenAm\FailedRequestException
 */
class FailedRequestExceptionTest extends MockeryTestCase
{
    public function testGet()
    {
        /** @var \Laminas\Http\Response | m\MockInterface $mockResp */
        $mockResp = m::mock(\Laminas\Http\Response::class);
        $mockResp->shouldReceive('getContent')->once()->andReturn('unit_RespCtx');

        $mockExp = m::mock(\Exception::class);

        $sut = new FailedRequestException($mockResp, 999, $mockExp);

        static::assertEquals('Invalid response from OpenAm service: unit_RespCtx', $sut->getMessage());
        static::assertEquals(999, $sut->getCode());
        static::assertSame($mockExp, $sut->getPrevious());

        static::assertSame($mockResp, $sut->getResponse());
    }
}
