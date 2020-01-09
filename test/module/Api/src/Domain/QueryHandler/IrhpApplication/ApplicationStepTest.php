<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Service\Qa\Element\SelfservePage;
use Dvsa\Olcs\Api\Service\Qa\Element\SelfservePageGenerator;
use Dvsa\Olcs\Api\Service\Qa\ApplicationStepObjectsProvider;
use Dvsa\Olcs\Transfer\Query\IrhpApplication\ApplicationStep as ApplicationStepQry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

class ApplicationStepTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new ApplicationStep();

        $this->mockedSmServices = [
            'QaApplicationStepObjectsProvider' => m::mock(ApplicationStepObjectsProvider::class),
            'QaSelfservePageGenerator' => m::mock(SelfservePageGenerator::class),
        ];

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $irhpApplicationId = 457;
        $slug = 'removals-eligibility';

        $query = ApplicationStepQry::create(
            [
                'id' => $irhpApplicationId,
                'slug' => $slug,
            ]
        );

        $applicationStepEntity = m::mock(ApplicationStepEntity::class);
        $irhpApplicationEntity = m::mock(IrhpApplicationEntity::class);

        $applicationStepObjects = [
            'applicationStep' => $applicationStepEntity,
            'irhpApplication' => $irhpApplicationEntity
        ];

        $this->mockedSmServices['QaApplicationStepObjectsProvider']->shouldReceive('getObjects')
            ->with($irhpApplicationId, $slug)
            ->andReturn($applicationStepObjects);

        $selfservePage = m::mock(SelfservePage::class);

        $this->mockedSmServices['QaSelfservePageGenerator']->shouldReceive('generate')
            ->with($applicationStepEntity, $irhpApplicationEntity)
            ->once()
            ->andReturn($selfservePage);

        $selfservePageRepresentation = ['selfservePageRepresentation'];

        $selfservePage->shouldReceive('getRepresentation')
            ->andReturn($selfservePageRepresentation);

        $this->assertEquals(
            $selfservePageRepresentation,
            $this->sut->handleQuery($query)
        );
    }
}
