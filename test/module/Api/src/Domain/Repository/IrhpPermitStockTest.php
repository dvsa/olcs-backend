<?php

/**
 * LegacyOffence Repo test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\Legacy\LegacyOffence;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as Repo;
use Doctrine\ORM\EntityRepository;

/**
 * IrhpPermitStock Repo test
 *
 */
class IrhpPermitStockTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(Repo::class);
    }

    public function testGetNextIrhpPermitStockByPermitType()
    {

    }
}
