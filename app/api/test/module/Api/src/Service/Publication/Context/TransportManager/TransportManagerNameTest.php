<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Context\TransportManager;

use Dvsa\Olcs\Api\Service\Publication\Context\TransportManager\TransportManagerName;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Entity\Person\Person as PersonEntity;
use Dvsa\Olcs\Api\Domain\QueryHandler\QueryHandlerInterface;

/**
 * Class TransportManagerNameTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class TransportManagerNameTest extends MockeryTestCase
{
    /**
     * @group publicationFilter
     *
     * Test the transport manager name filter
     */
    public function testProvide()
    {
        $title = 'title';
        $forename = 'forename';
        $familyName = 'family name';

        $output = [
            'transportManagerName' => $title . ' ' . $forename . ' ' . $familyName
        ];

        $expectedOutput = new \ArrayObject($output);

        $mockPerson = m::mock(PersonEntity::class);
        $mockPerson->shouldReceive('getTitle->getDescription')->once()->andReturn($title);
        $mockPerson->shouldReceive('getForename')->once()->andReturn($forename);
        $mockPerson->shouldReceive('getFamilyName')->once()->andReturn($familyName);

        $publication = m::mock(PublicationLink::class);
        $publication->shouldReceive('getTransportManager->getHomeCd->getPerson')->andReturn($mockPerson);

        $sut = new TransportManagerName(m::mock(QueryHandlerInterface::class));
        $this->assertEquals($expectedOutput, $sut->provide($publication, new \ArrayObject()));
    }
}
