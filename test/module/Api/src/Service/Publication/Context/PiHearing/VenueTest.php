<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Context\PiHearing;

use Dvsa\Olcs\Api\Service\Publication\Context\PiHearing\Venue;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Entity\Venue as VenueEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address as AddressEntity;
use Dvsa\Olcs\Api\Service\Helper\FormatAddress;
use Dvsa\Olcs\Api\Domain\QueryHandler\QueryHandlerInterface;

/**
 * Class VenueTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class VenueTest extends MockeryTestCase
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
        $venueId = 99;

        $input = [
            'venue' => $venueId
        ];

        $output = [
            'venue' => $venueId,
            'venueOther' => $venueName  . ', ' . $venueAddress
        ];

        $expectedOutput = new \ArrayObject($output);

        $addressEntityMock = m::mock(AddressEntity::class);

        $venueMock = m::mock(VenueEntity::class);
        $venueMock->shouldReceive('getName')->once()->andReturn($venueName);
        $venueMock->shouldReceive('getAddress')->once()->andReturn($addressEntityMock);

        $mockQueryHandler = m::mock(QueryHandlerInterface::class);
        $mockQueryHandler->shouldReceive('handleQuery')->once()->andReturn($venueMock);

        $mockAddressFormatter = m::mock(FormatAddress::class);
        $mockAddressFormatter->shouldReceive('format')->once()->andReturn($venueAddress);

        $sut = new Venue($mockQueryHandler);
        $sut->setAddressFormatter($mockAddressFormatter);

        $this->assertEquals($expectedOutput, $sut->provide(new PublicationLink(), new \ArrayObject($input)));
    }
}
