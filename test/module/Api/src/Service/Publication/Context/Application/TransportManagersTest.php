<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Context\Application;

use Dvsa\Olcs\Api\Service\Publication\Context\Application\TransportManagers;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Domain\QueryHandler\QueryHandlerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication as TransportManagerApplicationEntity;

/**
 * Class TransportManagersTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class TransportManagersTest extends MockeryTestCase
{
    /**
     * @group publicationFilter
     *
     * Test the application transport managers filter
     */
    public function testProvide()
    {
        $forename = 'forename';
        $familyName = 'family name';

        $tm1 = m::mock(TransportManagerApplicationEntity::class);
        $tm1->shouldReceive('getTransportManager->getHomeCd->getPerson->getForename')->andReturn($forename);
        $tm1->shouldReceive('getTransportManager->getHomeCd->getPerson->getFamilyName')->andReturn($familyName);

        $transportManagers = new ArrayCollection();
        $transportManagers->add($tm1);

        $publication = m::mock(PublicationLink::class);
        $publication->shouldReceive('getApplication->getTransportManagers')->andReturn($transportManagers);

        $sut = new TransportManagers(m::mock(QueryHandlerInterface::class));

        $output = [
            'transportManagers' => $forename. ' ' . $familyName
        ];

        $expectedOutput = new \ArrayObject($output);

        $this->assertEquals($expectedOutput, $sut->provide($publication, new \ArrayObject()));
    }
}
