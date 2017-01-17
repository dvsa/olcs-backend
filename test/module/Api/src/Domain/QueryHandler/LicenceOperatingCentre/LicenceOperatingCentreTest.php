<?php

/**
 * Licence Operating Centre Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\LicenceOperatingCentre;

use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\LicenceOperatingCentre\LicenceOperatingCentre;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Transfer\Query\LicenceOperatingCentre\LicenceOperatingCentre as Qry;
use Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre as LicenceOperatingCentreEntity;
use ZfcRbac\Service\AuthorizationService;

/**
 * Licence Operating Centre Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceOperatingCentreTest extends QueryHandlerTestCase
{
    protected $expectedBundle = [
        'operatingCentre' => [
            'address' => [
                'countryCode'
            ],
            'adDocuments'
        ]
    ];

    public function setUp()
    {
        $this->sut = new LicenceOperatingCentre();
        $this->mockRepo('LicenceOperatingCentre', \Dvsa\Olcs\Api\Domain\Repository\LicenceOperatingCentre::class);

        $this->mockedSmServices[AuthorizationService::class] = m::mock(AuthorizationService::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 111]);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->shouldReceive('isPsv')->with()->once()->andReturn(true);
        $licence->shouldReceive('getNiFlag')->with()->once()->andReturn('Y');

        /** @var LicenceOperatingCentreEntity $loc */
        $loc = m::mock(LicenceOperatingCentreEntity::class)->makePartial();
        $loc->setLicence($licence);

        $loc->shouldReceive('serialize')
            ->with($this->expectedBundle)
            ->andReturn(['foo' => 'bar']);

        $this->repoMap['LicenceOperatingCentre']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($loc);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::INTERNAL_USER, null)
            ->andReturn(true);

        $result = $this->sut->handleQuery($query);

        $expected = [
            'foo' => 'bar',
            'isPsv' => true,
            'canUpdateAddress' => true,
            'wouldIncreaseRequireAdditionalAdvertisement' => false,
            'currentVehiclesRequired' => null,
            'currentTrailersRequired' => null,
            'niFlag' => 'Y',
        ];

        $this->assertEquals($expected, $result->serialize());
    }
}
