<?php

/**
 * Variation Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Variation;

use Dvsa\Olcs\Api\Domain\QueryHandler\Application\Application;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Transfer\Query\Application\Application as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;
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

        $this->mockedSmServices = [
            'FeesHelperService' => m::mock(),
            'SectionAccessService' => m::mock(),
        ];

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $applicationId = 111;
        $query = Qry::create(['id' => $applicationId]);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application
            ->setId($applicationId)
            ->shouldReceive('getPublicationLinks')->with()->once()
                ->andReturn(new \Doctrine\Common\Collections\ArrayCollection())
            ->shouldReceive('serialize')->andReturn(['foo' => 'bar']);
        $application->setStatus((new \Dvsa\Olcs\Api\Entity\System\RefData())->setId('apsts_not_submitted'));

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($application);

        $sections = ['bar', 'cake'];

        $this->mockedSmServices['SectionAccessService']->shouldReceive('getAccessibleSections')
            ->once()
            ->with($application)
            ->andReturn($sections);

        $applicationFee = $this->getMockFee('100');
        $fees = [$applicationFee];
        $this->mockedSmServices['FeesHelperService']
            ->shouldReceive('getOutstandingFeesForApplication')
            ->with($applicationId)
            ->once()
            ->andReturn($fees);

        $result = $this->sut->handleQuery($query);

        $expected = [
            'foo' => 'bar',
            'sections' => ['bar', 'cake'],
            'outstandingFeeTotal' => '100.00',
            'variationCompletion' => null,
            'canCreateCase' => false,
            'existingPublication' => false,
            'isPublishable' => true,
        ];

        $this->assertEquals($expected, $result->serialize());
    }

    protected function getMockFee($amount)
    {
        $mock = m::mock(FeeEntity::class)->makePartial();
        $mock
            ->setAmount($amount)
            ->shouldReceive('getOutstandingAmount')
            ->andReturn($amount)
            ->shouldReceive('serialize')
            ->andReturn(['amount' => $amount]);
        return $mock;
    }
}
