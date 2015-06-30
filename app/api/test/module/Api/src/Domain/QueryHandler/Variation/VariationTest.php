<?php

/**
 * Variation Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Variation;

use Dvsa\Olcs\Api\Entity\Application\ApplicationCompletion;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Application\Application;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Transfer\Query\Application\Application as Qry;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use ZfcRbac\Service\AuthorizationService;

/**
 * Variation Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Application();
        $this->mockRepo('Application', ApplicationRepo::class);

        $this->mockedSmServices['SectionConfig'] = m::mock();
        $this->mockedSmServices['SectionAccessService'] = m::mock();
        $this->mockedSmServices[AuthorizationService::class] = m::mock(AuthorizationService::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 111]);

        $appCompletion = m::mock(ApplicationCompletion::class);

        /** @var RefData $goodsOrPsv */
        $goodsOrPsv = m::mock(RefData::class)->makePartial();
        $goodsOrPsv->setId(Licence::LICENCE_CATEGORY_GOODS_VEHICLE);

        /** @var RefData $licenceType */
        $licenceType = m::mock(RefData::class)->makePartial();
        $licenceType->setId(Licence::LICENCE_TYPE_STANDARD_NATIONAL);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->shouldReceive('hasApprovedUnfulfilledConditions')
            ->andReturn(true);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setApplicationCompletion($appCompletion);
        $application->setGoodsOrPsv($goodsOrPsv);
        $application->setLicenceType($licenceType);
        $application->setLicence($licence);
        $application->setIsVariation(true);
        $application->shouldReceive('serialize')
            ->with(['licence'])
            ->andReturn(['foo' => 'bar']);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::INTERNAL_USER, null)
            ->andReturn(false);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($application);

        $sections = [
            'fooBar' => 'foo',
            'barFoo' => 'bar'
        ];

        $this->mockedSmServices['SectionConfig']->shouldReceive('setVariationCompletion')
            ->once()
            ->with($appCompletion)
            ->shouldReceive('getAll')
            ->andReturn($sections);

        $expectedAccess = [
            'external',
            'variation',
            Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
            Licence::LICENCE_TYPE_STANDARD_NATIONAL,
            'hasConditions'
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

    public function testHandleQueryApplication()
    {
        $query = Qry::create(['id' => 111]);

        /** @var RefData $goodsOrPsv */
        $goodsOrPsv = m::mock(RefData::class)->makePartial();
        $goodsOrPsv->setId(Licence::LICENCE_CATEGORY_GOODS_VEHICLE);

        /** @var RefData $licenceType */
        $licenceType = m::mock(RefData::class)->makePartial();
        $licenceType->setId(Licence::LICENCE_TYPE_STANDARD_NATIONAL);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->shouldReceive('hasApprovedUnfulfilledConditions')
            ->andReturn(false);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setGoodsOrPsv($goodsOrPsv);
        $application->setLicenceType($licenceType);
        $application->setLicence($licence);
        $application->setIsVariation(false);
        $application->shouldReceive('serialize')
            ->with(['licence'])
            ->andReturn(['foo' => 'bar']);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::INTERNAL_USER, null)
            ->andReturn(false);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($application);

        $sections = [
            'fooBar' => 'foo',
            'barFoo' => 'bar'
        ];

        $this->mockedSmServices['SectionConfig']
            ->shouldReceive('getAll')
            ->andReturn($sections);

        $expectedAccess = [
            'external',
            'application',
            Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
            Licence::LICENCE_TYPE_STANDARD_NATIONAL,
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
