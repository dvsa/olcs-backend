<?php

namespace Dvsa\OlcsTest\Api\Entity\Organisation;

use Mockery as m;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as Entity;
use Doctrine\Common\Collections\Criteria;

/**
 * Organisation Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class OrganisationEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testJsonSerialize()
    {
        /** @var Entity $organisation */
        $organisation = $this->instantiate($this->entityClass);

        $organisation->setId(111);

        $values = $organisation->jsonSerialize();

        $expectedKeys = [
            'hasInforceLicences',
            'allowEmail',
            'companyCertSeen',
            'companyOrLlpNo',
            'contactDetails',
            'createdBy',
            'createdOn',
            'id',
            'irfoContactDetails',
            'irfoName',
            'irfoNationality',
            'isIrfo',
            'isMlh',
            'lastModifiedBy',
            'lastModifiedOn',
            'leadTcArea',
            'name',
            'natureOfBusinesses',
            'type',
            'version',
            'viAction',
            'irfoPartners',
            'licences',
            'organisationPersons',
            'organisationUsers',
            'tradingNames'
        ];

        $this->assertEquals($expectedKeys, array_keys($values));
        $this->assertFalse($values['hasInforceLicences']);
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

                    $collection = m::mock();
                    $collection->shouldReceive('toArray')
                        ->andReturn(['foo']);

                    return $collection;
                }
            );

        $this->assertTrue($organisation->hasInforceLicences());
    }
}
