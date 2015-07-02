<?php

/**
 * Variation Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Variation;

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

        $this->mockedSmServices['SectionAccessService'] = m::mock();

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 111]);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->shouldReceive('serialize')
            ->with(['licence'])
            ->andReturn(['foo' => 'bar']);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($application);

        $sections = ['bar', 'cake'];

        $this->mockedSmServices['SectionAccessService']->shouldReceive('getAccessibleSections')
            ->once()
            ->with($application)
            ->andReturn($sections);

        $result = $this->sut->handleQuery($query);

        $expected = [
            'foo' => 'bar',
            'sections' => ['bar', 'cake']
        ];

        $this->assertEquals($expected, $result->serialize());
    }
}
