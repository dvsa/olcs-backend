<?php

namespace Dvsa\OlcsTest\Api\Service;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Service\Publication\Formatter\TransportManagers as Formatter;

/**
 * TransportManagersTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class TransportManagersTest extends MockeryTestCase
{
    public function setUp()
    {
    }

    public function testEmpty()
    {
        $this->assertNull(Formatter::format([]));
    }

    public function testTransportManagers()
    {
        $tm1 = m::mock(\Dvsa\Olcs\Api\Entity\Tm\TransportManager::class);
        $tm1->shouldReceive('getHomeCd->getPerson->getFullName')->with()->once()->andReturn('Dave Jones');
        $tm2 = m::mock(\Dvsa\Olcs\Api\Entity\Tm\TransportManager::class);
        $tm2->shouldReceive('getHomeCd->getPerson->getFullName')->with()->once()->andReturn('Shirley Basey');

        $this->assertSame('Transport Manager(s): Dave Jones, Shirley Basey', Formatter::format([$tm1, $tm2]));
    }
}
