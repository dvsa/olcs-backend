<?php

/**
 * Type Of Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Variation;

use Dvsa\Olcs\Api\Entity\Application\Application;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Variation\TypeOfLicence;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Transfer\Query\Variation\TypeOfLicence as Qry;
use ZfcRbac\Service\AuthorizationService;

/**
 * Type Of Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TypeOfLicenceTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new TypeOfLicence();
        $this->mockRepo('Application', ApplicationRepo::class);

        $this->mockedSmServices[AuthorizationService::class] = m::mock(AuthorizationService::class);

        parent::setUp();
    }

    /**
     * @dataProvider dpHandleQuery
     */
    public function testHandleQuery($canUpdateLicenceGranted, $vehicleTypeId, $expectedCanUpdateLicenceType)
    {
        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->shouldReceive('canBecomeSpecialRestricted')
            ->once()
            ->andReturn(true);
        $licence->shouldReceive('canBecomeStandardInternational')
            ->withNoArgs()
            ->once()
            ->andReturn(true);

        $licence->shouldReceive('getLicenceType->getId')
            ->andReturn('curLicType');

        $licence->shouldReceive('getVehicleType->getId')
            ->withNoArgs()
            ->andReturn($vehicleTypeId);

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial()
            ->shouldReceive('serialize')
            ->once()
            ->andReturn(['foo' => 'bar'])
            ->getMock();

        $application->setLicence($licence);

        $query = Qry::create(['id' => 111]);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->once()
            ->with($query)
            ->andReturn($application);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->once()
            ->with(Permission::CAN_UPDATE_LICENCE_LICENCE_TYPE, $licence)
            ->andReturn($canUpdateLicenceGranted);

        $expected = [
            'foo' => 'bar',
            'canBecomeSpecialRestricted' => true,
            'canBecomeStandardInternational' => true,
            'canUpdateLicenceType' => $expectedCanUpdateLicenceType,
            'currentLicenceType' => 'curLicType'
        ];

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($expected, $result->serialize());
    }

    public function dpHandleQuery()
    {
        return [
            [false, RefData::APP_VEHICLE_TYPE_PSV, false],
            [false, RefData::APP_VEHICLE_TYPE_HGV, false],
            [false, RefData::APP_VEHICLE_TYPE_MIXED, false],
            [false, RefData::APP_VEHICLE_TYPE_LGV, false],
            [true, RefData::APP_VEHICLE_TYPE_PSV, true],
            [true, RefData::APP_VEHICLE_TYPE_HGV, true],
            [true, RefData::APP_VEHICLE_TYPE_MIXED, false],
            [true, RefData::APP_VEHICLE_TYPE_LGV, false],
        ];
    }
}
