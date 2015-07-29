<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Context\Bus;

use Dvsa\Olcs\Api\Service\Publication\Context\Licence\LicenceAddress;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Domain\QueryHandler\QueryHandlerInterface;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address as AddressEntity;
use Dvsa\Olcs\Api\Service\Helper\FormatAddress;

/**
 * Class LicenceAddressTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class LicenceAddressTest extends MockeryTestCase
{
    /**
     * @group publicationFilter
     *
     * Test the licence address filter
     */
    public function testProvide()
    {
        $licenceAddress = 'licence address';

        $addressEntityMock = m::mock(AddressEntity::class);
        $contactDetailsEntityMock = m::mock(ContactDetailsEntity::class);
        $contactDetailsEntityMock->shouldReceive('getAddress')->once()->andReturn($addressEntityMock);

        $publication = m::mock(PublicationLink::class);
        $publication->shouldReceive('getLicence->getCorrespondenceCd')->once()->andReturn($contactDetailsEntityMock);

        $mockAddressFormatter = m::mock(FormatAddress::class);
        $mockAddressFormatter->shouldReceive('format')->once()->andReturn($licenceAddress);

        $sut = new LicenceAddress(m::mock(QueryHandlerInterface::class));
        $sut->setAddressFormatter($mockAddressFormatter);

        $output = [
            'licenceAddress' => $licenceAddress
        ];

        $expectedOutput = new \ArrayObject($output);

        $this->assertEquals($expectedOutput, $sut->provide($publication, new \ArrayObject()));
    }

    /**
     * @group publicationFilter
     *
     * Test the licence address filter
     */
    public function testProvideWithNoAddress()
    {
        $publication = m::mock(PublicationLink::class);
        $publication->shouldReceive('getLicence->getCorrespondenceCd')->andReturn(null);

        $sut = new LicenceAddress(m::mock(QueryHandlerInterface::class));

        $expectedOutput = new \ArrayObject();

        $this->assertEquals($expectedOutput, $sut->provide($publication, new \ArrayObject()));
    }
}
