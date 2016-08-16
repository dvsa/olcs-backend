<?php

/**
 * Safety Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Application;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Application\Safety;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
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
    public function setUp()
    {
        $this->sut = new Safety();
        $this->mockRepo('Application', ApplicationRepo::class);

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

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($application);

        $this->assertEquals(
            [
                'foo' => 'bar',
                'canHaveTrailers' => $canHaveTrailers,
                'isShowTrailers' => false,
                'safetyDocuments' => [['foo' => 'bar']]
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
