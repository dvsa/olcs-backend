<?php

/**
 * Safety Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Licence;

use Doctrine\ORM\Query;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Licence\Safety;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Transfer\Query\Licence\Safety as Qry;
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
        $this->mockRepo('Licence', Repository\Licence::class);
        $this->mockRepo('Workshop', Repository\Workshop::class);

        $this->repoMap['Licence']->shouldReceive('getCategoryReference')
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
    public function testHandleQuery($canHaveTrailer)
    {
        $licence = m::mock(BundleSerializableInterface::class);

        $mockSafetyDocument = m::mock()
            ->shouldReceive('serialize')
            ->andReturn(['DOCUMENT'])
            ->once()
            ->getMock();

        $licence->shouldReceive('canHaveTrailer')
            ->withNoArgs()
            ->andReturn($canHaveTrailer)
            ->shouldReceive('getLicenceDocuments')
            ->andReturn([$mockSafetyDocument])
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
        $this->repoMap['Licence']->shouldReceive('fetchSafetyDetailsUsingId')
            ->with($query)
            ->andReturn($licence);

        $this->assertEquals(
            [
                'foo' => 'bar',
                'canHaveTrailers' => $canHaveTrailer,
                'isShowTrailers' => false,
                'safetyDocuments' => [['DOCUMENT']],
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
            [true],
            [false],
        ];
    }
}
