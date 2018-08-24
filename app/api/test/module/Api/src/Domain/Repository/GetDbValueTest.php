<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\Repository\GetDbValue as GetDbValueRepo;
use Dvsa\Olcs\Api\Entity\Application\Application;

class GetDbValueTest extends RepositoryTestCase
{

    /** @var GetDbValueRepo */
    protected $sut;

    public function setUp()
    {
        $this->setUpSut(GetDbValueRepo::class, true);
    }

    public function testFetchOneEntityByX()
    {
        $fetchBy = 'id';
        $args = 1;
        $this->sut->setEntity(Application::class);
        $qb = $this->createMockQb('Query');
        $qb->shouldReceive('getQuery->getSingleResult')->andReturn(['RESULTS']);
        $this->sut->shouldReceive('getRepository->createQueryBuilder')
            ->andReturn($qb);
        $this->assertEquals(['RESULTS'], $this->sut->fetchOneEntityByX($fetchBy, $args));
    }
}