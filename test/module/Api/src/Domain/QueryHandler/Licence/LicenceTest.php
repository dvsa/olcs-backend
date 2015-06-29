<?php

/**
 * Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Licence;

use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Licence\Licence;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Transfer\Query\Licence\Licence as Qry;
use ZfcRbac\Service\AuthorizationService;

/**
 * Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Licence();
        $this->mockRepo('Licence', LicenceRepo::class);

        $this->mockedSmServices['SectionConfig'] = m::mock();
        $this->mockedSmServices['SectionAccessService'] = m::mock();
        $this->mockedSmServices[AuthorizationService::class] = m::mock(AuthorizationService::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 111]);

        /** @var RefData $goodsOrPsv */
        $goodsOrPsv = m::mock(RefData::class)->makePartial();
        $goodsOrPsv->setId(LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE);

        /** @var RefData $licenceType */
        $licenceType = m::mock(RefData::class)->makePartial();
        $licenceType->setId(LicenceEntity::LICENCE_TYPE_STANDARD_NATIONAL);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setGoodsOrPsv($goodsOrPsv);
        $licence->setLicenceType($licenceType);
        $licence->shouldReceive('hasApprovedUnfulfilledConditions')
            ->andReturn(false)
            ->shouldReceive('serialize')
            ->with([])
            ->andReturn(['foo' => 'bar']);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::INTERNAL_USER, null)
            ->andReturn(false);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($licence);

        $sections = [
            'fooBar' => 'foo',
            'barFoo' => 'bar'
        ];

        $this->mockedSmServices['SectionConfig']
            ->shouldReceive('getAll')
            ->andReturn($sections);

        $expectedAccess = [
            'external',
            'licence',
            LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE,
            LicenceEntity::LICENCE_TYPE_STANDARD_NATIONAL,
            'noConditions'
        ];

        $this->mockedSmServices['SectionAccessService']->shouldReceive('setSections')
            ->once()
            ->with($sections)
            ->andReturnSelf()
            ->shouldReceive('getAccessibleSections')
            ->once()
            ->with($expectedAccess)
            ->andReturn(['bar', 'cake']);

        $result = $this->sut->handleQuery($query);

        $expected = [
            'foo' => 'bar',
            'sections' => ['bar', 'cake']
        ];

        $this->assertEquals($expected, $result->serialize());
    }
}
