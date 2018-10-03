<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\IrhpCandidatePermit;

use Dvsa\Olcs\Api\Domain\QueryHandler\IrhpCandidatePermit\GetScoredList as GetScoredListHandler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpCandidatePermit as IrhpCandidatePermitRepo;
use Dvsa\Olcs\Transfer\Query\IrhpCandidatePermit\GetScoredList as QryClass;
use Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit as IrhpCandidatePermitEntity;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository;



/**
 * GetScoredList Test
 *
 * @author Jason de Jonge <jason.de-jonge@capgemini.com>
 */
class GetScoredListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new GetScoredListHandler();
        $this->mockRepo('IrhpCandidatePermit', IrhpCandidatePermitRepo::class);
    }

    public function testHandleQuery()
    {
        $query = QryClass::create([ 'stockId' => 1]);

        $item1 = m::mock(IrhpCandidatePermitEntity::class)->makePartial();
        $item2 = m::mock(IrhpCandidatePermitEntity::class)->makePartial();
        $scoredPermits = new ArrayCollection();

        $scoredPermits->add($item1);
        $scoredPermits->add($item2);

        $this->repoMap['IrhpCandidatePermit']
            ->shouldReceive('fetchAllScoredForStock')
            ->with($query->getStockId())
            ->once()
            ->andReturn($scoredPermits);

        $result = $this->sut->handleQuery($query);

        $expected = [
            'result' => [
                0 => [],
                1 => []
            ]
        ];

        $this->assertArraySubset($expected, $result);
    }
}
