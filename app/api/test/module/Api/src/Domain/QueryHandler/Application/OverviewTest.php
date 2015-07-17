<?php

/**
 * Overview Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Application;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\Application\Overview;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\Fee as FeeRepo;
use Dvsa\Olcs\Api\Domain\Repository\Opposition as OppositionRepo;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Transfer\Query\Application\Overview as Qry;
use Dvsa\Olcs\Transfer\Query\Licence\Overview as LicenceOverviewQry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * Overview Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class OverviewTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        // $this->sut = new Overview();
        $this->sut = m::mock(Overview::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $this->mockRepo('Application', ApplicationRepo::class);
        $this->mockRepo('Fee', FeeRepo::class);
        $this->mockRepo('Opposition', OppositionRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $applicationId = 111;
        $licenceId = 7;

        $query = Qry::create(['id' => $applicationId]);

        $mockApplication = m::mock(ApplicationEntity::class)->makePartial();
        $mockApplication
            ->setId($applicationId)
            ->shouldReceive('serialize')
            ->andReturn(['id' => $applicationId]);
        $mockApplication
            ->shouldReceive('getLicence->getId')
            ->andReturn($licenceId);

        $this->repoMap['Application']
            ->shouldReceive('fetchUsingId')
            ->with($query)
            ->once()
            ->andReturn($mockApplication);

        $licenceResult = m::mock(Result::class)
            ->shouldReceive('serialize')
            ->andReturn(['id' => $licenceId])
            ->getMock();
        $this->sut->shouldReceive('getQueryHandler->handleQuery')
            ->with(m::type(LicenceOverviewQry::class))
            ->andReturn($licenceResult);

        $this->repoMap['Fee']
            ->shouldReceive('fetchOutstandingFeesByApplicationId')
            ->with($applicationId)
            ->once()
            ->andReturn(['fee1', 'fee2']);

        $this->repoMap['Opposition']
            ->shouldReceive('fetchByApplicationId')
            ->with($applicationId)
            ->once()
            ->andReturn(['oppo1', 'oppo2', 'oppo3']);

        $result = $this->sut->handleQuery($query);

        $this->assertInstanceOf(Result::class, $result);

        $this->assertEquals(
            [
                'id' => 111,
                'licence' => [
                    'id' => $licenceId,
                ],
                'feeCount' => 2,
                'oppositionCount' => 3,
                'valueOptions' => [
                    'tracking' => [
                        0 => '',
                        1 => 'Accepted',
                        2 => 'Not accepted',
                        3 => 'Not applicable',
                    ],
                ],
            ],
            $result->serialize()
        );
    }
}
