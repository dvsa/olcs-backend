<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Context\Application;

use Dvsa\Olcs\Api\Service\Publication\Context\Application\OperatingCentres as OperatingCentresContext;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre as ApplicationOperatingCentreEntity;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address as AddressEntity;
use Dvsa\Olcs\Api\Service\Helper\FormatAddress;

/**
 * Class OperatingCentresTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class OperatingCentresTest extends MockeryTestCase
{
    /**
     * @group publicationFilter
     * @dataProvider provideTestProvider
     *
     * Test the application operating centres filter
     *
     * @param string $licType
     * @param int $totVehicles
     * @param int $totTrailers
     * @param string $vehicleOutput
     * @param string $action
     * @param string $prefix
     */
    public function testProvide($licType, $totVehicles, $totTrailers, $vehicleOutput, $action, $prefix)
    {
        $ocAddress = 'oc address';

        $addressEntityMock = m::mock(AddressEntity::class);

        $operatingCentre = m::mock(ApplicationOperatingCentreEntity::class);
        $operatingCentre->shouldReceive('getNoOfTrailersRequired')->andReturn($totTrailers);
        $operatingCentre->shouldReceive('getNoOfVehiclesRequired')->andReturn($totVehicles);
        $operatingCentre->shouldReceive('getOperatingCentre->getAddress')->andReturn($addressEntityMock);
        $operatingCentre->shouldReceive('getAction')->andReturn($action);

        $operatingCentres = new ArrayCollection([$operatingCentre]);

        $application = m::mock(ApplicationEntity::class);
        $application->shouldReceive('getGoodsOrPsv->getId')->andReturn($licType);
        $application->shouldReceive('getOperatingCentres')->andReturn($operatingCentres);

        $mockAddressFormatter = m::mock(FormatAddress::class);
        $mockAddressFormatter->shouldReceive('format')->andReturn($ocAddress);

        $publication = m::mock(PublicationLink::class);
        $publication->shouldReceive('getApplication')->andReturn($application);

        $sut = new OperatingCentresContext(m::mock(\Dvsa\Olcs\Api\Domain\QueryHandlerManager::class));
        $sut->setAddressFormatter($mockAddressFormatter);

        $output = [
            'operatingCentres' => [
                0 => trim($prefix . 'Operating Centre: ' . $ocAddress . ' ' . $vehicleOutput)
            ]
        ];

        $expectedOutput = new \ArrayObject($output);

        $this->assertEquals($expectedOutput, $sut->provide($publication, new \ArrayObject()));
    }

    /**
     * provideTest provider
     *
     * @return array
     */
    public function provideTestProvider()
    {
        return [
            [
                LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE,
                4,
                2,
                'Authorisation: 4 Vehicle(s) and 2 Trailer(s)',
                'U',
                'Update '
            ],
            [
                LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE,
                0,
                2,
                'Authorisation: 2 Trailer(s)',
                'D',
                'Remove '
            ],
            ['other_type', 3, 0, 'Authorisation: 3 Vehicle(s)', 'some_action', 'New '],
            ['other_type', 0, 0, '', 'some_action', 'New '],
        ];
    }
}
