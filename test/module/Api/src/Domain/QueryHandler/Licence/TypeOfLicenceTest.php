<?php

/**
 * Type Of Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Licence;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Licence\TypeOfLicence;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Transfer\Query\Licence\Licence as Qry;
use ZfcRbac\Service\AuthorizationService;

/**
 * Type Of Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TypeOfLicenceTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new TypeOfLicence();
        $this->mockRepo('Licence', LicenceRepo::class);

        $this->mockedSmServices[AuthorizationService::class] = m::mock(AuthorizationService::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->shouldReceive('jsonSerialize')
            ->once()
            ->andReturn(['foo' => 'bar'])
            ->shouldReceive('canBecomeSpecialRestricted')
            ->once()
            ->andReturn(true);

        $query = Qry::create(['id' => 111]);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->once()
            ->with($query)
            ->andReturn($licence);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->once()
            ->with(Permission::CAN_UPDATE_LICENCE_LICENCE_TYPE, $licence)
            ->andReturn(true)
            ->shouldReceive('isGranted')
            ->once()
            ->with(Permission::SELFSERVE_USER, null)
            ->andReturn(true);

        $expected = [
            'foo' => 'bar',
            'canBecomeSpecialRestricted' => true,
            'canUpdateLicenceType' => true,
            'doesChangeRequireVariation' => true
        ];

        $this->assertEquals($expected, $this->sut->handleQuery($query));
    }
}
