<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\ContinuationDetail;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\ContinuationDetail\LicenceChecklist;
use Dvsa\Olcs\Api\Domain\Repository\ContinuationDetail as ContinuationDetailRepo;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail as ContinuationDetailEntity;
use Dvsa\Olcs\Transfer\Query\ContinuationDetail\LicenceChecklist as LicenceChecklistQry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

class LicenceChecklistTest extends QueryHandlerTestCase
{
    /** @var  LicenceChecklist */
    protected $sut;

    public function setUp()
    {
        $this->sut = new LicenceChecklist();

        $this->mockRepo('ContinuationDetail', ContinuationDetailRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        /** @var ContinuationDetailEntity $continuationDetail */
        $mockContinuationDetail = m::mock(ContinuationDetailEntity::class)
            ->shouldReceive('serialize')
            ->andReturn(
                [
                    'licence' => [
                        'licenceType' => 'expected',
                        'status' => 'expected',
                        'goodsOrPsv' => 'expected',
                        'trafficArea' => 'expected'
                    ]
                ]
            )
            ->getMock();

        $query = LicenceChecklistQry::create([]);

        $this->repoMap['ContinuationDetail']->shouldReceive('fetchUsingId')
            ->with($query)
            ->once()
            ->andReturn($mockContinuationDetail);

        $expected = [
            'licence' => [
                'licenceType' => 'expected',
                'status' => 'expected',
                'goodsOrPsv' => 'expected',
                'trafficArea' => 'expected'
            ]
        ];
        $this->assertEquals($expected, $this->sut->handleQuery($query)->serialize());
    }
}
