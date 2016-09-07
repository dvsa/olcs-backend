<?php

namespace Dvsa\OlcsTest\Api\Service;

use Dvsa\Olcs\Api\Service\CpmsResponseException;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * @covers Dvsa\Olcs\Api\Service\CpmsResponseException
 */
class CpmsResponseExceptionTest extends MockeryTestCase
{
    public function testSetGet()
    {
        $mockResp = m::mock(\Zend\Http\Response::class);

        $sut = new CpmsResponseException();
        $sut->setResponse($mockResp);

        static::assertSame($mockResp, $sut->getResponse());
    }
}
