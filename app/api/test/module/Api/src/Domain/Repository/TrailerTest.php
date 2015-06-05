<?php

/**
 * Class TrailerTest
 *
 * @package Dvsa\OlcsTest\Api\Domain\Repository
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Mockery as m;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

use Dvsa\Olcs\Transfer\Query\Trailer\Trailers as Qry;

use Dvsa\Olcs\Api\Domain\Repository\Trailer as TrailerRepo;

/**
 * Class TrailerTest
 *
 * @package Dvsa\OlcsTest\Api\Domain\Repository
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class TrailerTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(TrailerRepo::class);
    }

    public function testFetchByLicence()
    {
        $data = [
            'licence' => 1
        ];

        $this->em
            ->shouldReceive('getRepository->createQueryBuilder')
            ->with('m')
            ->once()
            ->andReturn(
                m::mock()
                    ->shouldReceive('expr')
                    ->once()
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('eq')
                            ->with('m.licence', ':licenceId')
                            ->once()
                            ->getMock()
                    )
                    ->shouldReceive('where')
                    ->once()
                    ->shouldReceive('setParameter')
                    ->with(':licenceId', $data['licence'])
                    ->shouldReceive('getQuery')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('getResult')
                            ->getMock()
                    )
                    ->getMock()
            );

        $query = Qry::create($data);
        $this->sut->fetchByLicenceId($query);
    }
}
