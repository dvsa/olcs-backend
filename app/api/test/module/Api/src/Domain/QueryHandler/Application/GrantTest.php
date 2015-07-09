<?php

/**
 * Grant Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Application;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\QueryHandler\Application\Grant as QueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Transfer\Query\Application\Application as Qry;
use Dvsa\Olcs\Transfer\Query\Application\Grant;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

/**
 * Grant Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GrantTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('Application', ApplicationRepo::class);

        $this->mockedSmServices['SectionAccessService'] = m::mock();

        parent::setUp();
    }

    public function testHandleQueryNewApp()
    {
        $sections = [

        ];

        $expectedMessages = [
            'application-grant-error-tracking' => 'application-grant-error-tracking',
            'application-grant-error-sections' => [
                'foo' => 'bar'
            ],
            'application-grant-error-fees' => 'application-grant-error-fees',
            'application-grant-error-enforcement-area' => 'application-grant-error-enforcement-area'
        ];

        /** @var Fee $fee1 */
        $fee1 = m::mock(Fee::class)->makePartial();
        $fee1->shouldReceive('getFeeType->getFeeType->getId')
            ->andReturn(RefData::FEE_TYPE_GRANTINT);

        /** @var Fee $fee2 */
        $fee2 = m::mock(Fee::class)->makePartial();
        $fee2->shouldReceive('getFeeType->getFeeType->getId')
            ->andReturn(RefData::FEE_TYPE_GRANT);

        $fees = new ArrayCollection();
        $fees->add($fee1);
        $fees->add($fee2);

        $query = Grant::create(['id' => 111]);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setIsVariation(false);
        $application->shouldReceive('getApplicationTracking->isValid')
            ->with($sections)
            ->andReturn(false);

        $application->shouldReceive('getApplicationCompletion->isComplete')
            ->andReturn(false);

        $application->shouldReceive('getApplicationCompletion->getIncompleteSections')
            ->andReturn(['foo' => 'bar']);

        $application->shouldReceive('getFees->matching')
            ->andReturn($fees);

        $application->shouldReceive('getLicence->getEnforcementArea')
            ->andReturn(null);

        $application->shouldReceive('isGoods')
            ->andReturn(true)
            ->shouldReceive('serialize')
            ->with([])
            ->andReturn(['foo' => 'bar']);

        $this->mockedSmServices['SectionAccessService']->shouldReceive('getAccessibleSections')
            ->with($application)
            ->andReturn($sections);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($application)
            ->shouldReceive('getRefdataReference')
            ->with(Fee::STATUS_OUTSTANDING)
            ->andReturn(Fee::STATUS_OUTSTANDING)
            ->shouldReceive('getRefdataReference')
            ->with(Fee::STATUS_WAIVE_RECOMMENDED)
            ->andReturn(Fee::STATUS_WAIVE_RECOMMENDED);

        $result = $this->sut->handleQuery($query);

        $expected = [
            'foo' => 'bar',
            'canGrant' => false,
            'reasons' => $expectedMessages,
            'canHaveInspectionRequest' => false
        ];

        $this->assertEquals($expected, $result->serialize());
    }

    public function testHandleQueryNewAppSr()
    {
        $sections = [

        ];

        $expectedMessages = [
            'application-grant-error-tracking' => 'application-grant-error-tracking',
            'application-grant-error-sections' => [
                'foo' => 'bar'
            ],
            'application-grant-error-fees' => 'application-grant-error-fees',
            'application-grant-error-enforcement-area' => 'application-grant-error-enforcement-area'
        ];

        /** @var Fee $fee1 */
        $fee1 = m::mock(Fee::class)->makePartial();
        $fee1->shouldReceive('getFeeType->getFeeType->getId')
            ->andReturn(RefData::FEE_TYPE_GRANTINT);

        /** @var Fee $fee2 */
        $fee2 = m::mock(Fee::class)->makePartial();
        $fee2->shouldReceive('getFeeType->getFeeType->getId')
            ->andReturn(RefData::FEE_TYPE_GRANT);

        $fees = new ArrayCollection();
        $fees->add($fee1);
        $fees->add($fee2);

        $query = Grant::create(['id' => 111]);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setIsVariation(false);
        $application->shouldReceive('getApplicationTracking->isValid')
            ->with($sections)
            ->andReturn(false);

        $application->shouldReceive('getApplicationCompletion->isComplete')
            ->andReturn(false);

        $application->shouldReceive('getApplicationCompletion->getIncompleteSections')
            ->andReturn(['foo' => 'bar']);

        $application->shouldReceive('getFees->matching')
            ->andReturn($fees);

        $application->shouldReceive('getLicence->getEnforcementArea')
            ->andReturn(null);

        $application->shouldReceive('isGoods')
            ->andReturn(true)
            ->shouldReceive('isSpecialRestricted')
            ->andReturn(true)
            ->shouldReceive('serialize')
            ->with([])
            ->andReturn(['foo' => 'bar']);

        $this->mockedSmServices['SectionAccessService']->shouldReceive('getAccessibleSections')
            ->with($application)
            ->andReturn($sections);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($application)
            ->shouldReceive('getRefdataReference')
            ->with(Fee::STATUS_OUTSTANDING)
            ->andReturn(Fee::STATUS_OUTSTANDING)
            ->shouldReceive('getRefdataReference')
            ->with(Fee::STATUS_WAIVE_RECOMMENDED)
            ->andReturn(Fee::STATUS_WAIVE_RECOMMENDED);

        $result = $this->sut->handleQuery($query);

        $expected = [
            'foo' => 'bar',
            'canGrant' => false,
            'reasons' => $expectedMessages,
            'canHaveInspectionRequest' => false
        ];

        $this->assertEquals($expected, $result->serialize());
    }

    public function testHandleQueryNewAppCanGrant()
    {
        $sections = [

        ];

        $fees = new ArrayCollection();

        $query = Grant::create(['id' => 111]);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setIsVariation(false);
        $application->shouldReceive('getApplicationTracking->isValid')
            ->with($sections)
            ->andReturn(true);

        $application->shouldReceive('getApplicationCompletion->isComplete')
            ->andReturn(true);

        $application->shouldReceive('getFees->matching')
            ->andReturn($fees);

        $application->shouldReceive('getLicence->getEnforcementArea')
            ->andReturn(['foo']);

        $application->shouldReceive('isGoods')
            ->andReturn(false)
            ->shouldReceive('isSpecialRestricted')
            ->andReturn(true)
            ->shouldReceive('serialize')
            ->with([])
            ->andReturn(['foo' => 'bar']);

        $this->mockedSmServices['SectionAccessService']->shouldReceive('getAccessibleSections')
            ->with($application)
            ->andReturn($sections);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($application)
            ->shouldReceive('getRefdataReference')
            ->with(Fee::STATUS_OUTSTANDING)
            ->andReturn(Fee::STATUS_OUTSTANDING)
            ->shouldReceive('getRefdataReference')
            ->with(Fee::STATUS_WAIVE_RECOMMENDED)
            ->andReturn(Fee::STATUS_WAIVE_RECOMMENDED);

        $result = $this->sut->handleQuery($query);

        $expected = [
            'foo' => 'bar',
            'canGrant' => true,
            'reasons' => [],
            'canHaveInspectionRequest' => true
        ];

        $this->assertEquals($expected, $result->serialize());
    }

    public function testHandleQueryVariation()
    {
        $sections = [

        ];

        $expectedMessages = [
            'application-grant-error-tracking' => 'application-grant-error-tracking',
            'variation-grant-error-no-change' => 'variation-grant-error-no-change',
            'application-grant-error-fees' => 'application-grant-error-fees'
        ];

        /** @var Fee $fee1 */
        $fee1 = m::mock(Fee::class)->makePartial();
        $fee1->shouldReceive('getFeeType->getFeeType->getId')
            ->andReturn(RefData::FEE_TYPE_GRANTINT);

        /** @var Fee $fee2 */
        $fee2 = m::mock(Fee::class)->makePartial();
        $fee2->shouldReceive('getFeeType->getFeeType->getId')
            ->andReturn(RefData::FEE_TYPE_GRANT);

        $fees = new ArrayCollection();
        $fees->add($fee1);
        $fees->add($fee2);

        $query = Grant::create(['id' => 111]);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setIsVariation(true);
        $application->shouldReceive('getApplicationTracking->isValid')
            ->with($sections)
            ->andReturn(false);

        $application->shouldReceive('hasVariationChanges')
            ->andReturn(false);

        $application->shouldReceive('getFees->matching')
            ->andReturn($fees);

        $application->shouldReceive('isGoods')
            ->andReturn(true)
            ->shouldReceive('serialize')
            ->with([])
            ->andReturn(['foo' => 'bar']);

        $this->mockedSmServices['SectionAccessService']->shouldReceive('getAccessibleSections')
            ->with($application)
            ->andReturn($sections);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($application)
            ->shouldReceive('getRefdataReference')
            ->with(Fee::STATUS_OUTSTANDING)
            ->andReturn(Fee::STATUS_OUTSTANDING)
            ->shouldReceive('getRefdataReference')
            ->with(Fee::STATUS_WAIVE_RECOMMENDED)
            ->andReturn(Fee::STATUS_WAIVE_RECOMMENDED);

        $result = $this->sut->handleQuery($query);

        $expected = [
            'foo' => 'bar',
            'canGrant' => false,
            'reasons' => $expectedMessages,
            'canHaveInspectionRequest' => false
        ];

        $this->assertEquals($expected, $result->serialize());
    }

    public function testHandleQueryVariationAlt()
    {
        $sections = [

        ];

        $expectedMessages = [
            'application-grant-error-tracking' => 'application-grant-error-tracking',
            'variation-grant-error-sections' => ['foo' => 'bar'],
            'application-grant-error-fees' => 'application-grant-error-fees'
        ];

        /** @var Fee $fee1 */
        $fee1 = m::mock(Fee::class)->makePartial();
        $fee1->shouldReceive('getFeeType->getFeeType->getId')
            ->andReturn(RefData::FEE_TYPE_GRANTINT);

        /** @var Fee $fee2 */
        $fee2 = m::mock(Fee::class)->makePartial();
        $fee2->shouldReceive('getFeeType->getFeeType->getId')
            ->andReturn(RefData::FEE_TYPE_GRANT);

        $fees = new ArrayCollection();
        $fees->add($fee1);
        $fees->add($fee2);

        $query = Grant::create(['id' => 111]);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setIsVariation(true);
        $application->shouldReceive('getApplicationTracking->isValid')
            ->with($sections)
            ->andReturn(false);

        $application->shouldReceive('hasVariationChanges')
            ->andReturn(true);

        $application->shouldReceive('getSectionsRequiringAttention')
            ->andReturn(['foo' => 'bar']);

        $application->shouldReceive('getFees->matching')
            ->andReturn($fees);

        $application->shouldReceive('isGoods')
            ->andReturn(true)
            ->shouldReceive('serialize')
            ->with([])
            ->andReturn(['foo' => 'bar']);

        $this->mockedSmServices['SectionAccessService']->shouldReceive('getAccessibleSections')
            ->with($application)
            ->andReturn($sections);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($application)
            ->shouldReceive('getRefdataReference')
            ->with(Fee::STATUS_OUTSTANDING)
            ->andReturn(Fee::STATUS_OUTSTANDING)
            ->shouldReceive('getRefdataReference')
            ->with(Fee::STATUS_WAIVE_RECOMMENDED)
            ->andReturn(Fee::STATUS_WAIVE_RECOMMENDED);

        $result = $this->sut->handleQuery($query);

        $expected = [
            'foo' => 'bar',
            'canGrant' => false,
            'reasons' => $expectedMessages,
            'canHaveInspectionRequest' => false
        ];

        $this->assertEquals($expected, $result->serialize());
    }
}
