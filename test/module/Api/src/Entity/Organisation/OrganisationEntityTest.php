<?php

namespace Dvsa\OlcsTest\Api\Entity\Organisation;

use Mockery as m;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as Entity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Disqualification;
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
            'cpid',
            'createdBy',
            'createdOn',
            'id',
            'irfoContactDetails',
            'irfoName',
            'irfoNationality',
            'isIrfo',
            'isUnlicensed',
            'lastModifiedBy',
            'lastModifiedOn',
            'leadTcArea',
            'name',
            'natureOfBusinesses',
            'type',
            'version',
            'viAction',
            'disqualifications',
            'irfoPartners',
            'licences',
            'organisationPersons',
            'organisationUsers',
            'tradingNames',
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

    /**
     * @dataProvider organisationDataProvider
     */
    public function testUpdateOrganisation($isIrfo, $lastName, $expectedName)
    {
         $organisation = m::mock(Entity::class)->makePartial();

         $mockBusinessType = m::mock()
             ->shouldReceive('getId')
            ->andReturn('type')
            ->getMock();

        $organisation->updateOrganisation(
            'name',
            '12345678',
            'fname',
            $lastName,
            $isIrfo,
            $mockBusinessType,
            ['nob'],
            ['cpid']
        );

        $this->assertEquals($organisation->getCpid(), ['cpid']);
        $this->assertEquals($organisation->getName(), $expectedName);
        $this->assertEquals($organisation->getCompanyOrLlpNo(), '12345678');
        $this->assertEquals($organisation->getIsIrfo(), $isIrfo);
        $this->assertEquals($organisation->getType()->getId(), 'type');
        $this->assertEquals($organisation->getNatureOfBusinesses(), ['nob']);
    }

    public function organisationDataProvider()
    {
        return [
            ['Y', 'lname', 'fname lname'],
            ['N', '', 'name']
        ];
    }

    public function testGetAdminOrganisationUsers()
    {
        $organisation = m::mock(Entity::class)->makePartial();
        $organisation->shouldReceive('getOrganisationUsers->matching')
            ->with(m::type(Criteria::class))
            ->andReturn(['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar'], $organisation->getAdminOrganisationUsers());
    }

    public function dpOrgTypes()
    {
        return [
            [Entity::ORG_TYPE_SOLE_TRADER],
            [Entity::ORG_TYPE_IRFO],
            [Entity::ORG_TYPE_LLP],
            [Entity::ORG_TYPE_OTHER],
            [Entity::ORG_TYPE_PARTNERSHIP],
            [Entity::ORG_TYPE_REGISTERED_COMPANY],
        ];
    }

    /**
     * @dataProvider dpOrgTypes
     *
     * @param string $typeId
     */
    public function testIsSoleTrader($typeId)
    {
        $type = new \Dvsa\Olcs\Api\Entity\System\RefData();
        $type->setId($typeId);
        $organisation = new Entity();
        $organisation->setType($type);

        $this->assertSame($typeId === Entity::ORG_TYPE_SOLE_TRADER, $organisation->isSoleTrader());
    }

    /**
     * @dataProvider dpOrgTypes
     *
     * @param string $typeId
     */
    public function testIsPartnership($typeId)
    {
        $type = new \Dvsa\Olcs\Api\Entity\System\RefData();
        $type->setId($typeId);
        $organisation = new Entity();
        $organisation->setType($type);

        $this->assertSame($typeId === Entity::ORG_TYPE_PARTNERSHIP, $organisation->isPartnership());
    }

    public function testIsUnlicensed()
    {
        /** @var Entity $organisation */
        $organisation = $this->instantiate($this->entityClass);

        $this->assertFalse($organisation->isUnlicensed());

        $organisation->setIsUnlicensed(true);

        $this->assertTrue($organisation->isUnlicensed());
    }

    public function testGetActiveLicences()
    {
        /** @var Entity $organisation */
        $organisation = m::mock(Entity::class)->makePartial();
        $organisation->shouldReceive('getLicences->matching')
            ->with(m::type(Criteria::class))
            ->andReturnUsing(
                function (Criteria $criteria) {

                    /** @var \Doctrine\Common\Collections\Expr\Comparison $expr */
                    $expr = $criteria->getWhereExpression();

                    $this->assertEquals('status', $expr->getField());
                    $this->assertEquals('IN', $expr->getOperator());
                    $this->assertEquals(
                        [
                            LicenceEntity::LICENCE_STATUS_VALID,
                            LicenceEntity::LICENCE_STATUS_SUSPENDED,
                            LicenceEntity::LICENCE_STATUS_CURTAILED,
                        ],
                        $expr->getValue()->getValue()
                    );

                    $collection = m::mock();
                    $collection->shouldReceive('toArray')
                        ->andReturn(['active licences']);

                    return $collection;
                }
            );

        $this->assertEquals(
            ['active licences'],
            $organisation->getActiveLicences()->toArray()
        );
    }

    public function testGetDisqualificationNull()
    {
        /* @var $organisation Entity */
        $organisation = $this->instantiate($this->entityClass);
        $organisation->setDisqualifications(new \Doctrine\Common\Collections\ArrayCollection());

        $this->assertSame(null, $organisation->getDisqualification());
    }

    public function testGetDisqualification()
    {
        $disqualification = new Disqualification();

        /* @var $organisation Entity */
        $organisation = $this->instantiate($this->entityClass);
        $organisation->setDisqualifications(new \Doctrine\Common\Collections\ArrayCollection([$disqualification]));

        $this->assertSame($disqualification, $organisation->getDisqualification());
    }

    public function testGetDisqualificationStatusNone()
    {
        /* @var $organisation Entity */
        $organisation = $this->instantiate($this->entityClass);
        $organisation->setDisqualifications(new \Doctrine\Common\Collections\ArrayCollection());

        $this->assertSame(Disqualification::STATUS_NONE, $organisation->getDisqualificationStatus());
    }

    public function testGetDisqualificationStatusActive()
    {
        $disqualification = new Disqualification();
        $disqualification->setIsDisqualified('Y');
        $disqualification->setStartDate('2015-01-01');

        /* @var $organisation Entity */
        $organisation = $this->instantiate($this->entityClass);
        $organisation->setDisqualifications(new \Doctrine\Common\Collections\ArrayCollection([$disqualification]));

        $this->assertSame(Disqualification::STATUS_ACTIVE, $organisation->getDisqualificationStatus());
    }
}
