<?php

/**
 * Safety Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Application;

use Doctrine\ORM\Query;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Application\Safety;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Transfer\Query\Application\Safety as Qry;
use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;

/**
 * Safety Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class SafetyTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new Safety();
        $this->mockRepo('Application', Repository\Application::class);
        $this->mockRepo('Workshop', Repository\Workshop::class);

        $this->repoMap['Application']->shouldReceive('getCategoryReference')
            ->andReturnUsing(
                function ($category) {
                    return $category;
                }
            )
            ->shouldReceive('getSubCategoryReference')
            ->andReturnUsing(
                function ($category) {
                    return $category;
                }
            );

        parent::setUp();
    }

    /**
     * @dataProvider trailersProvider
     */
    public function testHandleQuery($licenceType, $canHaveTrailers)
    {
        $application = m::mock(BundleSerializableInterface::class);

        $application->shouldReceive('getGoodsOrPsv')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getId')
                    ->andReturn($licenceType)
                    ->once()
                    ->getMock()
            )
            ->shouldReceive('getApplicationDocuments')
            ->andReturn(
                [
                    m::mock(BundleSerializableInterface::class)->shouldReceive('serialize')->andReturn(['foo' => 'bar'])
                        ->getMock()
                ]
            )
            ->once()
            ->shouldReceive('getTotAuthTrailers')
            ->andReturn(0)
            ->once()
            ->shouldReceive('serialize')
            ->andReturn(['foo' => 'bar'])
            ->once()
            ->getMock();

        $query = Qry::create(['id' => 111]);

        $mockWorkshop = m::mock();
        $mockWorkshop->shouldReceive('serialize')->with(['contactDetails' => ['address']])->once()
            ->andReturn('SERIALIZED WORKSHOP');
        $this->repoMap['Workshop']->shouldReceive('fetchList')->with($query, Query::HYDRATE_OBJECT)->once()
            ->andReturn([$mockWorkshop]);
        $this->repoMap['Workshop']->shouldReceive('fetchCount')->with($query)->once()->andReturn(99);
        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($application);

        $this->assertEquals(
            [
                'foo' => 'bar',
                'canHaveTrailers' => $canHaveTrailers,
                'isShowTrailers' => false,
                'safetyDocuments' => [['foo' => 'bar']],
                'workshops' => [
                    'results' => ['SERIALIZED WORKSHOP'],
                    'count' => 99,
                ]
            ],
            $this->sut->handleQuery($query)->serialize()
        );
    }

    public function trailersProvider()
    {
        return [
            [
                Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
                true
            ],
            [
                Licence::LICENCE_CATEGORY_PSV,
                false
            ],
        ];
    }
}
