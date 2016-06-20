<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Context\TransportManager;

use Dvsa\Olcs\Api\Service\Publication\Context\TransportManager\Person as Sut;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Entity\Person\Person as PersonEntity;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager as TmEntity;

/**
 * Class PersonTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PersonTest extends MockeryTestCase
{
    /**
     * @group publicationFilter
     *
     * Test the transport manager name filter
     */
    public function testProvideWithTitle()
    {
        $id = 66;

        $mockPerson = m::mock(PersonEntity::class);
        $mockPerson->shouldReceive('getId')->once()->andReturn($id);

        $output = [
            'tmPeople' => [
                $id => $mockPerson
            ]
        ];

        $expectedOutput = new \ArrayObject($output);

        $mockTm = m::mock(TmEntity::class);
        $mockTm->shouldReceive('getHomeCd->getPerson')->once()->andReturn($mockPerson);

        $publication = m::mock(PublicationLink::class);
        $publication->shouldReceive('getTransportManager')->once()->andReturn($mockTm);

        $sut = new Sut(m::mock(\Dvsa\Olcs\Api\Domain\QueryHandlerManager::class));
        $this->assertEquals($expectedOutput, $sut->provide($publication, new \ArrayObject()));
    }
}
