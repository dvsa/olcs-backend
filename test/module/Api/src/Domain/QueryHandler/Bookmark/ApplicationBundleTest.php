<?php

/**
 * Application Bundle Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Bookmark;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Bookmark\ApplicationBundle;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\ApplicationBundle as Qry;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;

/**
 * Application Bundle Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationBundleTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new ApplicationBundle();
        $this->mockRepo('Application', ApplicationRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 111, 'bundle' => ['foo' => ['bar']]]);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->shouldReceive('serialize')
            ->with(['foo' => ['bar']])
            ->andReturn(['id' => 111]);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($application);

        $this->assertEquals(['id' => 111], $this->sut->handleQuery($query));
    }
}
