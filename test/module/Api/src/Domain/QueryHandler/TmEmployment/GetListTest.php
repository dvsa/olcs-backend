<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\TmEmployment;

use Dvsa\Olcs\Api\Entity\Tm\TransportManager;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\TmEmployment\GetList as QueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\TmEmployment as Repo;
use Dvsa\Olcs\Transfer\Query\TmEmployment\GetList as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;

/**
 * GetListTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class GetListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('TmEmployment', Repo::class);
        $this->mockRepo('TransportManager', Repo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Query::create(['transportManager' => 1]);

        $tm = new TransportManager();

        $this->repoMap['TransportManager']->shouldReceive('fetchById')
            ->with(1)->andReturn([$tm]);

        $tmEmployment = m::mock(\Dvsa\Olcs\Api\Entity\Tm\TmEmployment::class);
        $tmEmployment->shouldReceive('serialize')->with(
            [
                'contactDetails' => [
                    'address' => [
                        'countryCode',
                    ]
                ]
            ]
        )->once()->andReturn('SERIALIZE');

        $this->repoMap['TmEmployment']->shouldReceive('fetchList')->with($query, \Doctrine\ORM\Query::HYDRATE_OBJECT)
            ->andReturn([$tmEmployment]);
        $this->repoMap['TmEmployment']->shouldReceive('fetchCount')->with($query)->andReturn('COUNT');

        $result = $this->sut->handleQuery($query);

        $this->assertSame(['SERIALIZE'], $result['result']);
        $this->assertSame('COUNT', $result['count']);
    }
}
