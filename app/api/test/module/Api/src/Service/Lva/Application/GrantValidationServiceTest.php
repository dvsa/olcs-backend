<?php

namespace Dvsa\OlcsTest\Api\Service\Lva\Application;

use Mockery as m;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;

/**
 * GrantValidationServiceTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class GrantValidationServiceTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    protected $sut;

    /**
     * Setup the helper
     */
    public function setUp()
    {
        $this->sectionAccessService = m::mock();

        $sm = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class);
        $sm->shouldReceive('get')->with('SectionAccessService')->once()->andReturn($this->sectionAccessService);

        $this->sut = (new \Dvsa\Olcs\Api\Service\Lva\Application\GrantValidationService)->createService($sm);
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
            ->andReturn(RefData::FEE_TYPE_VAR);

        /** @var Fee $fee2 */
        $fee2 = m::mock(Fee::class)->makePartial();
        $fee2->shouldReceive('getFeeType->getFeeType->getId')
            ->andReturn(RefData::FEE_TYPE_GRANT);

        $fees = new ArrayCollection();
        $fees->add($fee1);
        $fees->add($fee2);

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

        $application->shouldReceive('getS4s')->andReturn(new \Doctrine\Common\Collections\ArrayCollection());
        $application->shouldReceive('getOverrideOoo')->andReturn('Y');

        $this->sectionAccessService->shouldReceive('getAccessibleSections')
            ->with($application)
            ->andReturn($sections);

        $result = $this->sut->validate($application);

        $this->assertEquals($expectedMessages, $result);
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
            ->andReturn(RefData::FEE_TYPE_VAR);

        /** @var Fee $fee2 */
        $fee2 = m::mock(Fee::class)->makePartial();
        $fee2->shouldReceive('getFeeType->getFeeType->getId')
            ->andReturn(RefData::FEE_TYPE_GRANT);

        $fees = new ArrayCollection();
        $fees->add($fee1);
        $fees->add($fee2);

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

        $application->shouldReceive('getS4s')->andReturn(new \Doctrine\Common\Collections\ArrayCollection());
        $application->shouldReceive('getOverrideOoo')->andReturn('Y');

        $this->sectionAccessService->shouldReceive('getAccessibleSections')
            ->with($application)
            ->andReturn($sections);

        $result = $this->sut->validate($application);
        $this->assertEquals($expectedMessages, $result);
    }

    public function testHandleQueryNewAppCanGrant()
    {
        $sections = [

        ];

        $fees = new ArrayCollection();

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

        $application->shouldReceive('getS4s')->andReturn(new \Doctrine\Common\Collections\ArrayCollection());
        $application->shouldReceive('getOverrideOoo')->andReturn('Y');

        $this->sectionAccessService->shouldReceive('getAccessibleSections')
            ->with($application)
            ->andReturn($sections);

        $result = $this->sut->validate($application);
        $this->assertEquals([], $result);
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
            ->andReturn(RefData::FEE_TYPE_VAR);

        /** @var Fee $fee2 */
        $fee2 = m::mock(Fee::class)->makePartial();
        $fee2->shouldReceive('getFeeType->getFeeType->getId')
            ->andReturn(RefData::FEE_TYPE_GRANT);

        $fees = new ArrayCollection();
        $fees->add($fee1);
        $fees->add($fee2);

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

        $application->shouldReceive('getS4s')->andReturn(new \Doctrine\Common\Collections\ArrayCollection());
        $application->shouldReceive('getOverrideOoo')->andReturn('Y');

        $this->sectionAccessService->shouldReceive('getAccessibleSections')
            ->with($application)
            ->andReturn($sections);

        $result = $this->sut->validate($application);

        $this->assertEquals($expectedMessages, $result);
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
            ->andReturn(RefData::FEE_TYPE_VAR);

        /** @var Fee $fee2 */
        $fee2 = m::mock(Fee::class)->makePartial();
        $fee2->shouldReceive('getFeeType->getFeeType->getId')
            ->andReturn(RefData::FEE_TYPE_GRANT);

        $fees = new ArrayCollection();
        $fees->add($fee1);
        $fees->add($fee2);

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

        $application->shouldReceive('getS4s')->andReturn(new \Doctrine\Common\Collections\ArrayCollection());
        $application->shouldReceive('getOverrideOoo')->andReturn('Y');

        $this->sectionAccessService->shouldReceive('getAccessibleSections')
            ->with($application)
            ->andReturn($sections);

        $result = $this->sut->validate($application);

        $this->assertEquals($expectedMessages, $result);
    }

    public function testHandleCommandS4Validation()
    {
        $s4 = m::mock(\Dvsa\Olcs\Api\Entity\Application\S4::class)->makePartial();

        /* @var $application ApplicationEntity */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setId(111);
        $application->setS4s(new \Doctrine\Common\Collections\ArrayCollection());
        $application->addS4s($s4);
        $application->shouldReceive('getOverrideOoo')->andReturn('Y');
        $application->shouldReceive('getApplicationTracking->isValid')->andReturn(true);
        $application->shouldReceive('getApplicationCompletion->isComplete')->andReturn(false);
        $application->shouldReceive('getApplicationCompletion->getIncompleteSections')->andReturn(['foo' => 'bar']);
        $application->shouldReceive('getFees->matching')->andReturn(new ArrayCollection());
        $application->shouldReceive('getLicence->getEnforcementArea')->andReturn(null);

        $this->sectionAccessService->shouldReceive('getAccessibleSections')->andReturn([]);

        $result = $this->sut->validate($application);

        $this->assertArrayHasKey('APP-GRA-S4-EMPTY', $result);
        $this->assertArrayNotHasKey('APP-GRA-OOOD-UNKNOWN', $result);
        $this->assertArrayNotHasKey('APP-GRA-OORD-UNKNOWN', $result);
    }

    public function testHandleCommandOppositionUnknown()
    {
        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setId(111);
        $application->shouldReceive('getS4s')->andReturn(new \Doctrine\Common\Collections\ArrayCollection());
        $application->shouldReceive('getOutOfOppositionDate')->andReturn(ApplicationEntity::UNKNOWN);
        $application->shouldReceive('getOutOfRepresentationDate')->andReturn(ApplicationEntity::UNKNOWN);

        $application->shouldReceive('getApplicationTracking->isValid')->andReturn(true);
        $application->shouldReceive('getApplicationCompletion->isComplete')->andReturn(false);
        $application->shouldReceive('getApplicationCompletion->getIncompleteSections')->andReturn(['foo' => 'bar']);
        $application->shouldReceive('getFees->matching')->andReturn(new ArrayCollection());
        $application->shouldReceive('getLicence->getEnforcementArea')->andReturn(null);

        $this->sectionAccessService->shouldReceive('getAccessibleSections')->andReturn([]);

        $result = $this->sut->validate($application);

        $this->assertArrayNotHasKey('APP-GRA-S4-EMPTY', $result);
        $this->assertArrayHasKey('APP-GRA-OOOD-UNKNOWN', $result);
        $this->assertArrayHasKey('APP-GRA-OORD-UNKNOWN', $result);
    }

    public function testHandleCommandOppositionNotPassed()
    {
        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setId(111);
        $application->shouldReceive('getS4s')->andReturn(new \Doctrine\Common\Collections\ArrayCollection());
        $application->shouldReceive('getOutOfOppositionDate')->andReturn(new \DateTime('2093-12-02'));
        $application->shouldReceive('getOutOfRepresentationDate')->andReturn(new \DateTime('2056-03-23'));

        $application->shouldReceive('getApplicationTracking->isValid')->andReturn(true);
        $application->shouldReceive('getApplicationCompletion->isComplete')->andReturn(false);
        $application->shouldReceive('getApplicationCompletion->getIncompleteSections')->andReturn(['foo' => 'bar']);
        $application->shouldReceive('getFees->matching')->andReturn(new ArrayCollection());
        $application->shouldReceive('getLicence->getEnforcementArea')->andReturn(null);

        $this->sectionAccessService->shouldReceive('getAccessibleSections')->andReturn([]);

        $result = $this->sut->validate($application);

        $this->assertArrayNotHasKey('APP-GRA-S4-EMPTY', $result);
        $this->assertArrayHasKey('APP-GRA-OOOD-NOT-PASSED', $result);
        $this->assertArrayHasKey('APP-GRA-OORD-NOT-PASSED', $result);
    }
}
