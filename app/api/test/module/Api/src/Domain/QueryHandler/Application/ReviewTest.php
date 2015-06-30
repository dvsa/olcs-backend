<?php

/**
 * Review Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Entity\Application\ApplicationCompletion;
use Dvsa\Olcs\Transfer\Query\Application\Application;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Api\Domain\QueryHandler\Application\Review;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Transfer\Query\Application\Review as Qry;

/**
 * Review Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ReviewTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Review();
        $this->mockRepo('Application', ApplicationRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 111]);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setIsVariation(false);
        $application->shouldReceive('isGoods')
            ->andReturn(true)
            ->shouldReceive('isSpecialRestricted')
            ->andReturn(false)
            ->shouldReceive('serialize')
            ->once()
            ->andReturn(['foo' => 'bar']);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($application);

        $result = m::mock();
        $result->shouldReceive('serialize')
            ->andReturn(
                [
                    'sections' => [
                        'foo' => 'fooBar',
                        'bar' => 'barFoo',
                        'community_licences' => 'test'
                    ],
                ]
            );

        $this->queryHandler->shouldReceive('handleQuery')
            ->with(m::type(Application::class))
            ->andReturn($result);

        $result = $this->sut->handleQuery($query);

        $expected = [
            'foo' => 'bar',
            'sections' => [
                'foo',
                'bar'
            ],
            'isGoods' => true,
            'isSpecialRestricted' => false
        ];

        $this->assertEquals($expected, $result->serialize());
    }

    public function testHandleQueryVariation()
    {
        $query = Qry::create(['id' => 111]);

        /** @var ApplicationCompletion $appCompletion */
        $appCompletion = m::mock(ApplicationCompletion::class)->makePartial();
        $appCompletion->setAddressesStatus(ApplicationEntity::VARIATION_STATUS_UNCHANGED);
        $appCompletion->setVehiclesStatus(ApplicationEntity::VARIATION_STATUS_UPDATED);
        $appCompletion->setPeopleStatus(ApplicationEntity::VARIATION_STATUS_UPDATED);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setIsVariation(true);
        $application->setApplicationCompletion($appCompletion);
        $application->shouldReceive('isGoods')
            ->andReturn(true)
            ->shouldReceive('isSpecialRestricted')
            ->andReturn(false)
            ->shouldReceive('serialize')
            ->once()
            ->andReturn(['foo' => 'bar']);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($application);

        $result = m::mock();
        $result->shouldReceive('serialize')
            ->andReturn(
                [
                    'sections' => [
                        'vehicles' => 'bar',
                        'people' => 'foo',
                        'community_licences' => 'test',
                        'addresses' => 'foo',
                    ],
                ]
            );

        $this->queryHandler->shouldReceive('handleQuery')
            ->with(m::type(Application::class))
            ->andReturn($result);

        $result = $this->sut->handleQuery($query);

        $expected = [
            'foo' => 'bar',
            'sections' => [
                'vehicles',
                'people'
            ],
            'isGoods' => true,
            'isSpecialRestricted' => false
        ];

        $this->assertEquals($expected, $result->serialize());
    }
}
