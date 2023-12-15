<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Context\Bus;

use Dvsa\Olcs\Api\Service\Publication\Context\BusReg\ServiceDesignation;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusRegOtherService;

/**
 * Class ServiceDesignationTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class ServiceDesignationTest extends MockeryTestCase
{
    /**
     * @group publicationFilter
     *
     * Test the bus reg service designation filter
     */
    public function testProvide()
    {
        $serviceNo = 12345;
        $otherServiceNo = 67890;

        $otherServices = new ArrayCollection();

        $busReg = m::mock(BusRegEntity::class);
        $busReg->shouldReceive('getServiceNo')->andReturn($serviceNo);
        $busReg->shouldReceive('getOtherServices')->andReturn($otherServices);

        $otherService = new BusRegOtherService($busReg, $otherServiceNo);
        $otherServices->add($otherService);

        $publication = m::mock(PublicationLink::class);
        $publication->shouldReceive('getBusReg')->andReturn($busReg);

        $sut = new ServiceDesignation(m::mock(\Dvsa\Olcs\Api\Domain\QueryHandlerManager::class));

        $output = [
            'busServices' => $serviceNo . ' / ' . $otherServiceNo
        ];

        $expectedOutput = new \ArrayObject($output);

        $this->assertEquals($expectedOutput, $sut->provide($publication, new \ArrayObject()));
    }
}
