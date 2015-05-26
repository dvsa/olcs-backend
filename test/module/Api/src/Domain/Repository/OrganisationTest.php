<?php

/**
 * Organisation test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\Organisation as OrganisationRepo;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as Entity;

/**
 * Organisation test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OrganisationTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(OrganisationRepo::class);
    }

    public function testHasInforceLicences()
    {
        /** @var Entity $organisation */
        $organisation = m::mock(Entity::class)->makePartial();
        $organisation->shouldReceive('getLicences->matching')
            ->with(m::type(Criteria::class))
            ->andReturnUsing(
                function (Criteria $criteria) {

                    /** @var \Doctrine\Common\Collections\Expr\Comparison $expr */
                    $expr = $criteria->getWhereExpression();

                    $this->assertEquals('inForceDate', $expr->getField());
                    $this->assertEquals('<>', $expr->getOperator());
                    $this->assertEquals(null, $expr->getValue()->getValue());

                    return ['foo'];
                }
            );

        $this->em->shouldReceive('find')
            ->with(Entity::class, 111)
            ->andReturn($organisation);

        $this->assertTrue($this->sut->hasInforceLicences(111));
    }
}
