<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Context\PiHearing;

use Dvsa\Olcs\Api\Service\Publication\Context\PiHearing\PiVenue;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Entity\Person\Person as PersonEntity;
use Dvsa\Olcs\Api\Entity\Pi\PiVenue as PiVenueEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address as AddressEntity;
use Dvsa\Olcs\Api\Service\Helper\FormatAddress;
use Dvsa\Olcs\Api\Domain\QueryHandler\QueryHandlerInterface;

/**
 * Class PiVenueTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PiVenueTest extends MockeryTestCase
{
    /**
     * @group publicationFilter
     *
     * Test the pi venue filter
     */
    public function testProvide()
    {
        $venueAddress = 'venue address';
        $venueName = 'venue name';
        $piVenueId = 99;

        $input = [
            'piVenue' => $piVenueId
        ];

        $output = [
            'piVenue' => $piVenueId,
            'piVenueOther' => $venueName  . ', ' . $venueAddress
        ];

        $expectedOutput = new \ArrayObject($output);

        $addressEntityMock = m::mock(AddressEntity::class);

        $piVenueMock = m::mock(PiVenueEntity::class);
        $piVenueMock->shouldReceive('getName')->once()->andReturn($venueName);
        $piVenueMock->shouldReceive('getAddress')->once()->andReturn($addressEntityMock);

        $mockQueryHandler = m::mock(QueryHandlerInterface::class);
        $mockQueryHandler->shouldReceive('handleQuery')->once()->andReturn($piVenueMock);

        $mockAddressFormatter = m::mock(FormatAddress::class);
        $mockAddressFormatter->shouldReceive('format')->once()->andReturn($venueAddress);

        $sut = new PiVenue($mockQueryHandler);
        $sut->setAddressFormatter($mockAddressFormatter);

        $this->assertEquals($expectedOutput, $sut->provide(new PublicationLink(), new \ArrayObject($input)));
    }
}
